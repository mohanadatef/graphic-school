import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';

// Get saved locale from localStorage or default to Arabic
const savedLocale = localStorage.getItem('gs_locale') || 'ar';

const i18n = createI18n({
  locale: savedLocale,
  fallbackLocale: 'ar',
  messages: {
    en,
    ar,
  },
  legacy: true, // Enable $t() in templates for backward compatibility
  globalInjection: true, // Make $t available globally
});

export default i18n;

