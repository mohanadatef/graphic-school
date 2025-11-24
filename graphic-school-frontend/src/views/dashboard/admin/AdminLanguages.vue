<template>
  <div class="space-y-6 max-w-4xl">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('admin.language.title') || 'Language Settings' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
        {{ $t('admin.language.subtitle') || 'Manage default language, available languages, and RTL settings' }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6 space-y-6">
      <!-- Default Locale -->
      <div>
        <label class="label">
          {{ $t('admin.language.defaultLocale') || 'Default Language' }}
        </label>
        <select v-model="form.default_locale" class="input" required>
          <option value="ar">العربية (Arabic)</option>
          <option value="en">English</option>
        </select>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
          {{ $t('admin.language.defaultLocaleHint') || 'The language that will be shown when users first visit the site' }}
        </p>
      </div>

      <!-- Available Locales -->
      <div>
        <label class="label">
          {{ $t('admin.language.availableLocales') || 'Available Languages' }}
        </label>
        <div class="space-y-3 mt-2">
          <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
            <input
              type="checkbox"
              v-model="form.available_locales"
              value="ar"
              class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary"
            />
            <div class="flex-1">
              <span class="font-medium text-slate-900 dark:text-white">العربية (Arabic)</span>
              <p class="text-xs text-slate-500 dark:text-slate-400">Right-to-left language</p>
            </div>
          </label>
          <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
            <input
              type="checkbox"
              v-model="form.available_locales"
              value="en"
              class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary"
            />
            <div class="flex-1">
              <span class="font-medium text-slate-900 dark:text-white">English</span>
              <p class="text-xs text-slate-500 dark:text-slate-400">Left-to-right language</p>
            </div>
          </label>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
          {{ $t('admin.language.availableLocalesHint') || 'Languages that users can select from the language switcher' }}
        </p>
      </div>

      <!-- RTL Locales -->
      <div>
        <label class="label">
          {{ $t('admin.language.rtlLocales') || 'Right-to-Left Languages' }}
        </label>
        <div class="space-y-3 mt-2">
          <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
            <input
              type="checkbox"
              v-model="form.rtl_locales"
              value="ar"
              class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-primary focus:ring-primary"
            />
            <div class="flex-1">
              <span class="font-medium text-slate-900 dark:text-white">العربية (Arabic)</span>
              <p class="text-xs text-slate-500 dark:text-slate-400">Enable RTL layout for Arabic</p>
            </div>
          </label>
        </div>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
          {{ $t('admin.language.rtlLocalesHint') || 'Languages that should use right-to-left text direction' }}
        </p>
      </div>

      <!-- Preview -->
      <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">
          {{ $t('admin.language.preview') || 'Preview' }}
        </h3>
        <div class="space-y-2 text-sm">
          <div>
            <span class="text-slate-500 dark:text-slate-400">Default:</span>
            <span class="ml-2 font-medium text-slate-900 dark:text-white">
              {{ form.default_locale === 'ar' ? 'العربية' : 'English' }}
            </span>
          </div>
          <div>
            <span class="text-slate-500 dark:text-slate-400">Available:</span>
            <span class="ml-2 font-medium text-slate-900 dark:text-white">
              {{ form.available_locales.map(l => l === 'ar' ? 'العربية' : 'English').join(', ') }}
            </span>
          </div>
          <div>
            <span class="text-slate-500 dark:text-slate-400">RTL:</span>
            <span class="ml-2 font-medium text-slate-900 dark:text-white">
              {{ form.rtl_locales.length > 0 ? form.rtl_locales.map(l => l === 'ar' ? 'العربية' : 'English').join(', ') : 'None' }}
            </span>
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
          <span>{{ saving ? ($t('common.saving') || 'Saving...') : ($t('admin.save') || 'Save Changes') }}</span>
        </button>
        <button
          type="button"
          @click="loadSettings"
          :disabled="saving"
          class="btn-secondary"
        >
          {{ $t('common.cancel') || 'Cancel' }}
        </button>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <p class="text-sm text-green-700 dark:text-green-300">{{ successMessage }}</p>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          <p class="text-sm text-red-700 dark:text-red-300">{{ errorMessage }}</p>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useI18n } from 'vue-i18n';
import { useLocale } from '../../../composables/useLocale';

const { get, post } = useApi();
const toast = useToast();
const { t, locale: i18nLocale } = useI18n();
const { setLocale, locale: currentLocale } = useLocale();

const loading = ref(true);
const saving = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const form = ref({
  default_locale: 'ar',
  available_locales: ['ar', 'en'],
  rtl_locales: ['ar'],
});

async function loadSettings() {
  try {
    loading.value = true;
    errorMessage.value = '';
    const response = await get('/admin/language');
    
    if (response && response.data) {
      form.value = {
        default_locale: response.data.default_locale || 'ar',
        available_locales: response.data.available_locales || ['ar', 'en'],
        rtl_locales: response.data.rtl_locales || ['ar'],
      };
    }
  } catch (err) {
    console.error('Error loading language settings:', err);
    errorMessage.value = err.response?.data?.message || err.message || t('errors.loadDataError') || 'Failed to load language settings';
    toast.error(errorMessage.value);
  } finally {
    loading.value = false;
  }
}

async function submitForm() {
  try {
    saving.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    // Validate
    if (!form.value.available_locales || form.value.available_locales.length === 0) {
      errorMessage.value = t('admin.language.errors.noAvailableLocales') || 'At least one language must be available';
      return;
    }

    if (!form.value.available_locales.includes(form.value.default_locale)) {
      errorMessage.value = t('admin.language.errors.defaultNotAvailable') || 'Default language must be in available languages';
      return;
    }

    const response = await post('/admin/language/update', form.value);
    
    if (response && response.success) {
      successMessage.value = t('admin.language.saved') || 'Language settings saved successfully';
      toast.success(successMessage.value);
      
      // Update current locale if default changed
      if (form.value.default_locale !== currentLocale.value) {
        setLocale(form.value.default_locale);
      }
      
      // Update HTML dir and lang attributes
      if (typeof document !== 'undefined') {
        const isRTL = form.value.rtl_locales.includes(form.value.default_locale);
        document.documentElement.setAttribute('dir', isRTL ? 'rtl' : 'ltr');
        document.documentElement.setAttribute('lang', form.value.default_locale);
      }
      
      // Update localStorage for Accept-Language header
      localStorage.setItem('locale', form.value.default_locale);
      localStorage.setItem('gs_locale', form.value.default_locale);
      
      // Trigger locale change event
      if (typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent('locale-changed', { 
          detail: { 
            locale: form.value.default_locale,
            rtl_locales: form.value.rtl_locales,
            available_locales: form.value.available_locales
          } 
        }));
      }
    }
  } catch (err) {
    console.error('Error saving language settings:', err);
    errorMessage.value = err.response?.data?.message || err.message || t('errors.saveError') || 'Failed to save language settings';
    toast.error(errorMessage.value);
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  loadSettings();
});
</script>

