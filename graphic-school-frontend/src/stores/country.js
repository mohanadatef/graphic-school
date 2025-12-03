import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useApi } from '../composables/useApi';

export const useCountryStore = defineStore('country', () => {
  const countries = ref([]);
  const activeCountries = ref([]);
  const defaultCountry = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const { get } = useApi();

  /**
   * Load all countries
   */
  async function loadCountries() {
    try {
      loading.value = true;
      error.value = null;
      const response = await get('/admin/countries');
      const data = Array.isArray(response) ? response : (response?.data || []);
      countries.value = data;
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to load countries';
      console.error('Error loading countries:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Load active countries only
   */
  async function loadActiveCountries() {
    try {
      loading.value = true;
      error.value = null;
      const response = await get('/admin/countries/active');
      const data = Array.isArray(response) ? response : (response?.data || []);
      activeCountries.value = data;
      return data;
    } catch (err) {
      error.value = err.message || 'Failed to load active countries';
      console.error('Error loading active countries:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Get default country
   */
  const getDefaultCountry = computed(() => {
    if (defaultCountry.value) {
      return defaultCountry.value;
    }
    return countries.value.find(country => country.is_default) || countries.value.find(country => country.code === 'EG') || null;
  });

  /**
   * Get country by code
   */
  function getCountryByCode(code) {
    return countries.value.find(country => country.code === code) || null;
  }

  /**
   * Initialize store
   */
  async function init() {
    await Promise.all([
      loadCountries(),
      loadActiveCountries(),
    ]);
    
    // Set default country
    defaultCountry.value = getDefaultCountry.value;
  }

  return {
    // State
    countries,
    activeCountries,
    defaultCountry,
    loading,
    error,
    
    // Computed
    getDefaultCountry,
    
    // Actions
    loadCountries,
    loadActiveCountries,
    getCountryByCode,
    init,
  };
});

