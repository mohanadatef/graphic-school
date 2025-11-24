import { ref, onMounted } from 'vue';
import { useApi } from './useApi';
import { useFilters } from './useFilters';
import { usePagination } from './usePagination';

/**
 * Unified composable for list pages (CRUD operations)
 * Combines API, filters, and pagination
 */
export function useListPage(config = {}) {
  const {
    endpoint, // API endpoint (e.g., '/admin/users')
    initialFilters = {},
    perPage = 10,
    autoLoad = true, // Auto load on mount
    debounceMs = 500,
    autoApplyFilters = false, // Auto apply filters on change
  } = config;

  const items = ref([]);
  const { loading, error, get, post, put, delete: del, clearError } = useApi();
  const { filters, hasActiveFilters, resetFilters, clearFilter, debounceSearch, buildParams } = useFilters(
    initialFilters,
    {
      debounceMs,
      autoApply: autoApplyFilters,
    }
  );
  const { pagination, updatePagination, changePage, changePerPage, resetPage } = usePagination(perPage);

  /**
   * Load items from API
   */
  async function loadItems(additionalParams = {}) {
    try {
      // Ensure pagination is initialized
      if (!pagination) {
        console.warn('Pagination not initialized, using defaults');
        return;
      }

      const params = buildParams({
        page: pagination.current_page || 1,
        per_page: pagination.per_page || perPage,
        ...additionalParams,
      });

      const response = await get(endpoint, { params });
      
      // Backend returns unified format: { success, message, data: [...], meta: { pagination: {...} } }
      // The interceptor already extracts data, so response is the array
      // But we need to check if it's an array or an object with data property
      const data = Array.isArray(response) ? response : (response?.data || []);
      items.value = Array.isArray(data) ? data : [];
      
      // Check for pagination in meta (attached by interceptor) or in response
      if (response?.meta?.pagination) {
        updatePagination(response.meta.pagination);
      } else if (response?.meta) {
        updatePagination(response.meta);
      } else if (response?.pagination) {
        updatePagination(response.pagination);
      }

      return response;
    } catch (err) {
      console.error('Error loading items:', err);
      items.value = [];
      // Ensure pagination has default values even on error
      if (pagination) {
        updatePagination({ current_page: 1, last_page: 1, per_page: perPage, total: 0 });
      }
      throw err;
    }
  }

  /**
   * Load items with debounced search
   */
  function loadItemsDebounced() {
    resetPage();
    debounceSearch(() => {
      loadItems();
    });
  }

  /**
   * Apply filters manually (for non-search fields)
   */
  function applyFilters() {
    resetPage();
    loadItems();
  }

  /**
   * Create new item
   */
  async function createItem(data) {
    try {
      await post(endpoint, data);
      await loadItems();
      return true;
    } catch (err) {
      console.error('Error creating item:', err);
      throw err;
    }
  }

  /**
   * Update item
   */
  async function updateItem(id, data) {
    try {
      await put(`${endpoint}/${id}`, data);
      await loadItems();
      return true;
    } catch (err) {
      console.error('Error updating item:', err);
      throw err;
    }
  }

  /**
   * Delete item
   */
  async function deleteItem(id) {
    try {
      await del(`${endpoint}/${id}`);
      await loadItems();
      return true;
    } catch (err) {
      console.error('Error deleting item:', err);
      throw err;
    }
  }

  /**
   * Refresh current page
   */
  async function refresh() {
    await loadItems();
  }

  if (autoLoad) {
    onMounted(() => {
      loadItems();
    });
  }

  return {
    // Data
    items,
    loading,
    error,

    // Filters
    filters,
    hasActiveFilters,
    resetFilters,
    clearFilter,
    applyFilters,

    // Pagination
    pagination,
    changePage,
    changePerPage,
    resetPage,

    // Actions
    loadItems,
    loadItemsDebounced,
    createItem,
    updateItem,
    deleteItem,
    refresh,
    clearError,
  };
}

