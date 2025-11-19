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
        <button class="px-4 py-2 bg-primary text-white rounded-md" @click="openModal()">
          {{ $t('common.add') }} {{ $t('admin.translation') }}
        </button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <input
          v-model="filters.search"
          class="input w-48"
          :placeholder="$t('common.search')"
          @input="handleSearch"
        />
        <select v-model="filters.locale" class="input w-32" @change="handleFilterChange">
          <option value="">{{ $t('admin.allLocales') }}</option>
          <option v-for="locale in locales" :key="locale" :value="locale">
            {{ locale.toUpperCase() }}
          </option>
        </select>
        <select v-model="filters.group" class="input w-48" @change="handleFilterChange">
          <option value="">{{ $t('admin.allGroups') }}</option>
          <option v-for="group in groups" :key="group" :value="group">
            {{ group }}
          </option>
        </select>
        <select
          v-model.number="pagination.per_page"
          class="input w-32"
          @change="changePerPage(pagination.per_page)"
        >
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button class="px-4 py-2 border rounded-md" @click="loadItems">
          {{ $t('common.filter') }}
        </button>
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
              <button class="text-primary text-xs" @click="openModal(translation)">
                {{ $t('common.edit') }}
              </button>
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

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-2xl">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-xl font-semibold text-slate-900">
          {{ editing ? $t('common.edit') : $t('common.add') }} {{ $t('admin.translation') }}
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">{{ $t('admin.key') }}</label>
            <input
              v-model="form.key"
              type="text"
              required
              class="input"
              placeholder="auth.login"
            />
            <p class="text-xs text-slate-400 mt-1">{{ $t('admin.keyHint') }}</p>
          </div>
          <div>
            <label class="label">{{ $t('admin.locale') }}</label>
            <select v-model="form.locale" class="input" required>
              <option value="en">English (en)</option>
              <option value="ar">العربية (ar)</option>
            </select>
          </div>
          <div>
            <label class="label">{{ $t('admin.group') }}</label>
            <input
              v-model="form.group"
              type="text"
              class="input"
              placeholder="messages"
            />
            <p class="text-xs text-slate-400 mt-1">{{ $t('admin.groupHint') }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="label">{{ $t('admin.value') }}</label>
            <textarea
              v-model="form.value"
              required
              class="input"
              rows="3"
              :placeholder="$t('admin.valuePlaceholder')"
            ></textarea>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">
            {{ $t('common.cancel') }}
          </button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md" :disabled="saving">
            {{ saving ? $t('common.loading') : $t('common.save') }}
          </button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import PaginationControls from '../../../components/common/PaginationControls.vue';

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

const form = reactive({
  key: '',
  locale: 'en',
  value: '',
  group: 'messages',
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

function openModal(translation = null) {
  editing.value = !!translation;
  if (translation) {
    editingId.value = translation.id;
    Object.assign(form, {
      key: translation.key,
      locale: translation.locale,
      value: translation.value,
      group: translation.group,
    });
  } else {
    editingId.value = null;
    Object.assign(form, {
      key: '',
      locale: 'en',
      value: '',
      group: 'messages',
    });
  }
  dialogRef.value?.showModal();
}

function closeModal() {
  dialogRef.value?.close();
  editingId.value = null;
  Object.assign(form, {
    key: '',
    locale: 'en',
    value: '',
    group: 'messages',
  });
  editing.value = false;
}

async function submit() {
  saving.value = true;
  try {
    if (editing.value) {
      await updateItem(editingId.value, form);
    } else {
      await createItem(form);
    }
    closeModal();
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحفظ');
  } finally {
    saving.value = false;
  }
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

