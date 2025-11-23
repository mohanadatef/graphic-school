import api from '../services/api/client';

/**
 * Dynamic i18n loader
 * Fetches translations from backend API and merges with local fallback keys
 */
export async function loadTranslations(locale: string = 'ar'): Promise<Record<string, any>> {
  try {
    // Fetch translations from backend
    const response = await api.get(`/translations/all?locale=${locale}`);
    
    if (response.data?.data?.translations) {
      const backendTranslations = response.data.data.translations;
      
      // Load local fallback translations
      const localFallbacks = await loadLocalFallbacks(locale);
      
      // Merge: backend translations override local fallbacks
      return {
        ...localFallbacks,
        ...backendTranslations,
      };
    }
    
    // If API fails, return local fallbacks only
    return await loadLocalFallbacks(locale);
  } catch (error) {
    console.error('Error loading translations from API:', error);
    // Fallback to local translations
    return await loadLocalFallbacks(locale);
  }
}

/**
 * Load local fallback translations from JSON files
 */
async function loadLocalFallbacks(locale: string): Promise<Record<string, any>> {
  try {
    // Dynamic import of locale JSON files
    const module = await import(`./locales/${locale}.json`);
    return module.default || {};
  } catch (error) {
    console.error(`Error loading local translations for locale ${locale}:`, error);
    // Ultimate fallback: return empty object
    return {};
  }
}

/**
 * Flatten nested translation object to dot notation
 * Example: { common: { save: 'Save' } } => { 'common.save': 'Save' }
 */
export function flattenTranslations(translations: Record<string, any>, prefix = ''): Record<string, string> {
  const flattened: Record<string, string> = {};
  
  for (const key in translations) {
    const value = translations[key];
    const newKey = prefix ? `${prefix}.${key}` : key;
    
    if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
      // Recursively flatten nested objects
      Object.assign(flattened, flattenTranslations(value, newKey));
    } else {
      // Leaf node: assign value
      flattened[newKey] = String(value);
    }
  }
  
  return flattened;
}

/**
 * Unflatten dot notation to nested object
 * Example: { 'common.save': 'Save' } => { common: { save: 'Save' } }
 */
export function unflattenTranslations(translations: Record<string, string>): Record<string, any> {
  const unflattened: Record<string, any> = {};
  
  for (const key in translations) {
    const keys = key.split('.');
    let current = unflattened;
    
    for (let i = 0; i < keys.length - 1; i++) {
      const k = keys[i];
      if (!(k in current)) {
        current[k] = {};
      }
      current = current[k];
    }
    
    current[keys[keys.length - 1]] = translations[key];
  }
  
  return unflattened;
}

