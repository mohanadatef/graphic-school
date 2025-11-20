<template>
  <div class="min-h-screen flex bg-slate-100 relative overflow-hidden">
    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col">
      <div class="px-6 py-5 border-b border-slate-200">
        <p class="text-xs uppercase tracking-widest text-slate-400">Graphic School</p>
        <p class="text-base font-semibold text-slate-800">{{ $t('dashboard.title') }}</p>
      </div>
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm" aria-label="Main navigation">
        <div v-if="authStore.isAdmin">
          <p class="text-xs text-slate-400 mb-2">{{ $t('admin.section') }}</p>
          <RouterLink
            v-for="item in adminLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link"
          >
            {{ $t(item.labelKey) }}
          </RouterLink>
        </div>
        <div v-if="authStore.isInstructor">
          <p class="text-xs text-slate-400 mb-2">{{ $t('instructor.section') }}</p>
          <RouterLink
            v-for="item in instructorLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link"
          >
            {{ $t(item.labelKey) }}
          </RouterLink>
        </div>
        <div v-if="authStore.isStudent">
          <p class="text-xs text-slate-400 mb-2">{{ $t('student.section') }}</p>
          <RouterLink
            v-for="item in studentLinks"
            :key="item.to"
            :to="item.to"
            class="nav-link"
          >
            {{ $t(item.labelKey) }}
          </RouterLink>
        </div>
      </nav>
      <RouterLink
        to="/"
        class="mx-4 mb-2 px-4 py-2 text-sm text-primary border border-primary/30 rounded-md text-center hover:bg-primary/5 transition-colors"
      >
        {{ $t('nav.goToSite') }}
      </RouterLink>
      <button
        class="m-4 px-4 py-2 bg-slate-900 text-white rounded-md text-sm hover:bg-slate-800 transition-colors"
        @click="handleLogout"
      >
        {{ $t('auth.logout') }}
      </button>
    </aside>

    <div class="flex-1">
      <header
        class="bg-white border-b border-slate-200 px-4 py-4 flex items-center justify-between sticky top-0 z-20 relative"
      >
        <div>
          <p class="text-sm text-slate-400">{{ $t('dashboard.welcome') }}</p>
          <p class="text-lg font-semibold text-slate-800">{{ authStore.user?.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <LanguagePicker />
          <span class="px-3 py-1 text-xs rounded-full bg-slate-100 text-slate-600">
            {{ authStore.roleName }}
          </span>
          <RouterLink
            to="/"
            class="hidden md:inline-flex px-3 py-2 text-sm border border-slate-200 rounded-md text-slate-600 hover:text-primary transition-colors"
          >
            {{ $t('nav.goToSite') }}
          </RouterLink>
          <button
            class="md:hidden px-3 py-2 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors"
            @click="handleLogout"
          >
            {{ $t('auth.logout') }}
          </button>
        </div>
      </header>
      <main class="p-4 md:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';
import LanguagePicker from '../common/LanguagePicker.vue';
import { useI18n } from '../../composables/useI18n';

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();

const adminLinks = computed(() => [
  { labelKey: 'admin.dashboard', to: '/dashboard/admin' },
  { labelKey: 'admin.users', to: '/dashboard/admin/users' },
  { labelKey: 'admin.roles', to: '/dashboard/admin/roles' },
  { labelKey: 'admin.categories', to: '/dashboard/admin/categories' },
  { labelKey: 'admin.courses', to: '/dashboard/admin/courses' },
  { labelKey: 'admin.sessions', to: '/dashboard/admin/sessions' },
  { labelKey: 'admin.enrollments', to: '/dashboard/admin/enrollments' },
  { labelKey: 'admin.attendance', to: '/dashboard/admin/attendance' },
  { labelKey: 'admin.sliders', to: '/dashboard/admin/sliders' },
  { labelKey: 'admin.settings', to: '/dashboard/admin/settings' },
  { labelKey: 'admin.contacts', to: '/dashboard/admin/contacts' },
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
  display: block;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  color: #475569;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
  margin-bottom: 0.25rem;
}

.nav-link.router-link-active {
  background: #1d4ed8;
  color: white;
}

.nav-link:hover:not(.router-link-active) {
  background: #e2e8f0;
  color: #0f172a;
}
</style>
