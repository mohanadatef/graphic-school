<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">نظام التذاكر</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">إدارة تذاكر الدعم الفني</p>
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
          تذكرة جديدة
        </button>
      </div>
    </div>

    <!-- Reports Summary -->
    <div v-if="showReports && reports" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">إجمالي التذاكر</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ reports.summary?.total_tickets || 0 }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">مفتوحة</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ reports.by_status?.open || 0 }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">قيد المعالجة</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ reports.by_status?.in_progress || 0 }}</p>
      </div>
      <div class="card-premium p-4">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">محلولة</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ reports.by_status?.resolved || 0 }}</p>
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
            placeholder="بحث بالعنوان..."
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.status"
          :options="statusOptions"
          placeholder="كل الحالات"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.type"
          :options="typeOptions"
          placeholder="كل الأنواع"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.priority"
          :options="priorityOptions"
          placeholder="كل الأولويات"
          @update:modelValue="handleFilterChange"
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

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
            <tr>
              <th class="px-6 py-4 text-left">العنوان</th>
              <th class="px-6 py-4 text-left">النوع</th>
              <th class="px-6 py-4 text-left">الأولوية</th>
              <th class="px-6 py-4 text-left">الحالة</th>
              <th class="px-6 py-4 text-left">المستخدم</th>
              <th class="px-6 py-4 text-left">التاريخ</th>
              <th class="px-6 py-4 text-left">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
            <tr
              v-for="ticket in tickets"
              :key="ticket.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 cursor-pointer"
              @click="viewTicket(ticket)"
            >
              <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                {{ ticket.title }}
              </td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium" :class="getTypeClass(ticket.type)">
                  {{ getTypeLabel(ticket.type) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium" :class="getPriorityClass(ticket.priority)">
                  {{ getPriorityLabel(ticket.priority) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(ticket.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(ticket.status)"></span>
                  {{ getStatusLabel(ticket.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ ticket.user?.name || 'غير محدد' }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ formatDate(ticket.created_at) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2" @click.stop>
                  <button
                    @click="viewTicket(ticket)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    عرض
                  </button>
                  <button
                    @click="editTicket(ticket)"
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
      <p v-else-if="!tickets.length" class="text-center py-6 text-sm text-slate-400">لا توجد تذاكر.</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingTicket" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">
            {{ editingTicket ? 'تعديل تذكرة' : 'تذكرة جديدة' }}
          </h3>
        </div>
        <form @submit.prevent="saveTicket" class="p-6 space-y-4">
          <div>
            <label class="label">النوع</label>
            <select v-model="formData.type" class="input" required>
              <option value="bug">خطأ (Bug)</option>
              <option value="change_request">طلب تعديل</option>
              <option value="new_feature">ميزة جديدة</option>
            </select>
          </div>
          <div>
            <label class="label">العنوان</label>
            <input v-model="formData.title" type="text" class="input" required />
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="formData.description" class="input" rows="5" required></textarea>
          </div>
          <div>
            <label class="label">الأولوية</label>
            <select v-model="formData.priority" class="input">
              <option value="low">منخفضة</option>
              <option value="medium">متوسطة</option>
              <option value="high">عالية</option>
              <option value="urgent">عاجلة</option>
            </select>
          </div>
          <div v-if="editingTicket">
            <label class="label">الحالة</label>
            <select v-model="formData.status" class="input">
              <option value="open">مفتوحة</option>
              <option value="in_progress">قيد المعالجة</option>
              <option value="resolved">محلولة</option>
              <option value="closed">مغلقة</option>
            </select>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1">حفظ</button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Ticket Modal -->
    <div v-if="viewingTicket" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ viewingTicket.title }}</h3>
            <button @click="viewingTicket = null" class="text-slate-400 hover:text-slate-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">النوع</p>
              <p class="font-semibold">{{ getTypeLabel(viewingTicket.type) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">الأولوية</p>
              <p class="font-semibold">{{ getPriorityLabel(viewingTicket.priority) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">الحالة</p>
              <p class="font-semibold">{{ getStatusLabel(viewingTicket.status) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">المستخدم</p>
              <p class="font-semibold">{{ viewingTicket.user?.name }}</p>
            </div>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">الوصف</p>
            <p class="text-slate-900 dark:text-white whitespace-pre-wrap">{{ viewingTicket.description }}</p>
          </div>
          <div v-if="viewingTicket.attachments?.length" class="pt-4 border-t border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">المرفقات</p>
            <div class="space-y-2">
              <div v-for="(attachment, idx) in viewingTicket.attachments" :key="idx" class="flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">{{ attachment.name }}</span>
              </div>
            </div>
          </div>
        </div>
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
const editingTicket = ref(null);
const viewingTicket = ref(null);

const statusOptions = [
  { id: 'open', name: 'مفتوحة' },
  { id: 'in_progress', name: 'قيد المعالجة' },
  { id: 'resolved', name: 'محلولة' },
  { id: 'closed', name: 'مغلقة' },
];

const typeOptions = [
  { id: 'bug', name: 'خطأ' },
  { id: 'change_request', name: 'طلب تعديل' },
  { id: 'new_feature', name: 'ميزة جديدة' },
];

const priorityOptions = [
  { id: 'low', name: 'منخفضة' },
  { id: 'medium', name: 'متوسطة' },
  { id: 'high', name: 'عالية' },
  { id: 'urgent', name: 'عاجلة' },
];

const formData = reactive({
  type: 'bug',
  title: '',
  description: '',
  priority: 'medium',
  status: 'open',
});

const {
  items: tickets,
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
  endpoint: '/admin/tickets',
  initialFilters: {
    search: '',
    status: '',
    type: '',
    priority: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false,
});

async function loadReports() {
  try {
    const data = await get('/admin/tickets/reports');
    reports.value = data;
  } catch (err) {
    console.error('Error loading reports:', err);
  }
}

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

function getStatusClass(status) {
  const classes = {
    open: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    in_progress: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    resolved: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    closed: 'bg-slate-100 dark:bg-slate-900/30 text-slate-700 dark:text-slate-400',
  };
  return classes[status] || classes.open;
}

function getStatusDotClass(status) {
  const classes = {
    open: 'bg-blue-500',
    in_progress: 'bg-yellow-500',
    resolved: 'bg-green-500',
    closed: 'bg-slate-500',
  };
  return classes[status] || classes.open;
}

function getStatusLabel(status) {
  const labels = {
    open: 'مفتوحة',
    in_progress: 'قيد المعالجة',
    resolved: 'محلولة',
    closed: 'مغلقة',
  };
  return labels[status] || status;
}

function getTypeClass(type) {
  const classes = {
    bug: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
    change_request: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    new_feature: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
  };
  return classes[type] || classes.bug;
}

function getTypeLabel(type) {
  const labels = {
    bug: 'خطأ',
    change_request: 'طلب تعديل',
    new_feature: 'ميزة جديدة',
  };
  return labels[type] || type;
}

function getPriorityClass(priority) {
  const classes = {
    low: 'bg-slate-100 dark:bg-slate-900/30 text-slate-700 dark:text-slate-400',
    medium: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    high: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
    urgent: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
  };
  return classes[priority] || classes.medium;
}

function getPriorityLabel(priority) {
  const labels = {
    low: 'منخفضة',
    medium: 'متوسطة',
    high: 'عالية',
    urgent: 'عاجلة',
  };
  return labels[priority] || priority;
}

function viewTicket(ticket) {
  viewingTicket.value = ticket;
}

function editTicket(ticket) {
  editingTicket.value = ticket;
  formData.type = ticket.type;
  formData.title = ticket.title;
  formData.description = ticket.description;
  formData.priority = ticket.priority;
  formData.status = ticket.status;
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingTicket.value = null;
  Object.assign(formData, {
    type: 'bug',
    title: '',
    description: '',
    priority: 'medium',
    status: 'open',
  });
}

async function saveTicket() {
  try {
    if (editingTicket.value) {
      await put(`/admin/tickets/${editingTicket.value.id}`, formData);
      toast.success('تم تحديث التذكرة بنجاح');
    } else {
      await post('/admin/tickets', formData);
      toast.success('تم إنشاء التذكرة بنجاح');
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

