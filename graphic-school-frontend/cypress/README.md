# Cypress E2E Testing - Quick Start

## ⚠️ Important: Port Configuration

**Vite may use different ports if the default is busy!**

When you run `npm run dev`, check the output:
```
➜  Local:   http://localhost:5175/  ← Note the port number!
```

**Update `cypress.config.js` to match:**
```javascript
baseUrl: 'http://localhost:5175',  // Use the port Vite shows
```

## Quick Start

### 1. Start Servers

**Terminal 1 - Frontend:**
```powershell
cd graphic-school-frontend
npm run dev
# Note the port number shown (e.g., 5175)
```

**Terminal 2 - Backend:**
```powershell
# If using Valet/Homestead, API should be at http://graphic-school.test/api
# If using php artisan serve:
cd graphic-school-api
php artisan serve --host=graphic-school.test
# Or use default (will be accessible via graphic-school.test if configured)
```

**Terminal 3 - Cypress:**
```powershell
cd graphic-school-frontend
npm run cypress:open
```

### 2. Run Health Check First

In Cypress UI, select `health_check.cy.js` to verify:
- ✅ Frontend is accessible
- ✅ Login page loads
- ✅ API connectivity

### 3. Run Full Tests

After health check passes:
- `admin_spec.cy.js` - Admin tests
- `instructor_spec.cy.js` - Instructor tests
- `student_spec.cy.js` - Student tests
- `full_flow.cy.js` - Complete flow

## Common Issues

### Port Mismatch
**Problem:** Blank page in Cypress  
**Solution:** Update `baseUrl` in `cypress.config.js` to match Vite's port

### Frontend Not Running
**Problem:** Connection refused  
**Solution:** Run `npm run dev` in frontend directory

### Backend Not Running
**Problem:** API calls fail  
**Solution:** 
- Verify API is accessible at `http://graphic-school.test/api/health`
- If using Valet: Ensure site is linked
- If using `php artisan serve`: Start it in API directory

## Configuration Files

- `cypress.config.js` - Main configuration (update baseUrl here!)
- `cypress/support/commands.js` - Custom commands
- `cypress/fixtures/users.json` - Test user credentials
- `cypress.env.json` - Environment variables (optional)

## Test Files

- `health_check.cy.js` - **Run this first!**
- `login_debug.cy.js` - Debug login page structure
- `admin_spec.cy.js` - Admin E2E tests
- `instructor_spec.cy.js` - Instructor E2E tests
- `student_spec.cy.js` - Student E2E tests
- `full_flow.cy.js` - Complete multi-user flow

## Need Help?

See `CYPRESS_TROUBLESHOOTING.md` for detailed troubleshooting guide.

