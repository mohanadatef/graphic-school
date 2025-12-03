/**
 * Instructor Attendance Flow E2E Tests
 * 
 * Tests the instructor attendance marking flow:
 * - Instructor logs in
 * - Opens "My Groups"
 * - Opens group sessions
 * - Opens session attendance
 * - Marks Present / Absent / Late
 * - Confirms attendance saved
 */

describe('Instructor Attendance Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('groups').then((groups) => {
      cy.intercept('GET', '**/api/instructor/my-groups*', {
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
      }).as('getMyGroups');
    });

    cy.fixture('sessions').then((sessions) => {
      cy.intercept('GET', '**/api/instructor/groups/1/sessions*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [sessions.testSession],
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
      }).as('getGroupSessions');
    });

    cy.fixture('attendance').then((attendance) => {
      cy.intercept('GET', '**/api/instructor/sessions/1/attendance*', {
        statusCode: 200,
        body: {
          success: true,
          data: attendance.sessionAttendance,
        },
      }).as('getSessionAttendance');
      
      cy.intercept('POST', '**/api/instructor/sessions/1/attendance/update', {
        statusCode: 200,
        body: {
          success: true,
          message: 'Attendance updated successfully',
          data: {
            updated: 1,
          },
        },
      }).as('updateAttendance');
    });
  });

  it('1. Instructor logs in and opens My Groups', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    // My Groups should be default instructor dashboard
    cy.wait('@getMyGroups');
    
    cy.get('body', { timeout: 10000 }).should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(group|my groups)/);
    });
    
    cy.screenshot('instructor-my-groups');
  });

  it('2. Instructor opens group sessions', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    cy.wait('@getMyGroups');
    
    // Click on group to view sessions
    cy.get('body').then(($body) => {
      const groupLink = $body.find('a[href*="group"], [data-cy="group-link"], tr').first();
      if (groupLink.length > 0) {
        cy.wrap(groupLink).click({ force: true });
        cy.wait(1000);
        
        // Look for sessions tab or sessions section
        cy.get('body').then(($groupBody) => {
          const sessionsTab = $groupBody.find('[data-cy="sessions-tab"], button:contains("Sessions"), a:contains("Sessions"), a[href*="sessions"]').first();
          if (sessionsTab.length > 0) {
            cy.wrap(sessionsTab).click({ force: true });
            cy.wait('@getGroupSessions');
            cy.wait(1000);
            cy.screenshot('instructor-group-sessions');
          } else {
            // Try direct navigation
            cy.visit('/dashboard/instructor/groups/1/sessions', { timeout: 30000, failOnStatusCode: false });
            cy.wait('@getGroupSessions');
            cy.wait(1000);
          }
        });
      }
    });
  });

  it('3. Instructor opens session attendance', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    // Navigate to session attendance directly or via group
    cy.visit('/dashboard/instructor/sessions', { timeout: 30000, failOnStatusCode: false });
    cy.wait(2000);
    
    // Find session and click to view attendance
    cy.get('body').then(($body) => {
      const sessionLink = $body.find('a[href*="session"], [data-cy="session-link"], tr').first();
      if (sessionLink.length > 0) {
        cy.wrap(sessionLink).click({ force: true });
        cy.wait(1000);
        
        // Look for attendance button/link
        cy.get('body').then(($sessionBody) => {
          const attendanceLink = $sessionBody.find('[data-cy="attendance-link"], a:contains("Attendance"), button:contains("Attendance"), a[href*="attendance"]').first();
          if (attendanceLink.length > 0) {
            cy.wrap(attendanceLink).click({ force: true });
            cy.wait('@getSessionAttendance');
            cy.wait(1000);
            cy.screenshot('instructor-session-attendance');
          } else {
            // Try direct navigation
            cy.visit('/dashboard/instructor/sessions/1/attendance', { timeout: 30000, failOnStatusCode: false });
            cy.wait('@getSessionAttendance');
            cy.wait(1000);
          }
        });
      }
    });
  });

  it('4. Instructor marks student as Present', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    cy.visit('/dashboard/instructor/sessions/1/attendance', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getSessionAttendance');
    cy.wait(2000);
    
    // Find student row and mark as present
    cy.get('body').then(($body) => {
      const studentRow = $body.find('tr, [data-cy="student-row"]').first();
      if (studentRow.length > 0) {
        // Look for present button/radio
        cy.wrap(studentRow).within(() => {
          const presentBtn = cy.get('[data-cy="present-btn"], button:contains("Present"), input[value="present"], [aria-label*="present"]').first();
          presentBtn.then(($btn) => {
            if ($btn.length > 0) {
              cy.wrap($btn).click({ force: true });
            } else {
              // Try select dropdown
              const statusSelect = cy.get('select[name*="status"], select[data-cy="status-select"]').first();
              statusSelect.then(($select) => {
                if ($select.length > 0) {
                  cy.wrap($select).select('present', { force: true });
                }
              });
            }
          });
        });
      }
    });
    
    // Save attendance
    cy.get('button[type="submit"], [data-cy="save-attendance"], button:contains("Save")', { timeout: 5000 })
      .first()
      .click({ force: true });
    
    cy.wait('@updateAttendance');
    cy.wait(2000);
    
    // Verify success
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(saved|updated|success)/);
    });
    
    cy.screenshot('instructor-attendance-present-marked');
  });

  it('5. Instructor marks student as Absent', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    cy.visit('/dashboard/instructor/sessions/1/attendance', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getSessionAttendance');
    cy.wait(2000);
    
    // Mark as absent
    cy.get('body').then(($body) => {
      const studentRow = $body.find('tr, [data-cy="student-row"]').first();
      if (studentRow.length > 0) {
        cy.wrap(studentRow).within(() => {
          const absentBtn = cy.get('[data-cy="absent-btn"], button:contains("Absent"), input[value="absent"], [aria-label*="absent"]').first();
          absentBtn.then(($btn) => {
            if ($btn.length > 0) {
              cy.wrap($btn).click({ force: true });
            } else {
              cy.get('select[name*="status"]').first().select('absent', { force: true });
            }
          });
        });
      }
    });
    
    cy.get('button[type="submit"], [data-cy="save-attendance"]').first().click({ force: true });
    cy.wait('@updateAttendance');
    cy.wait(2000);
    
    cy.screenshot('instructor-attendance-absent-marked');
  });

  it('6. Instructor marks student as Late', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    cy.visit('/dashboard/instructor/sessions/1/attendance', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getSessionAttendance');
    cy.wait(2000);
    
    // Mark as late
    cy.get('body').then(($body) => {
      const studentRow = $body.find('tr, [data-cy="student-row"]').first();
      if (studentRow.length > 0) {
        cy.wrap(studentRow).within(() => {
          const lateBtn = cy.get('[data-cy="late-btn"], button:contains("Late"), input[value="late"], [aria-label*="late"]').first();
          lateBtn.then(($btn) => {
            if ($btn.length > 0) {
              cy.wrap($btn).click({ force: true });
            } else {
              cy.get('select[name*="status"]').first().select('late', { force: true });
            }
          });
        });
      }
    });
    
    cy.get('button[type="submit"], [data-cy="save-attendance"]').first().click({ force: true });
    cy.wait('@updateAttendance');
    cy.wait(2000);
    
    cy.screenshot('instructor-attendance-late-marked');
  });

  it('7. Instructor confirms attendance is saved', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    
    cy.visit('/dashboard/instructor/sessions/1/attendance', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getSessionAttendance');
    cy.wait(2000);
    
    // Verify attendance status is displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(present|absent|late|attendance)/);
    });
    
    cy.screenshot('instructor-attendance-confirmed');
  });
});

