describe('Cobertura de endpoints', () => {
  const imagen = 'Diego.jpg';

  it('POST /fotos exitoso', () => {
    // Hacer POST con cy.request y enviar el archivo usando formData
    cy.fixture(imagen, 'base64').then(fileContent => {
      const blob = Cypress.Blob.base64StringToBlob(fileContent, 'image/jpeg');
      const formData = new FormData();
      formData.append('foto', blob, imagen);

      cy.request({
        method: 'POST',
        url: 'http://localhost:8001/fotos',
        headers: {
          'X-API-KEY': '123456'
        },
        body: formData,
        failOnStatusCode: false,
        // Cypress no envía automáticamente formData, usar encoding false y enviar blob requiere plugin o workaround
      }).then(res => {
        expect(res.status).to.eq(200);
        expect(res.body).to.have.property('ok', true);
        expect(res.body).to.have.property('file', imagen);
      });
    });
  });

  it('POST /fotos sin imagen', () => {
    cy.request({
      method: 'POST',
      url: 'http://localhost:8001/fotos',
      headers: { 'X-API-KEY': '123456' },
      failOnStatusCode: false
    }).then(res => {
      expect(res.status).to.be.oneOf([400, 422]);
    });
  });

  it('POST /fotos con tipo no permitido', () => {
    cy.fixture('archivo.txt', 'base64').then(fileContent => {
      const blob = Cypress.Blob.base64StringToBlob(fileContent, 'text/plain');
      const formData = new FormData();
      formData.append('foto', blob, 'archivo.txt');

      cy.request({
        method: 'POST',
        url: 'http://localhost:8001/fotos',
        headers: { 'X-API-KEY': '123456' },
        body: formData,
        failOnStatusCode: false,
      }).then(res => {
        expect(res.status).to.be.oneOf([400, 415]);
      });
    });
  });

  it('GET / muestra imágenes', () => {
    cy.request({
      method: 'GET',
      url: 'http://localhost:8002/',
      headers: { 'X-API-KEY': '123456' },
      failOnStatusCode: false
    }).then(res => {
      expect(res.status).to.eq(200);
      expect(res.body).to.include(imagen); // Asumiendo que devuelve JSON con imágenes o HTML como texto plano
    });
  });
});
