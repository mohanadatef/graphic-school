# 💰 Currency Implementation Guide

## ✅ Completed

### Backend
1. ✅ `SystemSettingService` - Added currency group support
2. ✅ `SystemSettingController` - Returns currency group
3. ✅ `UpdateSystemSettingRequest` - Currency validation
4. ✅ `SystemSettingsSeeder` - Default currency settings
5. ✅ API Routes - `/api/admin/system-settings`

### Frontend
1. ✅ `useCurrency.js` composable - Currency formatting
2. ✅ `AdminSettings.vue` - Currency settings UI (tabs: General, Language, Currency)

## 📋 Remaining Tasks

### Replace Hardcoded Currency (19+ files)

**Files to update** (replace `currency: 'EGP'` with dynamic currency):

1. `graphic-school-frontend/src/views/public/CoursesPage.vue`
   - Line 135: `currency: 'EGP'` → Use `useCurrency()`

2. `graphic-school-frontend/src/views/dashboard/admin/ReportsPage.vue`
   - Line 555: `currency: 'EGP'` → Use `useCurrency()`

3. `graphic-school-frontend/src/views/dashboard/admin/StrategicReportsPage.vue`
   - Line 645: `currency: 'EGP'` → Use `useCurrency()`

4. `graphic-school-frontend/src/views/dashboard/admin/AdminDashboard.vue`
   - Line 295: `currency: 'EGP'` → Use `useCurrency()`

5. `graphic-school-frontend/src/views/dashboard/admin/AdminInvoices.vue`
   - Check for hardcoded currency

6. `graphic-school-frontend/src/views/dashboard/admin/AdminInvoiceView.vue`
   - Check for hardcoded currency

7. `graphic-school-frontend/src/views/dashboard/student/StudentInvoiceView.vue`
   - Check for hardcoded currency

8. `graphic-school-frontend/src/views/dashboard/student/StudentPayments.vue`
   - Check for hardcoded currency

9. `graphic-school-frontend/src/views/dashboard/academy/SubscriptionInvoices.vue`
   - Check for hardcoded currency

10. `graphic-school-frontend/src/views/dashboard/hq/HQPlans.vue`
    - Check for hardcoded currency

11. `graphic-school-frontend/src/views/dashboard/academy/PlanSelection.vue`
    - Check for hardcoded currency

12. `graphic-school-frontend/src/views/public/PublicProgramDetails.vue`
    - Check for hardcoded currency

13. `graphic-school-frontend/src/views/public/PublicPrograms.vue`
    - Check for hardcoded currency

14. `graphic-school-frontend/src/views/dashboard/student/StudentPrograms.vue`
    - Check for hardcoded currency

15. `graphic-school-frontend/src/views/dashboard/student/StudentProgramDetails.vue`
    - Check for hardcoded currency

16. `graphic-school-frontend/src/views/dashboard/admin/AdminPrograms.vue`
    - Check for hardcoded currency

17. `graphic-school-frontend/src/views/dashboard/admin/AdminPayments.vue`
    - Check for hardcoded currency

18. `graphic-school-frontend/src/views/public/InstructorDetailsPage.vue`
    - Check for hardcoded currency

19. Any other files with `formatCurrency` or `Intl.NumberFormat` with hardcoded currency

## 🔧 How to Replace

### Pattern 1: Intl.NumberFormat with hardcoded currency

**Before:**
```javascript
function formatCurrency(value) {
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP', // ❌ Hardcoded
  }).format(value);
}
```

**After:**
```javascript
import { useCurrency } from '../../composables/useCurrency';

const { formatCurrency } = useCurrency();

// Use directly
formatCurrency(value)
```

### Pattern 2: Manual currency formatting

**Before:**
```javascript
const formatted = `${value} ج.م`; // ❌ Hardcoded
```

**After:**
```javascript
import { useCurrency } from '../../composables/useCurrency';

const { formatCurrency } = useCurrency();
const formatted = formatCurrency(value);
```

## 🧪 Testing

After replacing currency:
1. Go to Admin Settings → Currency tab
2. Change currency to SAR
3. Check all pages that display prices:
   - Public courses page
   - Student invoices
   - Admin dashboard
   - Reports
   - Subscription plans
4. Verify currency symbol and position are correct

## 📝 Notes

- Currency settings are cached in localStorage for performance
- Currency is loaded from API on app initialization
- Currency updates trigger a reload event: `window.dispatchEvent(new Event('currency-updated'))`
- Default currency is EGP if not set

---

**Status**: Backend ✅ | Frontend Settings UI ✅ | Currency Replacement ⏳ (19+ files remaining)

