import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Locale Composable
 * Manages locale switching and RTL support
 */
export function useLocale() {
  const { locale, t } = useI18n();
  
  const isRTL = computed(() => locale.value === 'ar');
  
  function setLocale(newLocale) {
    locale.value = newLocale;
    localStorage.setItem('gs_locale', newLocale);
    
    // Update document direction
    document.documentElement.setAttribute('dir', newLocale === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', newLocale);
  }
  
  function toggleLocale() {
    const newLocale = locale.value === 'ar' ? 'en' : 'ar';
    setLocale(newLocale);
  }
  
  // Initialize document direction on mount
  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('dir', isRTL.value ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', locale.value);
  }
  
  return {
    locale,
    isRTL,
    setLocale,
    toggleLocale,
    t,
  };
}

