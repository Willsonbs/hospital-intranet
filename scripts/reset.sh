#!/usr/bin/env bash
# Apaga containers e volumes (banco + arquivos do Joomla) e reinstala do zero.
# Uso: scripts/reset.sh [-y]
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

if [[ "${1:-}" != "-y" ]]; then
    read -rp "Isso APAGA o banco e todos os arquivos do Joomla. Continuar? [s/N] " ok
    [[ "$ok" =~ ^[sS]$ ]] || exit 1
fi

docker compose down -v --remove-orphans
exec scripts/install.sh
