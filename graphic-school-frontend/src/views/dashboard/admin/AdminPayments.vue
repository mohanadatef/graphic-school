<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">إدارة المدفوعات</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">تتبع وإدارة جميع المدفوعات</p>
      </div>
      <div class="flex gap-2">
        <button
          @click="showReports = !showReports"
          class="btn-secondary inline-flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          التقارير
        </button>
        <button
          @click="showCreateModal = true"
          class="btn-primary inline-flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          إضافة دفعة
        </button>
      </div>
    </div>

    <!-- Reports Summary -->
    <div v-if="showReports && reports" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي المدفوع</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(reports.summary?.total_paid || 0) }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">المعلق</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ formatCurrency(reports.summary?.total_pending || 0) }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">المتبقي</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(reports.summary?.total_remaining || 0) }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">عدد المدفوعات</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ reports.summary?.payment_count || 0 }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
          <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="filters.search"
            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
            placeholder="بحث بالطالب أو الكورس..."
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.status"
          :options="statusOptions"
          placeholder="كل الحالات"
          @update:modelValue="handleFilterChange"
        />
        <input
          v-model="filters.from_date"
          type="date"
          class="input text-sm"
          placeholder="من تاريخ"
          @change="handleFilterChange"
        />
        <input
          v-model="filters.to_date"
          type="date"
          class="input text-sm"
          placeholder="إلى تاريخ"
          @change="handleFilterChange"
        />
        <FilterDropdown
          v-model.number="pagination.per_page"
          :options="[
            { id: 10, name: '10' },
            { id: 20, name: '20' },
            { id: 50, name: '50' }
          ]"
          placeholder="عدد الصفحات"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
            <tr>
              <th class="px-6 py-4 text-left">الطالب</th>
              <th class="px-6 py-4 text-left">الكورس</th>
              <th class="px-6 py-4 text-left">المبلغ</th>
              <th class="px-6 py-4 text-left">المتبقي</th>
              <th class="px-6 py-4 text-left">تاريخ الدفع</th>
              <th class="px-6 py-4 text-left">طريقة الدفع</th>
              <th class="px-6 py-4 text-left">الحالة</th>
              <th class="px-6 py-4 text-left">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
            <tr
              v-for="payment in payments"
              :key="payment.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
            >
              <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                {{ payment.student?.name || 'غير محدد' }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ payment.course?.title || 'غير محدد' }}
              </td>
              <td class="px-6 py-4 font-semibold text-green-600 dark:text-green-400">
                {{ formatCurrency(payment.amount) }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ formatCurrency(payment.remaining_amount) }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ formatDate(payment.payment_date) }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ payment.payment_method || 'غير محدد' }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(payment.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(payment.status)"></span>
                  {{ getStatusLabel(payment.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button
                    @click="editPayment(payment)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
      <p v-else-if="!payments.length" class="text-center py-6 text-sm text-slate-400">لا توجد مدفوعات.</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingPayment" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">
            {{ editingPayment ? 'تعديل دفعة' : 'إضافة دفعة جديدة' }}
          </h3>
        </div>
        <form @submit.prevent="savePayment" class="p-6 space-y-4">
          <div>
            <label class="label">التسجيل</label>
            <select v-model="formData.enrollment_id" class="input" required>
              <option value="">اختر التسجيل</option>
              <option v-for="enrollment in enrollments" :key="enrollment.id" :value="enrollment.id">
                {{ enrollment.student?.name }} - {{ enrollment.course?.title }}
              </option>
            </select>
          </div>
          <div>
            <label class="label">المبلغ</label>
            <input v-model.number="formData.amount" type="number" step="0.01" class="input" required />
          </div>
          <div>
            <label class="label">طريقة الدفع</label>
            <input v-model="formData.payment_method" type="text" class="input" placeholder="نقدي، تحويل، إلخ" />
          </div>
          <div>
            <label class="label">رقم المرجع</label>
            <input v-model="formData.payment_reference" type="text" class="input" />
          </div>
          <div>
            <label class="label">تاريخ الدفع</label>
            <input v-model="formData.payment_date" type="date" class="input" required />
          </div>
          <div>
            <label class="label">الحالة</label>
            <select v-model="formData.status" class="input">
              <option value="pending">معلق</option>
              <option value="completed">مكتمل</option>
              <option value="failed">فاشل</option>
              <option value="refunded">مسترد</option>
            </select>
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="formData.description" class="input" rows="3"></textarea>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1">حفظ</button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { get, post, put } = useApi();
const toast = useToast();
const showReports = ref(false);
const reports = ref(null);
const showCreateModal = ref(false);
const editingPayment = ref(null);
const enrollments = ref([]);

const statusOptions = [
  { id: 'pending', name: 'معلق' },
  { id: 'completed', name: 'مكتمل' },
  { id: 'failed', name: 'فاشل' },
  { id: 'refunded', name: 'مسترد' },
];

const formData = reactive({
  enrollment_id: '',
  amount: 0,
  payment_method: '',
  payment_reference: '',
  payment_date: new Date().toISOString().split('T')[0],
  status: 'completed',
  description: '',
});

const {
  items: payments,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
} = useListPage({
  endpoint: '/admin/payments',
  initialFilters: {
    search: '',
    status: '',
    from_date: '',
    to_date: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false,
});

async function loadReports() {
  try {
    const data = await get('/admin/payments/reports');
    reports.value = data;
  } catch (err) {
    console.error('Error loading reports:', err);
  }
}

async function loadEnrollments() {
  try {
    const data = await get('/admin/enrollments', { params: { per_page: 100 } });
    enrollments.value = Array.isArray(data) ? data : (data.data || []);
  } catch (err) {
    console.error('Error loading enrollments:', err);
  }
}

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP' }).format(amount || 0);
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

function getStatusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    completed: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    failed: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
    refunded: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
  };
  return classes[status] || classes.pending;
}

function getStatusDotClass(status) {
  const classes = {
    pending: 'bg-yellow-500',
    completed: 'bg-green-500',
    failed: 'bg-red-500',
    refunded: 'bg-blue-500',
  };
  return classes[status] || classes.pending;
}

function getStatusLabel(status) {
  const labels = {
    pending: 'معلق',
    completed: 'مكتمل',
    failed: 'فاشل',
    refunded: 'مسترد',
  };
  return labels[status] || status;
}

function editPayment(payment) {
  editingPayment.value = payment;
  formData.enrollment_id = payment.enrollment_id;
  formData.amount = payment.amount;
  formData.payment_method = payment.payment_method || '';
  formData.payment_reference = payment.payment_reference || '';
  formData.payment_date = payment.payment_date;
  formData.status = payment.status;
  formData.description = payment.description || '';
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingPayment.value = null;
  Object.assign(formData, {
    enrollment_id: '',
    amount: 0,
    payment_method: '',
    payment_reference: '',
    payment_date: new Date().toISOString().split('T')[0],
    status: 'completed',
    description: '',
  });
}

async function savePayment() {
  try {
    if (editingPayment.value) {
      await put(`/admin/payments/${editingPayment.value.id}`, formData);
      toast.success('تم تحديث الدفعة بنجاح');
    } else {
      await post('/admin/payments', formData);
      toast.success('تم إضافة الدفعة بنجاح');
    }
    closeModal();
    await loadItems();
    if (showReports.value) {
      await loadReports();
    }
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء الحفظ');
  }
}

onMounted(async () => {
  await loadEnrollments();
  await loadItems();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.15rem;
}
</style>

