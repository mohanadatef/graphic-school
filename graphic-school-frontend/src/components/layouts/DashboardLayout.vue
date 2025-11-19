<template>
  <div class="min-h-screen flex bg-slate-100">
    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col">
      <div class="px-6 py-5 border-b border-slate-200">
        <p class="text-xs uppercase tracking-widest text-slate-400">Graphic School</p>
        <p class="text-base font-semibold text-slate-800">{{ $t('dashboard.title') }}</p>
      </div>
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm">
        <div v-if="auth.isAdmin.value">
          <p class="text-xs text-slate-400 mb-2">الإدارة</p>
          <RouterLink v-for="item in adminLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ $t(item.label) }}
          </RouterLink>
        </div>
        <div v-if="auth.isInstructor.value">
          <p class="text-xs text-slate-400 mb-2">المدرب</p>
          <RouterLink v-for="item in instructorLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ $t(item.label) }}
          </RouterLink>
        </div>
        <div v-if="auth.isStudent.value">
          <p class="text-xs text-slate-400 mb-2">الطالب</p>
          <RouterLink v-for="item in studentLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ $t(item.label) }}
          </RouterLink>
        </div>
      </nav>
      <RouterLink
        to="/"
        class="mx-4 mb-2 px-4 py-2 text-sm text-primary border border-primary/30 rounded-md text-center hover:bg-primary/5"
      >
        {{ $t('navigation.home') }}
      </RouterLink>
      <button class="m-4 px-4 py-2 bg-slate-900 text-white rounded-md text-sm" @click="logoutAndGo">
        {{ $t('auth.logout') }}
      </button>
    </aside>

    <div class="flex-1">
      <header class="bg-white border-b border-slate-200 px-4 py-4 flex items-center justify-between sticky top-0 z-20">
        <div>
          <p class="text-sm text-slate-400">{{ $t('dashboard.welcome') }}</p>
          <p class="text-lg font-semibold text-slate-800">{{ auth.state.user?.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <LanguageSwitcher />
          <span class="px-3 py-1 text-xs rounded-full bg-slate-100 text-slate-600">
            {{ auth.roleName?.value }}
          </span>
          <RouterLink
            to="/"
            class="hidden md:inline-flex px-3 py-2 text-sm border border-slate-200 rounded-md text-slate-600 hover:text-primary"
          >
            {{ $t('navigation.home') }}
          </RouterLink>
          <button class="md:hidden px-3 py-2 bg-slate-900 text-white rounded-md" @click="logoutAndGo">
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
import { RouterLink, useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';
import LanguageSwitcher from '../common/LanguageSwitcher.vue';

const auth = useAuth();

const router = useRouter();

const adminLinks = [
  { label: 'admin.dashboard', to: '/dashboard/admin' },
  { label: 'admin.users', to: '/dashboard/admin/users' },
  { label: 'admin.roles', to: '/dashboard/admin/roles' },
  { label: 'admin.categories', to: '/dashboard/admin/categories' },
  { label: 'admin.courses', to: '/dashboard/admin/courses' },
  { label: 'admin.sessions', to: '/dashboard/admin/sessions' },
  { label: 'admin.enrollments', to: '/dashboard/admin/enrollments' },
  { label: 'admin.attendance', to: '/dashboard/admin/attendance' },
  { label: 'admin.sliders', to: '/dashboard/admin/sliders' },
  { label: 'admin.settings', to: '/dashboard/admin/settings' },
  { label: 'admin.contacts', to: '/dashboard/admin/contacts' },
  { label: 'admin.translations', to: '/dashboard/admin/translations' },
];

const instructorLinks = [
  { label: 'instructor.myCourses', to: '/dashboard/instructor/courses' },
  { label: 'instructor.sessions', to: '/dashboard/instructor/sessions' },
  { label: 'instructor.attendance', to: '/dashboard/instructor/attendance' },
  { label: 'instructor.notes', to: '/dashboard/instructor/notes' },
];

const studentLinks = [
  { label: 'student.myCourses', to: '/dashboard/student/courses' },
  { label: 'student.mySessions', to: '/dashboard/student/sessions' },
  { label: 'student.attendance', to: '/dashboard/student/attendance' },
  { label: 'student.profile', to: '/dashboard/student/profile' },
];

function logoutAndGo() {
  auth.logout().finally(() => router.push('/'));
}
</script>

<style scoped>
.nav-link {
  display: block;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  color: #475569;
  text-decoration: none;
  transition: background 0.2s;
  margin-bottom: 0.25rem;
}

.nav-link.router-link-active {
  background: #1d4ed8;
  color: white;
}

.nav-link:hover {
  background: #e2e8f0;
  color: #0f172a;
}
</style>

