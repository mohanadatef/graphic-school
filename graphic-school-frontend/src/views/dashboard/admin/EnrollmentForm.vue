<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">تعديل التسجيل</h2>
        <p class="text-sm text-slate-500">تعديل بيانات التسجيل</p>
      </div>
      <RouterLink
        to="/dashboard/admin/enrollments"
        class="px-4 py-2 border rounded-md hover:bg-slate-50 transition-colors"
      >
        رجوع
      </RouterLink>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-6">
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">حالة الطلب</label>
            <select v-model="form.status" class="input">
              <option value="pending">معلق</option>
              <option value="approved">مقبول</option>
              <option value="rejected">مرفوض</option>
              <option value="cancelled">ملغي</option>
            </select>
          </div>
          <div>
            <label class="label">السماح بالحضور</label>
            <select v-model="form.can_attend" class="input">
              <option :value="true">نعم</option>
              <option :value="false">لا</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t">
          <RouterLink
            to="/dashboard/admin/enrollments"
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
  status: 'pending',
  can_attend: false,
  note: '',
});

onMounted(async () => {
  if (route.params.id) {
    await loadEnrollment(route.params.id);
  } else {
    router.push('/dashboard/admin/enrollments');
  }
});

async function loadEnrollment(id) {
  try {
    loading.value = true;
    const data = await get(`/admin/enrollments/${id}`);
    const enrollment = Array.isArray(data) ? data[0] : (data.data || data);
    form.status = enrollment.status || 'pending';
    form.can_attend = enrollment.can_attend ?? false;
    form.note = enrollment.note || '';
  } catch (err) {
    console.error('Error loading enrollment:', err);
    toast.error('حدث خطأ أثناء تحميل بيانات التسجيل');
    router.push('/dashboard/admin/enrollments');
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    loading.value = true;
    await put(`/admin/enrollments/${route.params.id}`, {
      status: form.status,
      can_attend: form.can_attend,
      note: form.note,
    });
    toast.success('تم تحديث التسجيل بنجاح');
    router.push('/dashboard/admin/enrollments');
  } catch (err) {
    console.error('Error updating enrollment:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء التحديث');
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
  margin-bottom: 0.15rem;
}
</style>

