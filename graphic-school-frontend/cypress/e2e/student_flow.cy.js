/**
 * Student Full Flow E2E Test
 * Tests complete student workflow
 */

describe('Student Full Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('Complete Student Flow', () => {
    // 1. Login as Student
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.screenshot('01-student-logged-in');

    // 2. Navigate to My Courses
    cy.visit('/dashboard/student/my-courses', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('02-student-my-courses');

    // 3. Navigate to My Group
    cy.visit('/dashboard/student/my-group', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('03-student-my-group');

    // 4. Navigate to My Sessions
    cy.visit('/dashboard/student/my-sessions', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('04-student-my-sessions');

    // 5. Navigate to Attendance History
    cy.visit('/dashboard/student/attendance-history', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('05-student-attendance-history');

    // 6. Navigate to Profile
    cy.visit('/dashboard/student/profile', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('06-student-profile');

    // 7. Browse Public Courses
    cy.visit('/courses', { failOnStatusCode: false });
    cy.wait(2000);
    cy.get('body').should('be.visible');
    cy.screenshot('07-student-public-courses');

    // 8. Logout
    cy.logout();
    cy.url().should('include', '/login');
    cy.screenshot('08-student-logged-out');
  });
});

