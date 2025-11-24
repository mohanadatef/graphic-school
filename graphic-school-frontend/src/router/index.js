import { createRouter, createWebHistory } from 'vue-router';
import { authMiddleware, guestMiddleware, roleMiddleware, setupCheckMiddleware } from '../middleware';

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
    path: 'programs',
    name: 'public-programs',
    component: () => import('../views/public/PublicPrograms.vue'),
  },
  {
    path: 'programs/:slug',
    name: 'public-programs-details',
    component: () => import('../views/public/PublicProgramDetails.vue'),
    props: true,
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
    path: 'setup',
    name: 'setup',
    component: () => import('../views/public/SetupWizard.vue'),
    meta: { middleware: [] }, // No auth required for setup
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
    path: 'admin/reports',
    name: 'admin-reports',
    component: () => import('../views/dashboard/admin/ReportsPage.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/strategic-reports',
    name: 'admin-strategic-reports',
    component: () => import('../views/dashboard/admin/StrategicReportsPage.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/categories',
    name: 'admin-categories',
    component: () => import('../views/dashboard/admin/AdminCategories.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/categories/new',
    name: 'admin-categories-new',
    component: () => import('../views/dashboard/admin/CategoryForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/categories/:id/edit',
    name: 'admin-categories-edit',
    component: () => import('../views/dashboard/admin/CategoryForm.vue'),
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
    path: 'admin/sliders',
    name: 'admin-sliders',
    component: () => import('../views/dashboard/admin/AdminSliders.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/sliders/new',
    name: 'admin-sliders-new',
    component: () => import('../views/dashboard/admin/SliderForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/sliders/:id/edit',
    name: 'admin-sliders-edit',
    component: () => import('../views/dashboard/admin/SliderForm.vue'),
    props: true,
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
  {
    path: 'admin/branding',
    name: 'admin-branding',
    component: () => import('../views/dashboard/admin/BrandingEditor.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/contacts',
    name: 'admin-contacts',
    component: () => import('../views/dashboard/admin/AdminContacts.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/translations',
    name: 'admin-translations',
    component: () => import('../views/dashboard/admin/AdminTranslations.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/translations/new',
    name: 'admin-translations-new',
    component: () => import('../views/dashboard/admin/TranslationForm.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/translations/:id/edit',
    name: 'admin-translations-edit',
    component: () => import('../views/dashboard/admin/TranslationForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/payments',
    name: 'admin-payments',
    component: () => import('../views/dashboard/admin/AdminPayments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/tickets',
    name: 'admin-tickets',
    component: () => import('../views/dashboard/admin/AdminTickets.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/audit-logs',
    name: 'admin-audit-logs',
    component: () => import('../views/dashboard/admin/AdminAuditLogs.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/media',
    name: 'admin-media',
    component: () => import('../views/dashboard/admin/AdminMedia.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/faqs',
    name: 'admin-faqs',
    component: () => import('../views/dashboard/admin/AdminFAQs.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  // Phase 2: Programs, Batches, Groups
  {
    path: 'admin/programs',
    name: 'admin-programs',
    component: () => import('../views/dashboard/admin/AdminPrograms.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/programs/new',
    name: 'admin-programs-new',
    component: () => import('../views/dashboard/admin/AdminProgramCreate.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/programs/:id/edit',
    name: 'admin-programs-edit',
    component: () => import('../views/dashboard/admin/AdminProgramEdit.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/batches',
    name: 'admin-batches',
    component: () => import('../views/dashboard/admin/AdminBatches.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/programs/:programId/batches',
    name: 'admin-programs-batches',
    component: () => import('../views/dashboard/admin/AdminBatches.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
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
    path: 'admin/batches/:batchId/groups',
    name: 'admin-batches-groups',
    component: () => import('../views/dashboard/admin/AdminGroups.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/batches/new',
    name: 'admin-batches-new',
    component: () => import('../views/dashboard/admin/AdminBatchCreate.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/batches/:id/edit',
    name: 'admin-batches-edit',
    component: () => import('../views/dashboard/admin/AdminBatchEdit.vue'),
    props: true,
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
    path: 'admin/language',
    name: 'admin-language',
    component: () => import('../views/dashboard/admin/AdminLanguages.vue'),
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
    path: 'admin/enrollments/:id',
    name: 'admin-enrollment-review',
    component: () => import('../views/dashboard/admin/AdminEnrollmentReview.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/invoices',
    name: 'admin-invoices',
    component: () => import('../views/dashboard/admin/AdminInvoices.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/invoices/:id',
    name: 'admin-invoice-view',
    component: () => import('../views/dashboard/admin/AdminInvoiceView.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/attendance',
    name: 'admin-attendance-overview',
    component: () => import('../views/dashboard/admin/AdminAttendanceOverview.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/certificates',
    name: 'admin-certificates',
    component: () => import('../views/dashboard/admin/AdminCertificates.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/certificates/issue/:enrollmentId',
    name: 'admin-certificate-issue',
    component: () => import('../views/dashboard/admin/CertificateIssueForm.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  // Phase 4: QR Attendance, Assignments, Calendar, Gradebook
  {
    path: 'admin/attendance/qr',
    name: 'admin-attendance-qr',
    component: () => import('../views/dashboard/admin/AdminAttendanceQR.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/assignments',
    name: 'admin-assignments',
    component: () => import('../views/dashboard/admin/AdminAssignmentsOverview.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/gradebook',
    name: 'admin-gradebook',
    component: () => import('../views/dashboard/admin/AdminGradebookOverview.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/calendar',
    name: 'admin-calendar',
    component: () => import('../views/dashboard/admin/AdminCalendar.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  // Phase 5.2: Community Management
  {
    path: 'admin/community/posts',
    name: 'admin-community-posts',
    component: () => import('../views/dashboard/admin/AdminCommunityPosts.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/community/reports',
    name: 'admin-community-reports',
    component: () => import('../views/dashboard/admin/AdminCommunityReports.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  
  // Phase 5.1: Gamification Management
  {
    path: 'admin/gamification/rules',
    name: 'admin-gamification-rules',
    component: () => import('../views/dashboard/admin/AdminGamificationRules.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },

  // Instructor routes
  {
    path: 'instructor',
    redirect: { name: 'instructor-courses' },
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/courses',
    name: 'instructor-courses',
    component: () => import('../views/dashboard/instructor/InstructorCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/sessions',
    name: 'instructor-sessions',
    component: () => import('../views/dashboard/instructor/InstructorSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/attendance',
    name: 'instructor-attendance',
    component: () => import('../views/dashboard/instructor/InstructorAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/notes',
    name: 'instructor-notes',
    component: () => import('../views/dashboard/instructor/InstructorNotes.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/messaging',
    name: 'instructor-messaging',
    component: () => import('../views/dashboard/instructor/InstructorMessaging.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/assignments',
    name: 'instructor-assignments',
    component: () => import('../views/dashboard/instructor/InstructorAssignments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/calendar',
    name: 'instructor-calendar',
    component: () => import('../views/dashboard/instructor/InstructorCalendar.vue'),
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
    component: () => import('../views/dashboard/instructor/InstructorGroupSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  // Phase 3: Instructor Attendance
  {
    path: 'instructor/attendance',
    name: 'instructor-attendance',
    component: () => import('../views/dashboard/instructor/InstructorAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  {
    path: 'instructor/sessions/:id/attendance',
    name: 'instructor-session-attendance',
    component: () => import('../views/dashboard/instructor/InstructorSessionAttendance.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('instructor')], requiresAuth: true },
  },
  // Phase 5.1: Gamification
  {
    path: 'instructor/groups/:groupId/leaderboard',
    name: 'instructor-group-leaderboard',
    component: () => import('../views/dashboard/instructor/InstructorGroupLeaderboard.vue'),
    props: true,
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
    component: () => import('../views/dashboard/student/StudentCourses.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/sessions',
    name: 'student-sessions',
    component: () => import('../views/dashboard/student/StudentSessions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/attendance',
    name: 'student-attendance',
    component: () => import('../views/dashboard/student/StudentAttendance.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  // Phase 3: Student Enrollment, Payments, Certificates
  {
    path: 'student/enrollments',
    name: 'student-enrollments',
    component: () => import('../views/dashboard/student/StudentEnrollmentStatus.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/payments',
    name: 'student-payments',
    component: () => import('../views/dashboard/student/StudentPayments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/payments/:id',
    name: 'student-invoice-view',
    component: () => import('../views/dashboard/student/StudentInvoiceView.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/certificates',
    name: 'student-certificates',
    component: () => import('../views/dashboard/student/StudentCertificates.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  // Phase 4: QR Attendance, Assignments, Calendar, Gradebook
  {
    path: 'student/qr-scanner',
    name: 'student-qr-scanner',
    component: () => import('../views/dashboard/student/StudentQRScanner.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/assignments',
    name: 'student-assignments',
    component: () => import('../views/dashboard/student/StudentAssignments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/assignments/:id',
    name: 'student-assignment-view',
    component: () => import('../views/dashboard/student/AssignmentView.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/assignments/:id/submit',
    name: 'student-assignment-submit',
    component: () => import('../views/dashboard/student/AssignmentSubmit.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/gradebook',
    name: 'student-gradebook',
    component: () => import('../views/dashboard/student/StudentGradebook.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/calendar',
    name: 'student-calendar',
    component: () => import('../views/dashboard/student/StudentCalendar.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  // Phase 5.1: Gamification
  {
    path: 'student/gamification',
    name: 'student-gamification',
    component: () => import('../views/dashboard/student/StudentGamificationSummary.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/leaderboard',
    name: 'student-leaderboard',
    component: () => import('../views/dashboard/student/StudentLeaderboard.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  // Phase 5.2: Community
  {
    path: 'student/community',
    name: 'community-feed',
    component: () => import('../views/dashboard/student/CommunityFeed.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/community/posts/:id',
    name: 'community-post-view',
    component: () => import('../views/dashboard/student/CommunityPostView.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/community/my-posts',
    name: 'community-my-posts',
    component: () => import('../views/dashboard/student/CommunityMyPosts.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  
  // Phase 5.3: Academy Subscription Management
  {
    path: 'admin/subscription',
    redirect: '/dashboard/academy/subscription',
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'academy/subscription',
    name: 'academy-subscription',
    component: () => import('../views/dashboard/academy/SubscriptionOverview.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'academy/subscription/plans',
    name: 'academy-plan-selection',
    component: () => import('../views/dashboard/academy/PlanSelection.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'academy/subscription/usage',
    name: 'academy-usage',
    component: () => import('../views/dashboard/academy/UsageOverview.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'academy/subscription/invoices',
    name: 'academy-invoices',
    component: () => import('../views/dashboard/academy/SubscriptionInvoices.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  
  // Phase 5.3: HQ Admin Routes
  {
    path: 'hq/plans',
    name: 'hq-plans',
    component: () => import('../views/dashboard/hq/HQPlans.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('hq')], requiresAuth: true },
  },
  {
    path: 'hq/subscriptions',
    name: 'hq-subscriptions',
    component: () => import('../views/dashboard/hq/HQSubscriptions.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('hq')], requiresAuth: true },
  },
  
  // Phase 6: Page Builder
  {
    path: 'admin/page-builder',
    name: 'page-builder-pages',
    component: () => import('../views/dashboard/admin/PageBuilderPages.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'admin/page-builder/editor/:id',
    name: 'page-builder-editor',
    component: () => import('../views/dashboard/admin/PageBuilderEditor.vue'),
    props: true,
    meta: { middleware: [authMiddleware, roleMiddleware('admin')], requiresAuth: true },
  },
  {
    path: 'student/profile',
    name: 'student-profile',
    component: () => import('../views/dashboard/student/StudentProfile.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/payments',
    name: 'student-payments',
    component: () => import('../views/dashboard/student/StudentPayments.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/messaging',
    name: 'student-messaging',
    component: () => import('../views/dashboard/student/StudentMessaging.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/programs',
    name: 'student-programs',
    component: () => import('../views/dashboard/student/StudentPrograms.vue'),
    meta: { middleware: [authMiddleware, roleMiddleware('student')], requiresAuth: true },
  },
  {
    path: 'student/programs/:id',
    name: 'student-programs-details',
    component: () => import('../views/dashboard/student/StudentProgramDetails.vue'),
    props: true,
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

// Global setup check (runs first, before route-specific middleware)
router.beforeEach(async (to, from, next) => {
  // Import auth store here to avoid circular dependencies
  const { useAuthStore } = await import('../stores/auth');
  const authStore = useAuthStore();
  
  const isAuth = authStore.isAuthenticated;
  const role = authStore.roleName;
  
  // If user is authenticated and trying to access /login or /register, redirect based on role
  if (isAuth && (to.path === '/login' || to.path === '/register')) {
    if (role === 'student') {
      return next('/');
    } else if (role === 'instructor') {
      return next('/dashboard/instructor');
    } else if (role === 'admin' || role === 'super_admin') {
      return next('/dashboard/admin');
    }
    // Default fallback
    return next('/dashboard/admin');
  }
  
  // Handle /dashboard root path - redirect based on role
  if (isAuth && to.path === '/dashboard' && to.name === undefined) {
    if (role === 'instructor') {
      return next('/dashboard/instructor');
    } else if (role === 'admin' || role === 'super_admin') {
      return next('/dashboard/admin');
    }
    // For students, redirect to home
    return next('/');
  }
  
  // Check if route requires authentication
  // Forbidden routes: /dashboard, /dashboard/*, /instructor-dashboard
  if (!isAuth && (to.path.startsWith('/dashboard') || to.path === '/instructor-dashboard')) {
    return next('/login');
  }
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !isAuth) {
    return next('/login');
  }
  
  // Run setup check first
  await setupCheckMiddleware(to, from, (result) => {
    if (result === false || typeof result === 'object') {
      return next(result);
    }
    // Setup check passed, continue to route-specific middleware
    continueToRouteMiddleware();
  });

  // Self-healing: Check for 404 routes (only in development, not in tests)
  if (to.matched.length === 0 && to.path !== '/' && !to.path.startsWith('/setup') && !window.Cypress) {
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
