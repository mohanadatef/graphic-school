<template>
  <div class="min-h-screen flex bg-slate-100 dark:bg-slate-900 relative overflow-hidden">
    <aside class="w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 hidden md:flex flex-col shadow-sm">
      <div class="px-6 py-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-900">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-gradient-to-br from-primary to-primary/80 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400 font-semibold">Graphic School</p>
            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $t('dashboard.title') }}</p>
          </div>
        </div>
      </div>
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm" aria-label="Main navigation">
        <div v-if="authStore.isAdmin">
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3 px-2">
            {{ $t('admin.section') || 'Admin Section' }}
          </p>
          <RouterLink
            v-for="item in adminLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link group"
          >
            <span class="nav-link-icon">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="item.icon === 'dashboard'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                <path v-else-if="item.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                <path v-else-if="item.icon === 'roles'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                <path v-else-if="item.icon === 'courses'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                <path v-else-if="item.icon === 'sessions'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else-if="item.icon === 'enrollments'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                <path v-else-if="item.icon === 'attendance'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else-if="item.icon === 'sliders'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                <path v-else-if="item.icon === 'settings'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path v-else-if="item.icon === 'contacts'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </span>
            <span class="nav-link-text">{{ $t(item.labelKey) || item.labelKey }}</span>
          </RouterLink>
        </div>
        <div v-if="authStore.isInstructor">
          <p class="text-xs text-slate-400 mb-2">{{ $t('instructor.section') || 'Instructor Section' }}</p>
          <RouterLink
            v-for="item in instructorLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link"
          >
            {{ $t(item.labelKey) || item.labelKey }}
          </RouterLink>
        </div>
        <div v-if="authStore.isStudent">
          <p class="text-xs text-slate-400 mb-2">{{ $t('student.section') || 'Student Section' }}</p>
          <RouterLink
            v-for="item in studentLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link"
          >
            {{ $t(item.labelKey) || item.labelKey }}
          </RouterLink>
        </div>
        
        <!-- Show message if no role detected -->
        <div v-if="!authStore.isAdmin && !authStore.isInstructor && !authStore.isStudent && authStore.isAuthenticated" class="text-xs text-yellow-600 p-2 bg-yellow-50 rounded">
          <p>No role detected. Current role: {{ authStore.roleName || 'Unknown' }}</p>
          <p>User: {{ JSON.stringify(authStore.user) }}</p>
        </div>
      </nav>
      <div class="px-4 pb-4 space-y-2 border-t border-slate-200 pt-4">
        <RouterLink
          to="/"
          class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-primary border border-primary/30 dark:border-primary/50 rounded-lg hover:bg-primary/5 dark:hover:bg-primary/10 transition-all duration-200 hover:border-primary/50"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ $t('nav.goToSite') }}
        </RouterLink>
        <button
          class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-slate-700 text-white rounded-lg text-sm font-medium hover:bg-slate-800 dark:hover:bg-slate-600 transition-all duration-200 hover:shadow-lg"
          @click="handleLogout"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          {{ $t('auth.logout') }}
        </button>
      </div>
    </aside>

    <div class="flex-1">
      <header
        class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between sticky top-0 z-20 relative shadow-sm"
      >
        <div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ $t('dashboard.welcome') }}</p>
          <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ authStore.user?.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <ThemeToggle />
          <LanguagePicker />
          <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-gradient-to-r from-primary/10 to-primary/5 dark:from-primary/20 dark:to-primary/10 text-primary border border-primary/20 dark:border-primary/30">
            {{ authStore.roleName }}
          </span>
          <RouterLink
            to="/"
            class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-primary/30 hover:text-primary transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $t('nav.goToSite') }}
          </RouterLink>
          <button
            class="md:hidden px-4 py-2 bg-slate-900 dark:bg-slate-700 text-white rounded-lg hover:bg-slate-800 dark:hover:bg-slate-600 transition-colors font-medium text-sm"
            @click="handleLogout"
          >
            {{ $t('auth.logout') }}
          </button>
        </div>
      </header>
      <main class="p-4 md:p-8 bg-slate-50 dark:bg-slate-900">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';
import LanguagePicker from '../common/LanguagePicker.vue';
import ThemeToggle from '../common/ThemeToggle.vue';
import { useI18n } from '../../composables/useI18n';

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();

// Debug: Log auth state on mount
onMounted(() => {
  console.log('DashboardLayout mounted');
  console.log('Auth store state:', {
    isAuthenticated: authStore.isAuthenticated,
    roleName: authStore.roleName,
    isAdmin: authStore.isAdmin,
    isInstructor: authStore.isInstructor,
    isStudent: authStore.isStudent,
    user: authStore.user,
  });
});

const adminLinks = computed(() => [
  { labelKey: 'admin.dashboard', to: '/dashboard/admin', icon: 'dashboard' },
  { labelKey: 'admin.users', to: '/dashboard/admin/users', icon: 'users' },
  { labelKey: 'admin.roles', to: '/dashboard/admin/roles', icon: 'roles' },
  { labelKey: 'admin.categories', to: '/dashboard/admin/categories', icon: 'courses' },
  { labelKey: 'admin.courses', to: '/dashboard/admin/courses', icon: 'courses' },
  { labelKey: 'admin.sessions', to: '/dashboard/admin/sessions', icon: 'sessions' },
  { labelKey: 'admin.enrollments', to: '/dashboard/admin/enrollments', icon: 'enrollments' },
  { labelKey: 'admin.attendance', to: '/dashboard/admin/attendance', icon: 'attendance' },
  { labelKey: 'admin.sliders', to: '/dashboard/admin/sliders', icon: 'sliders' },
  { labelKey: 'admin.settings', to: '/dashboard/admin/settings', icon: 'settings' },
  { labelKey: 'admin.contacts', to: '/dashboard/admin/contacts', icon: 'contacts' },
]);

const instructorLinks = computed(() => [
  { labelKey: 'instructor.myCourses', to: '/dashboard/instructor/courses' },
  { labelKey: 'instructor.sessions', to: '/dashboard/instructor/sessions' },
  { labelKey: 'instructor.attendance', to: '/dashboard/instructor/attendance' },
  { labelKey: 'instructor.notes', to: '/dashboard/instructor/notes' },
]);

const studentLinks = computed(() => [
  { labelKey: 'student.myCourses', to: '/dashboard/student/courses' },
  { labelKey: 'student.schedule', to: '/dashboard/student/sessions' },
  { labelKey: 'student.attendance', to: '/dashboard/student/attendance' },
  { labelKey: 'student.profile', to: '/dashboard/student/profile' },
]);

async function handleLogout() {
  try {
    await authStore.logout();
    toast.success(t('auth.logoutSuccess'));
    router.push('/');
  } catch (error) {
    // Error handled in store
  }
}
</script>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.625rem 0.875rem;
  border-radius: 0.625rem;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s ease;
  margin-bottom: 0.25rem;
  position: relative;
  font-weight: 500;
}

.dark .nav-link {
  color: #94a3b8;
}

.nav-link-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease;
}

.nav-link:hover .nav-link-icon {
  transform: scale(1.1);
}

.nav-link.router-link-active {
  background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(29, 78, 216, 0.3);
}

.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 60%;
  background: white;
  border-radius: 2px 0 0 2px;
}

.nav-link:hover:not(.router-link-active) {
  background: #f1f5f9;
  color: #1e293b;
  transform: translateX(2px);
}

.dark .nav-link:hover:not(.router-link-active) {
  background: #1e293b;
  color: #e2e8f0;
}

.nav-link-text {
  flex: 1;
}
</style>
