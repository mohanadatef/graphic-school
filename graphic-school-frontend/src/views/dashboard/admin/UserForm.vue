<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ editing ? 'تعديل مستخدم' : 'مستخدم جديد' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ editing ? 'تعديل بيانات المستخدم' : 'إضافة مستخدم جديد' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/users"
        class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200 font-medium text-slate-700 dark:text-slate-300"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        رجوع
      </RouterLink>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="label">الاسم</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input-enhanced"
              placeholder="أدخل اسم المستخدم"
            />
          </div>
          <div class="space-y-2">
            <label class="label">الإيميل</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input-enhanced"
              placeholder="example@email.com"
            />
          </div>
          <div class="space-y-2">
            <label class="label">الدور</label>
            <select v-model="form.role_id" class="input-enhanced" required>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="label">الحالة</label>
            <select v-model="form.is_active" class="input-enhanced">
              <option :value="true">مفعل</option>
              <option :value="false">موقوف</option>
            </select>
          </div>
          <div class="md:col-span-2 space-y-2" v-if="!editing">
            <label class="label">كلمة المرور</label>
            <input
              v-model="form.password"
              type="password"
              :required="!editing"
              class="input-enhanced"
              placeholder="أدخل كلمة المرور"
            />
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
          <RouterLink
            to="/dashboard/admin/users"
            class="btn-secondary"
          >
            إلغاء
          </RouterLink>
          <button type="submit" class="btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner mr-2"></span>
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
import { reactive, ref, onMounted } from 'vue';
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
  email: '',
  password: '',
  role_id: null,
  is_active: true,
});

onMounted(async () => {
  await loadRoles();
  
  if (route.params.id) {
    editing.value = true;
    await loadUser(route.params.id);
  }
});

async function loadRoles() {
  try {
    const data = await get('/admin/roles');
    roles.value = Array.isArray(data) ? data : (data.data || []);
    if (!form.role_id && roles.value.length) {
      form.role_id = roles.value[0].id;
    }
  } catch (err) {
    console.error('Error loading roles:', err);
    toast.error('حدث خطأ أثناء تحميل الأدوار');
  }
}

async function loadUser(id) {
  try {
    loading.value = true;
    const data = await get(`/admin/users/${id}`);
    const user = Array.isArray(data) ? data[0] : (data.data || data);
    form.name = user.name || '';
    form.email = user.email || '';
    form.role_id = user.role_id || roles.value[0]?.id || null;
    form.is_active = user.is_active ?? true;
  } catch (err) {
    console.error('Error loading user:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات المستخدم');
    router.push('/dashboard/admin/users');
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    loading.value = true;
    const payload = {
      name: form.name,
      email: form.email,
      role_id: form.role_id,
      is_active: form.is_active,
    };
    
    if (!editing.value) {
      payload.password = form.password;
    }
    
    if (editing.value) {
      await put(`/admin/users/${route.params.id}`, payload);
      toast.success('تم تحديث المستخدم بنجاح');
    } else {
      await post('/admin/users', payload);
      toast.success('تم إنشاء المستخدم بنجاح');
    }
    
    router.push('/dashboard/admin/users');
  } catch (err) {
    console.error('Error saving user:', err);
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
  color: #475569;
  margin-bottom: 0.5rem;
}
</style>

