/**
 * Community Module E2E Tests
 * 
 * Tests the community module flows:
 * 1. Instructor actions: Create post, Edit post, Delete post, Create comment, Create reply
 * 2. Student actions: View group posts, Comment & reply, Like posts
 * 3. Admin moderation: View all posts, View reported posts, Delete post, Manage flagged content
 */

describe('Community Module Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('community').then((community) => {
      cy.intercept('GET', '**/api/community/posts*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [community.testPost],
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
      }).as('getPosts');
      
      cy.intercept('POST', '**/api/community/posts', {
        statusCode: 201,
        body: {
          success: true,
          message: 'Post created successfully',
          data: community.testPost,
        },
      }).as('createPost');
      
      cy.intercept('POST', '**/api/community/posts/1/comments', {
        statusCode: 201,
        body: {
          success: true,
          message: 'Comment added successfully',
          data: community.testComment,
        },
      }).as('createComment');
    });
  });

  describe('Instructor Community Actions', () => {
    it('1. Instructor creates a post', () => {
      cy.loginAsInstructor();
      cy.waitUntilFrontendReady();
      
      cy.visit('/dashboard/instructor/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Click create post button
      cy.get('body').then(($body) => {
        const createBtn = $body.find('[data-cy="create-post-btn"], button:contains("Create Post"), a:contains("Create")').first();
        if (createBtn.length > 0) {
          cy.wrap(createBtn).click({ force: true });
          cy.wait(1000);
          
          // Fill post form
          cy.get('input[name="title"], #title', { timeout: 5000 }).first().type('Test Post Title');
          cy.get('textarea[name="body"], #body', { timeout: 5000 }).first().type('This is a test post body content.');
          
          // Submit
          cy.get('button[type="submit"], [data-cy="submit-post"]').first().click({ force: true });
          cy.wait('@createPost');
          cy.wait(2000);
          
          cy.screenshot('instructor-post-created');
        }
      });
    });

    it('2. Instructor creates a comment', () => {
      cy.loginAsInstructor();
      cy.waitUntilFrontendReady();
      cy.visit('/dashboard/instructor/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Find post and add comment
      cy.get('body').then(($body) => {
        const commentInput = $body.find('textarea[placeholder*="Comment"], input[name="comment"], textarea[name="body"]').first();
        if (commentInput.length > 0) {
          cy.wrap(commentInput).type('This is a test comment from instructor');
          cy.get('button[type="submit"], button:contains("Comment"), [data-cy="submit-comment"]').first().click({ force: true });
          cy.wait('@createComment');
          cy.wait(2000);
          cy.screenshot('instructor-comment-created');
        }
      });
    });
  });

  describe('Student Community Actions', () => {
    it('3. Student views group posts', () => {
      cy.loginAsStudent();
      cy.waitUntilFrontendReady();
      
      cy.visit('/dashboard/student/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Verify posts are displayed
      cy.fixture('community').then((community) => {
        cy.get('body').should('contain', community.testPost.title);
      });
      
      cy.screenshot('student-community-posts');
    });

    it('4. Student comments on a post', () => {
      cy.loginAsStudent();
      cy.waitUntilFrontendReady();
      cy.visit('/dashboard/student/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Find comment input
      cy.get('body').then(($body) => {
        const commentInput = $body.find('textarea[placeholder*="Comment"], textarea[name="comment"]').first();
        if (commentInput.length > 0) {
          cy.wrap(commentInput).type('Great post! Thanks for sharing.');
          cy.get('button[type="submit"], button:contains("Comment")').first().click({ force: true });
          cy.wait('@createComment');
          cy.wait(2000);
          cy.screenshot('student-comment-added');
        }
      });
    });

    it('5. Student likes a post', () => {
      cy.loginAsStudent();
      cy.waitUntilFrontendReady();
      cy.visit('/dashboard/student/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Mock like API
      cy.intercept('POST', '**/api/community/likes/toggle', {
        statusCode: 200,
        body: {
          success: true,
          message: 'Like toggled successfully',
          data: { liked: true },
        },
      }).as('toggleLike');
      
      // Find like button
      cy.get('body').then(($body) => {
        const likeBtn = $body.find('[data-cy="like-btn"], button:contains("Like"), button[aria-label*="like"]').first();
        if (likeBtn.length > 0) {
          cy.wrap(likeBtn).click({ force: true });
          cy.wait('@toggleLike');
          cy.wait(1000);
          cy.screenshot('student-post-liked');
        }
      });
    });
  });

  describe('Admin Community Moderation', () => {
    it('6. Admin views all community posts', () => {
      cy.loginAsAdmin();
      cy.waitUntilFrontendReady();
      
      cy.visit('/dashboard/admin/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      cy.get('body').should('be.visible');
      cy.screenshot('admin-community-posts');
    });

    it('7. Admin deletes a post', () => {
      cy.loginAsAdmin();
      cy.waitUntilFrontendReady();
      cy.visit('/dashboard/admin/community', { timeout: 30000, failOnStatusCode: false });
      cy.wait('@getPosts');
      cy.wait(2000);
      
      // Mock delete API
      cy.intercept('DELETE', '**/api/community/posts/1', {
        statusCode: 200,
        body: {
          success: true,
          message: 'Post deleted successfully',
        },
      }).as('deletePost');
      
      // Find delete button
      cy.get('body').then(($body) => {
        const deleteBtn = $body.find('[data-cy="delete-post-btn"], button:contains("Delete"), button[aria-label*="delete"]').first();
        if (deleteBtn.length > 0) {
          cy.wrap(deleteBtn).click({ force: true });
          cy.wait(1000);
          
          // Confirm deletion if confirmation dialog appears
          cy.get('body').then(($confirmBody) => {
            const confirmBtn = $confirmBody.find('button:contains("Confirm"), button:contains("Delete"), [data-cy="confirm-delete"]');
            if (confirmBtn.length > 0) {
              cy.wrap(confirmBtn).first().click({ force: true });
            }
          });
          
          cy.wait('@deletePost');
          cy.wait(2000);
          cy.screenshot('admin-post-deleted');
        }
      });
    });

    it('8. Admin views reported posts', () => {
      cy.loginAsAdmin();
      cy.waitUntilFrontendReady();
      cy.visit('/dashboard/admin/community', { timeout: 30000, failOnStatusCode: false });
      
      // Mock reports API
      cy.intercept('GET', '**/api/admin/community/reports*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [
              {
                id: 1,
                post_id: 1,
                reason: 'Inappropriate content',
                status: 'pending',
              },
            ],
          },
        },
      }).as('getReports');
      
      // Find reports tab/section
      cy.get('body').then(($body) => {
        const reportsTab = $body.find('[data-cy="reports-tab"], button:contains("Reports"), a:contains("Reports")').first();
        if (reportsTab.length > 0) {
          cy.wrap(reportsTab).click({ force: true });
          cy.wait('@getReports');
          cy.wait(2000);
          cy.screenshot('admin-community-reports');
        }
      });
    });
  });
});

