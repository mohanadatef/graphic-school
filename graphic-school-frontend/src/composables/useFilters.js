import { ref, watch } from 'vue';

/**
 * Unified filters composable with debounce support
 * Filters only apply when needed (manual trigger or debounced search)
 */
export function useFilters(initialFilters = {}, options = {}) {
  const {
    debounceMs = 500, // Debounce time for search
    autoApply = false, // Auto-apply filters on change (for non-search fields)
  } = options;

  const filters = ref({ ...initialFilters });
  const searchTimeout = ref(null);
  const hasActiveFilters = ref(false);

  /**
   * Check if filters have values
   */
  function checkActiveFilters() {
    hasActiveFilters.value = Object.values(filters.value).some((value) => {
      if (Array.isArray(value)) return value.length > 0;
      if (typeof value === 'object' && value !== null) return Object.keys(value).length > 0;
      return value !== '' && value !== null && value !== undefined;
    });
  }

  /**
   * Reset filters to initial values
   */
  function resetFilters() {
    filters.value = { ...initialFilters };
    checkActiveFilters();
  }

  /**
   * Clear specific filter
   */
  function clearFilter(key) {
    if (key in filters.value) {
      filters.value[key] = initialFilters[key] || '';
      checkActiveFilters();
    }
  }

  /**
   * Debounced search function
   */
  function debounceSearch(callback) {
    clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => {
      callback();
    }, debounceMs);
  }

  /**
   * Build params object, excluding empty values
   */
  function buildParams(additionalParams = {}) {
    const params = {
      ...additionalParams,
    };

    Object.keys(filters.value).forEach((key) => {
      const value = filters.value[key];
      // Only include non-empty values
      if (value !== '' && value !== null && value !== undefined) {
        if (Array.isArray(value) && value.length > 0) {
          params[key] = value;
        } else if (!Array.isArray(value)) {
          params[key] = value;
        }
      }
    });

    return params;
  }

  /**
   * Watch for filter changes (only if autoApply is true)
   */
  if (autoApply) {
    watch(
      () => filters.value,
      () => {
        checkActiveFilters();
      },
      { deep: true }
    );
  } else {
    // Check on initialization
    checkActiveFilters();
  }

  return {
    filters,
    hasActiveFilters,
    resetFilters,
    clearFilter,
    debounceSearch,
    buildParams,
    checkActiveFilters,
  };
}

