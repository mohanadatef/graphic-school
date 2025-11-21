<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">الأدوار والصلاحيات</h2>
        <p class="text-sm text-slate-500">حدد صلاحيات لوحة التحكم لكل دور.</p>
      </div>
      <RouterLink
        to="/dashboard/admin/roles/new"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block"
      >
        دور جديد
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400">جاري التحميل...</div>
    <div v-else-if="error" class="text-center py-12 text-red-500">{{ error }}</div>
    <div v-else class="grid md:grid-cols-2 gap-4">
      <div
        v-for="role in roles"
        :key="role.id"
        class="bg-white rounded-2xl shadow border border-slate-100 p-5 flex flex-col gap-3"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-lg font-semibold text-slate-900">{{ role.name }}</p>
            <p class="text-xs text-slate-400">{{ role.description }}</p>
          </div>
          <div class="flex gap-3 text-xs">
            <RouterLink
              :to="`/dashboard/admin/roles/${role.id}/edit`"
              class="text-primary hover:underline"
            >
              تعديل
            </RouterLink>
            <button class="text-red-500" @click="remove(role)" :disabled="role.is_system">حذف</button>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="permission in role.permissions"
            :key="permission.id"
            class="px-2 py-1 text-xs bg-slate-100 rounded-full text-slate-600"
          >
            {{ permission.slug }}
          </span>
          <p v-if="!role.permissions?.length" class="text-xs text-slate-400">لا توجد صلاحيات محددة.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';

const roles = ref([]);

const permissions = [
  'dashboard.access',
  'dashboard.reports',
  'users.manage',
  'roles.manage',
  'permissions.manage',
  'categories.manage',
  'courses.manage',
  'courses.view',
  'sessions.manage',
  'sessions.view',
  'enrollments.manage',
  'attendance.take',
  'attendance.view',
  'settings.manage',
  'contacts.manage',
  'sliders.manage',
  'testimonials.manage',
  'instructor.access',
  'student.access',
  'notes.manage',
];

const { get, delete: del, loading, error } = useApi();

async function loadRoles() {
  try {
    const data = await get('/admin/roles');
    // Handle both unified format and direct array
    if (Array.isArray(data)) {
      roles.value = data;
    } else if (data && Array.isArray(data.data)) {
      roles.value = data.data;
    } else if (data && data.data && Array.isArray(data.data)) {
      roles.value = data.data;
    } else {
      roles.value = [];
    }
    console.log('Roles loaded:', roles.value);
  } catch (err) {
    console.error('Error loading roles:', err);
    roles.value = [];
  }
}


async function remove(role) {
  if (role.is_system || !confirm('حذف هذا الدور؟')) {
    return;
  }
  try {
    await del(`/admin/roles/${role.id}`);
    await loadRoles();
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحذف');
  }
}

onMounted(loadRoles);
</script>

