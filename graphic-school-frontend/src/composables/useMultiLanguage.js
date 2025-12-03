import { ref, computed, watch } from 'vue';
import { useLanguageStore } from '../stores/language';

/**
 * Composable for handling multi-language form fields
 * Automatically shows single field if 1 language, or multiple fields if >1 language
 */
export function useMultiLanguage(initialValue = {}) {
  const languageStore = useLanguageStore();
  const formData = ref(initialValue);

  // Load languages if not loaded
  if (languageStore.activeLanguages.length === 0) {
    languageStore.loadActiveLanguages();
  }

  const hasMultipleLanguages = computed(() => languageStore.hasMultipleLanguages);
  const activeLanguages = computed(() => languageStore.activeLanguages);
  const defaultLanguage = computed(() => languageStore.getDefaultLanguage);

  /**
   * Get value for a specific language
   */
  function getValue(languageCode, fieldName) {
    if (!hasMultipleLanguages.value) {
      // Single language - return direct value
      return formData.value[fieldName] || '';
    }
    // Multiple languages - return from translations object
    return formData.value[`${fieldName}_${languageCode}`] || formData.value[fieldName]?.[languageCode] || '';
  }

  /**
   * Set value for a specific language
   */
  function setValue(languageCode, fieldName, value) {
    if (!hasMultipleLanguages.value) {
      // Single language - set direct value
      formData.value[fieldName] = value;
    } else {
      // Multiple languages - set in translations object
      if (!formData.value[fieldName]) {
        formData.value[fieldName] = {};
      }
      if (typeof formData.value[fieldName] === 'object') {
        formData.value[fieldName][languageCode] = value;
      } else {
        // Convert existing string to object
        const existingValue = formData.value[fieldName];
        formData.value[fieldName] = {
          [defaultLanguage.value?.code || 'en']: existingValue,
          [languageCode]: value,
        };
      }
    }
  }

  /**
   * Get all values for a field (for all languages)
   */
  function getAllValues(fieldName) {
    if (!hasMultipleLanguages.value) {
      return {
        [defaultLanguage.value?.code || 'en']: formData.value[fieldName] || '',
      };
    }
    return formData.value[fieldName] || {};
  }

  /**
   * Build payload for API (converts to API format)
   */
  function buildPayload(fieldNames = []) {
    const payload = {};
    
    fieldNames.forEach(fieldName => {
      if (!hasMultipleLanguages.value) {
        payload[fieldName] = formData.value[fieldName] || null;
      } else {
        // Build translations object
        const translations = {};
        activeLanguages.value.forEach(lang => {
          const value = getValue(lang.code, fieldName);
          if (value) {
            translations[lang.code] = value;
          }
        });
        payload[fieldName] = Object.keys(translations).length > 0 ? translations : null;
      }
    });
    
    return payload;
  }

  /**
   * Initialize form data from API response
   */
  function initFromApi(apiData, fieldNames = []) {
    fieldNames.forEach(fieldName => {
      if (apiData[fieldName]) {
        if (typeof apiData[fieldName] === 'object' && !Array.isArray(apiData[fieldName])) {
          // Multi-language object
          Object.keys(apiData[fieldName]).forEach(langCode => {
            setValue(langCode, fieldName, apiData[fieldName][langCode]);
          });
        } else {
          // Single value
          formData.value[fieldName] = apiData[fieldName];
        }
      }
    });
  }

  return {
    formData,
    hasMultipleLanguages,
    activeLanguages,
    defaultLanguage,
    getValue,
    setValue,
    getAllValues,
    buildPayload,
    initFromApi,
  };
}

