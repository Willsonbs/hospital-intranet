#!/usr/bin/env bash
# Tira screenshots (desktop/tablet/mobile) e roda o axe-core nas páginas principais.
# Resultado em var/visual/ (PNG + report.json). Requer o ambiente no ar (scripts/install.sh).
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

AXE_VERSION=4.10.3
mkdir -p var/visual var/axe
[[ -f var/axe/axe.min.js ]] || curl -fsSL -o var/axe/axe.min.js "https://cdn.jsdelivr.net/npm/axe-core@$AXE_VERSION/axe.min.js"
rm -f var/visual/*.png

docker run --rm --network hospital-intranet_default --user "$(id -u):$(id -g)" \
    -v "$PWD/tests/visual:/usr/src/app/tests:ro" \
    -v "$PWD/var/visual:/out" \
    -v "$PWD/var/axe:/axe:ro" \
    --entrypoint node zenika/alpine-chrome:with-puppeteer tests/shoot.js

python3 - <<'PY'
import json
r = json.load(open("var/visual/report.json"))
for page, violations in r["axe"].items():
    print(f"{page}: {len(violations)} problema(s) de acessibilidade")
    for v in violations:
        print(f"   [{v['impact']}] {v['id']} ×{v['n']} — {v['help']} ({v['target']})")
print("Erros no console:", r["consoleErrors"] or "nenhum")
print("Foco após abrir a busca:", r["searchFocus"])
for step, st in r.get("ramais", {}).items():
    print(f"Ramais / {step}: {st['count']} | {st['visible']} | {st['sort']} | url={st['url']}")
PY
