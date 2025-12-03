import { defineStore } from 'pinia';
import { ref } from 'vue';
import { cmsService } from '../services/api';

export const usePageStore = defineStore('page', () => {
  // State
  const items = ref([]);
  const currentItem = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  });

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await cmsService.getPages({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        ...params,
      });
      items.value = response.data || [];
      if (response.meta?.pagination) {
        pagination.value = response.meta.pagination;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch pages';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      // Note: cmsService.getPage takes slug, not id
      // This might need adjustment based on API
      const response = await cmsService.getPage(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchBySlug(slug) {
    loading.value = true;
    error.value = null;
    try {
      const response = await cmsService.getPage(slug);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await cmsService.createPage(payload);
      const data = response.data || response;
      items.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await cmsService.updatePage(id, payload);
      const data = response.data || response;
      const index = items.value.findIndex((p) => p.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteItem(id) {
    loading.value = true;
    error.value = null;
    try {
      await cmsService.deletePage(id);
      items.value = items.value.filter((p) => p.id !== id);
      if (currentItem.value?.id === id) {
        currentItem.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete page';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function setPage(page) {
    pagination.value.current_page = page;
  }

  function setPerPage(perPage) {
    pagination.value.per_page = perPage;
    pagination.value.current_page = 1;
  }

  function clearStore() {
    items.value = [];
    currentItem.value = null;
    error.value = null;
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
    };
  }

  return {
    // State
    items,
    currentItem,
    loading,
    error,
    pagination,
    // Actions
    fetchAll,
    fetchOne,
    fetchById: fetchOne, // Alias for backward compatibility
    fetchBySlug,
    create,
    update,
    delete: deleteItem, // Alias
    remove: deleteItem, // Alias
    setPage,
    setPerPage,
    clearStore,
  };
});
