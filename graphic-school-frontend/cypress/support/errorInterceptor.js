/**
 * Error Interceptor for Cypress
 * Stops tests on 404/500 errors and reports them
 */

// Track errors during test run
const errorReports = {
  routes: new Set(),
  apis: new Set(),
  pages: new Set(),
  errors: [],
};

/**
 * Intercept network requests and stop on 404/500
 */
export function setupErrorInterceptor() {
  // Intercept all network requests
  cy.intercept('**', (req) => {
    req.continue((res) => {
      const status = res.statusCode;
      const url = req.url;
      const method = req.method;

      // Check if we should stop on this error
      const shouldStop = 
        (status === 404 && Cypress.env('ALLOW_404') !== 'true') ||
        (status === 500 && Cypress.env('ALLOW_500') !== 'true');

      if (shouldStop) {
        const errorInfo = {
          status,
          method,
          url,
          timestamp: new Date().toISOString(),
          spec: Cypress.currentTest?.titlePath?.join(' > ') || 'unknown',
        };

        errorReports.errors.push(errorInfo);

        // Log to console
        cy.log(`[ERROR INTERCEPTOR] ${status} ${method} ${url}`);

        // Report to Cypress task
        cy.task('reportError', errorInfo, { log: false });

        // Stop test if configured
        if (status === 404) {
          errorReports.routes.add(url);
          errorReports.apis.add(url);
        } else if (status === 500) {
          errorReports.apis.add(url);
        }

        // Throw error to stop test
        throw new Error(`HTTP ${status} error: ${method} ${url}`);
      }

      // Track 404s for reporting (even if not stopping)
      if (status === 404) {
        errorReports.routes.add(url);
        errorReports.apis.add(url);
        cy.task('report404', { url, method, spec: Cypress.currentTest?.titlePath?.join(' > ') || 'unknown' }, { log: false });
      }

      // Track 500s for reporting
      if (status === 500) {
        errorReports.apis.add(url);
        cy.task('report500', { url, method, spec: Cypress.currentTest?.titlePath?.join(' > ') || 'unknown' }, { log: false });
      }
    });
  }).as('networkRequest');
}

/**
 * Check for 404 pages (client-side routing)
 */
export function checkFor404Page() {
  cy.get('body').then(($body) => {
    // Check for common 404 indicators
    const has404 = 
      $body.text().includes('404') ||
      $body.text().includes('Not Found') ||
      $body.text().includes('Page not found') ||
      $body.find('[data-e2e="404"]').length > 0;

    if (has404 && Cypress.env('ALLOW_404') !== 'true') {
      const currentUrl = cy.url();
      errorReports.pages.add(currentUrl);
      cy.task('report404Page', { url: currentUrl, spec: Cypress.currentTest?.titlePath?.join(' > ') || 'unknown' }, { log: false });
      throw new Error(`404 Page detected: ${currentUrl}`);
    }
  });
}

/**
 * Get error reports
 */
export function getErrorReports() {
  return {
    routes: Array.from(errorReports.routes),
    apis: Array.from(errorReports.apis),
    pages: Array.from(errorReports.pages),
    errors: errorReports.errors,
  };
}

/**
 * Clear error reports
 */
export function clearErrorReports() {
  errorReports.routes.clear();
  errorReports.apis.clear();
  errorReports.pages.clear();
  errorReports.errors = [];
}

