import os
import time
from PIL import Image

WATCH = "/app/photos"
OUT = "/app/thumbnails"

# Crear directorio de salida si no existe
os.makedirs(OUT, exist_ok=True)

# Control de imágenes ya procesadas
seen = set()

def crear_miniatura(foto):
    try:
        img = Image.open(foto)
        img.thumbnail((150, 150))
        base = os.path.basename(foto)
        output_path = os.path.join(OUT, base)
        img.save(output_path)
        print(f"✅ Miniatura creada: {output_path}")
    except Exception as e:
        print(f"❌ Error al crear miniatura para {foto}: {e}")

print(f"🔍 Observando carpeta: {WATCH}")
while True:
    for archivo in os.listdir(WATCH):
        if not archivo.lower().endswith((".jpg", ".png")):
            continue

        ruta = os.path.join(WATCH, archivo)
        if archivo not in seen and os.path.isfile(ruta):
            crear_miniatura(ruta)
            seen.add(archivo)

    time.sleep(5)
