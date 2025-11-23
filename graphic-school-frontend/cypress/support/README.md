# Cypress E2E Testing - Troubleshooting Guide

## Common Issues & Solutions

### 1. Login Command Errors

**Error:** `Cannot read property 'email' of undefined`

**Solution:** Ensure `cypress/fixtures/users.json` exists and has the correct structure.

**Error:** `Timed out retrying: Expected to find element: #email`

**Solution:** 
- Ensure frontend is running (`npm run dev`)
- Check that login page is accessible at `/login`
- Verify the input field has `id="email"`

### 2. Navigation Issues

**Error:** `Cannot find navigation element`

**Solution:**
- The `navigateTo` command uses flexible selectors
- If navigation fails, try direct URL navigation:
  ```javascript
  cy.visit('/dashboard/admin/programs');
  ```

### 3. Form Submission Issues

**Error:** `Button is disabled`

**Solution:**
- Wait for form validation to complete
- Check that all required fields are filled
- Ensure button is not disabled by loading state

### 4. API Timeout Issues

**Error:** `Request timed out`

**Solution:**
- Ensure backend is running (`php artisan serve`)
- Check API base URL in `cypress.config.js`
- Increase timeout in test:
  ```javascript
  cy.wait(5000); // Wait longer
  ```

### 5. Screenshot/Video Issues

**Error:** Screenshots not saving

**Solution:**
- Check `cypress/screenshots` folder exists
- Verify write permissions
- Check `cypress.config.js` has correct paths

## Debugging Tips

### 1. Use Cypress UI
```bash
npm run cypress:open
```
This allows you to see what's happening in real-time.

### 2. Add Debug Statements
```javascript
cy.get('#email').then(($el) => {
  console.log('Email field:', $el);
});
```

### 3. Check Network Requests
In Cypress UI, open DevTools and check Network tab to see API calls.

### 4. Verify User Credentials
Ensure users in `cypress/fixtures/users.json` match seeded users:
```bash
cd graphic-school-api
php artisan tinker
>>> User::where('email', 'admin@example.com')->first();
```

## Best Practices

1. **Always wait for elements:**
   ```javascript
   cy.get('#email', { timeout: 10000 }).should('be.visible');
   ```

2. **Use data-cy attributes:**
   Add `data-cy="login-email"` to make tests more stable.

3. **Handle async operations:**
   ```javascript
   cy.intercept('POST', '/api/login').as('login');
   cy.get('button[type="submit"]').click();
   cy.wait('@login');
   ```

4. **Clean up between tests:**
   ```javascript
   beforeEach(() => {
     cy.clearLocalStorage();
     cy.clearCookies();
   });
   ```

## User Credentials

Ensure these users exist in your database:

- **Admin:** admin@example.com / password
- **Instructor:** instructor@example.com / password
- **Student:** student@example.com / password

If users don't exist, run:
```bash
cd graphic-school-api
php artisan db:seed
```

