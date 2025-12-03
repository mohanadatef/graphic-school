# Frontend Structure

## Overview

The frontend is a Vue.js 3 Single Page Application (SPA) located in `graphic-school-frontend/`. It uses Composition API, Pinia for state management, Vue Router for routing, and Tailwind CSS for styling.

**Framework:** Vue.js 3 (Composition API)  
**State Management:** Pinia  
**Routing:** Vue Router  
**Styling:** Tailwind CSS  
**i18n:** Vue I18n (Multi-language support, RTL for Arabic)

## Root Structure

```
graphic-school-frontend/
├── src/                    # Source code
├── public/                 # Static assets
├── cypress/                # E2E tests (Cypress)
├── tests/                  # Unit tests (Vitest)
├── package.json            # Dependencies
├── vite.config.js          # Vite configuration
├── tailwind.config.js      # Tailwind CSS configuration
└── cypress.config.js       # Cypress configuration
```

## Source Directory (`src/`)

```
src/
├── components/             # Reusable Vue components
├── views/                  # Page components
├── stores/                 # Pinia stores
├── services/               # API services
├── router/                 # Vue Router configuration
├── composables/            # Vue composables
├── middleware/             # Route middleware
├── utils/                  # Utility functions
├── i18n/                   # Internationalization
└── assets/                 # Static assets (images, fonts)
```

## Components (`src/components/`)

### Common Components (`src/components/common/`)

Reusable UI components used across the application:

- **`FilterDropdown.vue`** - Dropdown filter component
- **`PaginationControls.vue`** - Pagination component
- **`LoadingSkeleton.vue`** - Loading skeleton
- **`NotificationCenter.vue`** - Notification center
- **`ToastContainer.vue`** - Toast notifications
- **`LanguagePicker.vue`** - Language selection
- **`ThemeToggle.vue`** - Dark/light theme toggle

### Layout Components (`src/components/layouts/`)

- **`DashboardLayout.vue`** - Dashboard layout with sidebar navigation
- **`PublicLayout.vue`** - Public website layout with header/footer

### Admin Components (`src/components/admin/`)

- **`LanguageFormModal.vue`** - Language form modal
- **`CurrencyFormModal.vue`** - Currency form modal
- **`CountryFormModal.vue`** - Country form modal

### Public Components (`src/components/public/`)

- **`CMSPageRenderer.vue`** - CMS page renderer
- **`blocks/`** - CMS block components
  - `BlockHero.vue`
  - `BlockFeatures.vue`
  - `BlockFAQ.vue`
  - `BlockContact.vue`
  - etc.

### Community Components (`src/views/dashboard/admin/Community/`)

- **`PostCard.vue`** - Post card component (reusable)
- **`PostForm.vue`** - Post create/edit form
- **`PostComments.vue`** - Comments and replies component

## Views (`src/views/`)

### Public Views (`src/views/public/`)

Public-facing pages (no authentication required):

- **`HomePage.vue`** - Homepage (CMS-driven)
- **`CoursesPage.vue`** - Courses listing
- **`CourseDetailsPage.vue`** - Course details page
- **`InstructorsPage.vue`** - Instructors listing (alias: TrainersPage)
- **`InstructorDetailsPage.vue`** - Instructor details
- **`FAQPage.vue`** - FAQ page (CMS-driven)
- **`AboutPage.vue`** - About page (CMS-driven)
- **`ContactPage.vue`** - Contact page
- **`PublicEnrollmentForm.vue`** - Public enrollment form
- **`CertificateVerification.vue`** - Public certificate verification
- **`LoginPage.vue`** - Login page
- **`RegisterPage.vue`** - Registration page
- **`SetupWizard.vue`** - Setup wizard (first-time setup)
- **`NotFound.vue`** - 404 page

### Admin Dashboard Views (`src/views/dashboard/admin/`)

**Management Pages:**
- **`AdminDashboard.vue`** - Admin dashboard (overview widgets)
- **`AdminUsers.vue`** - Users management
- **`AdminRoles.vue`** - Roles management
- **`AdminCourses.vue`** - Courses management
- **`AdminGroups.vue`** - Groups management
- **`AdminSessions.vue`** - Sessions management
- **`AdminEnrollments.vue`** - Enrollments management
- **`AdminAttendance.vue`** - Attendance overview
- **`AdminCertificates.vue`** - Certificate issuance and management
- **`AdminCommunity.vue`** - Community management with moderation
- **`AdminPages.vue`** - CMS pages management
- **`AdminLanguages.vue`** - Languages management
- **`AdminCurrencies.vue`** - Currencies management
- **`AdminCountries.vue`** - Countries management
- **`AdminSettings.vue`** - System settings
- **`AdminCalendar.vue`** - Calendar view

**Form Components:**
- **`CourseForm.vue`** - Course create/edit form
- **`UserForm.vue`** - User create/edit form
- **`RoleForm.vue`** - Role create/edit form
- **`SessionForm.vue`** - Session edit form
- **`EnrollmentForm.vue`** - Enrollment edit form
- **`PageForm.vue`** - Page create/edit form
- **`CategoryForm.vue`** - Category form

**Group Management:**
- **`AdminGroupCreate.vue`** - Create group
- **`AdminGroupEdit.vue`** - Edit group
- **`AdminGroupView.vue`** - View group details

**CMS Blocks (`src/views/dashboard/admin/blocks/`):**
- `BlockHero.vue`
- `BlockFeatures.vue`
- `BlockFAQ.vue`
- `BlockContact.vue`
- etc.

### Instructor Dashboard Views (`src/views/dashboard/instructor/`)

- **`InstructorMyGroups.vue`** - My groups (groups I teach)
- **`InstructorSessions.vue`** - All sessions
- **`InstructorGroupSessions.vue`** - Sessions for a specific group
- **`InstructorStudentsList.vue`** - Students in a group
- **`InstructorTakeAttendance.vue`** - Take attendance for a session
- **`InstructorCommunity.vue`** - Community (post, comment, reply)
- **`InstructorCalendar.vue`** - Calendar view

### Student Dashboard Views (`src/views/dashboard/student/`)

- **`StudentMyCourses.vue`** - My enrolled courses
- **`StudentMyGroup.vue`** - My assigned group
- **`StudentMySessions.vue`** - My upcoming sessions (schedule view)
- **`StudentAttendanceHistory.vue`** - Attendance history
- **`StudentCertificates.vue`** - My certificates
- **`StudentCommunity.vue`** - Community (view posts, comment, reply)
- **`StudentProfile.vue`** - Profile management
- **`StudentCalendar.vue`** - Calendar view

## Stores (`src/stores/`)

Pinia stores for centralized state management:

### Core Stores
- **`auth.js`** - Authentication state (user, token, login/logout)
- **`branding.js`** - Branding settings
- **`settings.js`** - Application settings
- **`setupWizard.js`** - Setup wizard state
- **`websiteSettings.js`** - Website settings
- **`notifications.js`** - Notification state

### Domain Stores
- **`course.js`** - Course state management
- **`category.js`** - Category state
- **`group.js`** - Group state management
- **`session.js`** - Session state management
- **`enrollment.js`** - Enrollment state management
- **`attendance.js`** - Attendance state management
- **`certificate.js`** - Certificate state management
- **`community.js`** - Community state (posts, comments, replies, likes)

### Localization Stores
- **`language.js`** - Language state
- **`currency.js`** - Currency state
- **`country.js`** - Country state

### Store Structure

Each store follows this pattern:

```javascript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { someService } from '../services/api';

export const useSomeStore = defineStore('some', () => {
  // State
  const items = ref([]);
  const currentItem = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({ ... });

  // Getters
  const filteredItems = computed(() => { ... });

  // Actions
  async function fetchAll(params = {}) { ... }
  async function fetchById(id) { ... }
  async function create(payload) { ... }
  async function update(id, payload) { ... }
  async function remove(id) { ... }
  
  function clearStore() { ... }
  function setPage(page) { ... }
  function setPerPage(perPage) { ... }

  return {
    // State
    items,
    currentItem,
    loading,
    error,
    pagination,
    // Getters
    filteredItems,
    // Actions
    fetchAll,
    fetchById,
    create,
    update,
    remove,
    clearStore,
    setPage,
    setPerPage,
  };
});
```

## Services (`src/services/api/`)

API service layer that communicates with the backend:

### Core Services
- **`client.js`** - Axios client configuration
- **`index.js`** - Service exports

### Domain Services
- **`authService.js`** - Authentication API calls
- **`courseService.js`** - Course API calls
- **`categoryService.js`** - Category API calls
- **`groupService.js`** - Group API calls
- **`sessionService.js`** - Session API calls
- **`enrollmentService.js`** - Enrollment API calls
- **`attendanceService.js`** - Attendance API calls
- **`certificateService.js`** - Certificate API calls
- **`communityService.js`** - Community API calls

### User Services
- **`userService.js`** - User management
- **`instructorService.js`** - Instructor-specific API calls
- **`studentService.js`** - Student-specific API calls

### System Services
- **`settingsService.js`** - Settings API calls
- **`cmsService.js`** - CMS/page builder API calls

### Service Structure

```javascript
import api from './client';

export const someService = {
  async getAll(filters = {}) {
    const { data } = await api.get('/some-resource', { params: filters });
    return data;
  },

  async getById(id) {
    const { data } = await api.get(`/some-resource/${id}`);
    return data;
  },

  async create(payload) {
    const { data } = await api.post('/some-resource', payload);
    return data;
  },

  async update(id, payload) {
    const { data } = await api.put(`/some-resource/${id}`, payload);
    return data;
  },

  async delete(id) {
    const { data } = await api.delete(`/some-resource/${id}`);
    return data;
  },
};
```

## Router (`src/router/index.js`)

Vue Router configuration with role-based route guards.

### Route Structure

```javascript
const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      // Public routes
      { path: '', name: 'home', component: HomePage },
      { path: 'courses', name: 'courses', component: CoursesPage },
      { path: 'courses/:id', name: 'course-details', component: CourseDetailsPage },
      { path: 'enroll', name: 'public-enroll', component: PublicEnrollmentForm },
      { path: 'certificate/verify', name: 'certificate-verify', component: CertificateVerification },
      // ...
    ],
  },
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      // Admin routes
      { path: 'admin', name: 'admin-dashboard', component: AdminDashboard, meta: { role: 'admin' } },
      { path: 'admin/courses', name: 'admin-courses', component: AdminCourses, meta: { role: 'admin' } },
      { path: 'admin/certificates', name: 'admin-certificates', component: AdminCertificates, meta: { role: 'admin' } },
      { path: 'admin/community', name: 'admin-community', component: AdminCommunity, meta: { role: 'admin' } },
      // Instructor routes
      { path: 'instructor', name: 'instructor-dashboard', component: InstructorMyGroups, meta: { role: 'instructor' } },
      { path: 'instructor/community', name: 'instructor-community', component: InstructorCommunity, meta: { role: 'instructor' } },
      // Student routes
      { path: 'student', name: 'student-dashboard', component: StudentMyCourses, meta: { role: 'student' } },
      { path: 'student/certificates', name: 'student-certificates', component: StudentCertificates, meta: { role: 'student' } },
      { path: 'student/community', name: 'student-community', component: StudentCommunity, meta: { role: 'student' } },
      // ...
    ],
  },
];
```

### Route Guards

- **`authMiddleware`** - Check if user is authenticated
- **`guestMiddleware`** - Redirect authenticated users
- **`roleMiddleware`** - Check user role (admin, instructor, student)
- **`setupCheckMiddleware`** - Check if setup wizard is completed

### Route Protection

Routes are protected by:
1. Authentication check (`authMiddleware`)
2. Role-based access (`roleMiddleware`)
3. Setup wizard check (for public routes)

## Composables (`src/composables/`)

Reusable composition functions:

- **`useApi.js`** - API request helper
- **`usePagination.js`** - Pagination helper
- **`useFilters.js`** - Filter helper
- **`useToast.js`** - Toast notification helper
- **`useMultiLanguage.js`** - Multi-language form helper

### Composable Example

```javascript
import { ref } from 'vue';
import { someService } from '@/services/api';

export function useSomeComposable() {
  const loading = ref(false);
  const error = ref(null);
  const data = ref(null);

  async function fetch() {
    loading.value = true;
    error.value = null;
    try {
      data.value = await someService.getAll();
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  }

  return {
    loading,
    error,
    data,
    fetch,
  };
}
```

## Middleware (`src/middleware/`)

Route middleware functions:

- **`authMiddleware.js`** - Authentication check
- **`guestMiddleware.js`** - Guest check
- **`roleMiddleware.js`** - Role-based access control
- **`setupCheckMiddleware.js`** - Setup wizard completion check

## Utils (`src/utils/`)

Utility functions and helpers:

- **`date.js`** - Date formatting utilities
- **`validation.js`** - Validation helpers
- **`helpers.js`** - General helpers

## i18n (`src/i18n/`)

Internationalization configuration:

- **`index.js`** - i18n setup (Vue I18n)
- **`locales/`** - Translation files
  - `en.json` - English translations
  - `ar.json` - Arabic translations (RTL support)

### i18n Usage

```vue
<template>
  <h1>{{ $t('page.title') }}</h1>
  <p>{{ $t('page.description') }}</p>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
```

## Component Patterns

### Composition API

All components use Composition API:

```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useCourseStore } from '@/stores/course';

const courseStore = useCourseStore();
const loading = computed(() => courseStore.loading);

onMounted(async () => {
  await courseStore.fetchAll();
});
</script>
```

### State Management with Pinia

```vue
<script setup>
import { useCourseStore } from '@/stores/course';
import { storeToRefs } from 'pinia';

const courseStore = useCourseStore();
const { courses, loading, error } = storeToRefs(courseStore);

await courseStore.fetchAll();
</script>
```

### Service Integration

```vue
<script setup>
import { ref, onMounted } from 'vue';
import { courseService } from '@/services/api';

const courses = ref([]);
const loading = ref(false);

onMounted(async () => {
  loading.value = true;
  try {
    const response = await courseService.getAll();
    courses.value = response.data || [];
  } catch (error) {
    console.error('Error loading courses:', error);
  } finally {
    loading.value = false;
  }
});
</script>
```

## File Naming Conventions

### Components
- **PascalCase:** `CourseForm.vue`
- Descriptive names
- Grouped by feature

### Stores
- **camelCase:** `course.js`
- Singular names
- Feature-based

### Services
- **camelCase** with "Service" suffix: `courseService.js`
- Descriptive names

### Composables
- **camelCase** with "use" prefix: `useApi.js`
- Descriptive names

## Best Practices

### 1. Component Organization
- Group by feature
- Reusable components in `common/`
- Feature-specific in feature folders

### 2. State Management
- Use Pinia for global state
- Local state with `ref`/`reactive`
- Avoid prop drilling
- Use `storeToRefs()` for reactive store state

### 3. API Calls
- Use services for API calls
- Handle errors consistently
- Show loading states
- Use stores to cache data

### 4. Styling
- Use Tailwind CSS utility classes
- Component-scoped styles when needed
- Responsive design (mobile-first)
- Dark mode support

### 5. Accessibility
- Semantic HTML
- ARIA labels where needed
- Keyboard navigation
- Screen reader support

### 6. Error Handling
- Try-catch in async functions
- User-friendly error messages
- Error boundaries for critical sections
- Toast notifications for user feedback

### 7. Loading States
- Show loading indicators
- Skeleton screens for better UX
- Disable actions during loading
- Optimistic UI updates where appropriate

## Routing Structure

### Public Routes
- `/` - Home
- `/courses` - Courses list
- `/courses/:id` - Course details
- `/enroll` - Public enrollment
- `/certificate/verify` - Certificate verification
- `/instructors` - Instructors list
- `/about` - About page
- `/contact` - Contact page
- `/faq` - FAQ page
- `/login` - Login
- `/register` - Register
- `/setup` - Setup wizard

### Admin Routes
- `/dashboard/admin` - Dashboard
- `/dashboard/admin/courses` - Courses
- `/dashboard/admin/groups` - Groups
- `/dashboard/admin/sessions` - Sessions
- `/dashboard/admin/enrollments` - Enrollments
- `/dashboard/admin/attendance` - Attendance
- `/dashboard/admin/certificates` - Certificates
- `/dashboard/admin/community` - Community
- `/dashboard/admin/users` - Users
- `/dashboard/admin/settings` - Settings

### Instructor Routes
- `/dashboard/instructor` - Dashboard (My Groups)
- `/dashboard/instructor/my-groups` - My Groups
- `/dashboard/instructor/sessions` - Sessions
- `/dashboard/instructor/sessions/:sessionId/attendance` - Take Attendance
- `/dashboard/instructor/community` - Community

### Student Routes
- `/dashboard/student` - Dashboard (My Courses)
- `/dashboard/student/courses` - My Courses
- `/dashboard/student/my-group` - My Group
- `/dashboard/student/my-sessions` - My Sessions
- `/dashboard/student/attendance-history` - Attendance History
- `/dashboard/student/certificates` - My Certificates
- `/dashboard/student/community` - Community
- `/dashboard/student/profile` - Profile

## Development Workflow

### 1. Create a New Feature

1. Create API service (`services/api/`)
2. Create Pinia store (`stores/`)
3. Create Vue component (`views/`)
4. Add route (`router/index.js`)
5. Add translations (`i18n/locales/`)

### 2. Component Development

1. Use Composition API (`<script setup>`)
2. Use Pinia for state
3. Use services for API calls
4. Use Tailwind for styling
5. Add loading/error states
6. Add i18n translations

### 3. Testing

1. Unit tests for composables/utils
2. Component tests for Vue components
3. E2E tests for user flows

## Build & Deployment

### Development
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

### Build Output
- `dist/` - Production build
- Static assets optimized
- Code splitting by route
- Minified and tree-shaken

