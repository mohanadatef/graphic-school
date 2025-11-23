import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { loadTranslations } from '../i18n/loader';

export const useI18nStore = defineStore('i18n', () => {
  const locale = ref<string>(localStorage.getItem('locale') || 'ar');
  const messages = ref<Record<string, any>>({});
  const loading = ref<boolean>(false);
  const error = ref<string | null>(null);

  /**
   * Load translations for a specific locale
   */
  async function loadLocale(targetLocale: string = locale.value): Promise<void> {
    loading.value = true;
    error.value = null;

    try {
      const translations = await loadTranslations(targetLocale);
      messages.value = translations;
      locale.value = targetLocale;
      
      // Save to localStorage
      localStorage.setItem('locale', targetLocale);
      
      // Update document language and direction
      document.documentElement.lang = targetLocale;
      if (targetLocale === 'ar') {
        document.documentElement.dir = 'rtl';
        document.body.classList.add('rtl');
        document.body.classList.remove('ltr');
      } else {
        document.documentElement.dir = 'ltr';
        document.body.classList.add('ltr');
        document.body.classList.remove('rtl');
      }
    } catch (err) {
      error.value = 'Failed to load translations';
      console.error('Error loading locale:', err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Switch language
   */
  async function switchLanguage(newLocale: string): Promise<void> {
    if (newLocale === locale.value) {
      return; // Already set
    }

    if (!['ar', 'en'].includes(newLocale)) {
      console.warn(`Unsupported locale: ${newLocale}. Falling back to 'ar'`);
      newLocale = 'ar';
    }

    await loadLocale(newLocale);
  }

  /**
   * Get translation by key
   */
  function t(key: string, params?: Record<string, string>): string {
    const translation = messages.value[key] || key;
    
    // Replace parameters
    if (params) {
      return Object.entries(params).reduce(
        (str, [paramKey, paramValue]) => str.replace(`:${paramKey}`, paramValue),
        translation
      );
    }
    
    return translation;
  }

  /**
   * Check if locale is RTL
   */
  const isRTL = computed(() => locale.value === 'ar');

  /**
   * Get current locale name
   */
  const localeName = computed(() => {
    const names: Record<string, string> = {
      ar: 'العربية',
      en: 'English',
    };
    return names[locale.value] || locale.value;
  });

  return {
    locale,
    messages,
    loading,
    error,
    isRTL,
    localeName,
    loadLocale,
    switchLanguage,
    t,
  };
});

