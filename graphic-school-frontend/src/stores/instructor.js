import { defineStore } from 'pinia';
import { ref } from 'vue';
import { instructorService } from '../services/api';

export const useInstructorStore = defineStore('instructor', () => {
  // State
  const instructors = ref([]);
  const sessions = ref([]);
  const attendance = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await instructorService.getAll(params);
      instructors.value = Array.isArray(data) ? data : data.data || [];
      return instructors.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch instructors';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchSessions(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await instructorService.getSessions(params);
      sessions.value = data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch sessions';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchAttendance(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await instructorService.getAttendance(params);
      attendance.value = data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch attendance';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function markAttendance(payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await instructorService.markAttendance(payload);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to mark attendance';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    // State
    instructors,
    sessions,
    attendance,
    loading,
    error,
    pagination,
    // Actions
    fetchAll,
    fetchSessions,
    fetchAttendance,
    markAttendance,
  };
});

