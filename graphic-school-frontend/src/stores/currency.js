import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { currencyService } from '../services/api';

export const useCurrencyStore = defineStore('currency', () => {
  // State
  const items = ref([]);
  const activeCurrencies = ref([]);
  const defaultCurrency = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Computed
  const getDefaultCurrency = computed(() => {
    if (defaultCurrency.value) {
      return defaultCurrency.value;
    }
    return items.value.find(curr => curr.is_default) || items.value.find(curr => curr.code === 'EGP') || null;
  });

  // Actions
  async function fetchAll(filters = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await currencyService.getAll(filters);
      items.value = response.data || response || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load currencies';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await currencyService.getById(id);
      return response.data || response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load currency';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await currencyService.create(payload);
      const data = response.data || response;
      items.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create currency';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await currencyService.update(id, payload);
      const data = response.data || response;
      const index = items.value.findIndex((curr) => curr.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update currency';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteItem(id) {
    loading.value = true;
    error.value = null;
    try {
      await currencyService.delete(id);
      items.value = items.value.filter((curr) => curr.id !== id);
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete currency';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchActive() {
    loading.value = true;
    error.value = null;
    try {
      const response = await currencyService.getActive();
      activeCurrencies.value = response.data || response || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load active currencies';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Load all currencies (alias for backward compatibility)
   */
  async function loadCurrencies() {
    return fetchAll();
  }

  /**
   * Load active currencies only (alias for backward compatibility)
   */
  async function loadActiveCurrencies() {
    return fetchActive();
  }

  /**
   * Get currency by code
   */
  function getCurrencyByCode(code) {
    return items.value.find(curr => curr.code === code) || null;
  }

  /**
   * Format amount with currency
   */
  function formatAmount(amount, currencyCode = null) {
    const currency = currencyCode 
      ? getCurrencyByCode(currencyCode) 
      : getDefaultCurrency.value;
    
    if (!currency) {
      return amount.toFixed(2);
    }

    const formatted = amount.toFixed(2);
    return `${currency.symbol}${formatted}`;
  }

  /**
   * Initialize store
   */
  async function init() {
    await Promise.all([
      fetchAll(),
      fetchActive(),
    ]);
    
    // Set default currency
    defaultCurrency.value = getDefaultCurrency.value;
  }

  return {
    // State
    items,
    currencies: items, // Alias for backward compatibility
    activeCurrencies,
    defaultCurrency,
    loading,
    error,
    
    // Computed
    getDefaultCurrency,
    
    // Actions
    fetchAll,
    fetchOne,
    fetchById: fetchOne, // Alias
    create,
    update,
    delete: deleteItem, // Alias
    remove: deleteItem, // Alias
    fetchActive,
    loadCurrencies, // Alias for backward compatibility
    loadActiveCurrencies, // Alias for backward compatibility
    getCurrencyByCode,
    formatAmount,
    init,
  };
});
