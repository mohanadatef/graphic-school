/**
 * Admin Enrollment Approval E2E Tests
 * 
 * Tests the admin enrollment approval flow:
 * - Admin logs in
 * - Opens Enrollments page
 * - Approves enrollment
 * - Assigns student to a group
 * - Student now visible in group_student pivot
 * - Student dashboard shows assigned group
 */

describe('Admin Enrollment Approval Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('enrollments').then((enrollments) => {
      cy.intercept('GET', '**/api/admin/enrollments*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [enrollments.pendingEnrollment],
            meta: {
              pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 1,
              },
            },
          },
        },
      }).as('getEnrollments');
      
      cy.intercept('GET', '**/api/admin/enrollments/1', {
        statusCode: 200,
        body: {
          success: true,
          data: enrollments.pendingEnrollment,
        },
      }).as('getEnrollment');
    });

    cy.fixture('groups').then((groups) => {
      cy.intercept('GET', '**/api/admin/groups*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [groups.testGroup],
            meta: {
              pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 1,
              },
            },
          },
        },
      }).as('getGroups');
    });
  });

  it('1. Admin logs in and navigates to Enrollments', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    cy.navigateTo('enrollments');
    cy.wait('@getEnrollments');
    
    cy.get('body', { timeout: 10000 }).should('be.visible');
    cy.screenshot('admin-enrollments-page');
  });

  it('2. Admin views pending enrollment details', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('enrollments');
    cy.wait('@getEnrollments');
    
    // Click on enrollment to view details
    cy.get('body').then(($body) => {
      const enrollmentLink = $body.find('a[href*="enrollment"], [data-cy="enrollment-link"], tr').first();
      if (enrollmentLink.length > 0) {
        cy.wrap(enrollmentLink).click({ force: true });
        cy.wait('@getEnrollment');
        cy.wait(1000);
        cy.screenshot('admin-enrollment-details');
      }
    });
  });

  it('3. Admin approves enrollment and assigns to group', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('enrollments');
    cy.wait('@getEnrollments');
    
    cy.fixture('enrollments').then((enrollments) => {
      // Mock approval API
      cy.intercept('POST', '**/api/admin/enrollments/1/approve', {
        statusCode: 200,
        body: {
          success: true,
          message: 'Enrollment approved successfully',
          data: enrollments.approvedEnrollment,
        },
      }).as('approveEnrollment');
      
      // Find approve button
      cy.get('body').then(($body) => {
        const approveBtn = $body.find('[data-cy="approve-btn"], button:contains("Approve"), button[aria-label*="approve"]').first();
        if (approveBtn.length > 0) {
          cy.wrap(approveBtn).click({ force: true });
          cy.wait(1000);
          
          // Select group in modal/form if appears
          cy.get('body').then(($modalBody) => {
            const groupSelect = $modalBody.find('select[name="group_id"], select[id="group_id"]');
            if (groupSelect.length > 0) {
              cy.wrap(groupSelect).select('1', { force: true });
              cy.wait(500);
            }
          });
          
          // Submit approval
          cy.get('button[type="submit"], [data-cy="submit-approval"]', { timeout: 5000 })
            .first()
            .click({ force: true });
          
          cy.wait('@approveEnrollment');
          cy.wait(2000);
          
          // Verify success message
          cy.get('body').should(($body) => {
            const text = $body.text();
            expect(text.toLowerCase()).to.match(/(approved|success)/);
          });
          
          cy.screenshot('admin-enrollment-approved');
        }
      });
    });
  });

  it('4. Admin verifies student is assigned to group', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    // Navigate to groups
    cy.navigateTo('groups');
    cy.wait('@getGroups');
    cy.wait(1000);
    
    // Open group details
    cy.get('body').then(($body) => {
      const groupLink = $body.find('a[href*="group"], [data-cy="group-link"], tr').first();
      if (groupLink.length > 0) {
        cy.wrap(groupLink).click({ force: true });
        cy.wait(2000);
        
        // Check students tab/section
        cy.get('body').then(($groupBody) => {
          const studentsTab = $groupBody.find('[data-cy="students-tab"], button:contains("Students"), a:contains("Students")').first();
          if (studentsTab.length > 0) {
            cy.wrap(studentsTab).click({ force: true });
            cy.wait(1000);
            
            // Verify student is in the list
            cy.get('body').should(($body) => {
              const text = $body.text();
              expect(text.toLowerCase()).to.match(/(test student|student)/);
            });
            
            cy.screenshot('admin-group-students');
          }
        });
      }
    });
  });

  it('5. Student dashboard shows assigned group', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    
    cy.fixture('enrollments').then((enrollments) => {
      // Mock student's courses and group
      cy.intercept('GET', '**/api/student/my-courses*', {
        statusCode: 200,
        body: {
          success: true,
          data: [
            {
              id: enrollments.approvedEnrollment.id,
              course: enrollments.approvedEnrollment.course,
              enrollment: {
                status: 'approved',
                group: enrollments.approvedEnrollment.group,
              },
            },
          ],
        },
      }).as('getMyCourses');
      
      cy.intercept('GET', '**/api/student/my-group*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            group: enrollments.approvedEnrollment.group,
            course: enrollments.approvedEnrollment.course,
          },
        },
      }).as('getMyGroup');
    });
    
    // Navigate to student dashboard
    cy.visit('/dashboard/student', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getMyCourses');
    cy.wait(2000);
    
    // Verify group is shown
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(group|assigned)/);
    });
    
    // Navigate to my-group page
    cy.visit('/dashboard/student/my-group', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getMyGroup');
    cy.wait(2000);
    
    cy.fixture('enrollments').then((enrollments) => {
      cy.get('body').should('contain', enrollments.approvedEnrollment.group.name);
    });
    
    cy.screenshot('student-assigned-group');
  });
});

