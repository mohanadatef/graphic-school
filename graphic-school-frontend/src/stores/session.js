import { defineStore } from 'pinia';
import { ref } from 'vue';
import { sessionService } from '../services/api';

export const useSessionStore = defineStore('session', () => {
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
      const response = await sessionService.getAll({
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
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await sessionService.getById(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch session';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await sessionService.update(id, payload);
      const data = response.data || response;
      const index = items.value.findIndex((s) => s.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update session';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function remove(id) {
    loading.value = true;
    error.value = null;
    try {
      await sessionService.delete(id);
      items.value = items.value.filter((s) => s.id !== id);
      if (currentItem.value?.id === id) {
        currentItem.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete session';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchInstructorSessions() {
    loading.value = true;
    error.value = null;
    try {
      const response = await sessionService.getInstructorSessions();
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchGroupSessions(groupId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await sessionService.getGroupSessions(groupId);
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentSessions() {
    loading.value = true;
    error.value = null;
    try {
      const response = await sessionService.getStudentSessions();
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
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
    update,
    delete: remove, // Alias
    remove,
    fetchInstructorSessions,
    fetchGroupSessions,
    fetchStudentSessions,
    setPage,
    setPerPage,
    clearStore,
  };
});

