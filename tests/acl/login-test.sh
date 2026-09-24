#!/usr/bin/env bash
# Teste de ACL pelo navegador (HTTP): cria um usuário temporário por papel, entra no
# /administrator e verifica o que cada um consegue abrir. Apaga os usuários no fim.
# Uso: tests/acl/login-test.sh   (ambiente no ar)
set -uo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/../.."
set -a; source .env; set +a

BASE="http://localhost:${HTTP_PORT}/administrator/index.php"
PASS="Teste-$(LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c 12)1!"
TMP="$(mktemp -d)"
FAILS=0

jcli() { docker compose exec -T -u www-data joomla php cli/joomla.php "$@"; }

ok()   { printf '  \033[32m✓\033[0m %s\n' "$1"; }
bad()  { printf '  \033[31m✗\033[0m %s\n' "$1"; FAILS=$((FAILS + 1)); }

login() { # $1 = usuário, $2 = arquivo de cookies
    local page token
    page="$(curl -s -c "$2" "$BASE")"
    token="$(grep -oP 'name="\K[0-9a-f]{32}(?=" value="1")' <<<"$page" | head -1)"
    curl -s -b "$2" -c "$2" -o /dev/null \
        --data-urlencode "username=$1" --data-urlencode "passwd=$PASS" \
        -d "option=com_login&task=login&return=aW5kZXgucGhw&$token=1" "$BASE"
}

can_open() { # $1 = cookies, $2 = query → 0 se a tela abriu (HTTP 200), 1 se foi barrado (403/redirecionamento)
    [[ "$(curl -s -b "$1" -o /dev/null -w '%{http_code}' "$BASE?$2")" == "200" ]]
}

check() { # $1 = cookies, $2 = rótulo, $3 = query, $4 = esperado (sim|nao)
    if can_open "$1" "$3"; then got=sim; else got=nao; fi
    [[ "$got" == "$4" ]] && ok "$2: $got" || bad "$2: esperado $4, obtido $got"
}

categories_in_form() { # categorias oferecidas no formulário de novo artigo
    curl -s -L -b "$1" "$BASE?option=com_content&task=article.add" \
        | sed -n '/id="jform_catid"/,/<\/select>/p' \
        | grep -oP '<option[^>]*>\K[^<]+' | sed 's/^[- ]*//' | paste -sd ',' -
}

# Categorias que cada papel deve ver ao criar um artigo (raiz + subcategorias)
declare -A EXPECTED_CATS=(
    [tqualidade]="Biblioteca,Protocolos,POPs,Manuais,Formulários,Políticas,Normas,Fluxogramas,Treinamentos,Documentos de RH,Documentos de TI"
    [timprensa]="Notícias,Institucional,Pessoas,Saúde e Bem-estar,Tecnologia,Recursos Humanos,Eventos,Comunicados,TI,Eventos,Avisos"
)

declare -A USERS=(
    [tqualidade]="Qualidade e Educação Permanente"
    [timprensa]="Imprensa"
)

for u in "${!USERS[@]}"; do
    jcli user:add --username="$u" --name="Teste $u" --password="$PASS" --email="$u@teste.invalid" --usergroup="${USERS[$u]}" -n >/dev/null \
        || { echo "Falha ao criar $u"; exit 1; }
done
trap 'for u in "${!USERS[@]}"; do jcli user:delete --username="$u" -n >/dev/null; done; rm -rf "$TMP"' EXIT

for u in tqualidade timprensa; do
    echo "${USERS[$u]} ($u)"
    c="$TMP/$u"
    login "$u" "$c"
    check "$c" "Painel"                 "" sim
    check "$c" "Artigos"                "option=com_content&view=articles" sim
    check "$c" "Mídia"                  "option=com_media" sim
    check "$c" "Categorias"             "option=com_categories&extension=com_content" nao
    check "$c" "Menus"                  "option=com_menus&view=items" nao
    check "$c" "Módulos"                "option=com_modules" nao
    check "$c" "Usuários"               "option=com_users&view=users" nao
    check "$c" "Campos (estrutura)"     "option=com_fields&view=fields&context=com_content.article" nao
    check "$c" "Configuração global"    "option=com_config" nao
    cats="$(categories_in_form "$c" | python3 -c 'import html,sys; print(html.unescape(sys.stdin.read()), end="")')"
    [[ "$cats" == "${EXPECTED_CATS[$u]}" ]] && ok "Categorias no novo artigo: $cats" || bad "Categorias no novo artigo: $cats"
done

echo
(( FAILS == 0 )) && echo "Tudo certo." || echo "$FAILS verificação(ões) falharam."
exit $((FAILS > 0))
