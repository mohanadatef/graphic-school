<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('admin.attendance.title') || 'Attendance Records' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.attendance.subtitle') || 'View and manage attendance records' }}
        </p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg w-40 bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          :placeholder="$t('common.search') || 'Search...'"
          @input="handleSearch"
        />
        <FilterDropdown
          v-model="filters.group_id"
          :options="groups"
          :placeholder="$t('admin.attendance.filterByGroup') || 'Filter by Group'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.status"
          :options="statusOptions"
          :placeholder="$t('admin.attendance.filterByStatus') || 'Filter by Status'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model.number="pagination.per_page"
          :options="[
            { id: 10, name: '10' },
            { id: 20, name: '20' },
            { id: 50, name: '50' }
          ]"
          :placeholder="$t('pagination.rowsPerPage') || 'Rows per page'"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 text-xs uppercase">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('admin.attendance.student') || 'Student' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.groups.title') || 'Group' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.sessions.title') || 'Session' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.attendance.date') || 'Date' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.attendance.status') || 'Status' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.attendance.note') || 'Note' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in attendance" :key="record.id" class="border-t border-slate-100 dark:border-slate-700">
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-white">{{ record.student?.name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ record.student?.email }}</div>
              </td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">
                {{ record.group_session?.group?.name || record.group_session?.group?.code || '-' }}
              </td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">
                {{ record.group_session?.title || '-' }}
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ formatDate(record.group_session?.session_date) }}
              </td>
              <td class="px-4 py-3">
                <span
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': record.status === 'present',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': record.status === 'absent',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': record.status === 'late',
                  }"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ $t(`admin.attendance.status.${record.status}`) || record.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ record.note || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      <p v-else-if="!attendance.length" class="text-center py-6 text-sm text-slate-400">
        {{ $t('admin.attendance.noRecords') || 'No attendance records found' }}
      </p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="!loading && attendance.length > 0 && pagination"
      :meta="{
        current_page: pagination?.current_page || 1,
        last_page: pagination?.last_page || 1,
        per_page: pagination?.per_page || 10,
        total: pagination?.total || 0,
      }"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { t } = useI18n();
const { get } = useApi();
const groups = ref([]);

const statusOptions = computed(() => [
  { id: 'present', name: t('admin.attendance.status.present') || 'Present' },
  { id: 'absent', name: t('admin.attendance.status.absent') || 'Absent' },
  { id: 'late', name: t('admin.attendance.status.late') || 'Late' },
]);

// Use unified list page composable
const {
  items: attendance,
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
  endpoint: '/admin/attendance',
  initialFilters: {
    group_id: '',
    status: '',
    search: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false,
});

async function loadGroups() {
  try {
    const data = await get('/admin/groups', { params: { per_page: 100 } });
    const groupsData = Array.isArray(data) ? data : (data?.data || []);
    groups.value = groupsData.map(g => ({ id: g.id, name: g.name || g.code }));
  } catch (err) {
    console.error('Error loading groups:', err);
    groups.value = [];
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

onMounted(async () => {
  await loadGroups();
  await loadItems();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
}
.dark .input {
  background: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}
</style>
