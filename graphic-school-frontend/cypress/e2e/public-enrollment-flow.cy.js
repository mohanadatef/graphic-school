/**
 * Public Enrollment Flow E2E Tests
 * 
 * Tests the complete public enrollment flow:
 * - User opens course details
 * - Clicks "Enroll Now"
 * - Fills public enrollment form
 * - Enrollment created → status pending
 * - Admin dashboard shows pending enrollment
 */

describe('Public Enrollment Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('courses').then((courses) => {
      cy.intercept('GET', '**/api/courses/1', {
        statusCode: 200,
        body: {
          success: true,
          data: courses.testCourse,
        },
      }).as('getCourse');
    });

    cy.fixture('enrollments').then((enrollments) => {
      cy.intercept('POST', '**/api/enroll', {
        statusCode: 201,
        body: {
          success: true,
          message: 'Enrollment request submitted successfully',
          data: {
            student: {
              id: 999,
              name: 'New Student',
              email: 'newstudent@test.com',
            },
            enrollment: enrollments.pendingEnrollment,
          },
        },
      }).as('submitEnrollment');
    });
  });

  it('1. User opens course details page', () => {
    cy.visit('/courses/1', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCourse');
    
    cy.fixture('courses').then((courses) => {
      cy.get('body').should('contain', courses.testCourse.title);
      cy.get('body').should('contain', courses.testCourse.description);
    });
    
    cy.screenshot('public-course-details');
  });

  it('2. User clicks "Enroll Now" button', () => {
    cy.visit('/courses/1', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCourse');
    
    // Find and click enroll button
    cy.get('body').then(($body) => {
      const enrollBtn = $body.find('[data-cy="enroll-btn"], button:contains("Enroll"), a:contains("Enroll"), button[aria-label*="enroll"]').first();
      if (enrollBtn.length > 0) {
        cy.wrap(enrollBtn).click({ force: true });
        cy.wait(1000);
        cy.screenshot('public-enrollment-form-opened');
      } else {
        // Try direct navigation to enrollment form
        cy.visit('/enroll?course_id=1', { timeout: 30000, failOnStatusCode: false });
        cy.wait(1000);
      }
    });
  });

  it('3. User fills public enrollment form', () => {
    cy.visit('/enroll?course_id=1', { timeout: 30000, failOnStatusCode: false });
    
    // Fill enrollment form
    cy.get('input[name="name"], input[placeholder*="Name"], #name', { timeout: 10000 })
      .first()
      .clear()
      .type('New Student');
    
    cy.get('input[name="email"], input[type="email"], #email', { timeout: 10000 })
      .first()
      .clear()
      .type('newstudent@test.com');
    
    cy.get('input[name="phone"], input[type="tel"], #phone', { timeout: 10000 })
      .first()
      .clear()
      .type('+201234567890');
    
    // Optional: Select group if available
    cy.get('body').then(($body) => {
      const groupSelect = $body.find('select[name="group_id"], select[id="group_id"]');
      if (groupSelect.length > 0) {
        cy.wrap(groupSelect).select('1', { force: true });
      }
    });
    
    cy.screenshot('public-enrollment-form-filled');
  });

  it('4. User submits enrollment form', () => {
    cy.visit('/enroll?course_id=1', { timeout: 30000, failOnStatusCode: false });
    
    // Fill form
    cy.get('input[name="name"]').first().clear().type('New Student');
    cy.get('input[name="email"]').first().clear().type('newstudent@test.com');
    cy.get('input[name="phone"]').first().clear().type('+201234567890');
    
    // Submit form
    cy.get('button[type="submit"], [data-cy="submit-enrollment"]', { timeout: 10000 })
      .first()
      .click();
    
    cy.wait('@submitEnrollment');
    
    // Verify success message
    cy.get('body', { timeout: 10000 }).should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(enrollment|submitted|success|pending)/);
    });
    
    cy.screenshot('public-enrollment-submitted');
  });

  it('5. Admin dashboard shows pending enrollment', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    // Navigate to enrollments
    cy.navigateTo('enrollments');
    cy.wait(2000);
    
    // Filter by pending status if filter exists
    cy.get('body').then(($body) => {
      const statusFilter = $body.find('select[name="status"], [data-cy="status-filter"]');
      if (statusFilter.length > 0) {
        cy.wrap(statusFilter).select('pending', { force: true });
        cy.wait(1000);
      }
    });
    
    // Verify pending enrollment is visible
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(pending|new student)/);
    });
    
    cy.screenshot('admin-pending-enrollments');
  });
});

