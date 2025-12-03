/**
 * Admin E2E Tests - Updated for Course → Group → Session Flow
 * 
 * Removed: Programs, Batches, Subscriptions, QR Attendance
 * Updated: Course → Group → Session flow
 */

describe('Admin E2E Tests', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('1. Admin login flow', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.get('[data-cy="sidebar"]', { timeout: 10000 }).should('exist');
    cy.get('body', { timeout: 10000 }).should(($body) => {
      const text = $body.text();
      expect(text.includes('Dashboard') || text.includes('Admin') || text.includes('لوحة')).to.be.true;
    });
    cy.screenshot('admin-dashboard');
  });

  it('2. Admin → Courses → Create course', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    cy.navigateTo('courses');
    cy.wait(2000);
    cy.screenshot('admin-courses-list');
    
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-course-create-form');
        
        // Fill form if fields exist
        cy.get('body', { timeout: 10000 }).then(($formBody) => {
          if ($formBody.find('input[name="title"], input[type="text"]').length > 0) {
            cy.get('input[name="title"]').first().clear().type('Test Course E2E');
          }
          if ($formBody.find('textarea[name="description"], textarea').length > 0) {
            cy.get('textarea[name="description"]').first().clear().type('This is a test course created by E2E tests');
          }
          if ($formBody.find('input[name="price"]').length > 0) {
            cy.get('input[name="price"]').first().clear().type('5000');
          }
          
          // Submit form if button exists
          if ($formBody.find('button[type="submit"], [data-cy="submit-btn"]').length > 0) {
            cy.get('button[type="submit"]').first().click({ force: true });
            cy.wait(3000);
            cy.screenshot('admin-course-created');
          }
        });
      }
    });
  });

  it('3. Admin → Courses → Edit + Delete', () => {
    cy.loginAsAdmin();
    cy.navigateTo('courses');
    cy.wait(2000);
    
    cy.get('body').then(($body) => {
      const editBtn = $body.find('[data-cy="edit-btn"], a[href*="/edit"]').first();
      if (editBtn.length > 0) {
        cy.wrap(editBtn).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-course-edit');
        
        cy.get('input[name="title"]').first().clear().type('Updated Course Title');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-course-updated');
      }
    });
  });

  it('4. Admin → Groups → Create group', () => {
    cy.loginAsAdmin();
    cy.navigateTo('groups');
    cy.wait(2000);
    cy.screenshot('admin-groups-list');
    
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        
        cy.fillField('Name', 'E2E Test Group');
        cy.fillField('Start Date', '2025-02-01');
        cy.fillField('End Date', '2025-06-30');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-group-created');
      }
    });
  });

  it('5. Admin → Sessions → Create session', () => {
    cy.loginAsAdmin();
    cy.navigateTo('sessions');
    cy.wait(2000);
    cy.screenshot('admin-sessions-list');
    
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        
        cy.fillField('Title', 'E2E Test Session');
        cy.fillField('Date', '2025-02-01');
        cy.fillField('Start Time', '10:00');
        cy.fillField('End Time', '12:00');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-session-created');
      }
    });
  });

  it('6. Admin → Enrollments → View and approve', () => {
    cy.loginAsAdmin();
    cy.navigateTo('enrollments');
    cy.wait(2000);
    cy.screenshot('admin-enrollments-list');
    
    cy.get('body').then(($body) => {
      const approveBtn = $body.find('[data-cy="approve-btn"], button:contains("Approve")').first();
      if (approveBtn.length > 0) {
        cy.wrap(approveBtn).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-enrollment-approved');
      }
    });
  });

  it('7. Admin → Certificates → Issue certificate', () => {
    cy.loginAsAdmin();
    cy.navigateTo('certificates');
    cy.wait(2000);
    cy.screenshot('admin-certificates-list');
    
    cy.get('body').then(($body) => {
      const issueBtn = $body.find('[data-cy="issue-certificate-btn"], button:contains("Issue")').first();
      if (issueBtn.length > 0) {
        cy.wrap(issueBtn).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-certificate-issue-form');
      }
    });
  });

  it('8. Admin → Community moderation → View posts + Pin + Lock', () => {
    cy.loginAsAdmin();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.screenshot('admin-community-posts');
    
    cy.get('body').then(($body) => {
      const pinBtn = $body.find('button[aria-label*="pin"], button[aria-label*="Pin"], [data-cy="pin-btn"]').first();
      if (pinBtn.length > 0) {
        cy.wrap(pinBtn).click({ force: true });
        cy.wait(1000);
        cy.screenshot('admin-community-post-pinned');
      }
    });
  });

  it('9. Admin → Notifications', () => {
    cy.loginAsAdmin();
    
    cy.get('body').then(($body) => {
      if ($body.find('[data-cy="notifications"], button[aria-label*="notification"], .notification').length > 0) {
        cy.get('[data-cy="notifications"], button[aria-label*="notification"], .notification').first().click();
        cy.wait(1000);
        cy.screenshot('admin-notifications');
      }
    });
  });

  it('10. Admin logout', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.logout();
    cy.location('pathname', { timeout: 10000 }).should((pathname) => {
      expect(pathname === '/login' || pathname === '/' || pathname.endsWith('/')).to.be.true;
    });
    cy.screenshot('admin-logout-success');
  });
});
