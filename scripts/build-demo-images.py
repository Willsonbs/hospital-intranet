#!/usr/bin/env python3
"""Gera as imagens abstratas (WebP) usadas no conteúdo de demonstração.

São ilustrações provisórias nas cores do design system: substitua por fotos
reais do hospital pelo Gerenciador de Mídia. Requer Pillow.
Uso: python3 scripts/build-demo-images.py
"""
from pathlib import Path
import random

from PIL import Image, ImageDraw, ImageFilter

OUT = Path(__file__).resolve().parent.parent / "src/plugins/console/intranet/demo/images"
W, H = 1200, 750

# nome: (cor de fundo A, cor de fundo B, cor das formas, semente)
THEMES = {
    "certificacao": ("#064850", "#0B7378", "#6FD3C1", 1),
    "enfermagem": ("#1E4E9C", "#3563C9", "#BCD2FA", 2),
    "bem-estar": ("#1F7A5C", "#2E9B76", "#CFF1E3", 3),
    "materiais": ("#9A5215", "#D08030", "#FCE3C4", 4),
}


def hex_rgb(value: str) -> tuple[int, int, int]:
    value = value.lstrip("#")
    return tuple(int(value[i:i + 2], 16) for i in (0, 2, 4))


def gradient(a: str, b: str) -> Image.Image:
    ca, cb = hex_rgb(a), hex_rgb(b)
    img = Image.new("RGB", (W, H))
    px = img.load()
    for y in range(H):
        for x in range(0, W, 4):
            t = (x / W) * 0.6 + (y / H) * 0.4
            c = tuple(int(ca[i] + (cb[i] - ca[i]) * t) for i in range(3))
            for dx in range(4):
                if x + dx < W:
                    px[x + dx, y] = c
    return img


def compose(name: str, a: str, b: str, shape: str, seed: int) -> None:
    rnd = random.Random(seed)
    base = gradient(a, b).convert("RGBA")
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    draw = ImageDraw.Draw(layer)
    sc = hex_rgb(shape)

    # Círculos suaves
    for _ in range(7):
        r = rnd.randint(90, 260)
        x, y = rnd.randint(-100, W + 100), rnd.randint(-100, H + 100)
        draw.ellipse((x - r, y - r, x + r, y + r), fill=sc + (rnd.randint(18, 40),))

    # Cartões arredondados (lembram interface / documentos)
    for i in range(3):
        x0 = 620 + i * 70
        y0 = 150 + i * 90
        draw.rounded_rectangle((x0, y0, x0 + 360, y0 + 220), radius=28, fill=(255, 255, 255, 34 + i * 14))

    # Cruz hospitalar
    cx, cy, s = 330, 375, 70
    draw.rounded_rectangle((cx - s, cy - 3 * s, cx + s, cy + 3 * s), radius=24, fill=(255, 255, 255, 70))
    draw.rounded_rectangle((cx - 3 * s, cy - s, cx + 3 * s, cy + s), radius=24, fill=(255, 255, 255, 70))

    layer = layer.filter(ImageFilter.GaussianBlur(1.2))
    Image.alpha_composite(base, layer).convert("RGB").save(OUT / f"{name}.webp", "WEBP", quality=80, method=6)


if __name__ == "__main__":
    OUT.mkdir(parents=True, exist_ok=True)
    for name, (a, b, shape, seed) in THEMES.items():
        compose(name, a, b, shape, seed)
        print(OUT / f"{name}.webp")
