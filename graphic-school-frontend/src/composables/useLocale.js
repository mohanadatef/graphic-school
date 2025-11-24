import { computed } from 'vue';
import { useI18n as useVueI18n } from 'vue-i18n';
import i18n from '../i18n';

/**
 * Locale Composable
 * Manages locale switching and RTL support
 * Works with composition mode (legacy: false)
 */
export function useLocale() {
  // Use vue-i18n's useI18n composable (composition mode)
  const { locale: vueLocale } = useVueI18n({ useScope: 'global' });
  
  // Helper to safely get locale value
  const getLocaleValue = () => {
    try {
      // Check if vueLocale is a ref (has value property)
      if (vueLocale && typeof vueLocale === 'object' && 'value' in vueLocale) {
        const val = vueLocale.value;
        return (val && typeof val === 'string') ? val : 'ar';
      }
      // If vueLocale is a string directly
      if (typeof vueLocale === 'string') {
        return vueLocale;
      }
      // Fallback to i18n.global.locale
      if (i18n?.global?.locale) {
        const loc = i18n.global.locale;
        if (typeof loc === 'object' && loc !== null && 'value' in loc) {
          return loc.value || 'ar';
        }
        if (typeof loc === 'string') {
          return loc;
        }
      }
    } catch (e) {
      console.warn('Error getting locale value:', e);
    }
    return 'ar';
  };
  
  // Helper to safely set locale value
  const setLocaleValue = (value) => {
    try {
      // Try to set vueLocale if it's a ref
      if (vueLocale && typeof vueLocale === 'object' && vueLocale !== null) {
        if ('value' in vueLocale) {
          vueLocale.value = value;
          return;
        }
      }
      // Set on i18n.global.locale directly
      if (i18n?.global) {
        if (i18n.global.locale && typeof i18n.global.locale === 'object' && 'value' in i18n.global.locale) {
          i18n.global.locale.value = value;
        } else {
          i18n.global.locale = value;
        }
      }
    } catch (e) {
      console.warn('Error setting locale value:', e);
    }
  };
  
  // Create a computed for locale that works with refs
  const localeRef = computed({
    get: () => getLocaleValue(),
    set: (value) => setLocaleValue(value)
  });
  
  const isRTL = computed(() => getLocaleValue() === 'ar');
  
  function setLocale(newLocale) {
    if (!['en', 'ar'].includes(newLocale)) {
      return;
    }
    
    // Update locale safely
    setLocaleValue(newLocale);
    
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
    const currentLocale = getLocaleValue();
    const newLocale = currentLocale === 'ar' ? 'en' : 'ar';
    setLocale(newLocale);
  }
  
  // Initialize document direction on mount
  if (typeof document !== 'undefined') {
    const currentLocale = getLocaleValue();
    document.documentElement.setAttribute('dir', currentLocale === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', currentLocale);
  }
  
  return {
    locale: localeRef,
    isRTL,
    setLocale,
    toggleLocale,
  };
}

