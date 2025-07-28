import os, time
from PIL import Image

WATCH = "/app/photos"
OUT = "/app/thumbnails"

seen = set()

def crear_miniatura(foto):
    try:
        img = Image.open(foto)
        img.thumbnail((150, 150))
        base = os.path.basename(foto)
        img.save(os.path.join(OUT, base))
        print(f"Miniatura creada: {base}")
    except Exception as e:
        print("Error:", e)

while True:
    for archivo in os.listdir(WATCH):
        ruta = os.path.join(WATCH, archivo)
        if archivo.lower().endswith((".jpg", ".png")) and archivo not in seen:
            seen.add(archivo)
            crear_miniatura(ruta)
    time.sleep(5)
