<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.programs.title') || 'Programs Management' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.programs.subtitle') || 'Manage all programs' }}</p>
      </div>
      <button
        data-cy="create-btn"
        @click="$router.push({ name: 'admin-programs-new' })"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.programs.create') || 'Create Program' }}
      </button>
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
            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
            :placeholder="$t('common.search') || 'Search...'"
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.type"
          :options="typeOptions"
          :placeholder="$t('admin.programs.filterType') || 'All Types'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.is_active"
          :options="statusOptions"
          :placeholder="$t('admin.programs.filterStatus') || 'All Status'"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-if="pagination"
          v-model.number="pagination.per_page"
          :options="[
            { id: 10, name: '10' },
            { id: 20, name: '20' },
            { id: 50, name: '50' }
          ]"
          :placeholder="$t('common.perPage') || 'Per Page'"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <!-- Programs Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="programs.length === 0" class="text-center py-20">
        <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.programs.noPrograms') || 'No programs found' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.programs.name') || 'Name' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.programs.type') || 'Type' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.programs.duration') || 'Duration' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.programs.price') || 'Price' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('admin.programs.status') || 'Status' }}
              </th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                {{ $t('common.actions') || 'Actions' }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <tr v-for="program in programs" :key="program.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-white">{{ program.title || program.name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ program.slug }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                  {{ program.type }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ program.duration_weeks }} {{ $t('admin.programs.weeks') || 'weeks' }}
              </td>
              <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">
                {{ formatCurrency(program.price) }}
              </td>
              <td class="px-4 py-3">
                <span :class="program.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ program.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button
                    @click="$router.push({ name: 'admin-programs-edit', params: { id: program.id } })"
                    class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    :title="$t('common.edit') || 'Edit'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="confirmDelete(program)"
                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    :title="$t('common.delete') || 'Delete'"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <PaginationControls
      v-if="!loading && programs.length > 0 && pagination"
      :meta="{
        current_page: pagination?.current_page || 1,
        last_page: pagination?.last_page || 1,
        per_page: pagination?.per_page || 10,
        total: pagination?.total || 0,
      }"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- Delete Confirmation Modal -->
    <div v-if="deletingProgram" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
          {{ $t('common.confirmDelete') || 'Confirm Delete' }}
        </h3>
        <p class="text-slate-600 dark:text-slate-400 mb-6">
          {{ $t('admin.programs.deleteConfirm') || 'Are you sure you want to delete this program?' }}
        </p>
        <div class="flex gap-3">
          <button @click="deleteProgram" class="btn-danger flex-1">
            {{ $t('common.delete') || 'Delete' }}
          </button>
          <button @click="deletingProgram = null" class="btn-secondary flex-1">
            {{ $t('common.cancel') || 'Cancel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18nStore } from '../../../stores/i18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const router = useRouter();
const { get, delete: del } = useApi();
const toast = useToast();
const i18nStore = useI18nStore();

const typeOptions = [
  { id: 'bootcamp', name: 'Bootcamp' },
  { id: 'track', name: 'Track' },
  { id: 'workshop', name: 'Workshop' },
  { id: 'course', name: 'Course' },
];

const statusOptions = [
  { id: '1', name: 'Active' },
  { id: '0', name: 'Inactive' },
];

const deletingProgram = ref(null);

const {
  items: programs,
  loading,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
} = useListPage({
  endpoint: '/admin/programs',
  initialFilters: {
    search: '',
    type: '',
    is_active: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false,
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function formatCurrency(amount) {
  if (!amount) return '-';
  return new Intl.NumberFormat(i18nStore.locale === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
  }).format(amount);
}

function confirmDelete(program) {
  deletingProgram.value = program;
}

async function deleteProgram() {
  if (!deletingProgram.value) return;
  
  try {
    await del(`/admin/programs/${deletingProgram.value.id}`);
    toast.success('Program deleted successfully');
    deletingProgram.value = null;
    loadItems();
  } catch (error) {
    toast.error('Failed to delete program');
    console.error(error);
  }
}

onMounted(() => {
  loadItems();
});
</script>

