<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ $t('admin.translations') }}</h2>
        <p class="text-sm text-slate-500">{{ $t('admin.translationsDescription') }}</p>
      </div>
      <div class="flex gap-2">
        <button
          class="px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm"
          @click="clearCache"
          :disabled="clearingCache"
        >
          {{ clearingCache ? $t('common.loading') : $t('admin.clearCache') }}
        </button>
        <RouterLink
          to="/dashboard/admin/translations/new"
          class="px-4 py-2 bg-primary text-white rounded-md inline-block"
        >
          {{ $t('common.add') }} {{ $t('admin.translation') }}
        </RouterLink>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-3">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
          :placeholder="$t('common.search')"
          @input="handleSearch"
        />
        <FilterDropdown
          v-model="filters.locale"
          :options="locales.map(l => ({ id: l, name: l.toUpperCase() }))"
          :placeholder="$t('admin.allLocales')"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.group"
          :options="groups.map(g => ({ id: g, name: g }))"
          :placeholder="$t('admin.allGroups')"
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

    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
          <tr>
            <th class="px-4 py-3 text-left">{{ $t('admin.key') }}</th>
            <th class="px-4 py-3 text-left">{{ $t('admin.locale') }}</th>
            <th class="px-4 py-3 text-left">{{ $t('admin.value') }}</th>
            <th class="px-4 py-3 text-left">{{ $t('admin.group') }}</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="translation in translations" :key="translation.id" class="border-t border-slate-100">
            <td class="px-4 py-3 font-medium">{{ translation.key }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                {{ translation.locale.toUpperCase() }}
              </span>
            </td>
            <td class="px-4 py-3 max-w-md truncate" :title="translation.value">
              {{ translation.value }}
            </td>
            <td class="px-4 py-3 text-xs">{{ translation.group }}</td>
            <td class="px-4 py-3 text-right space-x-2 rtl:space-x-reverse">
              <RouterLink
                :to="`/dashboard/admin/translations/${translation.id}/edit`"
                class="text-primary text-xs hover:underline"
              >
                {{ $t('common.edit') }}
              </RouterLink>
              <button class="text-red-500 text-xs" @click="remove(translation.id)">
                {{ $t('common.delete') }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!translations.length && !loading" class="text-center py-6 text-sm text-slate-400">
        {{ $t('common.noData') }}
      </p>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">
        {{ $t('common.loading') }}...
      </p>
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
import { ref, reactive, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const groups = ref([]);
const locales = ref([]);
const saving = ref(false);
const clearingCache = ref(false);
const dialogRef = ref(null);
const editing = ref(false);
const editingId = ref(null);

// Use unified list page composable
const {
  items: translations,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
  createItem,
  updateItem,
  deleteItem,
} = useListPage({
  endpoint: '/admin/translations',
  initialFilters: {
    search: '',
    locale: '',
    group: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // Manual filter application
});

const { get, post } = useApi();

onMounted(() => {
  loadMeta();
});

function loadMeta() {
  Promise.all([
    get('/admin/translations/groups'),
    get('/admin/translations/locales'),
  ])
    .then(([groupsRes, localesRes]) => {
      groups.value = groupsRes.groups || [];
      locales.value = localesRes.locales || ['en', 'ar'];
    })
    .catch((err) => {
      console.error('Error loading meta:', err);
    });
}

// Handle search with debounce
function handleSearch() {
  loadItemsDebounced();
}

// Handle filter change - manual apply
function handleFilterChange() {
  applyFilters();
}

async function remove(id) {
  if (!confirm('Are you sure you want to delete this translation?')) {
    return;
  }
  try {
    await deleteItem(id);
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحذف');
  }
}

async function clearCache() {
  clearingCache.value = true;
  try {
    await post('/admin/translations/clear-cache');
    alert('تم مسح الذاكرة المؤقتة بنجاح');
  } catch (err) {
    alert('حدث خطأ أثناء مسح الذاكرة المؤقتة');
  } finally {
    clearingCache.value = false;
  }
}
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
}

.input:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
}

.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.25rem;
}
</style>

