#!/usr/bin/env bash
# Gera o sprite SVG do template a partir de scripts/icons.txt (ícones Lucide, licença ISC).
# Uso de um ícone no PHP: LayoutHelper::render('hospital.icon', ['name' => 'users'])
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."

LUCIDE_VERSION=1.48.0
OUT=src/media/templates/site/hospital_intranet/images/icons.svg
CACHE=var/lucide-$LUCIDE_VERSION
mkdir -p "$CACHE"

names=$(grep -vE '^\s*(#|$)' scripts/icons.txt)
for n in $names; do
    [[ -f "$CACHE/$n.svg" ]] || curl -fsS -o "$CACHE/$n.svg" \
        "https://cdn.jsdelivr.net/npm/lucide-static@$LUCIDE_VERSION/icons/$n.svg" \
        || { echo "ícone não encontrado: $n" >&2; exit 1; }
done

python3 - "$CACHE" "$OUT" $names <<'PY'
import re, sys
cache, out, names = sys.argv[1], sys.argv[2], sys.argv[3:]
parts = []
for n in names:
    svg = open(f"{cache}/{n}.svg", encoding="utf-8").read()
    body = re.search(r"<svg[^>]*>(.*)</svg>", svg, re.S).group(1)
    body = re.sub(r"\s+", " ", body).strip()
    parts.append(f'<symbol id="{n}" viewBox="0 0 24 24">{body}</symbol>')
with open(out, "w", encoding="utf-8") as f:
    f.write('<svg xmlns="http://www.w3.org/2000/svg">'
            '<!-- Lucide icons, ISC License, https://lucide.dev -->'
            + "".join(parts) + "</svg>\n")
print(f"{len(parts)} ícones -> {out}")
PY
