import { defineStore } from 'pinia';
import { ref } from 'vue';
import { attendanceService } from '../services/api';

export const useAttendanceStore = defineStore('attendance', () => {
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
      const response = await attendanceService.getOverview(params);
      items.value = response.data || [];
      if (response.meta?.pagination) {
        pagination.value = response.meta.pagination;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch attendance overview';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchOverview(params = {}) {
    return fetchAll(params);
  }

  async function fetchInstructorSessions() {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.getInstructorSessions();
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchSessionAttendance(sessionId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.getSessionAttendance(sessionId);
      currentItem.value = response.data || response;
      return currentItem.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch attendance';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateAttendance(sessionId, attendanceData) {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.updateAttendance(sessionId, attendanceData);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update attendance';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function takeAttendance(sessionId, attendanceRecords) {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.takeAttendance(sessionId, attendanceRecords);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to take attendance';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentAttendanceHistory(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.getStudentAttendanceHistory(params);
      items.value = response.data || [];
      if (response.meta?.pagination) {
        pagination.value = response.meta.pagination;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch attendance history';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentAttendance() {
    loading.value = true;
    error.value = null;
    try {
      const response = await attendanceService.getStudentAttendance();
      items.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch attendance';
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
    fetchOverview,
    fetchInstructorSessions,
    fetchSessionAttendance,
    updateAttendance,
    takeAttendance,
    fetchStudentAttendanceHistory,
    fetchStudentAttendance,
    setPage,
    setPerPage,
    clearStore,
  };
});
