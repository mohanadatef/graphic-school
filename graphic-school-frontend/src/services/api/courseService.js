import api from './client';

/**
 * Course Service
 * Handles all course-related API calls
 */
export const courseService = {
  /**
   * Get all courses (public)
   * @param {Object} params - Query parameters
   * @returns {Promise<Array>}
   */
  async getAll(params = {}) {
    const { data } = await api.get('/courses', { params });
    return data;
  },

  /**
   * Get course by ID
   * @param {number} id - Course ID
   * @returns {Promise<Object>}
   */
  async getById(id) {
    const { data } = await api.get(`/courses/${id}`);
    return data;
  },

  /**
   * Get admin dashboard courses with stats
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getDashboardData(params = {}) {
    const { data } = await api.get('/admin/dashboard', { params });
    return data;
  },

  /**
   * Get admin courses list
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAdminCourses(params = {}) {
    const { data } = await api.get('/admin/courses', { params });
    return data;
  },

  /**
   * Create course
   * @param {Object} payload - Course data
   * @returns {Promise<Object>}
   */
  async create(payload) {
    const { data } = await api.post('/admin/courses', payload);
    return data;
  },

  /**
   * Update course
   * @param {number} id - Course ID
   * @param {Object} payload - Course data
   * @returns {Promise<Object>}
   */
  async update(id, payload) {
    const { data } = await api.put(`/admin/courses/${id}`, payload);
    return data;
  },

  /**
   * Delete course
   * @param {number} id - Course ID
   * @returns {Promise<void>}
   */
  async delete(id) {
    await api.delete(`/admin/courses/${id}`);
  },

  /**
   * Get instructor courses
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getInstructorCourses(params = {}) {
    const { data } = await api.get('/instructor/courses', { params });
    return data;
  },

  /**
   * Get student courses
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getStudentCourses(params = {}) {
    const { data } = await api.get('/student/courses', { params });
    return data;
  },
};

