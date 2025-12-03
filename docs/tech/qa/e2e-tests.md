# E2E Testing Guide

## Overview

End-to-end (E2E) tests verify complete user workflows using Cypress. These tests simulate real user interactions across the entire application.

## E2E Test Structure

### Test Organization

Tests are organized by user role:
- `admin_flow.cy.js` - Admin user workflows
- `instructor_flow.cy.js` - Instructor workflows
- `student_flow.cy.js` - Student workflows
- `full_flow.cy.js` - Complete system workflows

## Cypress Configuration

### Configuration File

`cypress.config.js`:
```javascript
export default defineConfig({
  e2e: {
    baseUrl: 'http://localhost:5173',
    viewportWidth: 1280,
    viewportHeight: 720,
    video: true,
    screenshotOnRunFailure: true,
    env: {
      ALLOW_404: 'false',
      ALLOW_500: 'false',
    },
  },
});
```

### Custom Commands

#### Login Commands

```javascript
// cypress/support/commands.js
Cypress.Commands.add('loginAsAdmin', () => {
  cy.fixture('users').then((users) => {
    cy.visit('/login');
    cy.get('[data-e2e="login-email"]').type(users.admin.email);
    cy.get('[data-e2e="login-password"]').type(users.admin.password);
    cy.get('[data-e2e="login-submit"]').click();
    cy.url().should('include', '/dashboard/admin');
  });
});
```

#### Navigation Commands

```javascript
Cypress.Commands.add('navigateTo', (section) => {
  cy.get(`[data-cy="nav-${section}"]`).click();
});
```

## Test Flows

### Admin Flow

#### Test Cases

1. **Admin Login**
   - Navigate to login
   - Enter credentials
   - Verify dashboard access

2. **Course Management**
   - Create course
   - Edit course
   - Delete course

3. **Group Management**
   - Create group
   - Assign instructor
   - Manage students

4. **Enrollment Management**
   - View enrollments
   - Approve enrollment
   - Reject enrollment

5. **Attendance Overview**
   - View attendance
   - Filter attendance
   - Export reports

### Instructor Flow

#### Test Cases

1. **Instructor Login**
   - Navigate to login
   - Enter credentials
   - Verify dashboard access

2. **My Groups**
   - View assigned groups
   - View group details
   - Navigate to sessions

3. **Take Attendance**
   - Select session
   - Mark attendance
   - Save attendance

4. **View Students**
   - View group students
   - View student profiles
   - Check attendance

### Student Flow

#### Test Cases

1. **Student Login**
   - Navigate to login
   - Enter credentials
   - Verify dashboard access

2. **My Courses**
   - View enrolled courses
   - View course details
   - Check enrollment status

3. **My Group**
   - View assigned group
   - View group schedule
   - View instructor

4. **Attendance History**
   - View attendance records
   - Filter by course
   - Check statistics

## Error Detection

### 404/500 Detection

Cypress intercepts all requests:
```javascript
// cypress/support/errorInterceptor.js
cy.intercept('GET', '**/*', (req) => {
  req.continue((res) => {
    if (res.statusCode === 404 || res.statusCode === 500) {
      // Log and fail test
    }
  });
});
```

### Missing Routes

Tests detect missing routes:
- Log missing routes
- Report in test summary
- Generate route report

### Missing i18n Keys

Tests detect missing translations:
- Log missing keys
- Report in test summary
- Generate translation report

## Test Data

### Fixtures

Test data in `cypress/fixtures/`:
- `users.json` - Test users
- `courses.json` - Test courses
- `groups.json` - Test groups

### Example Fixture

```json
{
  "admin": {
    "email": "admin@example.com",
    "password": "password"
  },
  "instructor": {
    "email": "instructor@example.com",
    "password": "password"
  },
  "student": {
    "email": "student@example.com",
    "password": "password"
  }
}
```

## Running Tests

### Development

```bash
# Open Cypress UI
npm run cypress:open

# Run tests headless
npm run cypress:run
```

### CI/CD

```bash
# Run in CI
npm run cypress:run:headless
```

## Test Reports

### HTML Reports

Generate HTML reports:
```bash
npm run cypress:run -- --reporter html
```

### JSON Reports

Generate JSON reports:
```bash
npm run cypress:run -- --reporter json
```

### E2E Report

Generate comprehensive report:
```bash
npm run e2e:report
```

## Best Practices

1. **Use Data Attributes**
   - `data-e2e` for E2E tests
   - `data-cy` for Cypress
   - Stable selectors

2. **Page Object Model**
   - Reusable page objects
   - Centralized selectors
   - Easy maintenance

3. **Test Isolation**
   - Each test independent
   - Clean state between tests
   - No test dependencies

4. **Error Handling**
   - Catch and report errors
   - Meaningful error messages
   - Debug information

5. **Performance**
   - Optimize test speed
   - Parallel execution
   - Efficient selectors

## Conclusion

E2E tests provide:
- Complete workflow verification
- Real user simulation
- Error detection
- Route validation
- Translation validation

Comprehensive E2E testing ensures the application works correctly from end to end.

