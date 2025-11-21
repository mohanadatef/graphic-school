<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">تعديل الجلسة</h2>
        <p class="text-sm text-slate-500">تعديل بيانات الجلسة</p>
      </div>
      <RouterLink
        to="/dashboard/admin/sessions"
        class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
      >
        رجوع
      </RouterLink>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="label">العنوان</label>
          <input v-model="form.title" class="input" />
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">التاريخ</label>
            <input v-model="form.session_date" type="date" class="input" />
          </div>
          <div>
            <label class="label">الحالة</label>
            <select v-model="form.status" class="input">
              <option value="scheduled">مجدولة</option>
              <option value="completed">منتهية</option>
              <option value="cancelled">ملغاة</option>
            </select>
          </div>
        </div>
        <div>
          <label class="label">ملاحظة</label>
          <textarea v-model="form.note" rows="3" class="input"></textarea>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
          <RouterLink
            to="/dashboard/admin/sessions"
            class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
          >
            إلغاء
          </RouterLink>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md" :disabled="loading">
            {{ loading ? 'جاري الحفظ...' : 'حفظ' }}
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
const { get, put } = useApi();

const loading = ref(false);
const form = reactive({
  title: '',
  session_date: '',
  status: 'scheduled',
  note: '',
});

onMounted(async () => {
  if (route.params.id) {
    await loadSession(route.params.id);
  } else {
    router.push('/dashboard/admin/sessions');
  }
});

async function loadSession(id) {
  try {
    loading.value = true;
    const data = await get(`/admin/sessions/${id}`);
    const session = Array.isArray(data) ? data[0] : (data.data || data);
    form.title = session.title || '';
    form.session_date = session.session_date || '';
    form.status = session.status || 'scheduled';
    form.note = session.note || '';
  } catch (err) {
    console.error('Error loading session:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات الجلسة');
    router.push('/dashboard/admin/sessions');
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    loading.value = true;
    await put(`/admin/sessions/${route.params.id}`, {
      title: form.title,
      session_date: form.session_date,
      status: form.status,
      note: form.note,
    });
    toast.success('تم تحديث الجلسة بنجاح');
    router.push('/dashboard/admin/sessions');
  } catch (err) {
    console.error('Error saving session:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء الحفظ');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

