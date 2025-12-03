import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useApi } from '../composables/useApi';

export const useLanguageStore = defineStore('language', () => {
  const languages = ref([]);
  const activeLanguages = ref([]);
  const defaultLanguage = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const { get } = useApi();

  /**
   * Load all languages
   */
  async function loadLanguages() {
    try {
      loading.value = true;
      error.value = null;
      const response = await get('/admin/languages');
      const data = Array.isArray(response) ? response : (response?.data || []);
      languages.value = data;
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to load languages';
      console.error('Error loading languages:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Load active languages only
   */
  async function loadActiveLanguages() {
    try {
      loading.value = true;
      error.value = null;
      const response = await get('/admin/languages/active');
      const data = Array.isArray(response) ? response : (response?.data || []);
      activeLanguages.value = data;
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to load active languages';
      console.error('Error loading active languages:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Get default language
   */
  const getDefaultLanguage = computed(() => {
    if (defaultLanguage.value) {
      return defaultLanguage.value;
    }
    return languages.value.find(lang => lang.is_default) || languages.value.find(lang => lang.code === 'en') || null;
  });

  /**
   * Check if multiple languages are active
   */
  const hasMultipleLanguages = computed(() => {
    return activeLanguages.value.length > 1;
  });

  /**
   * Get language by code
   */
  function getLanguageByCode(code) {
    return languages.value.find(lang => lang.code === code) || null;
  }

  /**
   * Get RTL languages
   */
  const rtlLanguages = computed(() => {
    return languages.value.filter(lang => lang.is_rtl);
  });

  /**
   * Check if language is RTL
   */
  function isRtl(code) {
    const lang = getLanguageByCode(code);
    return lang ? lang.is_rtl : false;
  }

  /**
   * Initialize store
   */
  async function init() {
    await Promise.all([
      loadLanguages(),
      loadActiveLanguages(),
    ]);
    
    // Set default language
    defaultLanguage.value = getDefaultLanguage.value;
  }

  return {
    // State
    languages,
    activeLanguages,
    defaultLanguage,
    loading,
    error,
    
    // Computed
    getDefaultLanguage,
    hasMultipleLanguages,
    rtlLanguages,
    
    // Actions
    loadLanguages,
    loadActiveLanguages,
    getLanguageByCode,
    isRtl,
    init,
  };
});

