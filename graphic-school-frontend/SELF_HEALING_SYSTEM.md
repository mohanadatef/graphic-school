# 🔧 Self-Healing System Documentation

## Overview

The Self-Healing System automatically generates missing pages, translations, and E2E tests to ensure **ZERO 404 pages**, **ZERO missing translations**, and **ZERO missing routes**.

## Features

### 1. Auto Page Generator
- **When**: Vue Router hits 404 during navigation
- **Action**: Generates Vue page at `src/views/auto-generated/<route>.vue`
- **Result**: Page is created with i18n placeholders and automatically added to router

### 2. Missing Translation Auto Fixer
- **When**: Vue i18n encounters missing translation key
- **Action**: Adds key to `src/i18n/auto/auto-{locale}.json`
- **Result**: Translation is available immediately (merged with existing translations)

### 3. E2E Auto Test Generator
- **When**: Cypress visits a route without a test
- **Action**: Creates `cypress/e2e/auto/<route>.cy.js`
- **Result**: Basic test ensures page loads without errors

### 4. Cursor Integration Logs
- **Location**: `/self-heal-logs/`
- **Format**: JSON files with timestamp and action details
- **Purpose**: Track all auto-generated items

## Architecture

### Browser-Side (Client)
- **File**: `src/utils/selfHealBrowser.js`
- **Purpose**: Detects issues and logs them
- **Actions**: 
  - Detects 404 routes
  - Detects missing translations
  - Logs to localStorage and console

### Node.js Side (Server/Build)
- **File**: `scripts/selfHealNode.js`
- **Purpose**: Generates files (pages, translations, tests)
- **Actions**:
  - Creates Vue components
  - Adds routes to router
  - Generates Cypress tests
  - Updates translation files

### Router Integration
- **File**: `src/router/selfHealRouter.js`
- **Purpose**: Hooks into Vue Router to detect 404s
- **Integration**: Automatically loaded in `main.js`

### Cypress Integration
- **File**: `cypress/support/selfHeal.js`
- **Purpose**: Auto-generates tests for visited routes
- **Integration**: Loaded via `cypress/support/e2e.js`

## File Structure

```
graphic-school-frontend/
├── src/
│   ├── utils/
│   │   ├── selfHeal.js          # Node.js utilities (for build-time)
│   │   └── selfHealBrowser.js   # Browser-side detection
│   ├── router/
│   │   └── selfHealRouter.js    # Router hooks
│   ├── views/
│   │   └── auto-generated/      # Auto-generated pages
│   └── i18n/
│       └── auto/                # Auto-generated translations
│           ├── auto-en.json
│           └── auto-ar.json
├── cypress/
│   ├── e2e/
│   │   └── auto/                # Auto-generated tests
│   └── support/
│       └── selfHeal.js          # Cypress hooks
├── scripts/
│   └── selfHealNode.js          # Node.js file generator
└── self-heal-logs/              # Logs directory
```

## Usage

### Automatic (Default)
The system works automatically. No configuration needed.

### Manual Generation

#### Generate Page
```bash
node scripts/selfHealNode.js generate-page /dashboard/custom-page
```

#### Add Translation
```bash
node scripts/selfHealNode.js add-translation en common.newKey "New Value"
```

#### Generate Test
```bash
node scripts/selfHealNode.js generate-test /dashboard/custom-page
```

## How It Works

### 1. 404 Detection Flow

```
User navigates to /unknown-route
    ↓
Router doesn't find route
    ↓
selfHealRouter.js detects 404
    ↓
handle404Route() called
    ↓
┌─────────────────────────────┐
│ 1. Generate Vue component    │
│ 2. Add route to router       │
│ 3. Generate E2E test         │
│ 4. Add default translations  │
│ 5. Log action                │
└─────────────────────────────┘
    ↓
Page available on next navigation
```

### 2. Missing Translation Flow

```
Component uses $t('missing.key')
    ↓
Vue i18n missing handler triggered
    ↓
addTranslation() called
    ↓
┌─────────────────────────────┐
│ 1. Add key to auto-{locale} │
│ 2. Merge with existing       │
│ 3. Reload translations       │
│ 4. Log action                │
└─────────────────────────────┘
    ↓
Translation available immediately
```

### 3. E2E Test Generation Flow

```
Cypress visits /new-route
    ↓
selfHeal.js checks if test exists
    ↓
Test not found
    ↓
generateE2ETest() called
    ↓
┌─────────────────────────────┐
│ 1. Create test file          │
│ 2. Add basic assertions      │
│ 3. Log action                │
└─────────────────────────────┘
    ↓
Test available for next run
```

## Generated Files

### Auto-Generated Page Template

```vue
<template>
  <div class="min-h-screen p-8">
    <!-- Auto-generated page content -->
    <h1>{{ $t('auto.page.title') }}</h1>
  </div>
</template>
```

### Auto-Generated Test Template

```javascript
describe('Auto-Generated: Route Name', () => {
  it('Should load the page without errors', () => {
    cy.visit('/route');
    cy.get('body').should('be.visible');
  });
});
```

## Logs

All actions are logged to `self-heal-logs/self-heal-YYYY-MM-DD.json`:

```json
[
  {
    "timestamp": "2025-01-27T10:00:00.000Z",
    "action": "PAGE_GENERATED",
    "details": {
      "routePath": "/dashboard/custom",
      "filePath": "src/views/auto-generated/dashboard-custom.vue"
    }
  }
]
```

## Configuration

### Enable/Disable

Edit `src/main.js`:

```javascript
// Disable self-healing
// Comment out this line:
// setupSelfHealingRouter(router);
```

### Custom Templates

Edit `scripts/selfHealNode.js` to customize:
- Page templates
- Test templates
- Translation defaults

## Best Practices

1. **Review Auto-Generated Files**: Check `auto-generated/` and `auto/` directories regularly
2. **Customize Templates**: Update generated pages with actual content
3. **Monitor Logs**: Review `self-heal-logs/` to track system activity
4. **Test Coverage**: Run Cypress to ensure auto-generated tests pass

## Troubleshooting

### Pages Not Generating
- Check browser console for errors
- Verify `self-heal-logs/` directory exists
- Check file permissions

### Translations Not Appearing
- Clear browser cache
- Check `src/i18n/auto/` files exist
- Verify i18n is loading auto translations

### Tests Not Generating
- Check Cypress is running
- Verify `cypress/e2e/auto/` directory exists
- Check Cypress task configuration

## Integration with Development

The system is designed to work seamlessly:
- **Development**: Auto-generates during `npm run dev`
- **Testing**: Auto-generates during `npm run cypress:run`
- **Build**: Auto-generated files are included in build

## Future Enhancements

- [ ] API endpoint for remote file generation
- [ ] Real-time file watching
- [ ] Custom template selection
- [ ] Batch generation from route list
- [ ] Integration with design system

---

**Status**: ✅ Active  
**Version**: 1.0.0  
**Last Updated**: 2025-01-27

