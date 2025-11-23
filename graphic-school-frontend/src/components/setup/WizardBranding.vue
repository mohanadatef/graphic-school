<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $t('setup.branding.title') || 'Branding & Appearance' }}
      </h2>
      <p class="text-slate-600 dark:text-slate-400">
        {{ $t('setup.branding.description') || 'Customize your academy\'s look and feel' }}
      </p>
    </div>

    <form @submit.prevent="handleNext" class="space-y-6">
      <!-- Logo Upload -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.branding.logo') || 'Logo' }}
        </label>
        <div class="flex items-center gap-4">
          <div v-if="logoPreview" class="relative">
            <img :src="logoPreview" alt="Logo preview" class="h-24 w-24 rounded-lg object-cover border-2 border-slate-200 dark:border-slate-600" />
            <button
              type="button"
              @click="removeLogo"
              class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div>
            <input
              type="file"
              accept="image/*"
              @change="handleLogoChange"
              class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
            />
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              {{ $t('setup.branding.logoHint') || 'PNG, JPG up to 4MB' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Colors -->
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.branding.primaryColor') || 'Primary Color' }}
          </label>
          <div class="flex items-center gap-3">
            <input
              v-model="localData.primary_color"
              type="color"
              class="h-12 w-20 rounded border border-slate-300 dark:border-slate-600 cursor-pointer"
            />
            <input
              v-model="localData.primary_color"
              type="text"
              class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.branding.secondaryColor') || 'Secondary Color' }}
          </label>
          <div class="flex items-center gap-3">
            <input
              v-model="localData.secondary_color"
              type="color"
              class="h-12 w-20 rounded border border-slate-300 dark:border-slate-600 cursor-pointer"
            />
            <input
              v-model="localData.secondary_color"
              type="text"
              class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Color Preview -->
      <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-600" :style="{ backgroundColor: localData.primary_color + '20' }">
        <div class="flex items-center gap-4">
          <div class="h-16 w-16 rounded-lg" :style="{ backgroundColor: localData.primary_color }"></div>
          <div class="h-16 w-16 rounded-lg" :style="{ backgroundColor: localData.secondary_color }"></div>
          <div class="flex-1">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Color Preview</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">These colors will be used throughout your website</p>
          </div>
        </div>
      </div>

      <!-- Fonts -->
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.branding.mainFont') || 'Main Font' }}
          </label>
          <select
            v-model="localData.font_main"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            :style="{ fontFamily: localData.font_main }"
          >
            <option v-for="font in fonts" :key="font" :value="font" :style="{ fontFamily: font }">
              {{ font }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('setup.branding.headingsFont') || 'Headings Font' }}
          </label>
          <select
            v-model="localData.font_headings"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            :style="{ fontFamily: localData.font_headings }"
          >
            <option v-for="font in fonts" :key="font" :value="font" :style="{ fontFamily: font }">
              {{ font }}
            </option>
          </select>
        </div>
      </div>

      <!-- Font Preview -->
      <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-600">
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">Font Preview</p>
        <h3 class="text-2xl font-bold mb-2" :style="{ fontFamily: localData.font_headings }">
          Heading Example
        </h3>
        <p class="text-base" :style="{ fontFamily: localData.font_main }">
          This is how your main text will look. The font will be applied throughout your website.
        </p>
      </div>

      <!-- Default Theme -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
          {{ $t('setup.branding.defaultTheme') || 'Default Theme' }}
        </label>
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              v-model="localData.default_theme"
              type="radio"
              value="light"
              class="w-4 h-4 text-primary focus:ring-primary"
            />
            <span class="text-slate-700 dark:text-slate-300">{{ $t('setup.branding.light') || 'Light' }}</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              v-model="localData.default_theme"
              type="radio"
              value="dark"
              class="w-4 h-4 text-primary focus:ring-primary"
            />
            <span class="text-slate-700 dark:text-slate-300">{{ $t('setup.branding.dark') || 'Dark' }}</span>
          </label>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-between pt-4">
        <button
          type="button"
          @click="$emit('back')"
          class="px-6 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
        >
          {{ $t('setup.back') || 'Back' }}
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          {{ loading ? ($t('setup.saving') || 'Saving...') : ($t('setup.next') || 'Next') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { useSetupWizardStore } from '../../stores/setupWizard';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['update:modelValue', 'next', 'back']);

const store = useSetupWizardStore();
const toast = useToast();

const localData = reactive({
  logo: props.modelValue.logo || null,
  primary_color: props.modelValue.primary_color || '#3b82f6',
  secondary_color: props.modelValue.secondary_color || '#6366f1',
  font_main: props.modelValue.font_main || 'Cairo',
  font_headings: props.modelValue.font_headings || 'Poppins',
  default_theme: props.modelValue.default_theme || 'light',
});

const logoPreview = ref(null);
const loading = ref(false);

const fonts = [
  'Cairo',
  'Poppins',
  'Inter',
  'Roboto',
  'Open Sans',
  'Lato',
  'Montserrat',
  'Tajawal',
  'IBM Plex Sans Arabic',
  'Noto Sans Arabic',
  'Almarai',
];

// Watch for changes and emit
watch(localData, (newVal) => {
  emit('update:modelValue', newVal);
}, { deep: true });

// Handle logo change
function handleLogoChange(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  if (file.size > 4 * 1024 * 1024) {
    toast.error('Logo file size must be less than 4MB');
    return;
  }

  localData.logo = file;
  logoPreview.value = URL.createObjectURL(file);
}

// Remove logo
function removeLogo() {
  localData.logo = null;
  logoPreview.value = null;
}

// Handle next
async function handleNext() {
  try {
    loading.value = true;
    
    // Update store
    Object.assign(store.formData.branding, localData);
    
    // Save to backend
    await store.saveStep(2, {
      branding: localData,
    });

    toast.success('Branding settings saved');
    emit('next');
  } catch (error) {
    toast.error('Failed to save branding settings');
    console.error('Error saving step 2:', error);
  } finally {
    loading.value = false;
  }
}
</script>

