import { ref } from 'vue';

/**
 * Loading State Composable
 * Provides consistent loading state management
 */
export function useLoading(initialState = false) {
  const loading = ref(initialState);
  const error = ref(null);

  function setLoading(value) {
    loading.value = value;
    if (value) {
      error.value = null;
    }
  }

  function setError(err) {
    error.value = err;
    loading.value = false;
  }

  function reset() {
    loading.value = false;
    error.value = null;
  }

  async function withLoading(asyncFn) {
    try {
      setLoading(true);
      const result = await asyncFn();
      return result;
    } catch (err) {
      setError(err);
      throw err;
    } finally {
      setLoading(false);
    }
  }

  return {
    loading,
    error,
    setLoading,
    setError,
    reset,
    withLoading,
  };
}

