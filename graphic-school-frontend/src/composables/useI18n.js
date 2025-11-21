import { computed, watch, getCurrentInstance } from 'vue';
import api from '../api';
import i18n from '../i18n';

export function useI18n() {
  // In legacy mode, access i18n instance directly
  const i18nInstance = getCurrentInstance()?.appContext.config.globalProperties.$i18n || i18n;
  
  // In legacy mode, locale is a string, not a ref
  const locale = computed({
    get: () => i18nInstance.locale || 'ar',
    set: (value) => {
      if (i18nInstance.locale !== undefined) {
        i18nInstance.locale = value;
      }
      if (i18n && i18n.locale !== undefined) {
        i18n.locale = value;
      }
    }
  });
  
  function t(key, params) {
    return i18nInstance.t(key, params);
  }
  
  function te(key) {
    return i18nInstance.te(key);
  }

  // Check if current locale is RTL
  const isRTL = computed(() => locale.value === 'ar');

  // Set locale and update API header
  async function setLocale(newLocale) {
    if (!['en', 'ar'].includes(newLocale)) {
      return;
    }

    // Update vue-i18n locale (in legacy mode, it's a string property)
    if (i18nInstance.locale !== undefined) {
      i18nInstance.locale = newLocale;
    }
    if (i18n && i18n.locale !== undefined) {
      i18n.locale = newLocale;
    }
    
    // Save to localStorage
    localStorage.setItem('gs_locale', newLocale);
    localStorage.setItem('locale', newLocale);
    
    // Update API header
    if (api && api.defaults && api.defaults.headers) {
      api.defaults.headers.common['Accept-Language'] = newLocale;
    }
    
    // Update document direction and language
    const isRTLValue = newLocale === 'ar';
    document.documentElement.dir = isRTLValue ? 'rtl' : 'ltr';
    document.documentElement.lang = newLocale;
    
    // Trigger a custom event for components to react
    window.dispatchEvent(new CustomEvent('locale-changed', { detail: { locale: newLocale } }));

    // Optionally sync with backend (but don't wait for it)
    try {
      if (api && api.post) {
        await api.post(`/locale/${newLocale}`);
      }
    } catch (error) {
      // Silently fail if backend is not available
      console.warn('Failed to sync locale with backend:', error);
    }
  }

  // Initialize locale on mount
  function initLocale() {
    const savedLocale = localStorage.getItem('gs_locale') || localStorage.getItem('locale') || 'ar';
    const isRTLValue = savedLocale === 'ar';
    document.documentElement.dir = isRTLValue ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
    
    // Set locale in i18n instance
    if (i18nInstance.locale !== undefined) {
      i18nInstance.locale = savedLocale;
    }
    if (i18n && i18n.locale !== undefined) {
      i18n.locale = savedLocale;
    }
    
    api.defaults.headers.common['Accept-Language'] = savedLocale;
  }

  return {
    locale,
    t,
    te,
    setLocale,
    initLocale,
    isRTL,
  };
}

