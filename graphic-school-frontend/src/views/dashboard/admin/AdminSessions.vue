<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold">{{ $t('admin.sessions') || 'Sessions' }}</h2>
        <p class="text-sm text-slate-500">{{ $t('admin.sessionsDescription') || 'Manage session schedules' }}</p>
      </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-3">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
          :placeholder="$t('common.search') || 'Search...'"
          @input="handleSearch"
        />
        <FilterDropdown
          v-model="filters.group_id"
          :options="groups"
          :placeholder="$t('admin.sessions.filterByGroup') || 'Filter by Group'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.course_id"
          :options="courses"
          :placeholder="$t('admin.sessions.filterByCourse') || 'Filter by Course'"
          label-key="title"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.status"
          :options="statusOptions"
          :placeholder="$t('dashboard.allStatuses') || 'All Statuses'"
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

    <div class="bg-white border border-slate-100 rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">{{ $t('admin.groups.title') || 'Group' }}</th>
            <th class="px-4 py-3 text-left">{{ $t('courses.title') || 'Title' }}</th>
            <th class="px-4 py-3 text-left">{{ $t('course.startDate') || 'Date' }}</th>
            <th class="px-4 py-3 text-left">{{ $t('course.time') || 'Time' }}</th>
            <th class="px-4 py-3 text-left">{{ $t('course.status') || 'Status' }}</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="session in sessions" :key="session.id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ session.group?.name || session.group?.code || 'N/A' }}</td>
            <td class="px-4 py-3">{{ session.title }}</td>
            <td class="px-4 py-3">{{ formatDate(session.session_date) }}</td>
            <td class="px-4 py-3 text-xs">
              {{ formatTime(session.start_time) }} - {{ formatTime(session.end_time) }}
            </td>
            <td class="px-4 py-3 text-xs uppercase">{{ session.status }}</td>
            <td class="px-4 py-3 text-right">
              <RouterLink
                :to="`/dashboard/admin/sessions/${session.id}/edit`"
                class="text-primary text-xs hover:underline"
              >
                {{ $t('common.edit') || 'Edit' }}
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      <p v-else-if="!sessions.length" class="text-center py-6 text-sm text-slate-400">{{ $t('common.noData') || 'No data available' }}</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { t } = useI18n();
const { get } = useApi();
const courses = ref([]);
const groups = ref([]);

const statusOptions = computed(() => [
  { id: 'scheduled', name: t('admin.sessionsStatus.scheduled') || 'Scheduled' },
  { id: 'completed', name: t('admin.sessionsStatus.completed') || 'Completed' },
  { id: 'cancelled', name: t('admin.sessionsStatus.cancelled') || 'Cancelled' }
]);

// Use unified list page composable
const {
  items: sessions,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
  updateItem,
} = useListPage({
  endpoint: '/admin/sessions',
  initialFilters: {
    group_id: '',
    course_id: '',
    status: '',
    search: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // Manual filter application
});

async function loadCourses() {
  try {
    const data = await get('/admin/courses', { params: { per_page: 100 } });
    courses.value = Array.isArray(data) ? data : (data?.data || []);
  } catch (err) {
    try {
      const data = await get('/admin/courses');
      courses.value = Array.isArray(data) ? data : (data?.data || []);
    } catch (retryErr) {
      console.error('Error loading courses:', retryErr);
      courses.value = [];
    }
  }
}

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
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

function formatTime(time) {
  if (!time) return '--:--';
  return time.slice(0, 5);
}

function handleSearch() {
  loadItemsDebounced();
}

// Handle filter change - manual apply
function handleFilterChange() {
  applyFilters();
}

onMounted(async () => {
  await Promise.all([loadCourses(), loadGroups()]);
  await loadItems();
});
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

