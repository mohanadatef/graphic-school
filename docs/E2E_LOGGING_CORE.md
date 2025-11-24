# 📊 E2E Core Logging System

## Overview

The E2E Core Logging System provides comprehensive observability for Cypress E2E tests, automatically logging:
- Test results and status
- 404 routes and navigation issues
- Missing i18n translation keys

All logs are stored in structured JSON/JSONL formats that can be automatically processed by tools like Cursor HQ for auto-fixing.

---

## 📁 Log Storage Location

All logs are stored in:
```
cypress/e2e-logs/
├── spec-results/          # Individual spec results
│   ├── admin_spec.json
│   ├── student_spec.json
│   └── ...
├── summary.json           # Run summary
├── routes.log             # Route visits (JSONL)
└── i18n-missing.log       # Missing i18n keys (JSONL)
```

---

## 📋 Log Formats

### 1. Spec Results (`spec-results/<specName>.json`)

**Format**: JSON

**Structure**:
```json
{
  "specFile": "cypress/e2e/admin_spec.cy.js",
  "startedAt": "2025-01-27T10:00:00.000Z",
  "endedAt": "2025-01-27T10:05:00.000Z",
  "status": "passed" | "failed",
  "tests": [
    {
      "title": "Admin login flow",
      "fullTitle": "Admin E2E Tests > Admin login flow",
      "state": "passed" | "failed" | "pending",
      "duration": 1234,
      "error": {
        "message": "Error message",
        "stack": "Error stack trace"
      } | null,
      "screenshots": ["path/to/screenshot.png"],
      "video": "path/to/video.mp4" | null
    }
  ]
}
```

**Fields**:
- `specFile`: Relative path to spec file
- `startedAt`: ISO timestamp when spec started
- `endedAt`: ISO timestamp when spec ended
- `status`: Overall spec status
- `tests`: Array of individual test results
  - `title`: Test title
  - `fullTitle`: Full test path
  - `state`: Test state
  - `duration`: Test duration in milliseconds
  - `error`: Error object if failed, null otherwise
  - `screenshots`: Array of screenshot paths
  - `video`: Video path if available

---

### 2. Run Summary (`summary.json`)

**Format**: JSON

**Structure**:
```json
{
  "startedAt": "2025-01-27T10:00:00.000Z",
  "endedAt": "2025-01-27T10:30:00.000Z",
  "totalSpecs": 6,
  "passedSpecs": 5,
  "failedSpecs": 1,
  "specs": [
    "cypress/e2e/admin_spec.cy.js",
    "cypress/e2e/student_spec.cy.js",
    ...
  ]
}
```

**Fields**:
- `startedAt`: ISO timestamp when run started
- `endedAt`: ISO timestamp when run ended
- `totalSpecs`: Total number of specs run
- `passedSpecs`: Number of passed specs
- `failedSpecs`: Number of failed specs
- `specs`: Array of spec file paths

---

### 3. Route Visits (`routes.log`)

**Format**: JSONL (one JSON object per line)

**Structure** (per line):
```json
{"route":"/dashboard/admin","status":200,"source":"e2e","spec":"admin_spec.cy.js","test":"Admin login flow","timestamp":"2025-01-27T10:00:00.000Z"}
{"route":"/unknown-page","status":404,"source":"e2e","spec":"full_flow.cy.js","test":"Complete flow","timestamp":"2025-01-27T10:01:00.000Z"}
```

**Fields**:
- `route`: The route path visited
- `status`: HTTP status code (200, 404, etc.)
- `source`: Always "e2e"
- `spec`: Spec file name
- `test`: Test title
- `timestamp`: ISO timestamp

**404 Detection**:
- Routes are detected as 404 if the page contains `data-e2e-not-found="true"` attribute
- This attribute is added to `NotFound.vue` component

---

### 4. Missing i18n Keys (`i18n-missing.log`)

**Format**: JSONL (one JSON object per line)

**Structure** (per line):
```json
{"key":"common.newKey","locale":"en","spec":"admin_spec.cy.js","test":"Admin login flow","timestamp":"2025-01-27T10:00:00.000Z"}
{"key":"errors.customError","locale":"ar","spec":"student_spec.cy.js","test":"Student enrollment","timestamp":"2025-01-27T10:01:00.000Z"}
```

**Fields**:
- `key`: The missing translation key
- `locale`: The locale (en, ar, etc.)
- `spec`: Spec file name
- `test`: Test title
- `timestamp`: ISO timestamp

**Detection**:
- Missing keys are detected via Vue i18n's `missing` handler
- Only logged in test/E2E mode
- Stored in `window.__MISSING_I18N__` array during test execution
- Logged to file after each test

---

## 🔧 How It Works

### Spec Results Logging

1. **After each spec** (`after:spec` event):
   - Collects test results
   - Extracts test details (title, state, duration, errors)
   - Writes to `spec-results/<specName>.json`

2. **After entire run** (`after:run` event):
   - Aggregates all spec results
   - Calculates totals (passed/failed)
   - Writes to `summary.json`

### Route Logging

1. **cy.visit override**:
   - Wraps original `cy.visit` command
   - After page load, checks for 404 marker
   - Logs route with status via Cypress task

2. **404 Detection**:
   - Checks for `[data-e2e-not-found="true"]` attribute
   - If found, logs status as 404
   - Otherwise, logs status as 200

### i18n Missing Key Logging

1. **Vue i18n missing handler**:
   - Intercepts missing translation keys
   - Stores in `window.__MISSING_I18N__` array
   - Only active in test/E2E mode

2. **After each test**:
   - Reads `window.__MISSING_I18N__` array
   - Logs each missing key to file
   - Clears array for next test

---

## 🛠️ Auto-Fix Integration

### For Cursor HQ / Auto-Fix Tools

The logs are structured to enable automatic fixes:

#### 1. Fix 404 Routes

**Input**: `routes.log`
```json
{"route":"/dashboard/custom","status":404,"spec":"test.cy.js","test":"Test custom page"}
```

**Action**:
- Generate Vue page at `src/views/auto-generated/dashboard-custom.vue`
- Add route to `src/router/index.js`
- Generate E2E test if missing

#### 2. Fix Missing i18n Keys

**Input**: `i18n-missing.log`
```json
{"key":"common.newKey","locale":"en","spec":"test.cy.js","test":"Test"}
```

**Action**:
- Add key to `src/i18n/locales/en.json`
- Add key to `src/i18n/locales/ar.json` (with placeholder)
- Or use auto-translation service

#### 3. Fix Failing Tests

**Input**: `spec-results/<spec>.json`
```json
{
  "status": "failed",
  "tests": [{
    "title": "Admin login",
    "error": {"message": "Element not found"}
  }]
}
```

**Action**:
- Analyze error messages
- Update selectors in test file
- Fix component structure if needed

---

## 📊 Example Usage

### Reading Logs Programmatically

```javascript
const fs = require('fs');
const path = require('path');

// Read summary
const summary = JSON.parse(
  fs.readFileSync('cypress/e2e-logs/summary.json', 'utf-8')
);
console.log(`Passed: ${summary.passedSpecs}/${summary.totalSpecs}`);

// Read routes log (JSONL)
const routesLog = fs.readFileSync('cypress/e2e-logs/routes.log', 'utf-8');
const routes = routesLog
  .split('\n')
  .filter(Boolean)
  .map(line => JSON.parse(line));

const four04Routes = routes.filter(r => r.status === 404);
console.log(`404 routes found: ${four04Routes.length}`);

// Read i18n missing log (JSONL)
const i18nLog = fs.readFileSync('cypress/e2e-logs/i18n-missing.log', 'utf-8');
const missingKeys = i18nLog
  .split('\n')
  .filter(Boolean)
  .map(line => JSON.parse(line));

const uniqueKeys = [...new Set(missingKeys.map(k => k.key))];
console.log(`Unique missing keys: ${uniqueKeys.length}`);
```

---

## ⚙️ Configuration

### Enable/Disable Logging

Logging is automatically enabled in:
- Cypress test runs (`npm run cypress:run`)
- Development mode with `VITE_E2E=true`

To disable, remove the event handlers from `cypress.config.js`.

### Environment Variables

- `VITE_E2E=true`: Enables i18n missing key logging in browser

---

## 🔒 Safety & Production

- **All logging is wrapped in try/catch**: Logging failures won't break tests
- **Only active in test mode**: No logging in production builds
- **No performance impact**: Logging is asynchronous and non-blocking
- **Directory auto-creation**: Logs directory is created automatically

---

## 📝 Log Rotation

Logs are **not automatically rotated**. To manage disk space:

1. **Manual cleanup**: Delete old log files
2. **Git ignore**: Add `cypress/e2e-logs/` to `.gitignore` (optional)
3. **CI cleanup**: Clear logs before each CI run

---

## 🎯 Next Steps for Auto-Fix

1. **Parse logs**: Read JSON/JSONL files
2. **Identify issues**: Extract 404 routes, missing keys, failing tests
3. **Generate fixes**: Use self-healing system or manual fixes
4. **Apply fixes**: Update code, routes, translations
5. **Re-run tests**: Verify fixes work

---

**Last Updated**: 2025-01-27  
**Version**: 1.0.0  
**Status**: ✅ Active

