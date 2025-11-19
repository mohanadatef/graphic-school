import { reactive } from 'vue';

/**
 * Unified pagination composable
 */
export function usePagination(initialPerPage = 10) {
  const pagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: initialPerPage,
    total: 0,
  });

  /**
   * Update pagination from API response
   */
  function updatePagination(meta) {
    if (meta) {
      Object.assign(pagination, {
        current_page: meta.current_page || 1,
        last_page: meta.last_page || 1,
        per_page: meta.per_page || initialPerPage,
        total: meta.total || 0,
      });
    }
  }

  /**
   * Change page
   */
  function changePage(page) {
    pagination.current_page = page;
  }

  /**
   * Change per page
   */
  function changePerPage(perPage) {
    pagination.per_page = perPage;
    pagination.current_page = 1; // Reset to first page
  }

  /**
   * Reset to first page
   */
  function resetPage() {
    pagination.current_page = 1;
  }

  return {
    pagination,
    updatePagination,
    changePage,
    changePerPage,
    resetPage,
  };
}

