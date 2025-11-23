import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../api';

export const useWebsiteSettingsStore = defineStore('websiteSettings', () => {
  const settings = ref(null);
  const loading = ref(false);
  const error = ref(null);

  /**
   * Load website settings from API
   */
  async function loadSettings() {
    if (settings.value && !error.value) {
      // Already loaded, return cached
      return settings.value;
    }

    try {
      loading.value = true;
      error.value = null;
      
      const { data } = await api.get('/setup/status');
      
      settings.value = {
        is_activated: data.is_activated || false,
        branding: data.settings?.branding || {},
        default_language: data.settings?.default_language || 'en',
        default_currency: data.settings?.default_currency || 'USD',
        timezone: data.settings?.timezone || 'UTC',
        enabled_pages: data.settings?.enabled_pages || {
          home: true,
          about: true,
          contact: true,
          programs: true,
          community: true,
          faq: false,
        },
        general_info: data.settings?.general_info || {},
      };

      // Apply branding to DOM
      applyBranding(settings.value.branding);

      return settings.value;
    } catch (err) {
      error.value = err.message || 'Failed to load website settings';
      console.error('Error loading website settings:', err);
      
      // Use defaults on error
      settings.value = {
        is_activated: false,
        branding: {},
        default_language: 'en',
        default_currency: 'USD',
        enabled_pages: {
          home: true,
          about: true,
          contact: true,
          programs: true,
          community: true,
          faq: false,
        },
      };
      
      return settings.value;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Apply branding to DOM
   */
  function applyBranding(branding) {
    if (!branding || typeof document === 'undefined') return;

    const root = document.documentElement;

    // Colors
    if (branding.primary_color) {
      root.style.setProperty('--primary', branding.primary_color);
      root.style.setProperty('--primary-color', branding.primary_color);
    }
    if (branding.secondary_color) {
      root.style.setProperty('--secondary', branding.secondary_color);
      root.style.setProperty('--secondary-color', branding.secondary_color);
    }

    // Fonts
    if (branding.font_main) {
      root.style.setProperty('--font-main', `"${branding.font_main}", sans-serif`);
    }
    if (branding.font_headings) {
      root.style.setProperty('--font-headings', `"${branding.font_headings}", sans-serif`);
    }

    // Theme
    if (branding.default_theme) {
      const currentTheme = localStorage.getItem('gs_theme');
      if (!currentTheme) {
        // Only apply default theme if user hasn't manually set one
        if (branding.default_theme === 'dark') {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
      }
    }
  }

  /**
   * Get available languages
   */
  const availableLanguages = computed(() => {
    // Get from settings if available
    const langs = settings.value?.available_languages;
    if (langs && Array.isArray(langs) && langs.length > 0) {
      return langs.map(code => ({
        code,
        name: code === 'ar' ? 'العربية' : 'English',
        native_name: code === 'ar' ? 'العربية' : 'English',
      }));
    }
    
    // Default fallback
    return [
      { code: 'en', name: 'English', native_name: 'English' },
      { code: 'ar', name: 'Arabic', native_name: 'العربية' },
    ];
  });

  /**
   * Get default language
   */
  const defaultLanguage = computed(() => {
    return settings.value?.default_language || 'en';
  });

  /**
   * Get default currency
   */
  const defaultCurrency = computed(() => {
    return settings.value?.default_currency || 'USD';
  });

  /**
   * Check if page is enabled
   */
  function isPageEnabled(pageKey) {
    return settings.value?.enabled_pages?.[pageKey] ?? true;
  }

  /**
   * Refresh settings
   */
  async function refresh() {
    settings.value = null;
    error.value = null;
    return await loadSettings();
  }

  return {
    settings: computed(() => settings.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    availableLanguages,
    defaultLanguage,
    defaultCurrency,
    loadSettings,
    applyBranding,
    isPageEnabled,
    refresh,
  };
});

