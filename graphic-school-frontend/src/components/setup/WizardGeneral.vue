<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.general.title') || 'General Information' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.general.description') || 'Tell us about your academy' }}
      </p>
    </div>

    <form @submit.prevent="handleNext" class="space-y-6">
      <!-- Academy Name -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.general.academyName') || 'Academy Name' }} <span class="text-red-500">*</span>
        </label>
        <input
          v-model="localData.academy_name"
          type="text"
          required
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          :placeholder="$t('setup.general.academyNamePlaceholder') || 'Enter your academy name'"
        />
        <p v-if="errors.academy_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
          {{ errors.academy_name }}
        </p>
      </div>

      <!-- Country -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.general.country') || 'Country' }}
        </label>
        <select
          v-model="localData.country"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option value="">{{ $t('setup.general.selectCountry') || 'Select Country' }}</option>
          <option value="EG">Egypt</option>
          <option value="SA">Saudi Arabia</option>
          <option value="AE">United Arab Emirates</option>
          <option value="KW">Kuwait</option>
          <option value="BH">Bahrain</option>
          <option value="OM">Oman</option>
          <option value="QA">Qatar</option>
        </select>
      </div>

      <!-- Default Language -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.general.defaultLanguage') || 'Default Language' }}
        </label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              v-model="localData.default_language"
              type="radio"
              value="en"
              class="w-4 h-4 text-primary focus:ring-primary"
            />
            <span class="text-slate-700 dark:text-slate-300">English</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              v-model="localData.default_language"
              type="radio"
              value="ar"
              class="w-4 h-4 text-primary focus:ring-primary"
            />
            <span class="text-slate-700 dark:text-slate-300">العربية</span>
          </label>
        </div>
      </div>

      <!-- Timezone -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.general.timezone') || 'Timezone' }}
        </label>
        <select
          v-model="localData.timezone"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
            {{ tz.label }}
          </option>
        </select>
      </div>

      <!-- Default Currency -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.general.defaultCurrency') || 'Default Currency' }}
        </label>
        <select
          v-model="localData.default_currency"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
        >
          <option value="USD">USD - US Dollar ($)</option>
          <option value="EGP">EGP - Egyptian Pound (ج.م)</option>
          <option value="SAR">SAR - Saudi Riyal (ر.س)</option>
          <option value="AED">AED - UAE Dirham (د.إ)</option>
          <option value="KWD">KWD - Kuwaiti Dinar (د.ك)</option>
          <option value="BHD">BHD - Bahraini Dinar (د.ب)</option>
          <option value="OMR">OMR - Omani Rial (ر.ع)</option>
          <option value="QAR">QAR - Qatari Riyal (ر.ق)</option>
        </select>
      </div>

      <!-- Actions -->
      <div class="flex justify-between pt-4">
        <div></div>
        <div class="flex gap-3">
          <button
            type="button"
            @click="$emit('skip')"
            class="px-6 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
          >
            {{ $t('setup.skip') || 'Skip Setup' }}
          </button>
          <button
            type="submit"
            :disabled="loading || !localData.academy_name"
            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ loading ? ($t('setup.saving') || 'Saving...') : ($t('setup.next') || 'Next') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue';
import { useSetupWizardStore } from '../../stores/setupWizard';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:modelValue', 'next', 'skip']);

const store = useSetupWizardStore();
const toast = useToast();

const localData = reactive({
  academy_name: props.modelValue.academy_name || '',
  country: props.modelValue.country || '',
  default_language: props.modelValue.default_language || 'en',
  timezone: props.modelValue.timezone || 'UTC',
  default_currency: props.modelValue.default_currency || 'USD',
});

const errors = reactive({});
const loading = ref(false);

const timezones = [
  { value: 'UTC', label: 'UTC (Coordinated Universal Time)' },
  { value: 'Africa/Cairo', label: 'Africa/Cairo (Egypt)' },
  { value: 'Asia/Riyadh', label: 'Asia/Riyadh (Saudi Arabia)' },
  { value: 'Asia/Dubai', label: 'Asia/Dubai (UAE)' },
  { value: 'Asia/Kuwait', label: 'Asia/Kuwait (Kuwait)' },
  { value: 'Asia/Bahrain', label: 'Asia/Bahrain (Bahrain)' },
  { value: 'Asia/Muscat', label: 'Asia/Muscat (Oman)' },
  { value: 'Asia/Qatar', label: 'Asia/Qatar (Qatar)' },
];

// Auto-detect timezone
onMounted(() => {
  if (!localData.timezone || localData.timezone === 'UTC') {
    try {
      const detected = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (timezones.find(tz => tz.value === detected)) {
        localData.timezone = detected;
      }
    } catch (e) {
      // Fallback to UTC
    }
  }
});

// Watch for changes and emit
watch(localData, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

// Handle next
async function handleNext() {
  // Validate
  errors.academy_name = '';
  if (!localData.academy_name.trim()) {
    errors.academy_name = 'Academy name is required';
    return;
  }

  try {
    loading.value = true;
    
    // Update store
    Object.assign(store.formData.general, localData);
    
    // Save to backend - filter out empty strings
    const payload = {
      academy_name: localData.academy_name || null,
      country: localData.country || null,
      default_language: localData.default_language || null,
      timezone: localData.timezone || null,
      default_currency: localData.default_currency || null,
    };
    
    // Remove null values to avoid sending them
    Object.keys(payload).forEach(key => {
      if (payload[key] === null || payload[key] === '') {
        delete payload[key];
      }
    });
    
    await store.saveStep(1, payload);

    toast.success('General information saved');
    emit('next');
  } catch (error) {
    toast.error('Failed to save general information');
    console.error('Error saving step 1:', error);
  } finally {
    loading.value = false;
  }
}
</script>

