import { useI18n as useVueI18n } from 'vue-i18n';
import { computed, watch, getCurrentInstance } from 'vue';
import api from '../api';
import i18n from '../i18n';

export function useI18n() {
  const { locale, t, te } = useVueI18n();

  // Check if current locale is RTL
  const isRTL = computed(() => locale.value === 'ar');

  // Set locale and update API header
  async function setLocale(newLocale) {
    if (!['en', 'ar'].includes(newLocale)) {
      return;
    }

    // Update vue-i18n locale (both instance and global)
    locale.value = newLocale;
    if (i18n && i18n.global) {
      i18n.global.locale.value = newLocale;
    }
    
    // Save to localStorage
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
    const savedLocale = localStorage.getItem('locale') || 'ar';
    const isRTLValue = savedLocale === 'ar';
    document.documentElement.dir = isRTLValue ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
    locale.value = savedLocale;
    api.defaults.headers.common['Accept-Language'] = savedLocale;
  }

  // Watch for locale changes
  watch(locale, (newLocale) => {
    document.documentElement.dir = newLocale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = newLocale;
  }, { immediate: true });

  return {
    locale: computed(() => locale.value),
    t,
    te,
    setLocale,
    initLocale,
    isRTL,
  };
}

