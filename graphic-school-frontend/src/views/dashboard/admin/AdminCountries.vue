<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('admin.countries.title') || 'Countries' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.countries.subtitle') || 'Manage system countries' }}
        </p>
      </div>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.countries.create') || 'Add Country' }}
      </button>
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
        <select
          v-model="filters.is_active"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="handleFilterChange"
        >
          <option value="">{{ $t('admin.countries.allStatuses') || 'All Statuses' }}</option>
          <option value="1">{{ $t('common.active') || 'Active' }}</option>
          <option value="0">{{ $t('common.inactive') || 'Inactive' }}</option>
        </select>
      </div>
    </div>

    <!-- Countries Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="countries.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.countries.noCountries') || 'No countries found' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 text-xs uppercase">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('admin.countries.code') || 'Code' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.countries.name') || 'Name' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.countries.status') || 'Status' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('admin.countries.default') || 'Default' }}</th>
              <th class="px-4 py-3 text-left">{{ $t('common.actions') || 'Actions' }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="country in countries" :key="country.id" class="border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ country.code }}</td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">{{ country.name }}</td>
              <td class="px-4 py-3">
                <span
                  :class="country.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ country.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span v-if="country.is_default" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                  {{ $t('admin.countries.default') || 'Default' }}
                </span>
                <span v-else class="text-slate-400">-</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button
                    @click="editCountry(country)"
                    class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteCountry(country.id)"
                    :disabled="country.is_default"
                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
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

      <PaginationControls
        v-if="!loading && countries.length > 0 && pagination"
        :meta="{
          current_page: pagination?.current_page || 1,
          last_page: pagination?.last_page || 1,
          per_page: pagination?.per_page || 15,
          total: pagination?.total || 0,
        }"
        @change-page="changePage"
        @change-per-page="changePerPage"
      />
    </div>

    <!-- Create/Edit Modal -->
    <CountryFormModal
      v-if="showCreateModal || editingCountry"
      :country="editingCountry"
      @close="closeModal"
      @saved="handleCountrySaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from '../../../composables/useI18n';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import CountryFormModal from '../../../components/admin/CountryFormModal.vue';

const { t } = useI18n();
const { get, delete: del } = useApi();
const toast = useToast();

const showCreateModal = ref(false);
const editingCountry = ref(null);

// Use unified list page composable
const {
  items: countries,
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
  endpoint: '/admin/countries',
  initialFilters: {
    search: '',
    is_active: '',
  },
  perPage: 15,
  debounceMs: 500,
  autoApplyFilters: false,
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function editCountry(country) {
  editingCountry.value = country;
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingCountry.value = null;
}

function handleCountrySaved() {
  closeModal();
  loadItems();
}

async function deleteCountry(id) {
  const confirmMessage = t('admin.countries.confirmDelete') || 'Are you sure you want to delete this country?';
  if (!confirm(confirmMessage)) return;
  
  try {
    await del(`/admin/countries/${id}`);
    toast.success(t('admin.countries.deleted') || 'Country deleted successfully');
    await loadItems();
  } catch (err) {
    toast.error(err.response?.data?.message || t('errors.deleteError') || 'Failed to delete country');
  }
}

onMounted(async () => {
  await loadItems();
});
</script>

