#!/usr/bin/env bash
# Teste do cadastro de ramais pelo painel (login real do administrador):
# ramal inválido é recusado; válido aparece no site; desativado some; excluído sai do banco.
# Uso: tests/ramais/admin-test.sh   (ambiente no ar)
set -uo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/../.."
set -a; source .env; set +a

B="http://localhost:${HTTP_PORT}/administrator/index.php"
SITE="http://localhost:${HTTP_PORT}/ramais"
J="$(mktemp)"
FAILS=0
SETOR="Setor de Teste Automatizado"
trap 'rm -f "$J"' EXIT

ok()  { printf '  \033[32m✓\033[0m %s\n' "$1"; }
bad() { printf '  \033[31m✗\033[0m %s\n' "$1"; FAILS=$((FAILS + 1)); }
tok() { grep -oP 'name="\K[0-9a-f]{32}(?=" value="1")' | head -1; }
sql() { docker compose exec -T db mariadb -N -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "$1"; }
messages() { grep -oP '"joomla.messages":\K\[[^]]*\]\]?' | head -1; }
site_count() { curl -s -G --data-urlencode "q=$SETOR" "$SITE" | tr '\n' ' ' | grep -oP 'data-directory-count[^>]*>\s*\K[^<]+' | sed 's/ *$//'; }

t=$(curl -s -c "$J" "$B" | tok)
curl -s -b "$J" -c "$J" -o /dev/null --data-urlencode "username=$ADMIN_USERNAME" --data-urlencode "passwd=$ADMIN_PASSWORD" \
    -d "option=com_login&task=login&return=aW5kZXgucGhw&$t=1" "$B"

save() { # $1 = ramal → imprime as mensagens do Joomla
    local html t
    html=$(curl -s -L -b "$J" -c "$J" "$B?option=com_ramais&task=ramal.add")
    t=$(tok <<<"$html")
    curl -s -L -b "$J" -c "$J" --data-urlencode "jform[setor]=$SETOR" --data-urlencode "jform[ramal]=$1" \
        --data-urlencode "jform[localizacao]=Sala de testes" -d "jform[state]=1&jform[id]=0&task=ramal.save&$t=1" \
        "$B?option=com_ramais&layout=edit&id=0" | messages
    # Sai do formulário, se ele continuou aberto (erro de validação)
    t=$(curl -s -b "$J" -c "$J" "$B?option=com_ramais&view=ramal&layout=edit" | tok)
    [[ -n "$t" ]] && curl -s -b "$J" -c "$J" -o /dev/null -d "task=ramal.cancel&$t=1" "$B?option=com_ramais"
}

list_task() { # $1 = tarefa (ramais.unpublish, ramais.trash, ramais.delete), $2 = id
    local t
    t=$(curl -s -b "$J" -c "$J" "$B?option=com_ramais&view=ramais" | tok)
    curl -s -b "$J" -c "$J" -o /dev/null -d "task=$1&cid[]=$2&boxchecked=1&$t=1" "$B?option=com_ramais&view=ramais"
}

sql "DELETE FROM ${DB_PREFIX}ramais WHERE setor = '$SETOR'"

echo "Cadastro de ramais (painel)"
msg=$(save "abc")
[[ "$msg" == *danger* && -z "$(sql "SELECT id FROM ${DB_PREFIX}ramais WHERE setor = '$SETOR'")" ]] \
    && ok "Ramal inválido (abc) recusado" || bad "Ramal inválido: $msg"

msg=$(save "9999")
id=$(sql "SELECT id FROM ${DB_PREFIX}ramais WHERE setor = '$SETOR'")
[[ -n "$id" ]] && ok "Ramal válido (9999) salvo" || bad "Ramal válido não foi salvo: $msg"
[[ "$(site_count)" == "1 ramal" ]] && ok "Aparece no site" || bad "No site: $(site_count)"

list_task ramais.unpublish "$id"
[[ "$(site_count)" == "Nenhum ramal encontrado" ]] && ok "Desativado: some do site" || bad "Após desativar, no site: $(site_count)"

list_task ramais.trash "$id"
list_task ramais.delete "$id"
[[ -z "$(sql "SELECT id FROM ${DB_PREFIX}ramais WHERE id = ${id:-0}")" ]] && ok "Excluído pela lixeira" || bad "Continua no banco"

echo
(( FAILS == 0 )) && echo "Tudo certo." || echo "$FAILS verificação(ões) falharam."
exit $((FAILS > 0))
