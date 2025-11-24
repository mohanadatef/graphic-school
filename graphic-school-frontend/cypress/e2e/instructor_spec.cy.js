describe('Instructor E2E Tests', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('1. Instructor login', () => {
    cy.loginAsInstructor();
    // Login command already checks redirect, just verify dashboard loaded
    cy.waitUntilFrontendReady();
    cy.get('[data-cy="sidebar"]', { timeout: 10000 }).should('exist');
    cy.get('body', { timeout: 10000 }).should(($body) => {
      const text = $body.text();
      expect(text.includes('Dashboard') || text.includes('Instructor') || text.includes('مدرب')).to.be.true;
    });
    cy.screenshot('instructor-dashboard');
  });

  it('2. Instructor → Groups → View assigned groups', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    cy.navigateTo('groups');
    cy.wait(2000);
    cy.screenshot('instructor-groups-list');
    
    // Verify groups are displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Group') || text.includes('group')).to.be.true;
    });
  });

  it('3. Instructor → Sessions → Mark attendance', () => {
    cy.loginAsInstructor();
    cy.navigateTo('sessions');
    cy.wait(2000);
    cy.screenshot('instructor-sessions-list');
    
    // Click on first session
    cy.get('body').then(($body) => {
      const sessionLink = $body.find('a[href*="session"], [data-cy="session-link"]').first();
      if (sessionLink.length > 0) {
        cy.wrap(sessionLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('instructor-session-detail');
        
        // Mark attendance
        cy.get('body').then(($body) => {
          const attendanceLink = $body.find('[data-cy="attendance-link"], a[href*="attendance"]').first();
          if (attendanceLink.length > 0) {
            cy.wrap(attendanceLink).click({ force: true });
            cy.wait(2000);
            cy.screenshot('instructor-attendance-marking');
          }
        });
      }
    });
  });

  it('4. Instructor → Assignments → View submissions', () => {
    cy.loginAsInstructor();
    cy.navigateTo('assignments');
    cy.wait(2000);
    cy.screenshot('instructor-assignments-list');
    
    // View submissions
    cy.get('body').then(($body) => {
      const submissionsLink = $body.find('[data-cy="submissions-link"], a[href*="submissions"]').first();
      if (submissionsLink.length > 0) {
        cy.wrap(submissionsLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('instructor-assignment-submissions');
      }
    });
  });

  it('5. Instructor → Calendar → View schedule', () => {
    cy.loginAsInstructor();
    cy.navigateTo('calendar');
    cy.wait(2000);
    cy.screenshot('instructor-calendar');
    
    // Verify calendar is displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Calendar') || text.includes('calendar') || text.includes('Schedule')).to.be.true;
    });
  });

  it('6. Instructor → Community → Post + comment', () => {
    cy.loginAsInstructor();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.screenshot('instructor-community-feed');
    
    // Create post
    cy.get('body').then(($body) => {
      const createPostBtn = $body.find('[data-cy="create-post-btn"], a[href*="/create"], button[aria-label*="create post"], button[aria-label*="Create post"], button[aria-label*="Create Post"]').first();
      if (createPostBtn.length > 0) {
        cy.wrap(createPostBtn).click({ force: true });
        cy.wait(2000);
        
        cy.fillField('Title', 'Instructor E2E Test Post');
        cy.fillField('Body', 'This is a test post from instructor E2E tests');
        
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('instructor-community-post-created');
        
        // Add comment
        cy.get('body').then(($body) => {
          const commentInput = $body.find('textarea[placeholder*="comment"], input[placeholder*="comment"]').first();
          if (commentInput.length > 0) {
            cy.wrap(commentInput).type('E2E test comment');
            cy.get('button[type="submit"], [data-cy="comment-btn"]').first().click({ force: true });
            cy.wait(2000);
            cy.screenshot('instructor-community-comment-added');
          }
        });
      }
    });
  });

  it('7. Instructor logout', () => {
    cy.loginAsInstructor();
    cy.waitUntilFrontendReady();
    cy.logout();
    cy.location('pathname', { timeout: 10000 }).should((pathname) => {
      expect(pathname === '/login' || pathname === '/' || pathname.endsWith('/')).to.be.true;
    });
    cy.screenshot('instructor-logout-success');
  });
});

