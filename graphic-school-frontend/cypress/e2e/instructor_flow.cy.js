/**
 * Instructor Full Flow E2E Test
 * Tests complete instructor workflow
 */

describe('Instructor Full Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('Complete Instructor Flow', () => {
    // 1. Login as Instructor
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    cy.get('[data-cy="sidebar"]', { timeout: 10000 }).should('exist');
    cy.screenshot('01-instructor-logged-in');

    // 2. Navigate to My Groups
    cy.visit('/dashboard/instructor/my-groups', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('02-instructor-my-groups');

    // 3. If groups exist, navigate to group sessions
    cy.get('body').then(($body) => {
      const groupLink = $body.find('a[href*="/groups/"][href*="/sessions"]').first();
      if (groupLink.length > 0) {
        cy.wrap(groupLink).click({ force: true });
        cy.wait(2000);
        cy.get('body').should('be.visible');
        cy.screenshot('03-instructor-group-sessions');
      }
    });

    // 4. Navigate to Sessions
    cy.visit('/dashboard/instructor/sessions', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('04-instructor-sessions');

    // 5. Navigate to Attendance
    cy.visit('/dashboard/instructor/attendance', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('05-instructor-attendance');

    // 6. Navigate to Calendar
    cy.visit('/dashboard/instructor/calendar', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('06-instructor-calendar');

    // 7. Navigate to Courses
    cy.visit('/dashboard/instructor/courses', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('07-instructor-courses');

    // 8. Logout
    cy.logout();
    cy.url().should('include', '/login');
    cy.screenshot('08-instructor-logged-out');
  });
});

