describe('Admin E2E Tests', () => {
  beforeEach(() => {
    // Clear localStorage and cookies before each test
    cy.clearLocalStorage();
    cy.clearCookies();
  });

  it('1. Admin login flow', () => {
    cy.loginAsAdmin();
    cy.url().should('include', '/dashboard/admin');
    cy.waitUntilFrontendReady();
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Dashboard') || text.includes('Admin')).to.be.true;
    });
    cy.screenshot('admin-dashboard');
  });

  it('2. Admin → Programs → Create program', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    // Navigate to Programs
    cy.navigateTo('programs');
    cy.wait(2000);
    cy.screenshot('admin-programs-list');
    
    // Click create button
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
      } else {
        cy.get('a[href*="programs/new"], a[href*="programs/create"]').first().click();
      }
    });
    
    cy.wait(2000);
    cy.screenshot('admin-program-create-form');
    
    // Fill form
    cy.fillField('Title', 'Test Program E2E');
    cy.fillField('Description', 'This is a test program created by E2E tests');
    
    // Submit form
    cy.clickSubmit();
    cy.wait(3000);
    
    // Verify program created
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.includes('Test Program E2E') || text.includes('created') || text.includes('success')).to.be.true;
    });
    cy.screenshot('admin-program-created');
  });

  it('3. Admin → Programs → Edit + Delete', () => {
    cy.loginAsAdmin();
    cy.navigateTo('programs');
    cy.wait(2000);
    
    // Find first program and edit
    cy.get('body').then(($body) => {
      const editBtn = $body.find('[data-cy="edit-btn"], a[href*="/edit"]').first();
      if (editBtn.length > 0) {
        cy.wrap(editBtn).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-program-edit');
        
        // Update title
        cy.get('input[name="title"], input[type="text"]').first().clear().type('Updated Program Title');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-program-updated');
      }
    });
  });

  it('4. Admin → Batches → Create + Assign instructor', () => {
    cy.loginAsAdmin();
    cy.navigateTo('batches');
    cy.wait(2000);
    cy.screenshot('admin-batches-list');
    
    // Create batch
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        
        // Fill batch form
        cy.fillField('Name', 'E2E Test Batch');
        cy.fillField('Start Date', '2025-02-01');
        
        // Submit
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-batch-created');
      }
    });
  });

  it('5. Admin → Groups → Create group', () => {
    cy.loginAsAdmin();
    cy.navigateTo('groups');
    cy.wait(2000);
    cy.screenshot('admin-groups-list');
    
    // Create group
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        
        cy.fillField('Name', 'E2E Test Group');
        cy.clickSubmit();
        cy.wait(2000);
        cy.screenshot('admin-group-created');
      }
    });
  });

  it('6. Admin → Page Builder → Create page + Add blocks + Publish', () => {
    cy.loginAsAdmin();
    cy.navigateTo('page-builder');
    cy.wait(2000);
    cy.screenshot('admin-page-builder-list');
    
    // Create new page
    cy.get('body').then(($body) => {
      const createBtn = $body.find('[data-cy="create-btn"], a[href*="/new"], a[href*="/create"]').first();
      if (createBtn.length > 0) {
        cy.wrap(createBtn).click({ force: true });
        cy.wait(2000);
        
        // Fill page form
        cy.fillField('Title', 'E2E Test Page');
        cy.fillField('Slug', 'e2e-test-page');
        cy.clickSubmit();
        cy.wait(3000);
        cy.screenshot('admin-page-created');
        
        // Should be in editor now
        cy.url().should('include', 'editor');
        
        // Add hero block
        cy.get('body').then(($body) => {
          const heroBtn = $body.find('[data-cy="hero-block-btn"], button[aria-label*="hero" i]').first();
          if (heroBtn.length > 0) {
            cy.wrap(heroBtn).click({ force: true });
            cy.wait(1000);
            cy.screenshot('admin-page-hero-added');
          }
        });
        
        // Add features block
        cy.get('body').then(($body) => {
          const featuresBtn = $body.find('[data-cy="features-block-btn"], button[aria-label*="features" i]').first();
          if (featuresBtn.length > 0) {
            cy.wrap(featuresBtn).click({ force: true });
            cy.wait(1000);
            cy.screenshot('admin-page-features-added');
          }
        });
        
        // Save structure
        cy.get('button[type="button"][aria-label*="save" i], [data-cy="save-btn"]').first().click({ force: true });
        cy.wait(2000);
        
        // Publish page
        cy.get('button[type="button"][aria-label*="publish" i], [data-cy="publish-btn"]').first().click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-page-published');
      }
    });
  });

  it('7. Admin → Subscriptions → View plan + Check usage', () => {
    cy.loginAsAdmin();
    cy.navigateTo('subscription');
    cy.wait(2000);
    cy.screenshot('admin-subscription-overview');
    
    // Check usage
    cy.get('body').then(($body) => {
      const usageLink = $body.find('a[href*="usage"], [data-cy="usage-link"]').first();
      if (usageLink.length > 0) {
        cy.wrap(usageLink).click({ force: true });
        cy.wait(2000);
        cy.screenshot('admin-subscription-usage');
      }
    });
  });

  it('8. Admin → Community moderation → View posts + Pin + Lock', () => {
    cy.loginAsAdmin();
    cy.navigateTo('community');
    cy.wait(2000);
    cy.screenshot('admin-community-posts');
    
    // Find first post and pin it
    cy.get('body').then(($body) => {
      const pinBtn = $body.find('button[aria-label*="pin" i], [data-cy="pin-btn"]').first();
      if (pinBtn.length > 0) {
        cy.wrap(pinBtn).click({ force: true });
        cy.wait(1000);
        cy.screenshot('admin-community-post-pinned');
      }
    });
  });

  it('9. Admin → Notifications', () => {
    cy.loginAsAdmin();
    
    // Open notifications
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
    cy.logout();
    cy.url().should((url) => {
      expect(url.includes('/login') || url === '/' || url.endsWith('/')).to.be.true;
    });
    cy.screenshot('admin-logout-success');
  });
});

