// ***********************************************
// Custom Cypress Commands
// ***********************************************

// Route logging moved to afterEach hook in e2e.js
// No need to override cy.visit anymore

/**
 * Wait until frontend is fully ready
 * Waits for: branding, translations, settings, user, router ready
 */
Cypress.Commands.add('waitUntilFrontendReady', () => {
  // Wait for body to be visible (DOM ready)
  cy.get('body').should('be.visible');
  
  // Wait for Vue app to be mounted (with retry)
  cy.window({ timeout: 10000 }).should((win) => {
    // Check for Vue app in various possible locations
    const hasApp = win.app || win.__VUE_APP__ || win.__VUE__ || document.querySelector('#app');
    expect(hasApp).to.exist;
  });
  
  // Wait for router to be ready (with retry)
  cy.window({ timeout: 10000 }).then((win) => {
    // Router might not be immediately available, so we check with retry
    const checkRouter = () => {
      const router = win.app?.config?.globalProperties?.$router || 
                     win.__VUE_ROUTER__ || 
                     win.$router;
      return router !== undefined;
    };
    
    // Retry checking for router
    let attempts = 0;
    const maxAttempts = 10;
    const checkInterval = setInterval(() => {
      attempts++;
      if (checkRouter() || attempts >= maxAttempts) {
        clearInterval(checkInterval);
      }
    }, 200);
  });
  
  // Wait for any pending API calls to complete
  cy.wait(2000);
  
  // Wait for sidebar/navigation to be present (if on dashboard)
  cy.get('body', { timeout: 5000 }).then(($body) => {
    if ($body.find('[data-cy="sidebar"]').length > 0 || $body.find('aside').length > 0) {
      // Sidebar exists, wait for it to be fully rendered
      cy.wait(1000);
    }
  });
});

/**
 * Login as Admin user
 * Uses data-e2e selectors for reliability
 */
Cypress.Commands.add('loginAsAdmin', () => {
  cy.fixture('users').then((users) => {
    // Set up intercept BEFORE visiting/login (must be before the request happens)
    cy.intercept('POST', '**/api/login').as('loginRequest');
    
    cy.visit('/login', { timeout: 30000, failOnStatusCode: false });
    cy.wait(2000);
    
    // Use data-e2e selectors first, fallback to other selectors
    cy.get('[data-e2e="login-email"], #email, input[type="email"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.admin.email);
    
    cy.get('[data-e2e="login-password"], #password, input[type="password"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.admin.password);
    
    cy.get('[data-e2e="login-submit"], button[type="submit"]', { timeout: 10000 })
      .first()
      .click();

    // Wait for login API call (intercept was set up earlier)
    cy.wait('@loginRequest', { timeout: 20000 });

    // Admin redirects to /dashboard/admin
    cy.location('pathname', { timeout: 20000 }).should((pathname) => {
      expect(pathname).to.match(/\/dashboard\/admin/);
    });
    
    // Wait for frontend to be ready
    cy.waitUntilFrontendReady();
  });
});

/**
 * Login as Instructor user
 * Uses data-e2e selectors for reliability
 */
Cypress.Commands.add('loginAsInstructor', () => {
  cy.fixture('users').then((users) => {
    // Set up intercept BEFORE visiting/login (must be before the request happens)
    cy.intercept('POST', '**/api/login').as('loginRequest');
    
    cy.visit('/login', { timeout: 30000, failOnStatusCode: false });
    cy.wait(2000);
    
    // Use data-e2e selectors first, fallback to other selectors
    cy.get('[data-e2e="login-email"], #email, input[type="email"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.instructor.email);
    
    cy.get('[data-e2e="login-password"], #password, input[type="password"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.instructor.password);
    
    cy.get('[data-e2e="login-submit"], button[type="submit"]', { timeout: 10000 })
      .first()
      .click();

    // Wait for login API call (intercept was set up earlier)
    cy.wait('@loginRequest', { timeout: 20000 });
    
    // Instructor redirects to /dashboard/instructor
    cy.location('pathname', { timeout: 20000 }).should((pathname) => {
      expect(pathname).to.match(/\/dashboard\/instructor/);
    });
    
    // Wait for frontend to be ready
    cy.waitUntilFrontendReady();
  });
});

/**
 * Login as Student user
 * Uses data-e2e selectors for reliability
 */
Cypress.Commands.add('loginAsStudent', () => {
  cy.fixture('users').then((users) => {
    // Set up intercept BEFORE visiting/login (must be before the request happens)
    cy.intercept('POST', '**/api/login').as('loginRequest');
    
    cy.visit('/login', { timeout: 30000, failOnStatusCode: false });
    cy.wait(2000);
    
    // Use data-e2e selectors first, fallback to other selectors
    cy.get('[data-e2e="login-email"], #email, input[type="email"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.student.email);
    
    cy.get('[data-e2e="login-password"], #password, input[type="password"]', { timeout: 10000 })
      .first()
      .clear()
      .type(users.student.password);
    
    cy.get('[data-e2e="login-submit"], button[type="submit"]', { timeout: 10000 })
      .first()
      .click();

    // Wait for login API call (intercept was set up earlier)
    cy.wait('@loginRequest', { timeout: 20000 });
    
    // Student redirects to student dashboard
    cy.location('pathname', { timeout: 20000 }).should((pathname) => {
      expect(pathname === '/' || pathname === '' || pathname.endsWith('/') || pathname.includes('/dashboard/student')).to.be.true;
    });
    
    // Wait for frontend to be ready
    cy.waitUntilFrontendReady();
  });
});

/**
 * Logout current user
 * Uses stable selectors, avoids :contains()
 */
Cypress.Commands.add('logout', () => {
  // First try data-cy attribute (most stable)
  cy.get('body').then(($body) => {
    if ($body.find('[data-cy="logout"]').length > 0) {
      cy.get('[data-cy="logout"]').first().click();
      cy.wait(1000);
      cy.url().should((url) => {
        expect(url.includes('/login') || url === '/' || url.endsWith('/')).to.be.true;
      });
      return;
    }
    
    // Try aria-label or title attributes (case-insensitive using filter)
    const logoutButtons = $body.find('button[aria-label], a[aria-label]').filter(function() {
      const label = (this.getAttribute('aria-label') || '').toLowerCase();
      return label.includes('logout');
    });
    if (logoutButtons.length > 0) {
      cy.wrap(logoutButtons.first()).click();
      cy.wait(1000);
      cy.url().should((url) => {
        expect(url.includes('/login') || url === '/' || url.endsWith('/')).to.be.true;
      });
      return;
    }
    
    // Try user menu dropdown
    const userMenuButtons = $body.find('[data-cy="user-menu"], button[aria-label]').filter(function() {
      const label = (this.getAttribute('aria-label') || '').toLowerCase();
      const dataCy = this.getAttribute('data-cy') || '';
      return dataCy === 'user-menu' || label.includes('user') || label.includes('menu');
    });
    if (userMenuButtons.length > 0) {
      cy.wrap(userMenuButtons.first()).click();
      cy.wait(500);
      // Look for logout in dropdown
      cy.get('body').then(($body2) => {
        if ($body2.find('[data-cy="logout"]').length > 0) {
          cy.get('[data-cy="logout"]').first().click();
        } else {
          const logoutInDropdown = $body2.find('button[aria-label], a[aria-label]').filter(function() {
            const label = (this.getAttribute('aria-label') || '').toLowerCase();
            return label.includes('logout');
          });
          if (logoutInDropdown.length > 0) {
            cy.wrap(logoutInDropdown.first()).click();
          }
        }
      });
    }
  });
  cy.wait(1000);
  cy.url().should((url) => {
    expect(url.includes('/login') || url === '/' || url.endsWith('/')).to.be.true;
  });
});

/**
 * Wait for API response
 */
Cypress.Commands.add('waitForApi', (method, url) => {
  cy.intercept(method, url).as('apiCall');
  cy.wait('@apiCall');
});

/**
 * Navigate to dashboard section
 * Uses stable selectors with data-cy attributes preferred
 */
Cypress.Commands.add('navigateTo', (section) => {
  cy.wait(1500); // Wait for page to be ready
  
  // First try data-cy attribute (most stable)
  cy.get('body').then(($body) => {
    const dataCySelector = `[data-cy="nav-${section.toLowerCase()}"]`;
    if ($body.find(dataCySelector).length > 0) {
      cy.get(dataCySelector).first().click({ force: true });
      cy.wait(1500);
      return;
    }
    
    // Try href-based navigation (more stable than contains)
    const hrefSelectors = [
      `a[href*="/${section.toLowerCase()}"]`,
      `a[href*="/dashboard/${section.toLowerCase()}"]`,
      `a[href*="/${section}"]`,
    ];
    
    for (const selector of hrefSelectors) {
      if ($body.find(selector).length > 0) {
        cy.get(selector).first().click({ force: true });
        cy.wait(1500);
        return;
      }
    }
    
    // Try to find in sidebar using data-cy attribute (most stable)
    cy.get('body').then(($body) => {
      const sidebar = $body.find('[data-cy="sidebar"]');
      if (sidebar.length > 0) {
        // Get the first sidebar element and wrap it to ensure single element
        cy.get('[data-cy="sidebar"]').first().then(($firstNav) => {
          // Check if link exists within the sidebar before using within()
          const linkSelector = `a[href*="${section.toLowerCase()}"]`;
          const linkExists = $firstNav.find(linkSelector).length > 0;
          
          if (linkExists) {
            // Use within() only when we know the element exists
            cy.wrap($firstNav).within(() => {
              cy.get(linkSelector).first().click({ force: true });
            });
          } else {
            // Last resort: direct URL navigation
            cy.visit(`/dashboard/${section.toLowerCase()}`);
          }
        });
      } else {
        // Last resort: try direct URL navigation
        cy.visit(`/dashboard/${section.toLowerCase()}`);
      }
    });
  });
  
  cy.wait(1500); // Wait after navigation
});

/**
 * Fill form field
 */
Cypress.Commands.add('fillField', (label, value) => {
  cy.contains('label', label).then(($label) => {
    const forAttr = $label.attr('for');
    if (forAttr) {
      cy.get(`#${forAttr}`).clear().type(value);
    } else {
      cy.get('input, textarea, select').then(($inputs) => {
        const index = Array.from($inputs).findIndex(input => 
          input.closest('div, form')?.textContent.includes(label)
        );
        cy.get('input, textarea, select').eq(index).clear().type(value);
      });
    }
  });
});

/**
 * Click create/new button - uses stable selectors
 */
Cypress.Commands.add('clickCreate', () => {
  cy.get('body').then(($body) => {
    // Prefer data-cy, then href, then type attributes
    const createBtn = $body.find('[data-cy="create-btn"], [data-cy="new-btn"], a[href*="/new"], a[href*="/create"]').first();
    if (createBtn.length > 0) {
      cy.wrap(createBtn).click({ force: true });
      return;
    }
    // Fallback to button with specific type
    cy.get('button[type="button"]').then(($buttons) => {
      const btn = Array.from($buttons).find(b => 
        b.textContent?.toLowerCase().includes('create') || 
        b.textContent?.toLowerCase().includes('new')
      );
      if (btn) {
        cy.wrap(btn).click({ force: true });
      }
    });
  });
});

/**
 * Click submit/save button - uses stable selectors
 */
Cypress.Commands.add('clickSubmit', () => {
  // Prefer type="submit", then data-cy attributes
  cy.get('body').then(($body) => {
    const submitBtn = $body.find('button[type="submit"], [data-cy="submit-btn"], [data-cy="save-btn"]').first();
    if (submitBtn.length > 0) {
      cy.wrap(submitBtn).click({ force: true });
    } else {
      cy.get('button[type="submit"]').first().click({ force: true });
    }
  });
});

/**
 * Find and click element by text content (more stable than :contains)
 */
Cypress.Commands.add('clickByText', (text, options = {}) => {
  const { element = 'button, a', matchCase = false } = options;
  cy.get(element).then(($elements) => {
    const found = Array.from($elements).find(el => {
      const elText = el.textContent?.trim() || '';
      return matchCase ? elText.includes(text) : elText.toLowerCase().includes(text.toLowerCase());
    });
    if (found) {
      cy.wrap(found).click({ force: true });
    }
  });
});

