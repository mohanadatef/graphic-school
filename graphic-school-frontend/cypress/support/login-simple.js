/**
 * Simplified Login Commands
 * Use these if the main commands fail
 */

Cypress.Commands.add('loginAsAdminSimple', () => {
  cy.fixture('users').then((users) => {
    cy.visit('/login');
    cy.wait(3000);
    
    // Simple direct approach
    cy.get('#email').type(users.admin.email);
    cy.get('#password').type(users.admin.password);
    cy.get('button[type="submit"]').click();
    
    // Wait and check URL
    cy.wait(5000);
    
    // Just check we're not on login anymore
    cy.url().should('not.include', '/login');
    cy.wait(2000);
  });
});

Cypress.Commands.add('loginAsInstructorSimple', () => {
  cy.fixture('users').then((users) => {
    cy.visit('/login');
    cy.wait(3000);
    
    cy.get('#email').type(users.instructor.email);
    cy.get('#password').type(users.instructor.password);
    cy.get('button[type="submit"]').click();
    
    cy.wait(5000);
    cy.url().should('not.include', '/login');
    cy.wait(2000);
  });
});

Cypress.Commands.add('loginAsStudentSimple', () => {
  cy.fixture('users').then((users) => {
    cy.visit('/login');
    cy.wait(3000);
    
    cy.get('#email').type(users.student.email);
    cy.get('#password').type(users.student.password);
    cy.get('button[type="submit"]').click();
    
    cy.wait(5000);
    cy.url().should('not.include', '/login');
    cy.wait(2000);
  });
});

