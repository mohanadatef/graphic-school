/**
 * Cypress Self-Healing Support
 * Auto-generates tests for pages that don't have tests
 * 
 * Note: Uses Cypress tasks to call Node.js file generation
 */

// Track visited routes during test run
const visitedRoutes = new Set();

/**
 * Hook into Cypress to track route visits
 */
beforeEach(() => {
  // Track current route (non-blocking)
  // Cypress will handle errors automatically - no need for .catch()
  cy.window({ timeout: 2000, failOnStatusCode: false })
    .then((win) => {
      if (win && win.location && win.location.pathname) {
        const route = win.location.pathname;
        if (!visitedRoutes.has(route)) {
          visitedRoutes.add(route);
          
          // Check if test exists for this route (non-blocking)
          const routeName = route
            .replace(/^\//, '')
            .replace(/\//g, '-')
            .replace(/[^a-z0-9-]/gi, '-')
            .toLowerCase();
          
          const testFile = `cypress/e2e/auto/${routeName}.cy.js`;
          
          // Check if test exists (non-blocking)
          // Use { log: false } to suppress errors if task doesn't exist
          cy.task('checkFileExists', testFile, { log: false })
            .then((exists) => {
              if (!exists) {
                // Generate test (non-blocking)
                cy.task('generateE2ETest', route, { log: false })
                  .then((result) => {
                    if (result && result.success) {
                      cy.log(`[SELF-HEAL] Test generated for route: ${route}`);
                    }
                  });
              }
            });
        }
      }
    });
});

/**
 * After test suite, generate tests for all visited routes
 */
after(() => {
  cy.log(`[SELF-HEAL] Visited ${visitedRoutes.size} routes during test run`);
  
  // Generate tests for all visited routes (non-blocking)
  // Cypress commands handle errors automatically
  visitedRoutes.forEach((route) => {
    cy.task('generateE2ETest', route, { log: false }).then((result) => {
      if (result && result.success) {
        cy.log(`[SELF-HEAL] Test generated for: ${route}`);
      }
    });
  });
});

