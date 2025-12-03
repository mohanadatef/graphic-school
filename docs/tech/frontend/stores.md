# Frontend Stores (Pinia)

## Overview

Pinia stores manage global application state. Stores are located in `src/stores/`.

## Store Structure

All stores use Pinia's Composition API style:

```javascript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useStoreName = defineStore('storeName', () => {
  // State
  const items = ref([]);
  const loading = ref(false);
  
  // Getters
  const filteredItems = computed(() => { ... });
  
  // Actions
  async function fetchItems() { ... }
  
  return { items, loading, filteredItems, fetchItems };
});
```

## Core Stores

### auth.js

**Purpose:** Authentication state management.

**State:**
- `user` - Current user object
- `token` - Authentication token
- `isAuthenticated` - Authentication status
- `roleName` - User role name
- `loading` - Loading state
- `error` - Error state

**Getters:**
- `isAdmin` - Check if user is admin
- `isInstructor` - Check if user is instructor
- `isStudent` - Check if user is student

**Actions:**
- `login(credentials)` - Login user
- `logout()` - Logout user
- `register(data)` - Register new user
- `fetchUser()` - Fetch current user
- `updateProfile(data)` - Update user profile

### course.js

**Purpose:** Course state management.

**State:**
- `courses` - List of courses
- `currentCourse` - Currently selected course
- `loading` - Loading state
- `error` - Error state
- `pagination` - Pagination metadata
- `filters` - Filter state

**Getters:**
- `filteredCourses` - Filtered courses list

**Actions:**
- `fetchAll(params)` - Fetch all courses
- `fetchById(id)` - Fetch course by ID
- `create(data)` - Create course
- `update(id, data)` - Update course
- `delete(id)` - Delete course
- `fetchAdminCourses(params)` - Fetch admin courses
- `fetchPublicCourses(params)` - Fetch public courses

### language.js

**Purpose:** Language state management.

**State:**
- `languages` - List of languages
- `activeLanguages` - Active languages
- `defaultLanguage` - Default language
- `loading` - Loading state

**Getters:**
- `hasMultipleLanguages` - Check if multiple languages active
- `getDefaultLanguage` - Get default language

**Actions:**
- `fetchAll()` - Fetch all languages
- `loadActiveLanguages()` - Load active languages
- `create(data)` - Create language
- `update(id, data)` - Update language
- `delete(id)` - Delete language

### currency.js

**Purpose:** Currency state management.

**State:**
- `currencies` - List of currencies
- `activeCurrencies` - Active currencies
- `defaultCurrency` - Default currency
- `loading` - Loading state

**Actions:**
- `fetchAll()` - Fetch all currencies
- `loadActive()` - Load active currencies
- `create(data)` - Create currency
- `update(id, data)` - Update currency
- `delete(id)` - Delete currency

### country.js

**Purpose:** Country state management.

**State:**
- `countries` - List of countries
- `activeCountries` - Active countries
- `defaultCountry` - Default country
- `loading` - Loading state

**Actions:**
- `fetchAll()` - Fetch all countries
- `loadActive()` - Load active countries
- `create(data)` - Create country
- `update(id, data)` - Update country
- `delete(id)` - Delete country

### branding.js

**Purpose:** Branding state management.

**State:**
- `branding` - Branding settings
- `loading` - Loading state
- `error` - Error state

**Actions:**
- `fetchBranding()` - Fetch branding settings
- `updateBranding(data)` - Update branding

### settings.js

**Purpose:** Application settings state.

**State:**
- `settings` - Application settings
- `loading` - Loading state

**Actions:**
- `fetchSettings()` - Fetch settings
- `updateSettings(data)` - Update settings

### setupWizard.js

**Purpose:** Setup wizard state.

**State:**
- `currentStep` - Current wizard step
- `setupData` - Setup data
- `loading` - Loading state
- `error` - Error state

**Actions:**
- `loadStatus()` - Load setup status
- `saveStep(step, data)` - Save wizard step
- `completeSetup(data)` - Complete setup
- `activateDefault()` - Activate with defaults

### websiteSettings.js

**Purpose:** Website settings state.

**State:**
- `settings` - Website settings
- `isActivated` - Activation status
- `loading` - Loading state

**Actions:**
- `fetchSettings()` - Fetch website settings
- `updateSettings(data)` - Update settings
- `checkActivation()` - Check activation status

### notifications.js

**Purpose:** Notifications state.

**State:**
- `notifications` - List of notifications
- `unreadCount` - Unread count
- `loading` - Loading state

**Actions:**
- `fetchNotifications()` - Fetch notifications
- `markAsRead(id)` - Mark notification as read
- `markAllAsRead()` - Mark all as read
- `deleteNotification(id)` - Delete notification

### student.js

**Purpose:** Student-specific state.

**State:**
- `myCourses` - Student's courses
- `myGroup` - Student's group
- `mySessions` - Student's sessions
- `attendanceHistory` - Attendance history
- `loading` - Loading state

**Actions:**
- `fetchMyCourses()` - Fetch student courses
- `fetchMyGroup()` - Fetch student group
- `fetchMySessions()` - Fetch student sessions
- `fetchAttendanceHistory()` - Fetch attendance history

### instructor.js

**Purpose:** Instructor-specific state.

**State:**
- `myGroups` - Instructor's groups
- `sessions` - Instructor's sessions
- `loading` - Loading state

**Actions:**
- `fetchMyGroups()` - Fetch instructor groups
- `fetchSessions(params)` - Fetch instructor sessions
- `takeAttendance(sessionId, data)` - Take attendance

## Store Patterns

### State Management
- Use `ref()` for reactive primitives
- Use `reactive()` for objects (when needed)
- Keep state minimal and focused

### Getters
- Use `computed()` for derived state
- Cache expensive computations
- Keep getters pure

### Actions
- Async operations in actions
- Handle errors appropriately
- Update state atomically
- Return promises for chaining

### Error Handling
- Store errors in state
- Display errors in UI
- Log errors for debugging
- Clear errors on new actions

## Store Usage

### In Components
```vue
<script setup>
import { useCourseStore } from '../stores/course';

const courseStore = useCourseStore();
await courseStore.fetchAll();
</script>
```

### In Composables
```javascript
import { useAuthStore } from '../stores/auth';

export function useMyComposable() {
  const authStore = useAuthStore();
  // Use store
}
```

## Store Best Practices

1. **Single Source of Truth**
   - Store data in one place
   - Avoid duplicate state

2. **Minimal State**
   - Only store what's needed globally
   - Use local state when possible

3. **Async Actions**
   - Handle async operations in actions
   - Show loading states
   - Handle errors

4. **Computed Getters**
   - Use computed for derived state
   - Cache expensive operations
   - Keep getters pure

5. **State Persistence**
   - Persist critical state (auth token)
   - Use localStorage/sessionStorage
   - Restore on app load

## Store Testing

Stores should be tested with:
- Unit tests for actions
- Unit tests for getters
- Integration tests for workflows

## Conclusion

Pinia stores provide:
- Centralized state management
- Reactive state updates
- Computed getters
- Async action handling
- Type safety (with TypeScript)

Stores are the single source of truth for global application state.

