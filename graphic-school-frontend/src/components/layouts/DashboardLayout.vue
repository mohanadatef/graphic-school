<template>
  <div class="min-h-screen flex bg-slate-100">
    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col">
      <div class="px-6 py-5 border-b border-slate-200">
        <p class="text-xs uppercase tracking-widest text-slate-400">Graphic School</p>
        <p class="text-base font-semibold text-slate-800">لوحة التحكم</p>
      </div>
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm">
        <div v-if="auth.isAdmin.value">
          <p class="text-xs text-slate-400 mb-2">الإدارة</p>
          <RouterLink v-for="item in adminLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ item.label }}
          </RouterLink>
        </div>
        <div v-if="auth.isInstructor.value">
          <p class="text-xs text-slate-400 mb-2">المدرب</p>
          <RouterLink v-for="item in instructorLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ item.label }}
          </RouterLink>
        </div>
        <div v-if="auth.isStudent.value">
          <p class="text-xs text-slate-400 mb-2">الطالب</p>
          <RouterLink v-for="item in studentLinks" :key="item.to" :to="item.to" class="nav-link">
            {{ item.label }}
          </RouterLink>
        </div>
      </nav>
      <RouterLink
        to="/"
        class="mx-4 mb-2 px-4 py-2 text-sm text-primary border border-primary/30 rounded-md text-center hover:bg-primary/5"
      >
        الذهاب إلى الموقع
      </RouterLink>
      <button class="m-4 px-4 py-2 bg-slate-900 text-white rounded-md text-sm" @click="logoutAndGo">
        تسجيل الخروج
      </button>
    </aside>

    <div class="flex-1">
      <header class="bg-white border-b border-slate-200 px-4 py-4 flex items-center justify-between sticky top-0 z-20">
        <div>
          <p class="text-sm text-slate-400">مرحباً</p>
          <p class="text-lg font-semibold text-slate-800">{{ auth.state.user?.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <span class="px-3 py-1 text-xs rounded-full bg-slate-100 text-slate-600">
            {{ auth.roleName?.value }}
          </span>
          <RouterLink
            to="/"
            class="hidden md:inline-flex px-3 py-2 text-sm border border-slate-200 rounded-md text-slate-600 hover:text-primary"
          >
            عرض الموقع
          </RouterLink>
          <button class="md:hidden px-3 py-2 bg-slate-900 text-white rounded-md" @click="logoutAndGo">
            خروج
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

const auth = useAuth();

const router = useRouter();

const adminLinks = [
  { label: 'التقارير', to: '/dashboard/admin' },
  { label: 'المستخدمون', to: '/dashboard/admin/users' },
  { label: 'الأدوار و الصلاحيات', to: '/dashboard/admin/roles' },
  { label: 'التصنيفات', to: '/dashboard/admin/categories' },
  { label: 'الكورسات', to: '/dashboard/admin/courses' },
  { label: 'الجلسات', to: '/dashboard/admin/sessions' },
  { label: 'التسجيلات', to: '/dashboard/admin/enrollments' },
  { label: 'الحضور', to: '/dashboard/admin/attendance' },
  { label: 'السلايدر', to: '/dashboard/admin/sliders' },
  { label: 'الإعدادات', to: '/dashboard/admin/settings' },
  { label: 'الرسائل', to: '/dashboard/admin/contacts' },
];

const instructorLinks = [
  { label: 'كورساتي', to: '/dashboard/instructor/courses' },
  { label: 'الجلسات', to: '/dashboard/instructor/sessions' },
  { label: 'الحضور', to: '/dashboard/instructor/attendance' },
  { label: 'الملاحظات', to: '/dashboard/instructor/notes' },
];

const studentLinks = [
  { label: 'كورساتي', to: '/dashboard/student/courses' },
  { label: 'الجدول', to: '/dashboard/student/sessions' },
  { label: 'الحضور', to: '/dashboard/student/attendance' },
  { label: 'ملفي الشخصي', to: '/dashboard/student/profile' },
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

