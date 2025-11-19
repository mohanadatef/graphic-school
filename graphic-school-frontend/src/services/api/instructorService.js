import api from './client';

/**
 * Instructor Service
 * Handles all instructor-related API calls
 */
export const instructorService = {
  /**
   * Get all instructors (public)
   * @param {Object} params - Query parameters
   * @returns {Promise<Array>}
   */
  async getAll(params = {}) {
    const { data } = await api.get('/instructors', { params });
    return data;
  },

  /**
   * Get instructor by ID
   * @param {number} id - Instructor ID
   * @returns {Promise<Object>}
   */
  async getById(id) {
    const { data } = await api.get(`/instructors/${id}`);
    return data;
  },

  /**
   * Get instructor sessions
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getSessions(params = {}) {
    const { data } = await api.get('/instructor/sessions', { params });
    return data;
  },

  /**
   * Get instructor attendance
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAttendance(params = {}) {
    const { data } = await api.get('/instructor/attendance', { params });
    return data;
  },

  /**
   * Mark attendance
   * @param {Object} payload - Attendance data
   * @returns {Promise<Object>}
   */
  async markAttendance(payload) {
    const { data } = await api.post('/instructor/attendance', payload);
    return data;
  },
};

