<template>
  <div class="space-y-6 max-w-6xl">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.branding.title') }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $t('admin.branding.description') }}</p>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-8">
      <!-- Brand Name Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.brandName') }}</h3>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('admin.branding.displayName') }}
          </label>
          <input
            v-model="form['branding.name.display']"
            type="text"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
            :placeholder="$t('admin.branding.displayNamePlaceholder')"
          />
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $t('admin.branding.displayNameHint') }}</p>
        </div>
      </div>

      <!-- Logos Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.logos') }}</h3>
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Default Logo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.logoDefault') }}
            </label>
            <div class="space-y-2">
              <div v-if="logoPreviews.default" class="relative">
                <img :src="logoPreviews.default" alt="Default logo" class="h-24 w-24 rounded-lg object-cover border-2 border-slate-200 dark:border-slate-600" />
                <button
                  type="button"
                  @click="removeLogo('default')"
                  class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <input
                type="file"
                accept="image/*"
                @change="handleLogoChange('default', $event)"
                class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
              />
            </div>
          </div>

          <!-- Dark Logo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.logoDark') }}
            </label>
            <div class="space-y-2">
              <div v-if="logoPreviews.dark" class="relative">
                <img :src="logoPreviews.dark" alt="Dark logo" class="h-24 w-24 rounded-lg object-cover border-2 border-slate-200 dark:border-slate-600 bg-slate-800" />
                <button
                  type="button"
                  @click="removeLogo('dark')"
                  class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <input
                type="file"
                accept="image/*"
                @change="handleLogoChange('dark', $event)"
                class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
              />
            </div>
          </div>

          <!-- Favicon -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.favicon') }}
            </label>
            <div class="space-y-2">
              <div v-if="logoPreviews.favicon" class="relative">
                <img :src="logoPreviews.favicon" alt="Favicon" class="h-16 w-16 rounded-lg object-cover border-2 border-slate-200 dark:border-slate-600" />
                <button
                  type="button"
                  @click="removeLogo('favicon')"
                  class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <input
                type="file"
                accept="image/*"
                @change="handleLogoChange('favicon', $event)"
                class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
              />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $t('admin.branding.faviconHint') }}</p>
          </div>
        </div>
      </div>

      <!-- Colors Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.colors') }}</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.primaryColor') }}
            </label>
            <div class="flex items-center gap-3">
              <input
                v-model="form['branding.colors.primary']"
                type="color"
                class="h-12 w-20 rounded-lg border-2 border-slate-300 dark:border-slate-600 cursor-pointer"
              />
              <input
                v-model="form['branding.colors.primary']"
                type="text"
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="#3b82f6"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.secondaryColor') }}
            </label>
            <div class="flex items-center gap-3">
              <input
                v-model="form['branding.colors.secondary']"
                type="color"
                class="h-12 w-20 rounded-lg border-2 border-slate-300 dark:border-slate-600 cursor-pointer"
              />
              <input
                v-model="form['branding.colors.secondary']"
                type="text"
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="#0ea5e9"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.backgroundColor') }}
            </label>
            <div class="flex items-center gap-3">
              <input
                v-model="form['branding.colors.background']"
                type="color"
                class="h-12 w-20 rounded-lg border-2 border-slate-300 dark:border-slate-600 cursor-pointer"
              />
              <input
                v-model="form['branding.colors.background']"
                type="text"
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="#ffffff"
              />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.textColor') }}
            </label>
            <div class="flex items-center gap-3">
              <input
                v-model="form['branding.colors.text']"
                type="color"
                class="h-12 w-20 rounded-lg border-2 border-slate-300 dark:border-slate-600 cursor-pointer"
              />
              <input
                v-model="form['branding.colors.text']"
                type="text"
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="#111111"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Fonts Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.fonts') || 'Fonts' }}</h3>
        
        <!-- Font Source Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ $t('admin.branding.fontSource') || 'Font Source' }}
          </label>
          <select
            v-model="form['branding.fonts.source']"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
          >
            <option value="system">{{ $t('admin.branding.useSystemFont') || 'Use System Font' }}</option>
            <option value="custom">{{ $t('admin.branding.uploadCustomFont') || 'Upload Custom Font' }}</option>
          </select>
        </div>

        <!-- System Fonts -->
        <div v-if="form['branding.fonts.source'] === 'system'" class="space-y-4">
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('admin.branding.mainFont') || 'Main Font' }}
              </label>
              <select
                v-model="form['branding.fonts.main']"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                :style="{ fontFamily: form['branding.fonts.main'] }"
              >
                <option v-for="font in availableFonts" :key="font.id" :value="font.family">
                  {{ font.label }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('admin.branding.headingsFont') || 'Headings Font' }}
              </label>
              <select
                v-model="form['branding.fonts.headings']"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
                :style="{ fontFamily: form['branding.fonts.headings'] }"
              >
                <option v-for="font in availableFonts" :key="font.id" :value="font.family">
                  {{ font.label }}
                </option>
              </select>
            </div>
          </div>
          
          <!-- Live Preview -->
          <div class="mt-4 p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <h4 class="text-lg font-bold mb-2" :style="{ fontFamily: form['branding.fonts.headings'] }">
              {{ $t('admin.branding.fontPreview') || 'Font Preview' }}
            </h4>
            <p :style="{ fontFamily: form['branding.fonts.main'] }">
              {{ $t('admin.branding.fontPreviewText') || 'This is how the main font will look. The quick brown fox jumps over the lazy dog.' }}
            </p>
          </div>
        </div>

        <!-- Custom Font Upload -->
        <div v-else class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.customFontFile') || 'Custom Font File' }}
            </label>
            <div class="space-y-2">
              <div v-if="customFontPreview" class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $t('admin.branding.customFontPreview') || 'Custom Font Preview:' }}</p>
                <p :style="{ fontFamily: 'CustomFont' }" class="text-lg">
                  {{ $t('admin.branding.customFontPreviewText') || 'This is how your custom font will look. The quick brown fox jumps over the lazy dog.' }}
                </p>
              </div>
              <input
                type="file"
                accept=".ttf,.woff,.woff2"
                @change="handleCustomFontChange"
                class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
              />
              <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $t('admin.branding.customFontHint') || 'Accepted formats: TTF, WOFF, WOFF2. Max size: 5MB. Note: Large font files may affect loading performance.' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Layout Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.layout') }}</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.borderRadius') }}
            </label>
            <input
              v-model="form['branding.layout.radius']"
              type="text"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="0.5rem"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('admin.branding.shadowLevel') }}
            </label>
            <input
              v-model="form['branding.layout.shadow']"
              type="text"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
              placeholder="0"
            />
          </div>
        </div>
      </div>

      <!-- Preview Section -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.branding.preview') }}</h3>
        <div class="p-6 rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-600" :style="previewStyle">
          <div class="flex items-center gap-3 mb-4">
            <div v-if="logoPreviews.default" class="h-12 w-12 rounded-lg overflow-hidden">
              <img :src="logoPreviews.default" alt="Logo" class="h-full w-full object-cover" />
            </div>
            <h4 class="text-xl font-bold" :style="{ fontFamily: form['branding.fonts.headings'] }">
              {{ form['branding.name.display'] || 'Brand Name' }}
            </h4>
          </div>
          <p class="mb-4" :style="{ fontFamily: form['branding.fonts.main'] }">
            This is a preview of how your branding will look. The colors, fonts, and logo will be applied throughout the application.
          </p>
          <button
            type="button"
            class="px-6 py-2 rounded-lg font-semibold text-white"
            :style="{ backgroundColor: form['branding.colors.primary'] }"
          >
            Primary Button
          </button>
          <button
            type="button"
            class="ml-3 px-6 py-2 rounded-lg font-semibold text-white"
            :style="{ backgroundColor: form['branding.colors.secondary'] }"
          >
            Secondary Button
          </button>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-4">
        <button
          type="button"
          @click="resetForm"
          class="px-6 py-3 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
        >
          {{ $t('common.cancel') }}
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">{{ $t('common.loading') }}</span>
          <span v-else>{{ $t('common.save') }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, computed } from 'vue';
import { useBrandingStore } from '../../../stores/branding';
import { useToast } from '../../../composables/useToast';
import api from '../../../api';

const brandingStore = useBrandingStore();
const toast = useToast();
const loading = ref(false);

const form = reactive({
  'branding.name.display': '',
  'branding.colors.primary': '#3b82f6',
  'branding.colors.secondary': '#0ea5e9',
  'branding.colors.background': '#ffffff',
  'branding.colors.text': '#111111',
  'branding.fonts.source': 'system',
  'branding.fonts.main': 'Cairo',
  'branding.fonts.headings': 'Poppins',
  'branding.layout.radius': '0.5rem',
  'branding.layout.shadow': '0',
});

const availableFonts = ref([]);
const customFontFile = ref(null);
const customFontPreview = ref(null);

const logoFiles = reactive({
  default: null,
  dark: null,
  favicon: null,
});

const logoPreviews = reactive({
  default: null,
  dark: null,
  favicon: null,
});

const previewStyle = computed(() => ({
  backgroundColor: form['branding.colors.background'],
  color: form['branding.colors.text'],
  borderRadius: form['branding.layout.radius'],
}));

async function loadBranding() {
  try {
    const response = await api.get('/admin/branding');
    const branding = response.data.data || response.data;
    
    // Populate form
    if (branding['branding.name.display']) {
      form['branding.name.display'] = branding['branding.name.display'];
    }
    if (branding['branding.colors.primary']) {
      form['branding.colors.primary'] = branding['branding.colors.primary'];
    }
    if (branding['branding.colors.secondary']) {
      form['branding.colors.secondary'] = branding['branding.colors.secondary'];
    }
    if (branding['branding.colors.background']) {
      form['branding.colors.background'] = branding['branding.colors.background'];
    }
    if (branding['branding.colors.text']) {
      form['branding.colors.text'] = branding['branding.colors.text'];
    }
    if (branding['branding.fonts.source']) {
      form['branding.fonts.source'] = branding['branding.fonts.source'];
    }
    if (branding['branding.fonts.main']) {
      form['branding.fonts.main'] = branding['branding.fonts.main'];
    }
    if (branding['branding.fonts.headings']) {
      form['branding.fonts.headings'] = branding['branding.fonts.headings'];
    }
    if (branding['branding.layout.radius']) {
      form['branding.layout.radius'] = branding['branding.layout.radius'];
    }
    if (branding['branding.layout.shadow']) {
      form['branding.layout.shadow'] = branding['branding.layout.shadow'];
    }

    // Load available fonts
    if (branding.fonts?.available_fonts) {
      availableFonts.value = branding.fonts.available_fonts;
    } else if (branding['branding.fonts.available_fonts']) {
      try {
        availableFonts.value = JSON.parse(branding['branding.fonts.available_fonts']);
      } catch (e) {
        console.error('Error parsing available fonts:', e);
      }
    }

    // Load custom font preview if exists
    if (branding.fonts?.custom_file_url) {
      customFontPreview.value = branding.fonts.custom_file_url;
    } else if (branding['branding.fonts.custom_file']) {
      customFontPreview.value = branding['branding.fonts.custom_file'];
    }

    // Load logo previews
    if (branding['branding.logo.default']) {
      logoPreviews.default = branding['branding.logo.default'];
    }
    if (branding['branding.logo.dark']) {
      logoPreviews.dark = branding['branding.logo.dark'];
    }
    if (branding['branding.logo.favicon']) {
      logoPreviews.favicon = branding['branding.logo.favicon'];
    }
  } catch (error) {
    console.error('Error loading branding:', error);
    toast.error('Failed to load branding settings');
  }
}

function handleCustomFontChange(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  // Validate file type
  const validTypes = ['font/ttf', 'font/woff', 'font/woff2', 'application/font-woff', 'application/font-woff2'];
  const validExtensions = ['.ttf', '.woff', '.woff2'];
  const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
  
  if (!validExtensions.includes(fileExtension)) {
    toast.error('Invalid font file. Please upload a TTF, WOFF, or WOFF2 file.');
    return;
  }

  // Validate file size (5MB max)
  if (file.size > 5 * 1024 * 1024) {
    toast.error('Font file is too large. Maximum size is 5MB.');
    return;
  }

  customFontFile.value = file;
  
  // Create preview URL
  const reader = new FileReader();
  reader.onload = (e) => {
    const fontUrl = URL.createObjectURL(file);
    customFontPreview.value = fontUrl;
    
    // Inject @font-face for preview
    const styleId = 'custom-font-preview';
    let styleElement = document.getElementById(styleId);
    if (!styleElement) {
      styleElement = document.createElement('style');
      styleElement.id = styleId;
      document.head.appendChild(styleElement);
    }
    
    const format = fileExtension === '.woff2' ? 'woff2' : fileExtension === '.woff' ? 'woff' : 'truetype';
    styleElement.textContent = `
      @font-face {
        font-family: 'CustomFont';
        src: url('${fontUrl}') format('${format}');
        font-display: swap;
      }
    `;
  };
  reader.readAsDataURL(file);
}

function handleLogoChange(type, event) {
  const file = event.target.files?.[0];
  if (!file) return;

  logoFiles[type] = file;
  logoPreviews[type] = URL.createObjectURL(file);
}

function removeLogo(type) {
  logoFiles[type] = null;
  logoPreviews[type] = null;
}

function resetForm() {
  loadBranding();
}

async function handleSubmit() {
  loading.value = true;
  try {
    const formData = new FormData();

    // Add form fields
    Object.entries(form).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        formData.append(key, value);
      }
    });

    // Add logo files
    if (logoFiles.default) {
      formData.append('branding.logo.default', logoFiles.default);
    }
    if (logoFiles.dark) {
      formData.append('branding.logo.dark', logoFiles.dark);
    }
    if (logoFiles.favicon) {
      formData.append('branding.logo.favicon', logoFiles.favicon);
    }

    // Add custom font file
    if (customFontFile.value) {
      formData.append('branding.fonts.custom_file', customFontFile.value);
    }

    await api.post('/admin/branding/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    // Reload branding in store
    await brandingStore.fetchBranding();

    toast.success('Branding updated successfully');
  } catch (error) {
    console.error('Error updating branding:', error);
    toast.error(error.response?.data?.message || 'Failed to update branding');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadBranding();
});
</script>

