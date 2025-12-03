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
// Import self-healing support
import './selfHeal';
// Import error interceptor
import { setupErrorInterceptor, checkFor404Page } from './errorInterceptor';

// Log routes and i18n missing keys after each test
// Completely optional - won't break tests if it fails
afterEach(function() {
  // Only log if test passed or failed (not skipped)
  if (this.currentTest && this.currentTest.state !== 'pending') {
    // Use setTimeout to make it completely non-blocking
    setTimeout(() => {
      try {
        // Log route
        // Cypress commands handle errors automatically - no need for .catch()
        cy.location('pathname', { timeout: 1000 }).then((pathname) => {
          cy.get('body', { timeout: 2000 }).then(($body) => {
            const is404 = $body.find('[data-e2e-not-found="true"]').length > 0;
            const status = is404 ? 404 : 200;
            
            const test = Cypress.mocha?.getRunner()?.suite?.ctx?.currentTest;
            const spec = Cypress.spec?.relative || 'unknown';
            const testTitle = test?.title || 'unknown';
            
            cy.task('logRoute', {
              route: pathname,
              status,
              spec,
              test: testTitle,
            }, { log: false });
          });
        });
        
        // Log i18n missing keys
        // Cypress commands handle errors automatically - no need for .catch()
        cy.window({ timeout: 2000 }).then((win) => {
          if (win.__MISSING_I18N__ && Array.isArray(win.__MISSING_I18N__) && win.__MISSING_I18N__.length > 0) {
            const missingKeys = [...win.__MISSING_I18N__];
            const test = Cypress.mocha?.getRunner()?.suite?.ctx?.currentTest;
            const spec = Cypress.spec?.relative || 'unknown';
            const testTitle = test?.title || 'unknown';
            
            missingKeys.forEach(({ locale, key }) => {
              cy.task('logI18nMissing', {
                key,
                locale,
                spec,
                test: testTitle,
              }, { log: false });
            });
            
            win.__MISSING_I18N__ = [];
          }
        });
      } catch (error) {
        // Silently ignore all errors
      }
    }, 100);
  }
});

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
  console.error('[Cypress] Uncaught exception:', err.message, err.stack);
  // In test mode, ignore more errors to allow tests to continue
  if (Cypress.env('CI') || Cypress.config('baseUrl')?.includes('localhost')) {
    // In development, be more lenient
    return false;
  }
  return true;
});

// Set default viewport
Cypress.config('viewportWidth', 1280);
Cypress.config('viewportHeight', 720);

// Log all API requests (for debugging) - REMOVED: causes issues
// Cypress.on('window:before:load', (win) => {
//   // Intercept fetch
//   cy.stub(win, 'fetch').callsFake((...args) => {
//     console.log('Fetch called:', args);
//     return win.fetch(...args);
//   });
// });

// Stop tests on 404/500 errors by default (to catch missing routes/pages/backend errors)
// 304 (Not Modified) is allowed by default as it's a normal cache response, not an error
// Set Cypress.env('ALLOW_404') to 'true' to allow 404 errors
// Set Cypress.env('ALLOW_304') to 'false' to stop on 304 errors (default: true - allowed)
// Set Cypress.env('ALLOW_500') to 'true' to allow 500 errors
const ALLOW_404 = Cypress.env('ALLOW_404') === 'true';
const ALLOW_304 = Cypress.env('ALLOW_304') !== 'false'; // Default to true (allow 304)
const ALLOW_500 = Cypress.env('ALLOW_500') === 'true';

// Store detected errors globally (outside beforeEach/afterEach scope)
let globalDetectedErrors = [];

Cypress.on('fail', (error, runnable) => {
    const message = error.message || "";

    // Stop test immediately if 404 detected (unless explicitly allowed)
    if (!ALLOW_404 && (message.includes("404") || message.includes("status code 404"))) {
        console.error("❌ E2E STOPPED: Missing Route / Model / Page");
        console.error("Test:", runnable.title);
        console.error("Error:", message);

        throw new Error(
            "STOP: 404 detected.\n" +
            "Meaning: Missing model, missing route, or missing page.\n" +
            "Fix required before continuing.\n" +
            "To allow 404 errors, set Cypress.env('ALLOW_404') to 'true'"
        );
    }

    // Stop test immediately if 304 detected (only if explicitly disabled)
    // Note: 304 is a normal HTTP response (Not Modified), not an error
    if (!ALLOW_304 && (message.includes("304") || message.includes("status code 304"))) {
        console.error("❌ E2E STOPPED: Not Modified / Cache Issue");
        console.error("Test:", runnable.title);
        console.error("Error:", message);

        throw new Error(
            "STOP: 304 detected.\n" +
            "Meaning: Not Modified - this is a normal cache response, not an error.\n" +
            "To allow 304 responses (recommended), ensure Cypress.env('ALLOW_304') is not set to 'false'"
        );
    }

    // Stop test immediately if 500 detected (unless explicitly allowed)
    if (!ALLOW_500 && (message.includes("500") || message.includes("status code 500"))) {
        console.error("❌ E2E STOPPED: Backend Controller / Model Error");
        console.error("Test:", runnable.title);
        console.error("Error:", message);

        throw new Error(
            "STOP: 500 detected.\n" +
            "Meaning: backend error in controller/model/service.\n" +
            "Fix required before continuing.\n" +
            "To allow 500 errors, set Cypress.env('ALLOW_500') to 'true'"
        );
    }

    // If error is not important → let default behavior handle it
    throw error;
});

// Intercept all requests to check for 404/500 errors
beforeEach(() => {
    // Reset errors for this test
    globalDetectedErrors = [];
    
    // Intercept and immediately check for errors
    cy.intercept('GET', '**/*', (req) => {
        req.continue((res) => {
            const status = res.statusCode;
            const url = req.url;
            
            // 304 is a normal cache response, not an error - skip it
            if (status === 304) return;
            
            if ((!ALLOW_404 && status === 404) || (!ALLOW_500 && status === 500)) {
                globalDetectedErrors.push({ method: 'GET', url, status });
                console.error(`❌ ${status} detected on GET ${url}`);
            }
        });
    }).as('GET_REQUESTS');

    cy.intercept('POST', '**/*', (req) => {
        req.continue((res) => {
            const status = res.statusCode;
            const url = req.url;
            
            // 304 is a normal cache response, not an error - skip it
            if (status === 304) return;
            
            if ((!ALLOW_404 && status === 404) || (!ALLOW_500 && status === 500)) {
                globalDetectedErrors.push({ method: 'POST', url, status });
                console.error(`❌ ${status} detected on POST ${url}`);
            }
        });
    }).as('POST_REQUESTS');

    cy.intercept('PUT', '**/*', (req) => {
        req.continue((res) => {
            const status = res.statusCode;
            const url = req.url;
            
            // 304 is a normal cache response, not an error - skip it
            if (status === 304) return;
            
            if ((!ALLOW_404 && status === 404) || (!ALLOW_500 && status === 500)) {
                globalDetectedErrors.push({ method: 'PUT', url, status });
                console.error(`❌ ${status} detected on PUT ${url}`);
            }
        });
    }).as('PUT_REQUESTS');

    cy.intercept('DELETE', '**/*', (req) => {
        req.continue((res) => {
            const status = res.statusCode;
            const url = req.url;
            
            // 304 is a normal cache response, not an error - skip it
            if (status === 304) return;
            
            if ((!ALLOW_404 && status === 404) || (!ALLOW_500 && status === 500)) {
                globalDetectedErrors.push({ method: 'DELETE', url, status });
                console.error(`❌ ${status} detected on DELETE ${url}`);
            }
        });
    }).as('DELETE_REQUESTS');
});

// After each test, check for detected errors
afterEach(() => {
    // Wait for any pending requests to complete
    cy.wait(3000);
    
    // Check errors detected during request interception
    if (globalDetectedErrors.length > 0) {
        const error = globalDetectedErrors[0]; // Get first error
        let statusText = '';
        let allowEnv = '';
        
        // Skip 304 - it's a normal cache response, not an error
        if (error.status === 304) {
            return; // Don't throw error for 304
        }
        
        if (error.status === 404) {
            statusText = 'Missing Model, Route, or Page';
            allowEnv = 'ALLOW_404';
        } else if (error.status === 500) {
            statusText = 'Backend Controller or Model Error';
            allowEnv = 'ALLOW_500';
        }
        
        throw new Error(
            `STOP: ${error.status} detected on ${error.method} ${error.url}\n` +
            `Meaning: ${statusText}\n` +
            `To allow ${error.status} errors, set Cypress.env('${allowEnv}') to 'true'`
        );
    }
    
    // Also double-check intercepted requests as backup
    const checkIntercepted = (alias, method) => {
        cy.get(alias, { timeout: 5000 }).then((calls) => {
            if (calls && Array.isArray(calls) && calls.length > 0) {
                calls.forEach((call) => {
                    const status = call?.response?.statusCode;
                    const url = call?.request?.url || call?.url || 'unknown';
                    
                    if (!status) return;
                    
                    // Skip 304 - it's a normal cache response, not an error
                    if (status === 304) {
                        return;
                    }
                    
                    if (status === 404 && !ALLOW_404) {
                        console.error(`❌ 404 detected on ${method} ${url}`);
                        throw new Error(
                            `STOP: 404 detected on ${method} ${url}\n` +
                            `Meaning: Missing Model, Route, or Page\n` +
                            `To allow 404 errors, set Cypress.env('ALLOW_404') to 'true'`
                        );
                    }
                    if (status === 500 && !ALLOW_500) {
                        console.error(`❌ 500 detected on ${method} ${url}`);
                        throw new Error(
                            `STOP: 500 detected on ${method} ${url}\n` +
                            `Meaning: Backend Controller or Model Error\n` +
                            `To allow 500 errors, set Cypress.env('ALLOW_500') to 'true'`
                        );
                    }
                });
            }
        });
    };
    
    // Check all request types
    checkIntercepted('@GET_REQUESTS.all', 'GET');
    checkIntercepted('@POST_REQUESTS.all', 'POST');
    checkIntercepted('@PUT_REQUESTS.all', 'PUT');
    checkIntercepted('@DELETE_REQUESTS.all', 'DELETE');
});

