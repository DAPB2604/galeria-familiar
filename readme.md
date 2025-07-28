# Galería de Fotos Familiar

Este proyecto consiste en un sistema distribuido de microservicios que permite subir fotos familiares, ver álbumes y comentar imágenes. Incluye una función FaaS que genera miniaturas automáticamente.

---

#Tabla de Endpoints REST

| Ruta             | Verbo | Descripción                                | Ejemplo de Request                                     | Ejemplo de Response                           |
|------------------|-------|--------------------------------------------|--------------------------------------------------------|-----------------------------------------------|
| `/fotos`         | POST  | Subida de imagen                           | Form-data: `foto` (archivo), `nombre=cr7.jpg`         | `{ "ok": true, "path": "photos/cr7.jpg" }`    |
| `/albums`        | GET   | Listado de imágenes disponibles            | `GET /albums`                                          | `[ "cr7.jpg", "familia.png" ]`               |
| `/comentarios`   | POST  | Agrega un comentario a una imagen          | JSON: `{ "img": "cr7.jpg", "autor": "José", "texto": "¡Gran foto!" }` | `{ "ok": true }`                             |
| `/comentarios`   | GET   | Lista los comentarios de una imagen        | `GET /comentarios?img=cr7.jpg`                        | `[ { "autor": "José", "texto": "¡Gran foto!" } ]` |

---

## ⚙️ Función FaaS: Generador de Miniaturas

- **Nombre:** `ThumbnailFn`
- **Trigger:** Monitor de archivos (`inotify`) al detectar nueva imagen en carpeta `./photos`
- **Lenguaje:** Python
- **Biblioteca usada:** `Pillow`
- **Acción:** Genera automáticamente una miniatura de 150x150 px para cada imagen subida y la guarda en `./photos/thumbnails`.

---

