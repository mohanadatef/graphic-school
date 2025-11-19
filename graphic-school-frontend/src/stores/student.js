import { defineStore } from 'pinia';
import { ref } from 'vue';
import { studentService } from '../services/api';

export const useStudentStore = defineStore('student', () => {
  // State
  const sessions = ref([]);
  const attendance = ref([]);
  const profile = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });

  // Actions
  async function fetchSessions(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await studentService.getSessions(params);
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
      const data = await studentService.getAttendance(params);
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

  async function fetchProfile() {
    loading.value = true;
    error.value = null;
    try {
      const data = await studentService.getProfile();
      profile.value = data;
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch profile';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateProfile(payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await studentService.updateProfile(payload);
      profile.value = data;
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update profile';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    // State
    sessions,
    attendance,
    profile,
    loading,
    error,
    pagination,
    // Actions
    fetchSessions,
    fetchAttendance,
    fetchProfile,
    updateProfile,
  };
});

