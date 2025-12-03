import { defineStore } from 'pinia';
import { ref } from 'vue';
import { groupService } from '../services/api';

export const useGroupStore = defineStore('group', () => {
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
  const filters = ref({
    course_id: '',
    status: '',
  });

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await groupService.getAll({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        ...filters.value,
        ...params,
      });
      items.value = response.data || [];
      if (response.meta?.pagination) {
        pagination.value = response.meta.pagination;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch groups';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await groupService.getById(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch group';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await groupService.create(payload);
      const data = response.data || response;
      items.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create group';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await groupService.update(id, payload);
      const data = response.data || response;
      const index = items.value.findIndex((g) => g.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update group';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteItem(id) {
    loading.value = true;
    error.value = null;
    try {
      await groupService.delete(id);
      items.value = items.value.filter((g) => g.id !== id);
      if (currentItem.value?.id === id) {
        currentItem.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete group';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchInstructorGroups() {
    loading.value = true;
    error.value = null;
    try {
      const response = await groupService.getInstructorGroups();
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch groups';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function setFilters(newFilters) {
    filters.value = { ...filters.value, ...newFilters };
  }

  function resetFilters() {
    filters.value = {
      course_id: '',
      status: '',
    };
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
    filters.value = {
      course_id: '',
      status: '',
    };
  }

  return {
    // State
    items,
    currentItem,
    loading,
    error,
    pagination,
    filters,
    // Actions
    fetchAll,
    fetchOne,
    fetchById: fetchOne, // Alias for backward compatibility
    create,
    update,
    delete: deleteItem, // Alias
    remove: deleteItem, // Alias
    fetchInstructorGroups,
    setFilters,
    resetFilters,
    setPage,
    setPerPage,
    clearStore,
  };
});
