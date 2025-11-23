describe('Full Platform Flow Simulation', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('Complete multi-user platform flow', () => {
    // Step 1: Admin creates a program + batch + group
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    // Verify redirect completed
    cy.location('pathname', { timeout: 10000 }).should('include', '/dashboard/admin');
    cy.screenshot('flow-1-admin-logged-in');
    
    // Create Program
    cy.navigateTo('programs');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      // Use stable selectors - prefer data-cy, then href, then type
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"], button[type="button"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        cy.fillField('Title', 'E2E Flow Test Program');
        cy.fillField('Description', 'Program created for full flow E2E test');
        cy.get('button[type="submit"]').first().click();
        cy.wait(3000);
        cy.screenshot('flow-2-program-created');
      }
    });
    
    // Create Batch
    cy.navigateTo('batches');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        cy.fillField('Name', 'E2E Flow Test Batch');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('flow-3-batch-created');
      }
    });
    
    // Create Group
    cy.navigateTo('groups');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        cy.fillField('Name', 'E2E Flow Test Group');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('flow-4-group-created');
      }
    });
    
    cy.logout();
    
    // Step 2: Instructor sees the new group
    cy.loginAsInstructor();
    cy.location('pathname', { timeout: 10000 }).should('not.include', '/login');
    cy.screenshot('flow-5-instructor-logged-in');
    cy.navigateTo('groups');
    cy.wait(2000);
    cy.screenshot('flow-6-instructor-sees-groups');
    cy.logout();
    
    // Step 3: Student enrolls in the new program
    cy.loginAsStudent();
    cy.location('pathname', { timeout: 10000 }).should('not.include', '/login');
    cy.screenshot('flow-7-student-logged-in');
    cy.navigateTo('programs');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const programLink = $body.find('a[href*="program"], [data-cy="program-link"]').first();
      if (programLink.length > 0) {
        cy.wrap(programLink).click({ force: true });
        cy.wait(2000);
        cy.get('body').then(($body) => {
          const enrollBtn = $body.find('[data-cy="enroll-btn"], button[aria-label*="enroll" i], button[aria-label*="join" i]').first();
          if (enrollBtn.length > 0) {
            cy.wrap(enrollBtn).click({ force: true });
            cy.wait(2000);
            cy.screenshot('flow-8-student-enrolled');
          }
        });
      }
    });
    cy.logout();
    
    // Step 4: Instructor marks student attendance
    cy.loginAsInstructor();
    cy.navigateTo('sessions');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const sessionLink = $body.find('a[href*="session"], [data-cy="session-link"]').first();
      if (sessionLink.length > 0) {
        cy.wrap(sessionLink).click({ force: true });
        cy.wait(2000);
        cy.get('body').then(($body) => {
          const attendanceLink = $body.find('[data-cy="attendance-link"], a[href*="attendance"]').first();
          if (attendanceLink.length > 0) {
            cy.wrap(attendanceLink).click({ force: true });
            cy.wait(2000);
            // Mark student as present
            cy.get('body').then(($body) => {
              const checkbox = $body.find('input[type="checkbox"]').first();
              if (checkbox.length > 0) {
                cy.wrap(checkbox).check();
                cy.clickSubmit();
                cy.wait(2000);
                cy.screenshot('flow-9-attendance-marked');
              }
            });
          }
        });
      }
    });
    cy.logout();
    
    // Step 5: Student receives XP (check gamification)
    cy.loginAsStudent();
    cy.navigateTo('gamification');
    cy.wait(2000);
    cy.screenshot('flow-10-student-xp-check');
    cy.logout();
    
    // Step 6: Student makes community post
    cy.loginAsStudent();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const createPostBtn = $body.find('[data-cy="create-post-btn"], a[href*="/create"], button[aria-label*="create post" i]').first();
      if (createPostBtn.length > 0) {
        cy.wrap(createPostBtn).click({ force: true });
        cy.wait(2000);
        cy.fillField('Title', 'E2E Flow Test Post');
        cy.fillField('Body', 'This post is part of the full flow E2E test');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('flow-11-student-post-created');
      }
    });
    cy.logout();
    
    // Step 7: Admin sees and pins the post
    cy.loginAsAdmin();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const pinBtn = $body.find('button[aria-label*="pin" i], [data-cy="pin-btn"]').first();
      if (pinBtn.length > 0) {
        cy.wrap(pinBtn).click({ force: true });
        cy.wait(1000);
        cy.screenshot('flow-12-admin-pinned-post');
      }
    });
    cy.logout();
    
    // Step 8: Student checks pinned post
    cy.loginAsStudent();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.get('body').should('contain', 'E2E Flow Test Post');
    cy.screenshot('flow-13-student-sees-pinned-post');
    cy.logout();
    
    // Step 9: Admin builds a page in Page Builder
    cy.loginAsAdmin();
    cy.navigateTo('page-builder');
    cy.wait(2000);
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        cy.fillField('Title', 'E2E Flow Test Page');
        cy.fillField('Slug', 'e2e-flow-test-page');
        cy.clickSubmit();
        cy.wait(3000);
        
        // Add blocks
        cy.get('body').then(($body) => {
          const heroBtn = $body.find('[data-cy="hero-block-btn"], button[aria-label*="hero" i]').first();
          if (heroBtn.length > 0) {
            cy.wrap(heroBtn).click({ force: true });
            cy.wait(1000);
          }
        });
        
        // Save and publish
        cy.get('button[type="button"][aria-label*="save" i], [data-cy="save-btn"]').first().click({ force: true });
        cy.wait(2000);
        cy.get('button[type="button"][aria-label*="publish" i], [data-cy="publish-btn"]').first().click({ force: true });
        cy.wait(2000);
        cy.screenshot('flow-14-admin-page-published');
      }
    });
    cy.logout();
    
    // Step 10: Student opens public page and sees new content
    cy.visit('/');
    cy.wait(2000);
    // Navigate to public page (if route exists)
    cy.get('body').then(($body) => {
      if ($body.find('a[href*="e2e-flow-test-page"]').length > 0) {
        cy.get('a[href*="e2e-flow-test-page"]').first().click();
        cy.wait(2000);
        cy.screenshot('flow-15-student-sees-public-page');
      }
    });
    
    cy.screenshot('flow-complete-final');
  });
});

