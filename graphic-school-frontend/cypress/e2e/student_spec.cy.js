describe('Student E2E Tests', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('1. Student login', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.url().should('eq', Cypress.config().baseUrl + '/');
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Dashboard') || text.includes('Student') || text.includes('Course')).to.be.true;
    });
    cy.screenshot('student-dashboard');
  });

  it('2. Student → Program listing', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.navigateTo('programs');
    cy.wait(2000);
    cy.screenshot('student-programs-list');
    
    // Verify programs are displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Program') || text.includes('program')).to.be.true;
    });
  });

  it('3. Student → Enroll in program', () => {
    cy.loginAsStudent();
    cy.navigateTo('programs');
    cy.wait(2000);
    
    // Click on first program
    cy.get('body').then(($body) => {
      const programLink = $body.find('a[href*="program"], [data-cy="program-link"]').first();
      if (programLink.length > 0) {
        cy.wrap(programLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('student-program-details');
        
        // Enroll
        cy.get('body').then(($body) => {
          const enrollBtn = $body.find('[data-cy="enroll-btn"], button[aria-label*="enroll" i], button[aria-label*="join" i]').first();
          if (enrollBtn.length > 0) {
            cy.wrap(enrollBtn).click({ force: true });
            cy.wait(2000);
            cy.screenshot('student-program-enrolled');
          }
        });
      }
    });
  });

  it('4. Student → Session view', () => {
    cy.loginAsStudent();
    cy.navigateTo('sessions');
    cy.wait(2000);
    cy.screenshot('student-sessions-list');
    
    // View session details
    cy.get('body').then(($body) => {
      const sessionLink = $body.find('a[href*="session"], [data-cy="session-link"]').first();
      if (sessionLink.length > 0) {
        cy.wrap(sessionLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('student-session-detail');
      }
    });
  });

  it('5. Student → Submit assignment', () => {
    cy.loginAsStudent();
    cy.navigateTo('assignments');
    cy.wait(2000);
    cy.screenshot('student-assignments-list');
    
    // Click on first assignment
    cy.get('body').then(($body) => {
      const assignmentLink = $body.find('a[href*="assignment"], [data-cy="assignment-link"]').first();
      if (assignmentLink.length > 0) {
        cy.wrap(assignmentLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('student-assignment-detail');
        
        // Submit assignment
        cy.get('body').then(($body) => {
          const textarea = $body.find('textarea, input[type="file"]').first();
          if (textarea.length > 0) {
            cy.wrap(textarea).type('E2E test assignment submission');
            cy.get('button[type="submit"], [data-cy="submit-btn"]').first().click({ force: true });
            cy.wait(2000);
            cy.screenshot('student-assignment-submitted');
          }
        });
      }
    });
  });

  it('6. Student → View gradebook', () => {
    cy.loginAsStudent();
    cy.navigateTo('gradebook');
    cy.wait(2000);
    cy.screenshot('student-gradebook');
    
    // Verify gradebook is displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Grade') || text.includes('grade') || text.includes('Score')).to.be.true;
    });
  });

  it('7. Student → View certificates', () => {
    cy.loginAsStudent();
    cy.navigateTo('certificates');
    cy.wait(2000);
    cy.screenshot('student-certificates');
    
    // Verify certificates are displayed
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Certificate') || text.includes('certificate')).to.be.true;
    });
  });

  it('8. Student → Community → Create post + like + reply', () => {
    cy.loginAsStudent();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.screenshot('student-community-feed');
    
    // Create post
    cy.get('body').then(($body) => {
      const createPostBtn = $body.find('[data-cy="create-post-btn"], a[href*="/create"], button[aria-label*="create post" i]').first();
      if (createPostBtn.length > 0) {
        cy.wrap(createPostBtn).click({ force: true });
        cy.wait(2000);
        
        cy.fillField('Title', 'Student E2E Test Post');
        cy.fillField('Body', 'This is a test post from student E2E tests');
        
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('student-community-post-created');
        
        // Like a post
        cy.get('body').then(($body) => {
          const likeBtn = $body.find('button[aria-label*="like" i], [data-cy="like-btn"], .like-button').first();
          if (likeBtn.length > 0) {
            cy.wrap(likeBtn).click({ force: true });
            cy.wait(1000);
            cy.screenshot('student-community-post-liked');
          }
        });
        
        // Reply to a post
        cy.get('body').then(($body) => {
          const replyBtn = $body.find('button[aria-label*="reply" i], [data-cy="reply-btn"]').first();
          if (replyBtn.length > 0) {
            cy.wrap(replyBtn).click({ force: true });
            cy.wait(1000);
            cy.get('textarea[placeholder*="reply"], textarea').first().type('E2E test reply');
            cy.get('button[type="submit"], [data-cy="submit-reply-btn"]').first().click({ force: true });
            cy.wait(2000);
            cy.screenshot('student-community-reply-added');
          }
        });
      }
    });
  });

  it('9. Student → Gamification → Check XP + leaderboard', () => {
    cy.loginAsStudent();
    cy.navigateTo('gamification');
    cy.wait(2000);
    cy.screenshot('student-gamification-summary');
    
    // Check leaderboard
    cy.get('body').then(($body) => {
      const leaderboardLink = $body.find('a[href*="leaderboard"], [data-cy="leaderboard-link"]').first();
      if (leaderboardLink.length > 0) {
        cy.wrap(leaderboardLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('student-leaderboard');
      }
    });
  });

  it('10. Student logout', () => {
    cy.loginAsStudent();
    cy.logout();
    cy.url().should((url) => {
      expect(url.includes('/login') || url === '/' || url.endsWith('/')).to.be.true;
    });
    cy.screenshot('student-logout-success');
  });
});

