# Frontend Folder Structure

## Overview

The frontend is a Vue.js 3 SPA located in `graphic-school-frontend/`. It uses Composition API, Pinia for state management, and Vue Router for routing.

## Root Structure

```
graphic-school-frontend/
├── src/                    # Source code
├── public/                 # Static assets
├── cypress/                # E2E tests
├── tests/                  # Unit tests
├── scripts/                # Build scripts
├── package.json            # Dependencies
├── vite.config.js          # Vite configuration
├── tailwind.config.js      # Tailwind CSS configuration
└── cypress.config.js       # Cypress configuration
```

## Source Directory (`src/`)

### Components (`src/components/`)

#### Common Components (`src/components/common/`)
Reusable UI components:
- `AccessibleButton.vue` - Accessible button component
- `ErrorBoundary.vue` - Error boundary wrapper
- `FilterDropdown.vue` - Filter dropdown
- `Icon.vue` - Icon component
- `LanguagePicker.vue` - Language selection
- `LanguageSwitcher.vue` - Language switcher
- `LoadingSkeleton.vue` - Loading skeleton
- `MultiLanguageInput.vue` - Multi-language input field
- `MultiLanguageTextarea.vue` - Multi-language textarea
- `NotificationCenter.vue` - Notification center
- `NotificationDropdown.vue` - Notification dropdown
- `PaginationControls.vue` - Pagination component
- `ThemeToggle.vue` - Dark/light theme toggle
- `ToastContainer.vue` - Toast notifications

#### Layout Components (`src/components/layouts/`)
- `DashboardLayout.vue` - Dashboard layout with sidebar
- `PublicLayout.vue` - Public website layout

#### Admin Components (`src/components/admin/`)
- `CountryFormModal.vue` - Country form modal
- `CurrencyFormModal.vue` - Currency form modal
- `LanguageFormModal.vue` - Language form modal
- `WebsiteStatusPanel.vue` - Website status panel

#### Public Components (`src/components/public/`)
- `CMSPageRenderer.vue` - CMS page renderer
- `blocks/` - CMS block components
  - `ContentBlock.vue`
  - `CTABlock.vue`
  - `FeaturesBlock.vue`
  - `HeroBlock.vue`
  - `TestimonialsBlock.vue`

#### Setup Components (`src/components/setup/`)
- `WizardGeneral.vue` - General information step
- `WizardBranding.vue` - Branding step
- `WizardPages.vue` - Pages configuration step
- `WizardContact.vue` - Contact information step
- `WizardEmail.vue` - Email configuration step
- `WizardPayment.vue` - Payment configuration step
- `WizardReview.vue` - Review step
- `WizardLaunch.vue` - Launch step

### Views (`src/views/`)

#### Public Views (`src/views/public/`)
- `HomePage.vue` - Homepage (CMS-driven)
- `CoursesPage.vue` - Courses listing
- `CourseDetailsPage.vue` - Course details
- `TrainersPage.vue` - Instructors listing
- `InstructorDetailsPage.vue` - Instructor details
- `FAQPage.vue` - FAQ page (CMS-driven)
- `ContactPage.vue` - Contact page (CMS-driven)
- `AboutPage.vue` - About page (CMS-driven)
- `LoginPage.vue` - Login page
- `RegisterPage.vue` - Registration page
- `SetupWizard.vue` - Setup wizard
- `PublicEnrollmentForm.vue` - Public enrollment form
- `CertificateVerification.vue` - Certificate verification
- `NotFound.vue` - 404 page

#### Admin Views (`src/views/dashboard/admin/`)
- `AdminDashboard.vue` - Admin dashboard
- `AdminUsers.vue` - Users management
- `AdminRoles.vue` - Roles management
- `AdminCourses.vue` - Courses management
- `AdminGroups.vue` - Groups management
- `AdminSessions.vue` - Sessions management
- `AdminEnrollments.vue` - Enrollments management
- `AdminAttendance.vue` - Attendance overview
- `AdminPages.vue` - CMS pages management
- `AdminLanguages.vue` - Languages management
- `AdminCurrencies.vue` - Currencies management
- `AdminCountries.vue` - Countries management
- `AdminSettings.vue` - System settings
- `AdminCalendar.vue` - Calendar view
- `CMSEditor.vue` - CMS page editor

#### Form Components (`src/views/dashboard/admin/`)
- `CourseForm.vue` - Course create/edit form
- `UserForm.vue` - User create/edit form
- `RoleForm.vue` - Role create/edit form
- `SessionForm.vue` - Session create/edit form
- `EnrollmentForm.vue` - Enrollment create/edit form
- `PageForm.vue` - Page create/edit form

#### Instructor Views (`src/views/dashboard/instructor/`)
- `InstructorMyGroups.vue` - My groups
- `InstructorSessions.vue` - All sessions
- `InstructorTakeAttendance.vue` - Take attendance
- `InstructorStudentsList.vue` - Students list
- `InstructorCalendar.vue` - Calendar view

#### Student Views (`src/views/dashboard/student/`)
- `StudentMyCourses.vue` - My courses
- `StudentMyGroup.vue` - My group
- `StudentMySessions.vue` - My sessions
- `StudentAttendanceHistory.vue` - Attendance history
- `StudentProfile.vue` - Profile
- `StudentCalendar.vue` - Calendar view

### Stores (`src/stores/`)

Pinia stores for state management:
- `auth.js` - Authentication state
- `branding.js` - Branding settings
- `course.js` - Course state
- `country.js` - Country state
- `currency.js` - Currency state
- `language.js` - Language state
- `notifications.js` - Notifications state
- `settings.js` - Application settings
- `setupWizard.js` - Setup wizard state
- `websiteSettings.js` - Website settings

### Router (`src/router/`)
- `index.js` - Vue Router configuration

### Composables (`src/composables/`)

Reusable composition functions:
- `useApi.js` - API request helper
- `useI18n.js` - Internationalization helper
- `useListPage.js` - List page helper
- `usePagination.js` - Pagination helper
- `useFilters.js` - Filter helper
- `useToast.js` - Toast notification helper
- `useMultiLanguage.js` - Multi-language form helper

### Middleware (`src/middleware/`)
- `authMiddleware.js` - Authentication check
- `guestMiddleware.js` - Guest check
- `roleMiddleware.js` - Role-based access
- `setupCheckMiddleware.js` - Setup wizard check

### Services (`src/services/`)
- `api.js` - API client configuration

### Utils (`src/utils/`)
- Utility functions
- Helpers
- Constants

### i18n (`src/i18n/`)
- `index.js` - i18n configuration
- `locales/` - Translation files
  - `en.json` - English translations
  - `ar.json` - Arabic translations

## Component Patterns

### Composition API
All components use Composition API:
```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useApi } from '../composables/useApi';

const { get, post } = useApi();
const data = ref(null);

onMounted(async () => {
  data.value = await get('/api/endpoint');
});
</script>
```

### State Management
Using Pinia stores:
```vue
<script setup>
import { useCourseStore } from '../stores/course';

const courseStore = useCourseStore();
await courseStore.fetchAll();
</script>
```

### Multi-Language
Using i18n composable:
```vue
<script setup>
import { useI18n } from '../composables/useI18n';

const { t } = useI18n();
</script>

<template>
  <h1>{{ t('page.title') }}</h1>
</template>
```

## File Naming Conventions

### Components
- PascalCase: `CourseForm.vue`
- Descriptive names
- Grouped by feature

### Stores
- camelCase: `course.js`
- Singular names
- Feature-based

### Composables
- camelCase with "use" prefix: `useApi.js`
- Descriptive names
- Reusable functions

## Best Practices

1. **Component Organization**
   - Group by feature
   - Reusable components in `common/`
   - Feature-specific in feature folders

2. **State Management**
   - Use Pinia for global state
   - Local state with `ref`/`reactive`
   - Avoid prop drilling

3. **API Calls**
   - Use `useApi` composable
   - Handle errors consistently
   - Show loading states

4. **Styling**
   - Use Tailwind CSS
   - Component-scoped styles when needed
   - Responsive design

5. **Accessibility**
   - Semantic HTML
   - ARIA labels
   - Keyboard navigation
   - Screen reader support

