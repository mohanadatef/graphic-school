<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold">التسجيلات والمدفوعات</h2>
        <p class="text-sm text-slate-500">قبول الطلبات وتحديث حالة الدفع.</p>
      </div>
      <button class="px-4 py-2 border rounded-md" @click="loadEnrollments">تحديث</button>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400">جاري التحميل...</div>
    <div v-else-if="!enrollments.length" class="text-center py-12 text-slate-400">لا توجد تسجيلات.</div>
    <div v-else class="bg-white border border-slate-100 rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">الطالب</th>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">الدفع</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">حضور</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="enrollment in enrollments" :key="enrollment.id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ enrollment.student?.name || 'غير محدد' }}</td>
            <td class="px-4 py-3">{{ enrollment.course?.title || 'غير محدد' }}</td>
            <td class="px-4 py-3">{{ paymentLabels[enrollment.payment_status] || enrollment.payment_status }}</td>
            <td class="px-4 py-3">{{ statusLabels[enrollment.status] || enrollment.status }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full text-xs" :class="enrollment.can_attend ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                {{ enrollment.can_attend ? 'مسموح' : 'موقوف' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <RouterLink
                :to="`/dashboard/admin/enrollments/${enrollment.id}/edit`"
                class="text-primary hover:underline"
              >
                تعديل
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const enrollments = ref([]);
const loading = ref(false);

const paymentLabels = {
  not_paid: 'لم يدفع',
  partial: 'دفع جزئي',
  paid: 'مدفوع',
};

const statusLabels = {
  pending: 'معلق',
  approved: 'مقبول',
  rejected: 'مرفوض',
};

const { get } = useApi();
const toast = useToast();

async function loadEnrollments() {
  try {
    loading.value = true;
    // Ensure per_page is within the allowed range (max 100)
    const perPage = Math.min(100, 50); // Default to 50, max 100
    const response = await get('/admin/enrollments', {
      params: {
        per_page: perPage,
      },
    });
    
    console.log('Raw API response:', response);
    
    // Handle paginated response from unified format
    // ApiResponse::paginated returns { success, message, data: [...], meta: { pagination: {...} } }
    // The interceptor extracts data, so response should be the array directly
    if (Array.isArray(response)) {
      enrollments.value = response;
    } else if (response && typeof response === 'object') {
      // Check if response has a data property that is an array
      if (Array.isArray(response.data)) {
        enrollments.value = response.data;
      } else if (response.data && typeof response.data === 'object' && Array.isArray(response.data.data)) {
        // Nested data structure (shouldn't happen but handle it)
        enrollments.value = response.data.data;
      } else {
        // Try to extract array from response object
        enrollments.value = [];
      }
    } else {
      enrollments.value = [];
    }
    
    console.log('Enrollments after processing:', enrollments.value);
    console.log('Enrollments count:', enrollments.value.length);
  } catch (err) {
    console.error('Error loading enrollments:', err);
    console.error('Error response:', err.response);
    enrollments.value = [];
    const errorMessage = err.response?.data?.message || err.message || 'حدث خطأ أثناء تحميل التسجيلات';
    toast.error(errorMessage);
  } finally {
    loading.value = false;
  }
}


onMounted(loadEnrollments);
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

