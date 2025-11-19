<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">الأدوار والصلاحيات</h2>
        <p class="text-sm text-slate-500">حدد صلاحيات لوحة التحكم لكل دور.</p>
      </div>
      <button class="px-4 py-2 bg-primary text-white rounded-md" @click="openModal()">دور جديد</button>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
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
            <button class="text-primary" @click="openModal(role)">تعديل</button>
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

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-lg">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-xl font-semibold text-slate-900">{{ editing ? 'تعديل الدور' : 'دور جديد' }}</h3>
        <div>
          <label class="label">اسم الدور</label>
          <input v-model="form.name" required class="input" />
        </div>
        <div>
          <label class="label">الوصف</label>
          <textarea v-model="form.description" rows="3" class="input"></textarea>
        </div>
        <div>
          <label class="label">الصلاحيات</label>
          <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-3 space-y-2">
            <label v-for="permission in permissions" :key="permission" class="flex items-center gap-2 text-sm">
              <input
                type="checkbox"
                :value="permission"
                v-model="form.permissions"
                :disabled="currentRoleIsSystem"
              />
              <span>{{ permission }}</span>
            </label>
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">حفظ</button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import api from '../../../api';

const roles = ref([]);
const dialogRef = ref(null);
const editing = ref(false);
const form = reactive({
  id: null,
  name: '',
  description: '',
  permissions: [],
});

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

async function loadRoles() {
  const { data } = await api.get('/admin/roles');
  roles.value = data;
}

const currentRoleIsSystem = computed(() => {
  if (!editing.value || !form.id) return false;
  return roles.value.find((role) => role.id === form.id)?.is_system ?? false;
});

function openModal(role) {
  editing.value = Boolean(role);
  form.id = role?.id || null;
  form.name = role?.name || '';
  form.description = role?.description || '';
  form.permissions = role?.permissions?.map((p) => p.slug) || [];
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

async function submit() {
  const payload = {
    name: form.name,
    description: form.description,
    permissions: form.permissions,
  };
  if (editing.value) {
    await api.put(`/admin/roles/${form.id}`, payload);
  } else {
    await api.post('/admin/roles', payload);
  }
  closeModal();
  loadRoles();
}

async function remove(role) {
  if (role.is_system || !confirm('حذف هذا الدور؟')) {
    return;
  }
  await api.delete(`/admin/roles/${role.id}`);
  loadRoles();
}

onMounted(loadRoles);
</script>

