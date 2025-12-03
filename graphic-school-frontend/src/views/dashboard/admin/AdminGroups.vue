<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.groups.title') || 'Groups' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.groups.subtitle') || 'Manage groups for courses' }}
        </p>
      </div>
      <button
        data-cy="create-btn"
        @click="$router.push({ name: 'admin-groups-new' })"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.groups.create') || 'Create Group' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.groups.noGroups') || 'No groups found' }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow p-3 mb-6">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
          :placeholder="$t('common.search') || 'Search...'"
          @input="handleSearch"
        />
        <FilterDropdown
          v-model="filters.course_id"
          :options="courses"
          :placeholder="$t('admin.groups.filterByCourse') || 'Filter by Course'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.is_active"
          :options="[
            { id: '1', name: $t('common.active') || 'Active' },
            { id: '0', name: $t('common.inactive') || 'Inactive' }
          ]"
          :placeholder="$t('admin.groups.filterByStatus') || 'Filter by Status'"
          @update:modelValue="handleFilterChange"
        />
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.groups.noGroups') || 'No groups found' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="group in groups"
        :key="group.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ group.name || group.code }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ group.code }}</p>
          </div>
          <span :class="group.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ group.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ $t('admin.groups.capacity') || 'Capacity' }}: {{ group.capacity }}</span>
          </div>
          <div v-if="group.room" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>{{ group.room }}</span>
          </div>
          <div v-if="group.course" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span>{{ group.course?.title || $t('admin.groups.course') || 'Course' }}</span>
          </div>
          <div v-if="group.students_count !== undefined" class="flex items-center gap-2">
            <span>{{ $t('admin.groups.students') || 'Students' }}: {{ group.students_count }}</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="$router.push({ name: 'admin-groups-view', params: { groupId: group.id } })"
            class="btn-secondary flex-1 text-sm"
          >
            {{ $t('common.view') || 'View' }}
          </button>
          <button
            @click="$router.push({ name: 'admin-groups-edit', params: { id: group.id } })"
            class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <PaginationControls
      v-if="!loading && groups.length > 0 && pagination"
      :meta="{
        current_page: pagination?.current_page || 1,
        last_page: pagination?.last_page || 1,
        per_page: pagination?.per_page || 12,
        total: pagination?.total || 0,
      }"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useI18n } from '../../../composables/useI18n';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const { t } = useI18n();
const { get } = useApi();
const courses = ref([]);

// Use unified list page composable
const {
  items: groups,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
  deleteItem,
} = useListPage({
  endpoint: '/admin/groups',
  initialFilters: {
    course_id: '',
    search: '',
    is_active: '',
  },
  perPage: 12,
  debounceMs: 500,
  autoApplyFilters: false,
});

async function loadCourses() {
  try {
    const response = await get('/admin/courses');
    const data = Array.isArray(response) ? response : (response?.data || []);
    courses.value = data.map(c => ({ id: c.id, name: c.title }));
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  }
}

function handleFilterChange() {
  applyFilters();
}

function handleSearch() {
  loadItemsDebounced();
}

async function remove(id) {
  const confirmMessage = t('common.confirmDelete') || 'Are you sure you want to delete this item?';
  if (!confirm(confirmMessage)) return;
  try {
    await deleteItem(id);
  } catch (err) {
    alert(error.value || t('errors.deleteError'));
  }
}

onMounted(async () => {
  await loadCourses();
  await loadItems();
});
</script>


