/**
 * i18n Missing Key Logger
 * Logs missing translation keys for E2E testing
 * Only active in development/test mode
 */

/**
 * Log a missing translation key
 * @param {string} locale - The locale (en, ar, etc.)
 * @param {string} key - The missing translation key
 */
export function logMissingI18nKey(locale, key) {
  // Only log in development/test mode
  if (typeof window === 'undefined') {
    return;
  }
  
  // Check if we're in test/E2E mode
  const isTestMode = import.meta.env.MODE === 'test' || 
                     import.meta.env.VITE_E2E === 'true' ||
                     window.Cypress;
  
  if (!isTestMode) {
    return;
  }
  
  try {
    // Initialize array if it doesn't exist
    if (!window.__MISSING_I18N__) {
      window.__MISSING_I18N__ = [];
    }
    
    // Add missing key
    window.__MISSING_I18N__.push({
      locale,
      key,
      timestamp: new Date().toISOString(),
    });
    
    // Limit array size to prevent memory issues
    if (window.__MISSING_I18N__.length > 1000) {
      window.__MISSING_I18N__.shift();
    }
  } catch (error) {
    // Silently fail - don't break the app
    console.warn('[i18nMissingLogger] Failed to log missing key:', error.message);
  }
}

/**
 * Get all logged missing keys
 * @returns {Array} Array of missing key objects
 */
export function getMissingI18nKeys() {
  if (typeof window === 'undefined' || !window.__MISSING_I18N__) {
    return [];
  }
  
  return window.__MISSING_I18N__;
}

/**
 * Clear logged missing keys
 */
export function clearMissingI18nKeys() {
  if (typeof window !== 'undefined' && window.__MISSING_I18N__) {
    window.__MISSING_I18N__ = [];
  }
}

