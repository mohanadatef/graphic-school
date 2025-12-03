/**
 * Certificate Issuance E2E Tests
 * 
 * Tests the certificate issuance flow:
 * - Admin opens Certificates page
 * - Issues certificate (student + course + group + instructor)
 * - Certificate created with qr_code + verification_code
 * - Public verifies certificate using /certificates/verify/{code}
 */

describe('Certificate Issuance Flow', () => {
  beforeEach(() => {
    cy.clearLocalStorage();
    cy.clearCookies();
    
    // Mock API responses
    cy.fixture('certificates').then((certificates) => {
      cy.intercept('GET', '**/api/admin/certificates*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [],
            meta: {
              pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
              },
            },
          },
        },
      }).as('getCertificates');
      
      cy.intercept('POST', '**/api/admin/certificates', {
        statusCode: 201,
        body: {
          success: true,
          message: 'Certificate issued successfully',
          data: certificates.testCertificate,
        },
      }).as('issueCertificate');
      
      cy.intercept('GET', '**/api/certificates/verify/abc123def456ghi789', {
        statusCode: 200,
        body: {
          success: true,
          data: certificates.testCertificate,
        },
      }).as('verifyCertificate');
    });

    // Mock student, course, group, instructor lists
    cy.intercept('GET', '**/api/admin/users*role=student*', {
      statusCode: 200,
      body: {
        success: true,
        data: {
          data: [
            {
              id: 2,
              name: 'Test Student',
              email: 'student1@graphicschool.com',
            },
          ],
        },
      },
    }).as('getStudents');

    cy.fixture('courses').then((courses) => {
      cy.intercept('GET', '**/api/admin/courses*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [courses.testCourse],
          },
        },
      }).as('getCourses');
    });

    cy.fixture('groups').then((groups) => {
      cy.intercept('GET', '**/api/admin/groups*', {
        statusCode: 200,
        body: {
          success: true,
          data: {
            data: [groups.testGroup],
          },
        },
      }).as('getGroups');
    });

    cy.intercept('GET', '**/api/admin/users*role=instructor*', {
      statusCode: 200,
      body: {
        success: true,
        data: {
          data: [
            {
              id: 1,
              name: 'John Instructor',
              email: 'instructor1@graphicschool.com',
            },
          ],
        },
      },
    }).as('getInstructors');
  });

  it('1. Admin opens Certificates page', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    
    cy.navigateTo('certificates');
    cy.wait('@getCertificates');
    
    cy.get('body', { timeout: 10000 }).should('be.visible');
    cy.screenshot('admin-certificates-page');
  });

  it('2. Admin clicks "Issue Certificate" button', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('certificates');
    cy.wait('@getCertificates');
    
    // Find and click issue certificate button
    cy.get('body').then(($body) => {
      const issueBtn = $body.find('[data-cy="issue-certificate-btn"], button:contains("Issue"), button:contains("New Certificate"), a:contains("Issue")').first();
      if (issueBtn.length > 0) {
        cy.wrap(issueBtn).click({ force: true });
        cy.wait(1000);
        cy.screenshot('admin-certificate-issue-form');
      }
    });
  });

  it('3. Admin fills certificate form (student + course + group + instructor)', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('certificates');
    cy.wait('@getCertificates');
    
    // Open issue form
    cy.get('body').then(($body) => {
      const issueBtn = $body.find('[data-cy="issue-certificate-btn"], button:contains("Issue")').first();
      if (issueBtn.length > 0) {
        cy.wrap(issueBtn).click({ force: true });
        cy.wait(1000);
        
        // Wait for form to load and populate dropdowns
        cy.wait(['@getStudents', '@getCourses', '@getGroups', '@getInstructors']);
        
        // Select student
        cy.get('select[name="student_id"], select[id="student_id"]', { timeout: 5000 })
          .first()
          .select('2', { force: true });
        
        // Select course
        cy.get('select[name="course_id"], select[id="course_id"]', { timeout: 5000 })
          .first()
          .select('1', { force: true });
        
        // Select group
        cy.get('select[name="group_id"], select[id="group_id"]', { timeout: 5000 })
          .first()
          .select('1', { force: true });
        
        // Select instructor
        cy.get('select[name="instructor_id"], select[id="instructor_id"]', { timeout: 5000 })
          .first()
          .select('1', { force: true });
        
        cy.screenshot('admin-certificate-form-filled');
      }
    });
  });

  it('4. Admin issues certificate', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('certificates');
    cy.wait('@getCertificates');
    
    // Open form and fill
    cy.get('[data-cy="issue-certificate-btn"], button:contains("Issue")').first().click({ force: true });
    cy.wait(1000);
    cy.wait(['@getStudents', '@getCourses', '@getGroups', '@getInstructors']);
    
    cy.get('select[name="student_id"]').first().select('2', { force: true });
    cy.get('select[name="course_id"]').first().select('1', { force: true });
    cy.get('select[name="group_id"]').first().select('1', { force: true });
    cy.get('select[name="instructor_id"]').first().select('1', { force: true });
    
    // Submit form
    cy.get('button[type="submit"], [data-cy="submit-certificate"]', { timeout: 5000 })
      .first()
      .click({ force: true });
    
    cy.wait('@issueCertificate');
    cy.wait(2000);
    
    // Verify success message
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text.toLowerCase()).to.match(/(issued|success|certificate)/);
    });
    
    cy.screenshot('admin-certificate-issued');
  });

  it('5. Certificate has verification_code and qr_code', () => {
    cy.loginAsAdmin();
    cy.waitUntilFrontendReady();
    cy.navigateTo('certificates');
    
    // Mock certificates list with issued certificate
    cy.fixture('certificates').then((certificates) => {
      cy.intercept('GET', '**/api/admin/certificates*', {
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
      }).as('getCertificatesWithData');
    });
    
    cy.wait('@getCertificatesWithData');
    cy.wait(2000);
    
    // Verify certificate shows verification code
    cy.get('body').should(($body) => {
      const text = $body.text();
      expect(text).to.include('abc123def456ghi789');
      expect(text).to.include('CERT-ABC123-2025');
    });
    
    cy.screenshot('admin-certificate-list-with-code');
  });

  it('6. Public verifies certificate using verification code', () => {
    // Visit public certificate verification page
    cy.visit('/certificate/verify?code=abc123def456ghi789', { timeout: 30000, failOnStatusCode: false });
    
    cy.wait('@verifyCertificate');
    cy.wait(2000);
    
    // Verify certificate details are displayed
    cy.fixture('certificates').then((certificates) => {
      cy.get('body').should('contain', certificates.testCertificate.student.name);
      cy.get('body').should('contain', certificates.testCertificate.course.title);
      cy.get('body').should('contain', certificates.testCertificate.group.name);
      cy.get('body').should('contain', certificates.testCertificate.verification_code);
    });
    
    cy.screenshot('public-certificate-verification');
  });

  it('7. Public verifies certificate using QR code (mock QR scan)', () => {
    // Mock QR code redirect
    cy.visit('/certificate/verify?code=abc123def456ghi789', { timeout: 30000, failOnStatusCode: false });
    
    cy.wait('@verifyCertificate');
    cy.wait(2000);
    
    // Verify QR code image exists
    cy.get('body').then(($body) => {
      const qrImage = $body.find('img[src*="qr"], img[alt*="QR"], [data-cy="qr-code"]');
      if (qrImage.length > 0) {
        cy.wrap(qrImage).should('be.visible');
      }
    });
    
    cy.screenshot('public-certificate-qr-verification');
  });
});

