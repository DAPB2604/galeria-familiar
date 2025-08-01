describe('Flujo de subida y visualización de imagen', () => {
  it('Sube una imagen y muestra la miniatura', () => {
    // Visita la página de carga
    cy.visit('http://localhost:8080/index.html');

    // Adjunta la imagen
    cy.get('input[type="file"]').selectFile('cypress/fixtures/Diego.jpg');

    // Selecciona la licencia
    cy.get('select[name="licencia"]').select('CC BY');

    // Envía el formulario
    cy.get('form').submit();

    // Espera redirección a mostrar.html
    cy.location('pathname', { timeout: 5000 }).should('include', '/mostrar.html');

    // Verifica que la miniatura se muestre
    cy.get('img.thumbnail', { timeout: 5000 })
      .should('be.visible')
      .and('have.attr', 'src')
      .and('include', 'thumbnails/Diego.jpg');
  });
});
