<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.batches.title') || 'Batches' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.batches.subtitle') || 'Program' }}: {{ program?.title || programId }}
        </p>
      </div>
      <button
        @click="showCreateModal = true"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.batches.create') || 'Create Batch' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="batches.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.batches.noBatches') || 'No batches found' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="batch in batches"
        :key="batch.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ batch.name || batch.code }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ batch.code }}</p>
          </div>
          <span :class="batch.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ batch.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ formatDate(batch.start_date) }} - {{ formatDate(batch.end_date) }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ $t('admin.batches.maxStudents') || 'Max' }}: {{ batch.max_students || '-' }}</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="$router.push({ name: 'admin-batches-groups', params: { batchId: batch.id } })"
            class="btn-secondary flex-1 text-sm"
          >
            {{ $t('admin.batches.viewGroups') || 'View Groups' }}
          </button>
          <button
            @click="editBatch(batch)"
            class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingBatch" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
          {{ editingBatch ? ($t('admin.batches.edit') || 'Edit Batch') : ($t('admin.batches.create') || 'Create Batch') }}
        </h3>
        <form @submit.prevent="saveBatch" class="space-y-4">
          <div>
            <label class="label">{{ $t('admin.batches.code') || 'Code' }}</label>
            <input v-model="batchForm.code" class="input" />
          </div>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="label">{{ $t('admin.batches.startDate') || 'Start Date' }}</label>
              <input v-model="batchForm.start_date" type="date" class="input" required />
            </div>
            <div>
              <label class="label">{{ $t('admin.batches.endDate') || 'End Date' }}</label>
              <input v-model="batchForm.end_date" type="date" class="input" />
            </div>
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.maxStudents') || 'Max Students' }}</label>
            <input v-model.number="batchForm.max_students" type="number" min="1" class="input" />
          </div>
          <div>
            <label class="label mb-4 block">{{ $t('admin.batches.translations') || 'Translations' }}</label>
            <div class="space-y-3">
              <div v-for="lang in availableLanguages" :key="lang.code" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 block">{{ lang.native_name }}</label>
                <input
                  v-model="batchForm.translations[lang.code].name"
                  class="input text-sm"
                  :placeholder="`${$t('admin.batches.name') || 'Name'} (${lang.native_name})`"
                  :required="lang.code === 'ar'"
                />
                <textarea
                  v-model="batchForm.translations[lang.code].description"
                  class="input text-sm mt-2"
                  rows="2"
                  :placeholder="`${$t('admin.batches.description') || 'Description'} (${lang.native_name})`"
                ></textarea>
              </div>
            </div>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="checkbox" v-model="batchForm.is_active" class="w-5 h-5 text-primary border-slate-300 rounded" />
              <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('common.active') || 'Active' }}</span>
            </label>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1" :disabled="saving">
              {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
            </button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">
              {{ $t('common.cancel') || 'Cancel' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const { get, post, put } = useApi();
const toast = useToast();

const programId = route.params.programId || route.query.program_id;
const loading = ref(false);
const saving = ref(false);
const program = ref(null);
const batches = ref([]);
const showCreateModal = ref(false);
const editingBatch = ref(null);
const availableLanguages = ref([]);

const batchForm = reactive({
  code: '',
  start_date: '',
  end_date: '',
  max_students: null,
  is_active: true,
  translations: {},
});

onMounted(async () => {
  await loadLanguages();
  await loadProgram();
  await loadBatches();
});

async function loadLanguages() {
  try {
    const response = await get('/locales');
    let languages = [];
    if (response?.data?.locales) languages = response.data.locales;
    else if (response?.locales) languages = response.locales;
    else if (Array.isArray(response)) languages = response;
    
    if (languages.length === 0) {
      languages = [
        { code: 'en', name: 'English', native_name: 'English' },
        { code: 'ar', name: 'Arabic', native_name: 'العربية' },
      ];
    }
    availableLanguages.value = languages;
    
    languages.forEach(lang => {
      batchForm.translations[lang.code] = {
        locale: lang.code,
        name: '',
        description: '',
      };
    });
  } catch (error) {
    console.error('Error loading languages:', error);
  }
}

async function loadProgram() {
  if (!programId) return;
  try {
    const response = await get(`/admin/programs/${programId}`);
    program.value = response?.data || response;
  } catch (error) {
    console.error('Error loading program:', error);
  }
}

async function loadBatches() {
  try {
    loading.value = true;
    const response = await get(`/admin/batches?program_id=${programId}`);
    batches.value = response?.data || response || [];
  } catch (error) {
    toast.error('Failed to load batches');
    console.error(error);
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

function editBatch(batch) {
  editingBatch.value = batch;
  batchForm.code = batch.code || '';
  batchForm.start_date = batch.start_date || '';
  batchForm.end_date = batch.end_date || '';
  batchForm.max_students = batch.max_students || null;
  batchForm.is_active = batch.is_active;
  
  if (batch.translations) {
    batch.translations.forEach(trans => {
      if (batchForm.translations[trans.locale]) {
        batchForm.translations[trans.locale].name = trans.name || '';
        batchForm.translations[trans.locale].description = trans.description || '';
      }
    });
  }
  
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingBatch.value = null;
  Object.keys(batchForm.translations).forEach(locale => {
    batchForm.translations[locale].name = '';
    batchForm.translations[locale].description = '';
  });
  batchForm.code = '';
  batchForm.start_date = '';
  batchForm.end_date = '';
  batchForm.max_students = null;
  batchForm.is_active = true;
}

async function saveBatch() {
  try {
    saving.value = true;
    const translationsArray = availableLanguages.value.map(lang => batchForm.translations[lang.code]);
    
    const data = {
      program_id: programId,
      code: batchForm.code,
      start_date: batchForm.start_date,
      end_date: batchForm.end_date,
      max_students: batchForm.max_students,
      is_active: batchForm.is_active,
      translations: translationsArray,
    };
    
    if (editingBatch.value) {
      await put(`/admin/batches/${editingBatch.value.id}`, data);
      toast.success('Batch updated successfully');
    } else {
      await post('/admin/batches', data);
      toast.success('Batch created successfully');
    }
    
    closeModal();
    await loadBatches();
  } catch (error) {
    toast.error('Failed to save batch');
    console.error(error);
  } finally {
    saving.value = false;
  }
}
</script>

