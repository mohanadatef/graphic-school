<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.batches.edit') || 'Edit Batch' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.batches.editSubtitle') || 'Update batch information' }}</p>
      </div>
      <RouterLink
        to="/dashboard/admin/batches"
        class="btn-secondary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        {{ $t('common.back') || 'Back' }}
      </RouterLink>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-8">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Program Selection -->
        <div>
          <label class="label">{{ $t('admin.batches.program') || 'Program' }} *</label>
          <select v-model="form.program_id" class="input" required>
            <option value="">{{ $t('common.select') || 'Select Program' }}</option>
            <option v-for="program in programs" :key="program.id" :value="program.id">
              {{ program.title || program.name || program.slug }}
            </option>
          </select>
        </div>

        <!-- Basic Fields -->
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="label">{{ $t('admin.batches.name') || 'Name' }}</label>
            <input v-model="form.name" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.code') || 'Code' }}</label>
            <input v-model="form.code" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.startDate') || 'Start Date' }} *</label>
            <input v-model="form.start_date" type="date" class="input" required />
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.endDate') || 'End Date' }}</label>
            <input v-model="form.end_date" type="date" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.maxStudents') || 'Max Students' }}</label>
            <input v-model.number="form.max_students" type="number" min="1" class="input" />
          </div>
          <div>
            <label class="label">{{ $t('admin.batches.status') || 'Status' }}</label>
            <select v-model="form.status" class="input">
              <option value="upcoming">{{ $t('admin.batches.upcoming') || 'Upcoming' }}</option>
              <option value="active">{{ $t('admin.batches.active') || 'Active' }}</option>
              <option value="completed">{{ $t('admin.batches.completed') || 'Completed' }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.is_active" class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary focus:ring-2" />
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('common.active') || 'Active' }}</span>
          </label>
        </div>

        <!-- Translations -->
        <div>
          <label class="label mb-4 block">{{ $t('admin.batches.translations') || 'Translations' }}</label>
          <div class="space-y-4">
            <div
              v-for="lang in availableLanguages"
              :key="lang.code"
              class="p-4 rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50"
            >
              <div class="flex items-center gap-3 mb-3">
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ lang.native_name }}</span>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="label text-sm">{{ $t('admin.batches.name') || 'Name' }} *</label>
                  <input
                    v-model="form.translations[lang.code].name"
                    class="input"
                    :required="lang.code === 'ar'"
                    :placeholder="`${$t('admin.batches.name') || 'Name'} (${lang.native_name})`"
                  />
                </div>
                <div>
                  <label class="label text-sm">{{ $t('admin.batches.description') || 'Description' }}</label>
                  <textarea
                    v-model="form.translations[lang.code].description"
                    class="input"
                    rows="3"
                    :placeholder="`${$t('admin.batches.description') || 'Description'} (${lang.native_name})`"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
          <button
            type="submit"
            :disabled="saving"
            class="btn-primary inline-flex items-center gap-2"
          >
            <svg v-if="saving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}</span>
          </button>
          <RouterLink to="/dashboard/admin/batches" class="btn-secondary">
            {{ $t('common.cancel') || 'Cancel' }}
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink, useRouter, useRoute } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from 'vue-i18n';

const router = useRouter();
const route = useRoute();
const { get, put } = useApi();
const toast = useToast();
const { t } = useI18n();

const loading = ref(true);
const saving = ref(false);
const programs = ref([]);
const availableLanguages = ref([
  { code: 'ar', native_name: 'العربية' },
  { code: 'en', native_name: 'English' },
]);

const form = ref({
  program_id: '',
  name: '',
  code: '',
  start_date: '',
  end_date: '',
  max_students: null,
  status: 'upcoming',
  is_active: true,
  translations: {
    ar: { locale: 'ar', name: '', description: '' },
    en: { locale: 'en', name: '', description: '' },
  },
});

async function loadPrograms() {
  try {
    const response = await get('/admin/programs?per_page=1000');
    programs.value = response?.data || response || [];
  } catch (err) {
    console.error('Error loading programs:', err);
  }
}

async function loadBatch() {
  try {
    loading.value = true;
    const response = await get(`/admin/batches/${route.params.id}?include_translations=true`);
    
    if (response && response.data) {
      const batch = response.data;
      form.value = {
        program_id: batch.program_id || '',
        name: batch.name || '',
        code: batch.code || '',
        start_date: batch.start_date ? batch.start_date.split('T')[0] : '',
        end_date: batch.end_date ? batch.end_date.split('T')[0] : '',
        max_students: batch.max_students || null,
        status: batch.status || 'upcoming',
        is_active: batch.is_active !== undefined ? batch.is_active : true,
        translations: {
          ar: batch.translations?.find(t => t.locale === 'ar') || { locale: 'ar', name: '', description: '' },
          en: batch.translations?.find(t => t.locale === 'en') || { locale: 'en', name: '', description: '' },
        },
      };
    }
  } catch (err) {
    console.error('Error loading batch:', err);
    toast.error(err.response?.data?.message || err.message || t('errors.loadDataError') || 'Failed to load batch');
    router.push({ name: 'admin-batches' });
  } finally {
    loading.value = false;
  }
}

async function submit() {
  try {
    saving.value = true;

    // Prepare translations array
    const translations = Object.values(form.value.translations).filter(t => t.name);

    const payload = {
      ...form.value,
      translations,
    };

    const response = await put(`/admin/batches/${route.params.id}`, payload);

    if (response && response.success) {
      toast.success(response.message || t('admin.batches.updated') || 'Batch updated successfully');
      router.push({ name: 'admin-batches' });
    }
  } catch (err) {
    console.error('Error updating batch:', err);
    toast.error(err.response?.data?.message || err.message || t('errors.saveError') || 'Failed to update batch');
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  await loadPrograms();
  await loadBatch();
});
</script>

