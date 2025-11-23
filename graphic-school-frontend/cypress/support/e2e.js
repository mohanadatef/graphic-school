// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands';
// Import simplified login commands (optional, use if main commands fail)
// import './login-simple';

// Alternatively you can use CommonJS syntax:
// require('./commands')

// Prevent Cypress from failing on uncaught exceptions
Cypress.on('uncaught:exception', (err, runnable) => {
  // returning false here prevents Cypress from failing the test
  // Ignore common non-critical errors
  if (
    err.message.includes('ResizeObserver') ||
    err.message.includes('Non-Error promise rejection') ||
    err.message.includes('NetworkError') ||
    err.message.includes('ChunkLoadError') ||
    err.message.includes('Loading chunk') ||
    err.message.includes('Failed to fetch dynamically imported module') ||
    err.message.includes('about:blank')
  ) {
    return false;
  }
  // Log the error for debugging
  console.error('Uncaught exception:', err.message);
  // return false to prevent the error from failing this test
  return true;
});

// Set default viewport
Cypress.config('viewportWidth', 1280);
Cypress.config('viewportHeight', 720);

// Log all API requests (for debugging)
Cypress.on('window:before:load', (win) => {
  // Intercept fetch
  cy.stub(win, 'fetch').callsFake((...args) => {
    console.log('Fetch called:', args);
    return win.fetch(...args);
  });
});

