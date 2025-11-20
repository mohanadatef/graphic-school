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
  },
  {
    path: 'courses',
    name: 'courses',
    component: () => import('../views/public/CoursesPage.vue'),
  },
  {
    path: 'courses/:id',
    name: 'course-details',
    component: () => import('../views/public/CourseDetailsPage.vue'),
    props: true,
  },
  {
    path: 'instructors',
    name: 'instructors',
    component: () => import('../views/public/InstructorsPage.vue'),
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
  },
  {
    path: 'contact',
    name: 'contact',
    component: () => import('../views/public/ContactPage.vue'),
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
  // Admin routes
  {
    path: 'admin',
    name: 'admin-dashboard',
    component: () => import('../views/dashboard/admin/AdminDashboard.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/users',
    name: 'admin-users',
    component: () => import('../views/dashboard/admin/AdminUsers.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/roles',
    name: 'admin-roles',
    component: () => import('../views/dashboard/admin/AdminRoles.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/categories',
    name: 'admin-categories',
    component: () => import('../views/dashboard/admin/AdminCategories.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/courses',
    name: 'admin-courses',
    component: () => import('../views/dashboard/admin/AdminCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/sessions',
    name: 'admin-sessions',
    component: () => import('../views/dashboard/admin/AdminSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/enrollments',
    name: 'admin-enrollments',
    component: () => import('../views/dashboard/admin/AdminEnrollments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/attendance',
    name: 'admin-attendance',
    component: () => import('../views/dashboard/admin/AdminAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/sliders',
    name: 'admin-sliders',
    component: () => import('../views/dashboard/admin/AdminSliders.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/settings',
    name: 'admin-settings',
    component: () => import('../views/dashboard/admin/AdminSettings.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },
  {
    path: 'admin/contacts',
    name: 'admin-contacts',
    component: () => import('../views/dashboard/admin/AdminContacts.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')] },
  },

  // Instructor routes
  {
    path: 'instructor',
    redirect: { name: 'instructor-courses' },
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')] },
  },
  {
    path: 'instructor/courses',
    name: 'instructor-courses',
    component: () => import('../views/dashboard/instructor/InstructorCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')] },
  },
  {
    path: 'instructor/sessions',
    name: 'instructor-sessions',
    component: () => import('../views/dashboard/instructor/InstructorSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')] },
  },
  {
    path: 'instructor/attendance',
    name: 'instructor-attendance',
    component: () => import('../views/dashboard/instructor/InstructorAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')] },
  },
  {
    path: 'instructor/notes',
    name: 'instructor-notes',
    component: () => import('../views/dashboard/instructor/InstructorNotes.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')] },
  },

  // Student routes
  {
    path: 'student',
    redirect: { name: 'student-courses' },
    meta: { middleware: [authMiddleware, roleMiddleware('student')] },
  },
  {
    path: 'student/courses',
    name: 'student-courses',
    component: () => import('../views/dashboard/student/StudentCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')] },
  },
  {
    path: 'student/sessions',
    name: 'student-sessions',
    component: () => import('../views/dashboard/student/StudentSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')] },
  },
  {
    path: 'student/attendance',
    name: 'student-attendance',
    component: () => import('../views/dashboard/student/StudentAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')] },
  },
  {
    path: 'student/profile',
    name: 'student-profile',
    component: () => import('../views/dashboard/student/StudentProfile.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')] },
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
      meta: { middleware: [authMiddleware] },
      children: dashboardChildren,
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      redirect: '/',
    },
  ],
});

// Middleware execution
router.beforeEach((to, from, next) => {
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
});

export default router;
