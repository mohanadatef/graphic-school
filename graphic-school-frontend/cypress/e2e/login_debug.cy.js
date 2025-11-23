/**
 * Debug test to verify login page structure
 * Run this first to understand the page structure
 */
describe('Login Page Debug', () => {
  it('Should inspect login page structure', () => {
    cy.visit('/login');
    cy.wait(3000); // Wait for page to fully load
    
    // Take screenshot of the page
    cy.screenshot('login-page-structure');
    
    // Log page HTML
    cy.get('body').then(($body) => {
      console.log('Page HTML:', $body.html());
    });
    
    // Check for form
    cy.get('body').then(($body) => {
      const hasForm = $body.find('form').length > 0;
      console.log('Form found:', hasForm);
      
      if (hasForm) {
        // Log form HTML
        cy.get('form').then(($form) => {
          console.log('Form HTML:', $form.html());
        });
      }
    });
    
    // Check for email input
    cy.get('body').then(($body) => {
      const emailSelectors = [
        '#email',
        'input[type="email"]',
        'input[name="email"]',
        'input[id*="email"]',
        'input[placeholder*="email" i]',
      ];
      
      emailSelectors.forEach(selector => {
        const found = $body.find(selector).length;
        console.log(`Selector "${selector}": ${found} found`);
        if (found > 0) {
          cy.get(selector).then(($el) => {
            console.log(`Element details:`, {
              id: $el.attr('id'),
              name: $el.attr('name'),
              type: $el.attr('type'),
              placeholder: $el.attr('placeholder'),
              class: $el.attr('class'),
            });
          });
        }
      });
    });
    
    // Check for password input
    cy.get('body').then(($body) => {
      const passwordSelectors = [
        '#password',
        'input[type="password"]',
        'input[name="password"]',
        'input[id*="password"]',
        'input[placeholder*="password" i]',
      ];
      
      passwordSelectors.forEach(selector => {
        const found = $body.find(selector).length;
        console.log(`Selector "${selector}": ${found} found`);
        if (found > 0) {
          cy.get(selector).then(($el) => {
            console.log(`Element details:`, {
              id: $el.attr('id'),
              name: $el.attr('name'),
              type: $el.attr('type'),
              placeholder: $el.attr('placeholder'),
              class: $el.attr('class'),
            });
          });
        }
      });
    });
    
    // Check for submit button
    cy.get('body').then(($body) => {
      const buttonSelectors = [
        'button[type="submit"]',
        'form button',
        'button[aria-label*="login" i]',
        'button[aria-label*="sign in" i]',
      ];
      
      buttonSelectors.forEach(selector => {
        const found = $body.find(selector).length;
        console.log(`Button selector "${selector}": ${found} found`);
      });
    });
    
    // Wait so we can inspect
    cy.wait(5000);
  });
});

