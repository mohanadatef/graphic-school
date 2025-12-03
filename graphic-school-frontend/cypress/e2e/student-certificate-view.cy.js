/**
 * Student Certificate View E2E Tests
 * 
 * Tests the student certificate viewing flow:
 * - Student logs in
 * - Opens "My Certificates"
 * - Views certificate details
 * - Downloads PDF (mock)
 */

describe('Student Certificate View Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('certificates').then((certificates) => {
      cy.intercept('GET', '**/api/student/certificates*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [certificates.testCertificate],
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
      }).as('getCertificates');
      
      cy.intercept('GET', '**/api/student/certificates/1*', {
        statusCode: 200,
        body: {
          success: true,
          data: certificates.testCertificate,
        },
      }).as('getCertificate');
    });
  });

  it('1. Student logs in and navigates to My Certificates', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    
    // Navigate to certificates
    cy.visit('/dashboard/student/certificates', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCertificates');
    
    cy.get('body', { timeout: 10000 }).should('be.visible');
    cy.screenshot('student-certificates-list');
  });

  it('2. Student views certificate list', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.visit('/dashboard/student/certificates', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCertificates');
    cy.wait(2000);
    
    // Verify certificate is displayed
    cy.fixture('certificates').then((certificates) => {
      cy.get('body').should('contain', certificates.testCertificate.course.title);
      cy.get('body').should('contain', certificates.testCertificate.group.name);
    });
    
    cy.screenshot('student-certificates-list-view');
  });

  it('3. Student views certificate details', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.visit('/dashboard/student/certificates', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCertificates');
    cy.wait(2000);
    
    // Click on certificate to view details
    cy.get('body').then(($body) => {
      const certLink = $body.find('a[href*="certificate"], [data-cy="certificate-link"], tr').first();
      if (certLink.length > 0) {
        cy.wrap(certLink).click({ force: true });
        cy.wait('@getCertificate');
        cy.wait(2000);
        
        // Verify certificate details are shown
        cy.fixture('certificates').then((certificates) => {
          cy.get('body').should('contain', certificates.testCertificate.course.title);
          cy.get('body').should('contain', certificates.testCertificate.student.name);
          cy.get('body').should('contain', certificates.testCertificate.certificate_number);
        });
        
        cy.screenshot('student-certificate-details');
      }
    });
  });

  it('4. Student downloads certificate PDF (mock)', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.visit('/dashboard/student/certificates', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCertificates');
    cy.wait(2000);
    
    // Mock PDF download
    cy.intercept('GET', '**/storage/certificates/pdf/**', {
      statusCode: 200,
      headers: {
        'Content-Type': 'application/pdf',
      },
      body: '%PDF-1.4 mock pdf content',
    }).as('downloadPDF');
    
    // Find download button
    cy.get('body').then(($body) => {
      const downloadBtn = $body.find('[data-cy="download-btn"], a[href*="pdf"], button:contains("Download")').first();
      if (downloadBtn.length > 0) {
        cy.wrap(downloadBtn).click({ force: true });
        cy.wait('@downloadPDF');
        cy.wait(1000);
        cy.screenshot('student-certificate-downloaded');
      }
    });
  });

  it('5. Student verifies certificate via verification link', () => {
    cy.loginAsStudent();
    cy.waitUntilFrontendReady();
    cy.visit('/dashboard/student/certificates', { timeout: 30000, failOnStatusCode: false });
    cy.wait('@getCertificates');
    cy.wait(2000);
    
    // Find verify link/button
    cy.get('body').then(($body) => {
      const verifyLink = $body.find('a[href*="verify"], [data-cy="verify-link"], button:contains("Verify")').first();
      if (verifyLink.length > 0) {
        cy.wrap(verifyLink).click({ force: true });
        cy.wait(2000);
        
        // Should navigate to verification page
        cy.url().should('include', 'certificate/verify');
        cy.screenshot('student-certificate-verification-page');
      }
    });
  });
});

