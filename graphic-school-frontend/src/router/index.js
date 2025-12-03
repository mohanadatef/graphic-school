import { createRouter, createWebHistory } from 'vue-router';
import { authMiddleware, guestMiddleware, roleMiddleware } from '../middleware';

// Lazy load layouts
const PublicLayout = () => import('../components/layouts/PublicLayout.vue');
const DashboardLayout = () => import('../components/layouts/DashboardLayout.vue');

// Public routes
const publicChildren = [
  {
    path: '',
    name: 'home',
    component: () => import('../views/public/HomePage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'courses',
    name: 'courses',
    component: () => import('../views/public/CoursesPage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'courses/:id',
    name: 'course-details',
    component: () => import('../views/public/CourseDetailsPage.vue'),
    props: true,
    meta: { middleware: [] },
  },
  // Phase 3: Public Enrollment and Certificate Verification
  {
    path: 'enroll',
    name: 'public-enroll',
    component: () => import('../views/public/PublicEnrollmentForm.vue'),
  },
  {
    path: 'certificate/verify',
    name: 'certificate-verify',
    component: () => import('../views/public/CertificateVerification.vue'),
  },
  {
    path: 'instructors',
    name: 'instructors',
    component: () => import('../views/public/TrainersPage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'trainers',
    name: 'trainers',
    redirect: { name: 'instructors' },
  },
  {
    path: 'faq',
    name: 'faq',
    component: () => import('../views/public/FAQPage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'instructors/:id',
    name: 'instructor-details',
    component: () => import('../views/public/InstructorDetailsPage.vue'),
    props: true,
  },
  {
    path: 'about',
    name: 'about',
    component: () => import('../views/public/AboutPage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'contact',
    name: 'contact',
    component: () => import('../views/public/ContactPage.vue'),
    meta: { middleware: [] },
  },
  {
    path: 'login',
    name: 'login',
    component: () => import('../views/public/LoginPage.vue'),
    meta: { middleware: [guestMiddleware] },
  },
  {
    path: 'register',
    name: 'register',
    component: () => import('../views/public/RegisterPage.vue'),
    meta: { middleware: [guestMiddleware] },
  },
];

// Dashboard routes
const dashboardChildren = [
  // Default dashboard redirect (for admin)
  {
    path: '',
    redirect: (to) => {
      // This will be handled by the router guard based on role
      return '/dashboard/admin';
    },
    meta: { middleware: [authMiddleware], requiresAuth: true },
  },
  // Admin routes
  {
    path: 'admin',
    name: 'admin-dashboard',
    component: () => import('../views/dashboard/admin/AdminDashboard.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/users',
    name: 'admin-users',
    component: () => import('../views/dashboard/admin/AdminUsers.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/users/new',
    name: 'admin-users-new',
    component: () => import('../views/dashboard/admin/UserForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/users/:id/edit',
    name: 'admin-users-edit',
    component: () => import('../views/dashboard/admin/UserForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/roles',
    name: 'admin-roles',
    component: () => import('../views/dashboard/admin/AdminRoles.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/roles/new',
    name: 'admin-roles-new',
    component: () => import('../views/dashboard/admin/RoleForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/roles/:id/edit',
    name: 'admin-roles-edit',
    component: () => import('../views/dashboard/admin/RoleForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/courses',
    name: 'admin-courses',
    component: () => import('../views/dashboard/admin/AdminCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/courses/new',
    name: 'admin-courses-new',
    component: () => import('../views/dashboard/admin/CourseForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/courses/:id/edit',
    name: 'admin-courses-edit',
    component: () => import('../views/dashboard/admin/CourseForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/sessions',
    name: 'admin-sessions',
    component: () => import('../views/dashboard/admin/AdminSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/sessions/:id/edit',
    name: 'admin-sessions-edit',
    component: () => import('../views/dashboard/admin/SessionForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/enrollments',
    name: 'admin-enrollments',
    component: () => import('../views/dashboard/admin/AdminEnrollments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/enrollments/:id/edit',
    name: 'admin-enrollments-edit',
    component: () => import('../views/dashboard/admin/EnrollmentForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/attendance',
    name: 'admin-attendance',
    component: () => import('../views/dashboard/admin/AdminAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/certificates',
    name: 'admin-certificates',
    component: () => import('../views/dashboard/admin/AdminCertificates.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/community',
    name: 'admin-community',
    component: () => import('../views/dashboard/admin/AdminCommunity.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/pages',
    name: 'admin-pages',
    component: () => import('../views/dashboard/admin/AdminPages.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/pages/new',
    name: 'admin-pages-new',
    component: () => import('../views/dashboard/admin/PageForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/pages/:id/edit',
    name: 'admin-pages-edit',
    component: () => import('../views/dashboard/admin/PageForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/settings',
    name: 'admin-settings',
    component: () => import('../views/dashboard/admin/AdminSettings.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  // Groups Management
  // Redirect /dashboard/groups to /dashboard/admin/groups for admins
  {
    path: 'groups',
    redirect: '/dashboard/admin/groups',
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/groups',
    name: 'admin-groups',
    component: () => import('../views/dashboard/admin/AdminGroups.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/groups/new',
    name: 'admin-groups-new',
    component: () => import('../views/dashboard/admin/AdminGroupCreate.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/groups/:id/edit',
    name: 'admin-groups-edit',
    component: () => import('../views/dashboard/admin/AdminGroupEdit.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/languages',
    name: 'admin-languages',
    component: () => import('../views/dashboard/admin/AdminLanguages.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/currencies',
    name: 'admin-currencies',
    component: () => import('../views/dashboard/admin/AdminCurrencies.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/countries',
    name: 'admin-countries',
    component: () => import('../views/dashboard/admin/AdminCountries.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/cms',
    name: 'admin-cms-editor',
    component: () => import('../views/dashboard/admin/CMSEditor.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/groups/:groupId',
    name: 'admin-groups-view',
    component: () => import('../views/dashboard/admin/AdminGroupView.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  // Phase 3: Enrollment, Payments, Attendance, Certificates
  {
    path: 'admin/enrollments',
    name: 'admin-enrollments',
    component: () => import('../views/dashboard/admin/AdminEnrollments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/calendar',
    name: 'admin-calendar',
    component: () => import('../views/dashboard/admin/AdminCalendar.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },

  // Instructor routes
  {
    path: 'instructor',
    redirect: { name: 'instructor-my-groups' },
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/sessions',
    name: 'instructor-sessions',
    component: () => import('../views/dashboard/instructor/InstructorSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/calendar',
    name: 'instructor-calendar',
    component: () => import('../views/dashboard/instructor/InstructorCalendar.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/my-groups',
    name: 'instructor-my-groups',
    component: () => import('../views/dashboard/instructor/InstructorMyGroups.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/groups/:groupId/sessions',
    name: 'instructor-group-sessions',
    component: () => import('../views/dashboard/instructor/InstructorGroupSessions.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/groups/:groupId/students',
    name: 'instructor-group-students',
    component: () => import('../views/dashboard/instructor/InstructorStudentsList.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/sessions/:sessionId/attendance',
    name: 'instructor-take-attendance',
    component: () => import('../views/dashboard/instructor/InstructorTakeAttendance.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/community',
    name: 'instructor-community',
    component: () => import('../views/dashboard/instructor/InstructorCommunity.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
          {
            path: 'instructor/groups',
            name: 'instructor-groups',
            redirect: { name: 'instructor-my-groups' },
            meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
          },

  // Student routes
  {
    path: 'student',
    redirect: { name: 'student-courses' },
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/courses',
    name: 'student-courses',
    component: () => import('../views/dashboard/student/StudentMyCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/my-courses',
    name: 'student-my-courses',
    component: () => import('../views/dashboard/student/StudentMyCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/my-group',
    name: 'student-my-group',
    component: () => import('../views/dashboard/student/StudentMyGroup.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/my-sessions',
    name: 'student-my-sessions',
    component: () => import('../views/dashboard/student/StudentMySessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/attendance-history',
    name: 'student-attendance-history',
    component: () => import('../views/dashboard/student/StudentAttendanceHistory.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/certificates',
    name: 'student-certificates',
    component: () => import('../views/dashboard/student/StudentCertificates.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/certificates/:id',
    name: 'student-certificate-detail',
    component: () => import('../views/dashboard/student/StudentCertificates.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/community',
    name: 'student-community',
    component: () => import('../views/dashboard/student/StudentCommunity.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/calendar',
    name: 'student-calendar',
    component: () => import('../views/dashboard/student/StudentCalendar.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
          {
            path: 'student/profile',
            name: 'student-profile',
            component: () => import('../views/dashboard/student/StudentProfile.vue'),
            meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
          },
];

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: PublicLayout,
      children: publicChildren,
    },
    {
      path: '/dashboard',
      component: DashboardLayout,
      meta: { middleware: [authMiddleware], requiresAuth: true },
      children: dashboardChildren,
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/public/NotFound.vue'),
    },
  ],
});

// Global router middleware
router.beforeEach(async (to, from, next) => {
  // Import stores here to avoid circular dependencies
  const { useAuthStore } = await import('../stores/auth');
  const authStore = useAuthStore();
  
  const isAuth = authStore.isAuthenticated;
  
  // Skip authentication checks for public routes (login, register, etc.)
  const publicRoutes = ['/login', '/register'];
  const isPublicRoute = publicRoutes.some(route => to.path === route || to.path.startsWith(route));
  
  // If authenticated but user data is missing or role is missing
  // First, try to load from localStorage if available
  // Only do this for protected routes, not public routes
  if (!isPublicRoute && isAuth && (!authStore.user || !authStore.roleName) && 
      (to.path.startsWith('/dashboard') || to.meta.requiresAuth)) {
    // First, try to load from localStorage if available
    const savedUser = localStorage.getItem('gs_user') || localStorage.getItem('user');
    if (savedUser) {
      try {
        const parsedUser = JSON.parse(savedUser);
        // Normalize role
        if (parsedUser.role && typeof parsedUser.role === 'object' && parsedUser.role.name) {
          parsedUser.role = parsedUser.role.name;
          parsedUser.role_name = parsedUser.role;
        }
        if (parsedUser.role && !parsedUser.role_name) {
          parsedUser.role_name = parsedUser.role;
        }
        // Use setSession to properly update the store
        if (authStore.setSession) {
          authStore.setSession(parsedUser, authStore.token);
        }
      } catch (e) {
        console.warn('Failed to parse saved user data:', e);
        // Remove corrupted data
        localStorage.removeItem('gs_user');
        localStorage.removeItem('user');
      }
    }
    
    // Only try to fetch from API if user is still missing after loading from localStorage
    // Skip fetch if we already have user data from localStorage
    let hadTokenBeforeFetch = false;
    if (!authStore.user && authStore.fetchCurrentUser && !authStore.loading) {
      // Track if we had a token before fetching (to determine if 401 is expected)
      hadTokenBeforeFetch = !!authStore.token;
      try {
        await authStore.fetchCurrentUser();
        // fetchCurrentUser handles 401 and 404 gracefully:
        // - 401: clears session silently and returns (expected when token is invalid)
        // - 404: returns without clearing session (endpoint might not exist)
        // - Other errors: throws error
      } catch (error) {
        // Only handle unexpected errors (not 401 or 404, which are handled by fetchCurrentUser)
        if (error.response?.status !== 401 && error.response?.status !== 404) {
          console.warn('Failed to fetch user data:', error);
          // For unexpected errors, don't clear session (might be network issue)
        }
      }
    }
    
    // If still no user data after all attempts, redirect to login
    if (!authStore.user && (to.path.startsWith('/dashboard') || to.meta.requiresAuth)) {
      // Only warn if we had a token before fetch attempt (meaning we expected user data)
      // If no token existed, it's expected that there's no user, so don't warn
      if (hadTokenBeforeFetch) {
        // Had token but no user data after fetch - token was likely invalid (expected)
        // This is normal when token expires, so we don't need to warn
      }
      // Always clear session and redirect silently (this is expected behavior)
      if (authStore.clearSession) {
        authStore.clearSession();
      }
      return next('/login');
    }
  }
  
  // Get role from multiple sources and validate it
  const role = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
  
  // Validate role - only allow valid roles
  const validRoles = ['admin', 'super_admin', 'instructor', 'student'];
  const isValidRole = role && validRoles.includes(role);
  
  // Routes that are always accessible
  const alwaysAccessibleRoutes = ['/login', '/register'];
  const isAlwaysAccessible = alwaysAccessibleRoutes.some(route => to.path.startsWith(route));
  
  // Setup Wizard disabled - skip all setup checks
  let setupActivated = true; // Always assume activated
  
  // If user is authenticated and trying to access /login or /register, redirect based on role
  if (isAuth && (to.path === '/login' || to.path === '/register')) {
    if (isValidRole) {
      if (role === 'student') {
        return next('/');
      } else if (role === 'instructor') {
        return next('/dashboard/instructor');
      } else if (role === 'admin' || role === 'super_admin') {
        return next('/dashboard/admin');
      }
    }
    // If role is invalid or missing, check if we have user data
    // If no user data, token is likely invalid - clear session and allow login
    if (!authStore.user) {
      // Token exists but no user data - token is invalid
      if (authStore.clearSession) {
        authStore.clearSession();
      }
      // Allow access to login page
      return next();
    }
    // If user data exists but role is missing/invalid, clear session and allow login
    // This shouldn't happen normally, but handle it gracefully
    if (authStore.clearSession) {
      authStore.clearSession();
    }
    // Allow access to login page
    return next();
  }
  
  // Handle /dashboard root path - redirect based on role
  if (isAuth && to.path === '/dashboard' && to.name === undefined) {
    if (isValidRole) {
      if (role === 'instructor') {
        return next('/dashboard/instructor');
      } else if (role === 'admin' || role === 'super_admin') {
        return next('/dashboard/admin');
      } else if (role === 'student') {
        // For students, redirect to home
        return next('/');
      }
    }
    // If role is invalid or missing, check if we have user data
    // If no user data, token is likely invalid - clear session and redirect to login
    if (!authStore.user) {
      if (authStore.clearSession) {
        authStore.clearSession();
      }
      return next('/login');
    }
    // If user data exists but role is missing/invalid, clear session and redirect to login
    // This shouldn't happen normally, but handle it gracefully
    if (authStore.clearSession) {
      authStore.clearSession();
    }
    return next('/login');
  }
  
  // Check if route requires authentication
  // Setup check disabled - proceed with auth checks
  if (true) {
    // Forbidden routes: /dashboard, /dashboard/*, /instructor-dashboard
    if (!isAuth && (to.path.startsWith('/dashboard') || to.path === '/instructor-dashboard')) {
      return next('/login');
    }
    
    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuth) {
      return next('/login');
    }
  } else {
    // Setup check disabled - proceed normally
    // Just allow access to public routes
  }
  
  // Continue to route middleware
  // Setup Wizard disabled - no setup check needed
  continueToRouteMiddleware();

  // Self-healing: Check for 404 routes (only in development, not in tests)
  if (to.matched.length === 0 && to.path !== '/' && !window.Cypress) {
    // Route not found - trigger self-healing (non-blocking)
    try {
      import('../utils/selfHealBrowser').then(({ handle404Route }) => {
        handle404Route(to.path).catch(() => {
          // Silently ignore errors
        });
      }).catch(() => {
        // Silently ignore
      });
    } catch (error) {
      // Silently ignore
    }
  }

  function continueToRouteMiddleware() {
    const middlewares = to.matched
      .flatMap((record) => record.meta.middleware || [])
      .filter(Boolean);

    if (middlewares.length === 0) {
      return next();
    }

    // Execute middlewares sequentially
    let index = 0;
    const nextMiddleware = () => {
      if (index >= middlewares.length) {
        return next();
      }

      const middleware = middlewares[index++];
      middleware(to, from, (result) => {
        if (result === false) {
          return next(false);
        }
        if (typeof result === 'object') {
          return next(result);
        }
        nextMiddleware();
      });
    };

    nextMiddleware();
  }
});

export default router;
