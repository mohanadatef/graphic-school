import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';

// Get saved locale from localStorage or default to 'ar' (since current UI is Arabic)
const savedLocale = localStorage.getItem('locale') || 'ar';

const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: {
    en,
    ar,
  },
  // Enable RTL support for Arabic
  rtl: {
    ar: true,
    en: false,
  },
});

export default i18n;

