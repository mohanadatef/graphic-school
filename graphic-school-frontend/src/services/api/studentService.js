import api from './client';

/**
 * Student Service
 * Handles all student-related API calls
 */
export const studentService = {
  /**
   * Get student sessions
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getSessions(params = {}) {
    const { data } = await api.get('/student/sessions', { params });
    return data;
  },

  /**
   * Get student attendance
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAttendance(params = {}) {
    const { data } = await api.get('/student/attendance', { params });
    return data;
  },

  /**
   * Get student profile
   * @returns {Promise<Object>}
   */
  async getProfile() {
    const { data } = await api.get('/student/profile');
    return data;
  },

  /**
   * Update student profile
   * @param {Object} payload - Profile data
   * @returns {Promise<Object>}
   */
  async updateProfile(payload) {
    const { data } = await api.put('/student/profile', payload);
    return data;
  },
};

