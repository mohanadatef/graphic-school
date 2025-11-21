<template>
  <div class="relative" style="pointer-events: auto; z-index: 50;">
    <button
      type="button"
      @click="toggleDropdown"
      class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer"
      :class="{ 'bg-gray-100 dark:bg-gray-800': isOpen }"
      :disabled="loading"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
        />
      </svg>
      <img
        v-if="currentLanguageImage"
        :src="currentLanguageImage"
        :alt="currentLanguageName"
        class="w-5 h-5 object-cover rounded"
        @error="handleImageError"
      />
      <span class="text-sm font-medium">{{ currentLanguageName }}</span>
      <svg
        v-if="!loading"
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 transition-transform"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
      <svg
        v-else
        class="animate-spin h-4 w-4"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </button>

    <Transition name="dropdown">
      <div
        v-if="isOpen"
        v-click-outside="closeDropdown"
        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg z-[100] border border-gray-200 dark:border-gray-700 max-h-80 overflow-y-auto"
      >
      <div class="py-1">
        <div v-if="loading" class="px-4 py-2 text-sm text-gray-500 text-center">
          {{ $t('common.loading') || 'Loading...' }}
        </div>
        <div v-else-if="availableLocales.length === 0" class="px-4 py-2 text-sm text-gray-500 text-center">
          {{ $t('common.noLanguages') || 'No languages available' }}
        </div>
        <button
          v-for="lang in availableLocales"
          :key="lang.code"
          @click="switchLanguage(lang.code)"
          class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center gap-3"
          :class="{ 'bg-gray-100 dark:bg-gray-700': currentLocale === lang.code }"
        >
          <div class="flex items-center gap-2 flex-1">
            <img
              v-if="lang.image_url || lang.image_path"
              :src="lang.image_url || lang.image_path"
              :alt="lang.native_name || lang.name"
              class="w-5 h-5 object-cover rounded"
              @error="handleImageError"
            />
            <span>{{ lang.native_name || lang.name }}</span>
          </div>
          <svg
            v-if="currentLocale === lang.code"
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4 text-primary flex-shrink-0"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>
      </div>
    </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useLocale } from '../../composables/useLocale';
import i18nGlobal from '../../i18n';
import api from '../../api';

const { locale, setLocale, isRTL } = useLocale();
const isOpen = ref(false);
const loading = ref(false);
const availableLocales = ref([]);

// Language flag/icon mapping
const languageFlags = {
  'en': '🇬🇧',
  'ar': '🇸🇦',
  'fr': '🇫🇷',
  'es': '🇪🇸',
  'de': '🇩🇪',
};

// Fetch available locales from API
async function fetchLocales() {
  if (loading.value) return;
  
  try {
    loading.value = true;
    const response = await api.get('/locales');
    
    // Handle unified API response format: { success, message, data: { locales: [...] } }
    let locales = [];
    if (response.data) {
      // Check if it's unified format
      if (response.data.data && response.data.data.locales) {
        locales = response.data.data.locales;
      } else if (response.data.locales) {
        locales = response.data.locales;
      } else if (Array.isArray(response.data)) {
        locales = response.data;
      }
    }
    
    // If no locales from API, use default
    if (Array.isArray(locales) && locales.length > 0) {
      availableLocales.value = locales;
    } else {
      // Fallback to default locales
      availableLocales.value = [
        { code: 'en', name: 'English', native_name: 'English' },
        { code: 'ar', name: 'العربية', native_name: 'العربية' },
      ];
    }
  } catch (error) {
    console.error('Error fetching locales:', error);
    // Fallback to default locales on error
    availableLocales.value = [
      { code: 'en', name: 'English', native_name: 'English' },
      { code: 'ar', name: 'العربية', native_name: 'العربية' },
    ];
  } finally {
    loading.value = false;
  }
}

// Watch for locale changes to update UI
watch(() => locale.value, (newLocale) => {
  // Force reactivity update
  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('dir', newLocale === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', newLocale);
  }
});

const currentLanguageName = computed(() => {
  const currentLocale = locale.value || 'ar';
  // Find the language name from available locales
  const lang = availableLocales.value.find(l => l.code === currentLocale);
  if (lang) {
    return lang.native_name || lang.name;
  }
  // Fallback
  return currentLocale === 'ar' ? 'العربية' : 'English';
});

const currentLanguageImage = computed(() => {
  const currentLocale = locale.value || 'ar';
  // Find the language image from available locales
  const lang = availableLocales.value.find(l => l.code === currentLocale);
  if (lang && (lang.image_url || lang.image_path)) {
    return lang.image_url || lang.image_path;
  }
  return null;
});

function handleImageError(event) {
  // Hide broken image
  event.target.style.display = 'none';
}

// Get current locale for checking active state
const currentLocale = computed(() => {
  // Try to get from useLocale first
  if (locale.value) {
    return locale.value;
  }
  // Fallback to i18n instance
  const i18nInstance = i18nGlobal?.global || i18nGlobal;
  if (i18nInstance) {
    return i18nInstance.locale || (typeof i18nInstance.locale === 'object' ? i18nInstance.locale.value : null) || 'ar';
  }
  // Final fallback
  return localStorage.getItem('gs_locale') || localStorage.getItem('locale') || 'ar';
});

function toggleDropdown(event) {
  event?.preventDefault();
  event?.stopPropagation();
  
  console.log('Toggle dropdown clicked, current state:', isOpen.value);
  
  // If loading, don't toggle
  if (loading.value) {
    console.log('Still loading locales, cannot toggle');
    return;
  }
  
  // If no locales loaded yet, fetch them first
  if (availableLocales.value.length === 0 && !loading.value) {
    console.log('No locales loaded, fetching...');
    fetchLocales().then(() => {
      isOpen.value = true;
    });
    return;
  }
  
  isOpen.value = !isOpen.value;
  console.log('Dropdown toggled, new state:', isOpen.value);
}

function closeDropdown() {
  isOpen.value = false;
}

function switchLanguage(newLocale) {
  try {
    // Get current locale
    const current = locale.value || 'ar';
    
    // If clicking the same language, do nothing
    if (current === newLocale) {
      closeDropdown();
      return;
    }
    
    // First, update i18n instance directly (this is critical for legacy mode)
    try {
      // Update the global i18n instance
      if (i18nGlobal) {
        // In legacy mode, locale is a string property directly on the instance
        if (i18nGlobal.locale !== undefined) {
          i18nGlobal.locale = newLocale;
        }
        // Also try global.locale (for composition API mode)
        if (i18nGlobal.global && i18nGlobal.global.locale !== undefined) {
          i18nGlobal.global.locale = newLocale;
        }
      }
    } catch (e) {
      console.warn('Could not update i18n instance directly:', e);
    }
    
    // Then use setLocale from useLocale composable (it handles localStorage, document, etc.)
    setLocale(newLocale);
    
    closeDropdown();
    
    // Force Vue reactivity update
    if (typeof window !== 'undefined') {
      window.dispatchEvent(new CustomEvent('locale-changed', { detail: { locale: newLocale } }));
    }
  } catch (error) {
    console.error('Error switching language:', error);
    closeDropdown();
  }
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      // Check if click is outside the element
      if (!(el === event.target || el.contains(event.target))) {
        // Also check if the click is not on the toggle button
        const toggleButton = el.querySelector('button[type="button"]');
        if (toggleButton && !toggleButton.contains(event.target)) {
          binding.value();
        }
      }
    };
    // Use capture phase to ensure it runs before other click handlers
    document.addEventListener('click', el.clickOutsideEvent, true);
  },
  unmounted(el) {
    if (el.clickOutsideEvent) {
      document.removeEventListener('click', el.clickOutsideEvent, true);
    }
  },
};

// Fetch locales on mount
onMounted(() => {
  fetchLocales();
});
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

