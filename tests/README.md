# Pruebas E2E con Cypress para Galería Familiar

## Objetivo

Automatizar la prueba del flujo completo de subir una imagen y ver la miniatura generada.

## Ejecución

1. Levanta los servicios con `docker-compose up -d`
2. Ejecuta `npm install` dentro de la carpeta tests
3. Corre los tests con `npm test` o `make test`

## Resultados esperados

- La imagen debe subirse correctamente
- La página debe redirigir a mostrar la miniatura
- La miniatura debe ser visible y corresponder a la imagen subida
