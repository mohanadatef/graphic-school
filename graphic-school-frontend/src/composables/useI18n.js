import { computed } from 'vue';
import { useI18n as useVueI18n } from 'vue-i18n';
import api from '../api';
import i18n from '../i18n';

export function useI18n() {
  // Use vue-i18n's useI18n composable (composition mode)
  const { locale, t, te } = useVueI18n({ useScope: 'global' });
  
  // Create a computed for locale that works with refs
  const localeRef = computed({
    get: () => {
      // In composition mode, locale is a ref
      return locale.value || 'ar';
    },
    set: (value) => {
      locale.value = value;
    }
  });

  // Check if current locale is RTL
  const isRTL = computed(() => locale.value === 'ar');

  // Set locale and update API header
  async function setLocale(newLocale) {
    if (!['en', 'ar'].includes(newLocale)) {
      return;
    }

    // Update vue-i18n locale (composition mode - it's a ref)
    locale.value = newLocale;
    
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
    
    // Set locale in i18n (composition mode - it's a ref)
    locale.value = savedLocale;
    
    api.defaults.headers.common['Accept-Language'] = savedLocale;
  }

  return {
    locale: localeRef,
    t,
    te,
    setLocale,
    initLocale,
    isRTL,
  };
}

