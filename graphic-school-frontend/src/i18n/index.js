import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';
import { loadTranslations } from './loader';

// Get saved locale from localStorage or default to Arabic
const savedLocale = localStorage.getItem('locale') || localStorage.getItem('gs_locale') || 'ar';

// Initialize with static fallbacks
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

/**
 * Load dynamic translations from API and merge with static fallbacks
 */
export async function loadDynamicTranslations(locale = savedLocale) {
  try {
    const dynamicTranslations = await loadTranslations(locale);
    
    // Merge dynamic translations into vue-i18n messages
    if (i18n.global.messages[locale]) {
      // Deep merge: dynamic translations override static
      i18n.global.setLocaleMessage(locale, {
        ...i18n.global.messages[locale],
        ...dynamicTranslations,
      });
    } else {
      // If locale doesn't exist, set it
      i18n.global.setLocaleMessage(locale, dynamicTranslations);
    }
    
    // Set locale
    i18n.global.locale = locale;
    
    return dynamicTranslations;
  } catch (error) {
    console.error('Failed to load dynamic translations:', error);
    // Fallback to static translations
    i18n.global.locale = locale;
    return i18n.global.messages[locale] || i18n.global.messages.ar;
  }
}

export default i18n;

