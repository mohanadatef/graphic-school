<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">سجل التدقيق</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">تتبع جميع الأنشطة والتغييرات في النظام</p>
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
            placeholder="بحث..."
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.action"
          :options="actionOptions"
          placeholder="كل الإجراءات"
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

    <!-- Audit Logs Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-slate-600 dark:text-slate-400 uppercase text-xs font-semibold">
            <tr>
              <th class="px-6 py-4 text-left">الإجراء</th>
              <th class="px-6 py-4 text-left">النوع</th>
              <th class="px-6 py-4 text-left">المستخدم</th>
              <th class="px-6 py-4 text-left">الوصف</th>
              <th class="px-6 py-4 text-left">التاريخ</th>
              <th class="px-6 py-4 text-left">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
            <tr
              v-for="log in logs"
              :key="log.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
            >
              <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium" :class="getActionClass(log.action)">
                  {{ getActionLabel(log.action) }}
                </span>
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ getModelTypeLabel(log.model_type) }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ log.user?.name || 'نظام' }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ log.description || '-' }}
              </td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="px-6 py-4">
                <button
                  @click="viewLog(log)"
                  class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors duration-200"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  عرض التفاصيل
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
      <p v-else-if="!logs.length" class="text-center py-6 text-sm text-slate-400">لا توجد سجلات.</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- View Log Modal -->
    <div v-if="viewingLog" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">تفاصيل السجل</h3>
            <button @click="viewingLog = null" class="text-slate-400 hover:text-slate-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">الإجراء</p>
              <p class="font-semibold">{{ getActionLabel(viewingLog.action) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">النوع</p>
              <p class="font-semibold">{{ getModelTypeLabel(viewingLog.model_type) }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">المستخدم</p>
              <p class="font-semibold">{{ viewingLog.user?.name || 'نظام' }}</p>
            </div>
            <div>
              <p class="text-sm text-slate-500 dark:text-slate-400">التاريخ</p>
              <p class="font-semibold">{{ formatDate(viewingLog.created_at) }}</p>
            </div>
          </div>
          <div v-if="viewingLog.description">
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">الوصف</p>
            <p class="text-slate-900 dark:text-white">{{ viewingLog.description }}</p>
          </div>
          <div v-if="viewingLog.old_values || viewingLog.new_values" class="pt-4 border-t border-slate-200 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">التغييرات</p>
            <div class="grid grid-cols-2 gap-4">
              <div v-if="viewingLog.old_values">
                <p class="text-xs font-semibold text-slate-500 mb-2">القيم القديمة</p>
                <pre class="text-xs bg-slate-100 dark:bg-slate-900 p-3 rounded-lg overflow-auto">{{ JSON.stringify(viewingLog.old_values, null, 2) }}</pre>
              </div>
              <div v-if="viewingLog.new_values">
                <p class="text-xs font-semibold text-slate-500 mb-2">القيم الجديدة</p>
                <pre class="text-xs bg-slate-100 dark:bg-slate-900 p-3 rounded-lg overflow-auto">{{ JSON.stringify(viewingLog.new_values, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const viewingLog = ref(null);

const actionOptions = [
  { id: 'created', name: 'إنشاء' },
  { id: 'updated', name: 'تحديث' },
  { id: 'deleted', name: 'حذف' },
];

const {
  items: logs,
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
  endpoint: '/admin/audit-logs',
  initialFilters: {
    search: '',
    action: '',
    from_date: '',
    to_date: '',
  },
  perPage: 20,
  debounceMs: 500,
  autoApplyFilters: false,
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

function getActionClass(action) {
  const classes = {
    created: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    updated: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    deleted: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
  };
  return classes[action] || classes.updated;
}

function getActionLabel(action) {
  const labels = {
    created: 'إنشاء',
    updated: 'تحديث',
    deleted: 'حذف',
  };
  return labels[action] || action;
}

function getModelTypeLabel(modelType) {
  if (!modelType) return '-';
  const parts = modelType.split('\\');
  return parts[parts.length - 1] || modelType;
}

function viewLog(log) {
  viewingLog.value = log;
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
</style>

