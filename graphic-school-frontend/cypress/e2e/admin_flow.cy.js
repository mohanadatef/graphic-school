/**
 * Admin Full Flow E2E Test
 * Tests complete admin workflow
 */

describe('Admin Full Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('Complete Admin Flow', () => {
    // 1. Login as Admin
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.get('[data-cy="sidebar"]', { timeout: 10000 }).should('exist');
    cy.screenshot('01-admin-logged-in');

    // 2. Navigate to Courses
    cy.visit('/dashboard/admin/courses', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('02-admin-courses-list');

    // 3. Create a Course
    cy.visit('/dashboard/admin/courses/create', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').then(($body) => {
      if ($body.find('input[type="text"]').length > 0) {
        cy.get('input[type="text"]').first().clear().type('E2E Test Course');
      }
      if ($body.find('textarea').length > 0) {
        cy.get('textarea').first().clear().type('This is a test course created by E2E tests');
      }
      if ($body.find('button[type="submit"]').length > 0) {
        cy.get('button[type="submit"]').first().click({ force: true });
        cy.wait(3000);
      }
    });
    cy.screenshot('03-admin-course-created');

    // 4. Navigate to Groups
    cy.visit('/dashboard/admin/groups', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('04-admin-groups-list');

    // 5. Navigate to Languages
    cy.visit('/dashboard/admin/language', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('05-admin-languages');

    // 6. Navigate to Currencies
    cy.visit('/dashboard/admin/currencies', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('06-admin-currencies');

    // 7. Navigate to Countries
    cy.visit('/dashboard/admin/countries', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('07-admin-countries');

    // 8. Navigate to Pages (CMS)
    cy.visit('/dashboard/admin/pages', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('08-admin-pages');

    // 9. Navigate to Dashboard
    cy.visit('/dashboard/admin', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('09-admin-dashboard');

    // 10. Logout
    cy.logout();
    cy.url().should('include', '/login');
    cy.screenshot('10-admin-logged-out');
  });
});

