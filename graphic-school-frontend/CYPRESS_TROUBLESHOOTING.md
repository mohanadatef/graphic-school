# Cypress Troubleshooting Guide

## Blank Page Issue

If you see a blank page (`about:blank`) in Cypress, follow these steps:

### 1. Verify Frontend Server is Running

**Check if Vite dev server is running:**
```powershell
# In graphic-school-frontend directory
npm run dev
```

You should see:
```
  VITE v7.x.x  ready in xxx ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
```

**If not running, start it:**
```powershell
cd graphic-school-frontend
npm run dev
```

### 2. Verify Backend API is Running

**Check if Laravel server is accessible:**
```powershell
# Verify API is accessible
curl http://graphic-school.test/api/health
```

**If using Laravel Valet/Homestead:**
- API should be accessible at `http://graphic-school.test/api`
- No need to run `php artisan serve` if using Valet

**If using `php artisan serve`:**
```powershell
cd graphic-school-api
php artisan serve --host=graphic-school.test
```

### 3. Check Cypress Configuration

**Verify `baseUrl` in `cypress.config.js`:**
```javascript
baseUrl: 'http://localhost:5173',
```

**Important:** Vite may use a different port if 5173 is busy. Check the terminal output:
```
➜  Local:   http://localhost:5175/  ← Use this port!
```

**Update `cypress.config.js` to match the actual port:**
```javascript
baseUrl: 'http://localhost:5175',  // Match the port Vite shows
```

**Or use environment variable for flexibility:**
```javascript
baseUrl: process.env.CYPRESS_BASE_URL || 'http://localhost:5173',
```

Then set in `cypress.env.json`:
```json
{
  "baseUrl": "http://localhost:5175"
}
```

### 4. Run Health Check Test First

Before running full E2E tests, run the health check:

```powershell
cd graphic-school-frontend
npm run cypress:open
```

Then select `health_check.cy.js` to verify:
- Frontend is accessible
- Login page loads
- API connectivity works

### 5. Check Browser Console

In Cypress UI:
1. Open DevTools (F12)
2. Check Console tab for errors
3. Check Network tab for failed requests

### 6. Common Issues & Solutions

#### Issue: "about:blank" page
**Cause:** Frontend server not running or wrong baseUrl
**Solution:** 
- Start `npm run dev` in frontend directory
- Verify baseUrl matches the running port

#### Issue: "Failed to fetch" errors
**Cause:** Backend API not accessible
**Solution:**
- Verify API is accessible at `http://graphic-school.test/api/health`
- If using Valet: Ensure site is linked (`valet link graphic-school`)
- If using `php artisan serve`: Start it in API directory
- Check `VITE_API_URL` in `.env` or environment variables

#### Issue: "Cannot find element" errors
**Cause:** Page not fully loaded
**Solution:**
- Increase `defaultCommandTimeout` in `cypress.config.js`
- Add `cy.wait()` after `cy.visit()`

#### Issue: Vue app not mounting
**Cause:** JavaScript errors preventing Vue from mounting
**Solution:**
- Check browser console for errors
- Verify all dependencies are installed (`npm install`)
- Check if API calls are failing

### 7. Manual Verification

**Test in regular browser:**
1. Open `http://localhost:5173` in Chrome/Firefox
2. Verify page loads correctly
3. Try navigating to `/login`
4. Check browser console for errors

If it works in regular browser but not in Cypress:
- Check Cypress browser settings
- Try different browser in Cypress
- Clear Cypress cache: `npx cypress cache clear`

### 8. Environment Variables

**Check `.env` or environment variables:**
```powershell
# Frontend might need:
VITE_API_URL=http://graphic-school.test/api
```

**Create `.env.local` if needed:**
```env
VITE_API_URL=http://graphic-school.test/api
```

**Note:** The default in `client.js` is already `http://graphic-school.test/api`, so you may not need to set this.

### 9. Reset Everything

If nothing works, try:
```powershell
# Stop all servers
# Clear node_modules and reinstall
cd graphic-school-frontend
rm -rf node_modules
npm install

# Clear Cypress cache
npx cypress cache clear

# Restart servers
npm run dev  # Terminal 1
cd ../graphic-school-api
php artisan serve  # Terminal 2

# Then run Cypress
cd ../graphic-school-frontend
npm run cypress:open
```

### 10. Quick Test Commands

**Test if frontend is accessible:**
```powershell
curl http://localhost:5173
```

**Test if API is accessible:**
```powershell
curl http://graphic-school.test/api/health
```

Both should return content, not errors.

## Still Having Issues?

1. **Check Cypress logs:** Look at the terminal output when running tests
2. **Check browser console:** Open DevTools in Cypress UI
3. **Check network tab:** See if requests are failing
4. **Run health check test:** `health_check.cy.js` will diagnose issues
5. **Check file paths:** Ensure all files exist and are in correct locations

## Success Indicators

✅ Frontend server shows "ready" message  
✅ Backend server shows "Server running" message  
✅ Health check test passes  
✅ Login page loads in Cypress  
✅ No console errors in browser DevTools  

