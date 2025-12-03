import { ref } from 'vue';
import api from '../services/api/client';
import { translate } from '../i18n';

// Cache for failed endpoints (to prevent repeated requests to non-existent endpoints)
const failedEndpoints = new Set();

/**
 * Check if an endpoint has previously failed with 404/500
 */
function isEndpointFailed(url) {
  return failedEndpoints.has(url);
}

/**
 * Mark an endpoint as failed
 */
function markEndpointFailed(url) {
  failedEndpoints.add(url);
}

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
    // Skip request if we know this endpoint has failed before (404/500)
    // This prevents repeated failed requests and console spam
    if (isEndpointFailed(url)) {
      const fakeError = {
        response: { status: 404, data: { message: 'Endpoint not available' } },
        message: 'Endpoint not available',
      };
      error.value = fakeError.response.data.message;
      throw fakeError;
    }
    
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get(url, config);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || err.message || translate('errors.loadDataError');
      
      // Cache failed endpoints (404/500) to prevent repeated requests
      // BUT don't cache 404s for public endpoints (pages might be created later)
      const status = err?.response?.status;
      const isPublicEndpoint = url.includes('/public/');
      
      if (status === 404 || status === 500) {
        // Don't cache 404s for public endpoints (they might be created later)
        if (!(isPublicEndpoint && status === 404)) {
          markEndpointFailed(url);
        }
        // Silently handle - don't log to console, but still throw for error handling
        // The calling code will handle the error appropriately
      } else if (import.meta.env.DEV) {
        // Only log unexpected errors in development
        console.warn('[useApi] GET request failed:', {
          url,
          status,
          message: err.response?.data?.message || err.message,
        });
      }
      
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
      error.value = err.response?.data?.message || err.message || translate('errors.saveError');
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
      error.value = err.response?.data?.message || err.message || translate('errors.updateError');
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
      error.value = err.response?.data?.message || err.message || translate('errors.deleteError');
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

