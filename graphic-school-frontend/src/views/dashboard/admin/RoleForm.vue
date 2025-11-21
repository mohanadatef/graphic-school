<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ editing ? 'تعديل الدور' : 'دور جديد' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ editing ? 'تعديل بيانات الدور' : 'إضافة دور جديد' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/roles"
        class="btn-secondary"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        رجوع
      </RouterLink>
    </div>

    <div v-if="loading && editing" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
      <div class="text-center py-12">
        <div class="spinner mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">جاري تحميل بيانات الدور...</p>
      </div>
    </div>
    <div v-else class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="label">اسم الدور</label>
          <input 
            v-model="form.name" 
            required 
            class="input-enhanced" 
            placeholder="مثال: مدرب مساعد"
            :disabled="loading"
          />
        </div>
        <div>
          <label class="label">الوصف</label>
          <textarea 
            v-model="form.description" 
            rows="3" 
            class="input-enhanced" 
            placeholder="وصف مختصر للدور..."
            :disabled="loading"
          ></textarea>
        </div>
        <div>
          <label class="label mb-3 block">الصلاحيات</label>
          <div class="max-h-96 overflow-y-auto border-2 border-slate-200 dark:border-slate-700 rounded-xl p-4 space-y-3 bg-slate-50 dark:bg-slate-900/50">
            <label 
              v-for="permission in permissions" 
              :key="permission" 
              class="flex items-center gap-3 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors cursor-pointer"
            >
              <input
                type="checkbox"
                :value="permission"
                v-model="form.permissions"
                :disabled="currentRoleIsSystem || loading"
                class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary focus:ring-2 cursor-pointer"
              />
              <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ permission }}</span>
            </label>
            <p v-if="form.permissions.length > 0" class="text-xs text-slate-500 dark:text-slate-400 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
              تم اختيار {{ form.permissions.length }} صلاحية
            </p>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
          <RouterLink
            to="/dashboard/admin/roles"
            class="btn-secondary"
            :class="{ 'pointer-events-none opacity-50': loading }"
          >
            إلغاء
          </RouterLink>
          <button 
            type="submit" 
            class="btn-primary" 
            :disabled="loading || !form.name.trim()"
          >
            <span v-if="loading" class="inline-flex items-center gap-2">
              <div class="spinner"></div>
              جاري الحفظ...
            </span>
            <span v-else class="inline-flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              حفظ
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const { get, post, put } = useApi();

const editing = ref(false);
const loading = ref(false);
const roles = ref([]);
const form = reactive({
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

onMounted(async () => {
  if (route.params.id) {
    editing.value = true;
    await loadRole(route.params.id);
  }
});

async function loadRole(id) {
  try {
    loading.value = true;
    const response = await get(`/admin/roles/${id}`);
    
    // Handle unified API response format
    let role;
    if (response && response.data) {
      role = response.data;
    } else if (Array.isArray(response)) {
      role = response[0];
    } else {
      role = response;
    }
    
    if (!role) {
      throw new Error('Role not found');
    }
    
    form.name = role.name || '';
    form.description = role.description || '';
    
    // Handle permissions - could be array of objects with slug or array of strings
    if (role.permissions && Array.isArray(role.permissions)) {
      form.permissions = role.permissions.map((p) => {
        if (typeof p === 'string') {
          return p;
        }
        return p.slug || p.name || p;
      });
    } else {
      form.permissions = [];
    }
  } catch (err) {
    console.error('Error loading role:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء تحميل بيانات الدور');
    router.push('/dashboard/admin/roles');
  } finally {
    loading.value = false;
  }
}

const currentRoleIsSystem = computed(() => {
  // Check if role is system role (would need to load all roles to check)
  return false; // For now, allow editing
});

async function submit() {
  try {
    loading.value = true;
    const payload = {
      name: form.name,
      description: form.description,
      permissions: form.permissions,
    };
    
    if (editing.value) {
      await put(`/admin/roles/${route.params.id}`, payload);
      toast.success('تم تحديث الدور بنجاح');
    } else {
      await post('/admin/roles', payload);
      toast.success('تم إنشاء الدور بنجاح');
    }
    
    router.push('/dashboard/admin/roles');
  } catch (err) {
    console.error('Error saving role:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء الحفظ');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.dark .label {
  color: #94a3b8;
}
</style>

