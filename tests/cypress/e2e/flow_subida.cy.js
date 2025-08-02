describe('Flujo de subida y visualización de imagen', () => {
  it('Sube una imagen y muestra la miniatura', () => {
    cy.visit('http://localhost:8080/index.html');

    cy.get('input[type="file"]').selectFile('cypress/fixtures/Diego.jpg', { force: true });

    cy.get('select[name="licencia"]').select('CC BY');

    cy.intercept('POST', '/fotos').as('uploadImage');

    cy.get('form').submit();

    cy.wait('@uploadImage').its('response.statusCode').should('eq', 200);

    cy.location('pathname', { timeout: 10000 }).should('include', '/mostrar.html');

    cy.get('img.thumbnail', { timeout: 10000 })
      .should('be.visible')
      .and('have.attr', 'src')
      .and('include', 'thumbnails/Diego.jpg');
  });
});
