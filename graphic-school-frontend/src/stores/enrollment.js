import { defineStore } from 'pinia';
import { ref } from 'vue';
import { enrollmentService } from '../services/api';

export const useEnrollmentStore = defineStore('enrollment', () => {
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
    status: '',
    course_id: '',
    group_id: '',
  });

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.getAll({
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
      error.value = err.response?.data?.message || 'Failed to fetch enrollments';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOne(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.getById(id);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function create(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.create(payload);
      const data = response.data || response;
      items.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function update(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.update(id, payload);
      const data = response.data || response;
      const index = items.value.findIndex((e) => e.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteItem(id) {
    loading.value = true;
    error.value = null;
    try {
      await enrollmentService.delete(id);
      items.value = items.value.filter((e) => e.id !== id);
      if (currentItem.value?.id === id) {
        currentItem.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function approve(id, groupId = null) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.approve(id, groupId);
      const data = response.data || response;
      const index = items.value.findIndex((e) => e.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to approve enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function reject(id, reason = '') {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.reject(id, reason);
      const data = response.data || response;
      const index = items.value.findIndex((e) => e.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to reject enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function withdraw(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.withdraw(id);
      const data = response.data || response;
      const index = items.value.findIndex((e) => e.id === id);
      if (index !== -1) {
        items.value[index] = data;
      }
      if (currentItem.value?.id === id) {
        currentItem.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to withdraw enrollment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function publicEnroll(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.publicEnroll(payload);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to enroll';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function studentEnroll(courseId, groupId = null) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.studentEnroll(courseId, groupId);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to enroll';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentEnrollments(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await enrollmentService.getStudentEnrollments(params);
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch enrollments';
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
      status: '',
      course_id: '',
      group_id: '',
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
      status: '',
      course_id: '',
      group_id: '',
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
    approve,
    reject,
    withdraw,
    publicEnroll,
    studentEnroll,
    fetchStudentEnrollments,
    setFilters,
    resetFilters,
    setPage,
    setPerPage,
    clearStore,
  };
});
