import { ref } from 'vue';
import api from '../api';

/**
 * Unified API composable for consistent API calls
 * Handles loading, errors, and response formatting
 */
export function useApi() {
  const loading = ref(false);
  const error = ref(null);

  /**
   * Make a GET request
   */
  async function get(url, config = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get(url, config);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'حدث خطأ أثناء تحميل البيانات';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Make a POST request
   */
  async function post(url, data = {}, config = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.post(url, data, config);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'حدث خطأ أثناء الحفظ';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Make a PUT request
   */
  async function put(url, data = {}, config = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.put(url, data, config);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'حدث خطأ أثناء التحديث';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Make a DELETE request
   */
  async function del(url, config = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.delete(url, config);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message || 'حدث خطأ أثناء الحذف';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Clear error
   */
  function clearError() {
    error.value = null;
  }

  return {
    loading,
    error,
    get,
    post,
    put,
    delete: del,
    clearError,
  };
}

