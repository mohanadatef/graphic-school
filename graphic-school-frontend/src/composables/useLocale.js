import { computed, getCurrentInstance } from 'vue';
import i18nGlobal from '../i18n';

/**
 * Locale Composable
 * Manages locale switching and RTL support
 * Works with legacy mode (legacy: true)
 */
export function useLocale() {
  // In legacy mode, access i18n instance directly
  // Try to get from Vue instance first, then fallback to global
  let i18nInstance;
  try {
    const instance = getCurrentInstance();
    i18nInstance = instance?.appContext.config.globalProperties.$i18n;
  } catch (e) {
    // getCurrentInstance might not be available in all contexts
  }
  
  // Fallback to global i18n instance
  if (!i18nInstance) {
    i18nInstance = i18nGlobal?.global || i18nGlobal;
  }
  
  // In legacy mode, locale is a string, not a ref
  const locale = computed({
    get: () => {
      // Try multiple sources for locale
      if (i18nInstance && i18nInstance.locale) {
        return typeof i18nInstance.locale === 'string' ? i18nInstance.locale : i18nInstance.locale.value || 'ar';
      }
      if (i18nGlobal && i18nGlobal.global && i18nGlobal.global.locale) {
        return typeof i18nGlobal.global.locale === 'string' ? i18nGlobal.global.locale : i18nGlobal.global.locale.value || 'ar';
      }
      if (i18nGlobal && i18nGlobal.locale) {
        return typeof i18nGlobal.locale === 'string' ? i18nGlobal.locale : i18nGlobal.locale.value || 'ar';
      }
      // Fallback to localStorage
      return localStorage.getItem('gs_locale') || localStorage.getItem('locale') || 'ar';
    },
    set: (value) => {
      // Update all possible i18n instances
      if (i18nInstance && i18nInstance.locale !== undefined) {
        i18nInstance.locale = value;
      }
      if (i18nGlobal && i18nGlobal.global && i18nGlobal.global.locale !== undefined) {
        i18nGlobal.global.locale = value;
      }
      if (i18nGlobal && i18nGlobal.locale !== undefined) {
        i18nGlobal.locale = value;
      }
    }
  });
  
  const isRTL = computed(() => locale.value === 'ar');
  
  function setLocale(newLocale) {
    if (!['en', 'ar'].includes(newLocale)) {
      return;
    }
    
    // Update locale computed value first (this triggers reactivity)
    locale.value = newLocale;
    
    // Update i18n instance locale (in legacy mode, it's a string property)
    // Try multiple ways to access and update locale
    try {
      // Update from instance (if available)
      if (i18nInstance) {
        // Direct property access (legacy mode)
        if (i18nInstance.locale !== undefined) {
          i18nInstance.locale = newLocale;
        }
        // Try global property
        if (i18nInstance.global && i18nInstance.global.locale !== undefined) {
          i18nInstance.global.locale = newLocale;
        }
      }
      
      // Update global i18n instance directly (always do this)
      if (i18nGlobal) {
        // In legacy mode, locale is directly on the instance
        if (i18nGlobal.locale !== undefined) {
          i18nGlobal.locale = newLocale;
        }
        // Also try global.locale
        if (i18nGlobal.global) {
          if (i18nGlobal.global.locale !== undefined) {
            i18nGlobal.global.locale = newLocale;
          }
        }
      }
    } catch (error) {
      console.warn('Error updating i18n locale:', error);
    }
    
    // Save to localStorage
    localStorage.setItem('gs_locale', newLocale);
    localStorage.setItem('locale', newLocale);
    
    // Update document direction and language
    if (typeof document !== 'undefined') {
      document.documentElement.setAttribute('dir', newLocale === 'ar' ? 'rtl' : 'ltr');
      document.documentElement.setAttribute('lang', newLocale);
    }
    
    // Trigger locale change event for components to react
    if (typeof window !== 'undefined') {
      window.dispatchEvent(new CustomEvent('locale-changed', { detail: { locale: newLocale } }));
    }
  }
  
  function toggleLocale() {
    const newLocale = locale.value === 'ar' ? 'en' : 'ar';
    setLocale(newLocale);
  }
  
  function t(key, params) {
    try {
      if (i18nInstance && typeof i18nInstance.t === 'function') {
        return i18nInstance.t(key, params);
      }
      if (i18nGlobal && i18nGlobal.global && typeof i18nGlobal.global.t === 'function') {
        return i18nGlobal.global.t(key, params);
      }
      // Fallback: return key if translation not available
      return key;
    } catch (error) {
      console.warn('Translation error:', error);
      return key;
    }
  }
  
  // Initialize document direction on mount
  if (typeof document !== 'undefined') {
    const currentLocale = locale.value || 'ar';
    document.documentElement.setAttribute('dir', currentLocale === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', currentLocale);
  }
  
  return {
    locale,
    isRTL,
    setLocale,
    toggleLocale,
    t,
  };
}

