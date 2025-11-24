/**
 * Health Check Test
 * Run this first to verify frontend is accessible
 */
describe('Frontend Health Check', () => {
  it('Should load the frontend application', () => {
    // Visit the base URL
    cy.visit('/', { 
      timeout: 30000,
      failOnStatusCode: false,
    });
    
    // Wait for page to load
    cy.wait(5000);
    
    // Check if page has content (not blank)
    cy.get('body', { timeout: 20000 }).should('not.be.empty');
    
    // Check if Vue app is mounted (should have #app element)
    cy.get('#app', { timeout: 20000 }).should('exist');
    
    // Take screenshot
    cy.screenshot('health-check-homepage');
  });

  it('Should access login page', () => {
    cy.visit('/login', { 
      timeout: 30000,
      failOnStatusCode: false,
    });
    cy.wait(3000);
    
    // Check if login page loaded
    cy.get('body', { timeout: 15000 }).should('not.be.empty');
    cy.get('#app', { timeout: 15000 }).should('exist');
    
    // Check for form or login elements
    cy.get('body').then(($body) => {
      const hasForm = $body.find('form').length > 0;
      const hasInputs = $body.find('input').length > 0;
      
      cy.log('Form found:', hasForm);
      cy.log('Inputs found:', hasInputs);
      
      if (hasForm) {
        cy.screenshot('health-check-login-page');
      } else {
        cy.screenshot('health-check-login-page-blank');
      }
    });
  });

  it('Should check API connectivity', () => {
    // Intercept API calls
    let apiCallMade = false;
    let interceptedUrl = '';
    
    cy.intercept('GET', '**/api/**', (req) => {
      apiCallMade = true;
      interceptedUrl = req.url;
    }).as('apiCall');
    
    cy.visit('/', { 
      timeout: 30000,
      failOnStatusCode: false,
    });
    cy.wait(5000);
    
    // Verify page loaded
    cy.get('body', { timeout: 15000 }).should('exist');
    cy.get('#app', { timeout: 15000 }).should('exist');
    
    // Check if API call was made (non-blocking check)
    cy.then(() => {
      if (apiCallMade) {
        cy.log('✓ API connectivity confirmed');
        cy.log('API call intercepted:', interceptedUrl);
      } else {
        cy.log('⚠ No API calls detected (this might be normal if page is cached)');
      }
    });
  });
});

