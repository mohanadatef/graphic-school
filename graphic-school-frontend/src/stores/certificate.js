import { defineStore } from 'pinia';
import { ref } from 'vue';
import { certificateService } from '../services/api';

export const useCertificateStore = defineStore('certificate', () => {
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
      const response = await certificateService.getAll({
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
      error.value = err.response?.data?.message || 'Failed to fetch certificates';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await certificateService.getById(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch certificate';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await certificateService.issue(payload);
      const data = response.data || response;
      items.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to issue certificate';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function issue(payload) {
    return create(payload);
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      // Note: Certificate update might not be available in API
      // If available, implement here
      throw new Error('Certificate update not implemented');
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update certificate';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteItem(id) {
    loading.value = true;
    error.value = null;
    try {
      await certificateService.delete(id);
      items.value = items.value.filter((c) => c.id !== id);
      if (currentItem.value?.id === id) {
        currentItem.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete certificate';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentCertificates() {
    loading.value = true;
    error.value = null;
    try {
      const response = await certificateService.getStudentCertificates();
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch certificates';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentCertificate(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await certificateService.getStudentCertificate(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch certificate';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function verify(code) {
    loading.value = true;
    error.value = null;
    try {
      const response = await certificateService.verify(code);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to verify certificate';
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
    create,
    issue,
    update,
    delete: deleteItem, // Alias
    remove: deleteItem, // Alias
    fetchStudentCertificates,
    fetchStudentCertificate,
    verify,
    setPage,
    setPerPage,
    clearStore,
  };
});
