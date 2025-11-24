import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import ar from './locales/ar.json';
import { loadTranslations } from './loader';

// Get saved locale from localStorage or default to Arabic
const savedLocale = typeof window !== 'undefined' 
  ? (localStorage.getItem('locale') || localStorage.getItem('gs_locale') || 'ar')
  : 'ar';

// Initialize with static translations first
const i18n = createI18n({
  locale: savedLocale,
  fallbackLocale: 'ar',
  messages: {
    en: en,
    ar: ar,
  },
  legacy: false, // Use composition API mode
  globalInjection: true, // Make $t available globally in templates
  missingWarn: false, // Disable default warning, we'll handle it
  fallbackWarn: false,
  silentTranslationWarn: true,
  // Custom missing handler
  missing(locale, key, vm, values) {
    // Log missing key for E2E
    if (typeof window !== 'undefined') {
      // Log for E2E testing
      import('../utils/i18nMissingLogger').then(({ logMissingI18nKey }) => {
        logMissingI18nKey(locale, key);
      }).catch(() => {
        // Ignore errors
      });
      
      // Auto-add missing translation (browser-side)
      import('../utils/selfHealBrowser').then(({ requestTranslation }) => {
        requestTranslation(locale, key);
      }).catch(() => {
        // Ignore errors
      });
    }
    
    // Return the key as fallback
    return key;
  },
});

// Try to load auto translations (async, will merge later)
const loadAutoTranslations = async () => {
  try {
    const autoEnModule = await import('./auto/auto-en.json');
    const autoArModule = await import('./auto/auto-ar.json');
    const autoEn = autoEnModule.default || {};
    const autoAr = autoArModule.default || {};
    
    // Merge with existing messages
    if (i18n.global.messages.en) {
      i18n.global.setLocaleMessage('en', {
        ...i18n.global.messages.en,
        ...autoEn,
      });
    }
    if (i18n.global.messages.ar) {
      i18n.global.setLocaleMessage('ar', {
        ...i18n.global.messages.ar,
        ...autoAr,
      });
    }
  } catch (e) {
    // Auto translations don't exist yet, that's okay
    console.log('[i18n] Auto translations not found, will be created on demand');
  }
};

// Load auto translations in background (after i18n is created)
loadAutoTranslations();

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

/**
 * Translate helper function for use outside of Vue components
 * Use this in stores, services, utilities, etc.
 * @param {string} key - Translation key (e.g., 'common.save')
 * @param {object} params - Optional parameters for interpolation
 * @returns {string} Translated string
 */
export function translate(key, params) {
  if (i18n.global && typeof i18n.global.t === 'function') {
    return i18n.global.t(key, params);
  }
  // Fallback: manual lookup
  const currentLocale = i18n.global.locale?.value || i18n.global.locale || 'ar';
  const messages = i18n.global.messages?.[currentLocale] || i18n.global.messages?.ar || {};
  const keys = key.split('.');
  let value = messages;
  for (const k of keys) {
    value = value?.[k];
    if (value === undefined) break;
  }
  return value || key;
}

export default i18n;

