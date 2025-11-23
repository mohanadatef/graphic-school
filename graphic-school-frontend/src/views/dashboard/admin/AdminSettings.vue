<template>
  <div class="space-y-6 max-w-5xl">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.settings') || 'إعدادات الموقع' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $t('admin.settingsDescription') || 'تحديث بيانات الاتصال والهوية والإعدادات العامة.' }}</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-slate-200 dark:border-slate-700">
      <nav class="flex space-x-8" aria-label="Tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            activeTab === tab.id
              ? 'border-primary text-primary dark:text-primary'
              : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300',
            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
          ]"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- General Settings Tab -->
    <form v-if="activeTab === 'general'" @submit.prevent="submitGeneral" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6 space-y-4">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="label">{{ $t('admin.siteName') || 'اسم الموقع' }}</label>
          <input v-model="generalForm.site_name" class="input" />
        </div>
        <div>
          <label class="label">{{ $t('admin.email') || 'البريد الإلكتروني' }}</label>
          <input v-model="generalForm.email" type="email" class="input" />
        </div>
        <div>
          <label class="label">{{ $t('admin.phone') || 'رقم الهاتف' }}</label>
          <input v-model="generalForm.phone" class="input" />
        </div>
        <div>
          <label class="label">{{ $t('admin.address') || 'العنوان' }}</label>
          <input v-model="generalForm.address" class="input" />
        </div>
        <div>
          <label class="label">{{ $t('admin.primaryColor') || 'اللون الأساسي' }}</label>
          <input v-model="generalForm.primary_color" type="color" class="input h-12" />
        </div>
        <div>
          <label class="label">{{ $t('admin.secondaryColor') || 'اللون الثانوي' }}</label>
          <input v-model="generalForm.secondary_color" type="color" class="input h-12" />
        </div>
        <div class="md:col-span-2">
          <label class="label">{{ $t('admin.aboutUs') || 'عن الأكاديمية' }}</label>
          <textarea v-model="generalForm.about_us" rows="4" class="input"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="label">{{ $t('admin.logo') || 'شعار الموقع' }}</label>
          <div class="flex items-center gap-4">
            <img v-if="logoPreview" :src="logoPreview" alt="logo" class="h-16 w-16 rounded-lg object-cover border border-slate-200 dark:border-slate-600" />
            <input type="file" accept="image/*" class="input" @change="handleLogoChange" />
          </div>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.logoHint') || 'يدعم صور PNG / JPG بحد أقصى 4MB' }}</p>
        </div>
      </div>
      <button type="submit" class="px-5 py-3 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
        {{ $t('admin.save') || 'حفظ التغييرات' }}
      </button>
      <p v-if="generalSaved" class="text-green-600 dark:text-green-400 text-sm mt-2">{{ $t('admin.saved') || 'تم التحديث بنجاح' }}</p>
    </form>

    <!-- Language Settings Tab -->
    <form v-if="activeTab === 'language'" @submit.prevent="submitLanguage" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6 space-y-4">
      <div class="space-y-4">
        <div>
          <label class="label">{{ $t('admin.defaultLanguage') || 'اللغة الافتراضية' }}</label>
          <select v-model="languageForm.default_language" class="input">
            <option value="ar">العربية</option>
            <option value="en">English</option>
          </select>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.defaultLanguageHint') || 'اللغة التي ستظهر عند فتح الموقع لأول مرة' }}</p>
        </div>
        <div>
          <label class="label">{{ $t('admin.availableLanguages') || 'اللغات المتاحة' }}</label>
          <div class="space-y-2">
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="languageForm.available_languages" value="ar" class="rounded" />
              <span>العربية</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="checkbox" v-model="languageForm.available_languages" value="en" class="rounded" />
              <span>English</span>
            </label>
          </div>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.availableLanguagesHint') || 'اللغات التي يمكن للمستخدمين الاختيار منها' }}</p>
        </div>
      </div>
      <button type="submit" class="px-5 py-3 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
        {{ $t('admin.save') || 'حفظ التغييرات' }}
      </button>
      <p v-if="languageSaved" class="text-green-600 dark:text-green-400 text-sm mt-2">{{ $t('admin.saved') || 'تم التحديث بنجاح' }}</p>
    </form>

    <!-- Currency Settings Tab -->
    <form v-if="activeTab === 'currency'" @submit.prevent="submitCurrency" class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6 space-y-4">
      <div class="space-y-4">
        <div>
          <label class="label">{{ $t('admin.defaultCurrency') || 'العملة الافتراضية' }}</label>
          <select v-model="currencyForm.default_currency" class="input">
            <option value="EGP">EGP - جنيه مصري (ج.م)</option>
            <option value="SAR">SAR - ريال سعودي (ر.س)</option>
            <option value="AED">AED - درهم إماراتي (د.إ)</option>
            <option value="KWD">KWD - دينار كويتي (د.ك)</option>
            <option value="BHD">BHD - دينار بحريني (د.ب)</option>
            <option value="OMR">OMR - ريال عماني (ر.ع)</option>
            <option value="QAR">QAR - ريال قطري (ر.ق)</option>
            <option value="USD">USD - دولار أمريكي ($)</option>
          </select>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.defaultCurrencyHint') || 'العملة المستخدمة في الفواتير والاشتراكات' }}</p>
        </div>
        <div>
          <label class="label">{{ $t('admin.currencySymbol') || 'رمز العملة' }}</label>
          <input v-model="currencyForm.currency_symbol" class="input" placeholder="ج.م" />
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.currencySymbolHint') || 'الرمز الذي سيظهر بجانب المبالغ (مثال: ج.م، ر.س، $)' }}</p>
        </div>
        <div>
          <label class="label">{{ $t('admin.currencyPosition') || 'موضع رمز العملة' }}</label>
          <select v-model="currencyForm.currency_position" class="input">
            <option value="after">{{ $t('admin.after') || 'بعد الرقم' }}</option>
            <option value="before">{{ $t('admin.before') || 'قبل الرقم' }}</option>
          </select>
          <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('admin.currencyPositionHint') || 'هل يظهر رمز العملة قبل أو بعد المبلغ؟' }}</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-lg">
          <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('admin.preview') || 'معاينة' }}:</p>
          <p class="text-2xl font-bold text-primary">
            {{ formatCurrencyPreview(1000) }}
          </p>
        </div>
      </div>
      <button type="submit" class="px-5 py-3 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
        {{ $t('admin.save') || 'حفظ التغييرات' }}
      </button>
      <p v-if="currencySaved" class="text-green-600 dark:text-green-400 text-sm mt-2">{{ $t('admin.saved') || 'تم التحديث بنجاح' }}</p>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, computed } from 'vue';
import api from '../../../api';
import { useCurrency } from '../../../composables/useCurrency';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const { formatCurrency } = useCurrency();

const activeTab = ref('general');
const tabs = [
  { id: 'general', label: 'الإعدادات العامة' },
  { id: 'language', label: 'اللغة' },
  { id: 'currency', label: 'العملة' },
];

// General Settings
const generalForm = reactive({
  site_name: '',
  email: '',
  phone: '',
  address: '',
  about_us: '',
  primary_color: '#1d4ed8',
  secondary_color: '#f97316',
});
const generalSaved = ref(false);
const logoPreview = ref('');
const logoFile = ref(null);

// Language Settings
const languageForm = reactive({
  default_language: 'ar',
  available_languages: ['ar', 'en'],
});
const languageSaved = ref(false);

// Currency Settings
const currencyForm = reactive({
  default_currency: 'EGP',
  currency_symbol: 'ج.م',
  currency_position: 'after',
});
const currencySaved = ref(false);

// Format currency preview
function formatCurrencyPreview(value) {
  const formattedNumber = new Intl.NumberFormat('ar-EG', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(value);
  
  if (currencyForm.currency_position === 'before') {
    return `${currencyForm.currency_symbol} ${formattedNumber}`;
  } else {
    return `${formattedNumber} ${currencyForm.currency_symbol}`;
  }
}

// Load general settings
async function loadGeneralSettings() {
  try {
    const { data } = await api.get('/admin/settings');
    generalForm.site_name = data.site_name || '';
    generalForm.email = data.email || '';
    generalForm.phone = data.phone || '';
    generalForm.address = data.address || '';
    generalForm.about_us = data.about_us || '';
    generalForm.primary_color = data.primary_color || generalForm.primary_color;
    generalForm.secondary_color = data.secondary_color || generalForm.secondary_color;
    logoPreview.value = data.logo_url || data.logo || '';
  } catch (error) {
    console.error('Error loading general settings:', error);
  }
}

// Load language settings
async function loadLanguageSettings() {
  try {
    const { data } = await api.get('/admin/system-settings');
    const languages = data.languages || {};
    languageForm.default_language = languages.default_language || 'ar';
    languageForm.available_languages = languages.available_languages || ['ar', 'en'];
  } catch (error) {
    console.error('Error loading language settings:', error);
  }
}

// Load currency settings
async function loadCurrencySettings() {
  try {
    const { data } = await api.get('/admin/system-settings');
    const currency = data.currency || {};
    currencyForm.default_currency = currency.default_currency || 'EGP';
    currencyForm.currency_symbol = currency.currency_symbol || getDefaultSymbol(currencyForm.default_currency);
    currencyForm.currency_position = currency.currency_position || 'after';
  } catch (error) {
    console.error('Error loading currency settings:', error);
  }
}

// Get default symbol for currency
function getDefaultSymbol(curr) {
  const symbols = {
    'EGP': 'ج.م',
    'SAR': 'ر.س',
    'AED': 'د.إ',
    'KWD': 'د.ك',
    'BHD': 'د.ب',
    'OMR': 'ر.ع',
    'QAR': 'ر.ق',
    'USD': '$',
  };
  return symbols[curr] || curr;
}

// Handle logo change
function handleLogoChange(event) {
  const file = event.target.files?.[0];
  if (!file) {
    logoFile.value = null;
    return;
  }
  logoFile.value = file;
  logoPreview.value = URL.createObjectURL(file);
}

// Submit general settings
async function submitGeneral() {
  try {
    const payload = new FormData();
    Object.entries(generalForm).forEach(([key, value]) => {
      payload.append(key, value ?? '');
    });
    if (logoFile.value) {
      payload.append('logo_image', logoFile.value);
    }
    await api.post('/admin/settings', payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    generalSaved.value = true;
    toast.success('تم تحديث الإعدادات العامة بنجاح');
    setTimeout(() => (generalSaved.value = false), 2000);
  } catch (error) {
    console.error('Error updating general settings:', error);
    toast.error('حدث خطأ أثناء تحديث الإعدادات');
  }
}

// Submit language settings
async function submitLanguage() {
  try {
    await api.put('/admin/system-settings', {
      default_language: languageForm.default_language,
      available_languages: languageForm.available_languages,
    });
    languageSaved.value = true;
    toast.success('تم تحديث إعدادات اللغة بنجاح');
    setTimeout(() => (languageSaved.value = false), 2000);
    // Reload currency to get updated settings
    await loadCurrencySettings();
  } catch (error) {
    console.error('Error updating language settings:', error);
    toast.error('حدث خطأ أثناء تحديث إعدادات اللغة');
  }
}

// Submit currency settings
async function submitCurrency() {
  try {
    await api.put('/admin/system-settings', {
      default_currency: currencyForm.default_currency,
      currency_symbol: currencyForm.currency_symbol,
      currency_position: currencyForm.currency_position,
    });
    currencySaved.value = true;
    toast.success('تم تحديث إعدادات العملة بنجاح');
    setTimeout(() => (currencySaved.value = false), 2000);
    // Reload currency composable
    if (typeof window !== 'undefined') {
      localStorage.setItem('gs_currency', currencyForm.default_currency);
      localStorage.setItem('gs_currency_symbol', currencyForm.currency_symbol);
      localStorage.setItem('gs_currency_position', currencyForm.currency_position);
      // Trigger currency reload
      window.dispatchEvent(new Event('currency-updated'));
    }
  } catch (error) {
    console.error('Error updating currency settings:', error);
    toast.error('حدث خطأ أثناء تحديث إعدادات العملة');
  }
}

// Load all settings on mount
onMounted(async () => {
  await Promise.all([
    loadGeneralSettings(),
    loadLanguageSettings(),
    loadCurrencySettings(),
  ]);
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
  background-color: white;
  color: #111827;
}

.dark .input {
  border-color: #475569;
  background-color: #1e293b;
  color: #f1f5f9;
}

.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
  font-weight: 500;
}

.dark .label {
  color: #cbd5e1;
}
</style>
