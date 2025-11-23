# COMPREHENSIVE FIX PLAN - Cypress, i18n, Dashboard

## Status: IN PROGRESS

This document tracks the systematic fixes for:
1. Cypress selector stability
2. Missing i18n keys
3. Dashboard layout restructuring

---

## PROBLEM 1: CYPRESS SELECTORS - FIXED ✅

### Changes Made:

1. **commands.js** - Updated with stable selectors:
   - `navigateTo()` - Now uses data-cy attributes and href-based navigation
   - `logout()` - Uses data-cy and aria-label attributes
   - Added helper commands: `clickCreate()`, `clickSubmit()`, `clickByText()`

2. **Pattern for Test Files:**
   - Replace `:contains()` with data-cy attributes or href-based selectors
   - Use `.first()` when multiple elements match
   - Prefer `[data-cy="..."]` > `[href*="..."]` > `[type="..."]` > text matching

### Remaining Work:

All test files need systematic updates. Pattern:
```javascript
// OLD (brittle):
cy.contains('button', /Create/i).first().click();

// NEW (stable):
cy.clickCreate(); // or
cy.get('[data-cy="create-btn"]').first().click();
```

---

## PROBLEM 2: I18N KEYS - IN PROGRESS

### Strategy:

1. Scan all Vue components for `$t()`, `t()`, `{{ $t(...) }}`
2. Extract all translation keys
3. Compare with ar.json and en.json
4. Add missing keys with placeholder values

### Files to Scan:
- `src/views/dashboard/**/*.vue`
- `src/components/dashboard/**/*.vue`
- `src/views/admin/**/*.vue` (if exists)
- `src/components/layouts/**/*.vue`
- `src/components/common/**/*.vue`

---

## PROBLEM 3: DASHBOARD LAYOUT - PENDING

### New Structure:
1. Header section (welcome, notifications)
2. Quick stats (cards with key metrics)
3. Activity summary (recent activity feed)
4. Community summary (recent posts)
5. Gamification summary (XP, badges, leaderboard)
6. Quick actions (common tasks)
7. Charts and insights (data visualizations)

### Requirements:
- Remove empty/zero widgets
- Group related panels
- Consistent spacing/typography
- Dark mode support
- Responsive design
- Loading skeletons

---

## NEXT STEPS:

1. ✅ Complete Cypress command improvements
2. ⏳ Systematically update test files (use helper commands)
3. ⏳ Scan and fix i18n keys
4. ⏳ Restructure dashboard components

