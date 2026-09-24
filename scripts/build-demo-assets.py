#!/usr/bin/env python3
"""Gera os arquivos do conteúdo de demonstração:

- imagens abstratas (WebP) das notícias: ilustrações provisórias nas cores do
  design system; substitua por fotos reais pelo Gerenciador de Mídia;
- PDFs de uma página para os documentos da Biblioteca (botão "Baixar").

Requer Pillow. Uso: python3 scripts/build-demo-assets.py
"""
from pathlib import Path
import random

from PIL import Image, ImageDraw, ImageFilter

DEMO = Path(__file__).resolve().parent.parent / "src/plugins/console/intranet/demo"
OUT = DEMO / "images"
FILES = DEMO / "files"
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


# código: (título, setor)
DOCUMENTS = {
    "POP-ENF-042": ("Administração segura de medicamentos", "Enfermagem"),
    "PRT-CCIH-018": ("Precauções e isolamento hospitalar", "Controle de Infecção (CCIH)"),
    "POP-FAR-027": ("Armazenamento de medicamentos termolábeis", "Farmácia"),
    "PRT-SEG-005": ("Identificação correta do paciente", "Segurança do Paciente"),
    "MAN-TI-001": ("Manual do e-mail institucional", "Tecnologia da Informação"),
}


def pdf(path: Path, code: str, title: str, sector: str) -> None:
    """PDF mínimo (uma página, Helvetica, WinAnsi) escrito à mão, sem dependências."""
    def text(value: str) -> str:
        return value.replace("\\", "\\\\").replace("(", "\\(").replace(")", "\\)")

    lines = [
        "Hospital Santa Aurora - Biblioteca institucional",
        f"Setor responsável: {sector}",
        "",
        "DOCUMENTO DE DEMONSTRAÇÃO",
        "Este arquivo foi gerado automaticamente para testar a intranet.",
        "Substitua pelo documento oficial no Gerenciador de Mídia.",
    ]
    stream = (
        f"BT /F1 10 Tf 72 790 Td ({text(code)}) Tj ET\n"
        f"BT /F1 18 Tf 72 765 Td ({text(title)}) Tj ET\n"
        "BT /F1 11 Tf 72 730 Td 16 TL "
        + " ".join(f"({text(line)}) '" for line in lines)
        + " ET"
    ).encode("cp1252")

    objects = [
        b"<< /Type /Catalog /Pages 2 0 R >>",
        b"<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
        b"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R"
        b" /Resources << /Font << /F1 5 0 R >> >> >>",
        b"<< /Length %d >>\nstream\n" % len(stream) + stream + b"\nendstream",
        b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>",
    ]

    out = bytearray(b"%PDF-1.4\n")
    offsets = []
    for number, body in enumerate(objects, start=1):
        offsets.append(len(out))
        out += b"%d 0 obj\n" % number + body + b"\nendobj\n"
    xref = len(out)
    out += b"xref\n0 %d\n0000000000 65535 f \n" % (len(objects) + 1)
    out += b"".join(b"%010d 00000 n \n" % offset for offset in offsets)
    out += b"trailer\n<< /Size %d /Root 1 0 R >>\nstartxref\n%d\n%%%%EOF\n" % (len(objects) + 1, xref)
    path.write_bytes(bytes(out))


if __name__ == "__main__":
    OUT.mkdir(parents=True, exist_ok=True)
    for name, (a, b, shape, seed) in THEMES.items():
        compose(name, a, b, shape, seed)
        print(OUT / f"{name}.webp")

    FILES.mkdir(parents=True, exist_ok=True)
    for code, (title, sector) in DOCUMENTS.items():
        pdf(FILES / f"{code.lower()}.pdf", code, title, sector)
        print(FILES / f"{code.lower()}.pdf")
