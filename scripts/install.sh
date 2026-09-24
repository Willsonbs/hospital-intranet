#!/usr/bin/env bash
# Sobe o ambiente e deixa o Joomla instalado e configurado.
# Idempotente: pode ser executado de novo a qualquer momento.
# Uso: scripts/install.sh [--demo]   (--demo cria conteúdo de demonstração)
set -euo pipefail

SETUP_ARGS=()
for arg in "$@"; do
    case "$arg" in
        --demo) SETUP_ARGS+=(--demo) ;;
        *) echo "Opção desconhecida: $arg" >&2; exit 2 ;;
    esac
done

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

log()  { printf '\033[1;36m==>\033[0m %s\n' "$*"; }
fail() { printf '\033[1;31mERRO:\033[0m %s\n' "$*" >&2; exit 1; }

command -v docker >/dev/null || fail "docker não encontrado"
docker compose version >/dev/null 2>&1 || fail "docker compose v2 não encontrado"

# --- 1. .env ------------------------------------------------------------------
if [[ ! -f .env ]]; then
    log "Criando .env com senhas aleatórias"
    cp .env.example .env
    rand() { LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c "$1"; }
    sed -i \
        -e "s/^DB_PASSWORD=$/DB_PASSWORD=$(rand 24)/" \
        -e "s/^DB_ROOT_PASSWORD=$/DB_ROOT_PASSWORD=$(rand 24)/" \
        -e "s/^ADMIN_PASSWORD=$/ADMIN_PASSWORD=$(rand 20)/" \
        .env
    chmod 600 .env
fi
set -a; source .env; set +a

jexec() { docker compose exec -T -u www-data -w /var/www/html joomla "$@"; }
jcli()  { jexec php cli/joomla.php "$@"; }
sql()   { docker compose exec -T db mariadb -N -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "$1"; }

# O instalador escreve ao lado do pacote, e /opt/intranet/packages é somente
# leitura: copia para o tmp/ do Joomla antes de instalar.
install_zip() {
    local name; name="$(basename "$1")"
    jexec cp "/opt/intranet/packages/${1#var/packages/}" "tmp/$name"
    jcli extension:install --path="/var/www/html/tmp/$name" -n | grep -E 'OK|ERROR' || true
    jexec rm -f "tmp/$name"
}

# --- 2. Containers ------------------------------------------------------------
mkdir -p var/packages/vendor
log "Subindo containers"
docker compose up -d

log "Aguardando a instalação automática do Joomla"
for i in $(seq 1 120); do
    if jexec sh -c 'test -f configuration.php && test ! -d installation' 2>/dev/null; then
        break
    fi
    if ! docker compose ps --status running --services | grep -qx joomla; then
        docker compose logs --tail 40 joomla
        fail "container joomla parou durante a instalação"
    fi
    (( i == 120 )) && { docker compose logs --tail 40 joomla; fail "tempo esgotado aguardando a instalação"; }
    sleep 3
done

# --- 3. Configuração global ---------------------------------------------------
log "Aplicando configuração global"
jcli config:set \
    sef=true sef_rewrite=true \
    offset="$TIMEZONE" \
    mailfrom="$MAIL_FROM" fromname="$SITE_NAME" \
    debug=false error_reporting=none \
    lifetime=30 \
    >/dev/null

# Nome do site depois do título da página na aba do navegador ("Início - Intranet ...").
# A opção não existe no configuration.php inicial e o config:set só altera opções existentes.
jexec sh -c "grep -q 'sitename_pagetitles' configuration.php || sed -i 's/^}\$/\tpublic \$sitename_pagetitles = 2;\n}/' configuration.php"

# .htaccess é necessário para URLs sem index.php (sef_rewrite)
jexec sh -c 'test -f .htaccess || sed -r "s/^(Options -Indexes.*)$/#\1/" htaccess.txt > .htaccess'

# --- 4. Idioma pt-BR ----------------------------------------------------------
if [[ "$(sql "SELECT COUNT(*) FROM ${DB_PREFIX}extensions WHERE element='pkg_pt-BR'")" == "0" ]]; then
    zip="var/packages/vendor/pt-BR.zip"
    if [[ ! -f "$zip" ]]; then
        log "Baixando pacote de idioma pt-BR"
        details="$(curl -fsSL https://update.joomla.org/language/details6/pt-BR_details.xml)" \
            || fail "sem acesso a update.joomla.org — coloque o pacote pt-BR manualmente em $zip"
        url="$(grep -oP '<downloadurl[^>]*>\K[^<]+' <<<"$details" | head -1 | sed 's/&amp;/\&/g')"
        sha="$(grep -oP '<sha256>\K[^<]+' <<<"$details" | head -1)"
        curl -fsSL -o "$zip.tmp" "$url"
        echo "$sha  $zip.tmp" | sha256sum -c --quiet - || { rm -f "$zip.tmp"; fail "checksum do pacote pt-BR não confere"; }
        mv "$zip.tmp" "$zip"
    fi
    log "Instalando idioma pt-BR"
    install_zip "$zip"
fi

log "Definindo pt-BR como idioma padrão (site e administrador)"
sql "UPDATE ${DB_PREFIX}extensions
     SET params = JSON_SET(COALESCE(NULLIF(params,''),'{}'), '$.site', 'pt-BR', '$.administrator', 'pt-BR')
     WHERE type='component' AND element='com_languages'"

# --- 5. Pacotes da intranet (gerados por scripts/build.sh) --------------------
shopt -s nullglob
for pkg in var/packages/*.zip; do
    log "Instalando $(basename "$pkg")"
    install_zip "$pkg"
done
shopt -u nullglob

# --- 6. Desenvolvimento: registra as extensões montadas a partir de src/ ------
# (docker-compose.override.yml). discover:install só pega o que ainda não está instalado.
if [[ -f docker-compose.override.yml ]]; then
    log "Registrando extensões de src/ (modo desenvolvimento)"
    jcli extension:discover >/dev/null
    jcli extension:discover:install -n | grep -E 'OK|ERROR|nstalled' || true
    sql "UPDATE ${DB_PREFIX}extensions SET enabled = 1
         WHERE type = 'plugin' AND folder IN ('console', 'system') AND element = 'intranet'"
fi

# --- 7. Estrutura da intranet (template, categorias, menus, módulos) ---------
if [[ "$(sql "SELECT enabled FROM ${DB_PREFIX}extensions WHERE type='plugin' AND folder='console' AND element='intranet'")" == "1" ]]; then
    log "Criando a estrutura da intranet"
    jcli intranet:setup "${SETUP_ARGS[@]}"
fi

jcli cache:clean >/dev/null 2>&1 || true

# --- Fim ----------------------------------------------------------------------
cat <<EOF

$(printf '\033[1;32m')Intranet pronta.$(printf '\033[0m')
  Site:           http://localhost:${HTTP_PORT}/
  Administrador:  http://localhost:${HTTP_PORT}/administrator
                  usuário: ${ADMIN_USERNAME}   senha: veja ADMIN_PASSWORD em .env
  E-mails (dev):  http://localhost:${MAIL_UI_PORT}/
EOF
