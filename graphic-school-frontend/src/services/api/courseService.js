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
    const response = await api.get('/courses', { params });
    // Interceptor already extracts data from unified format
    // Response is now the array directly, with meta attached if exists
    return response.data || response;
  },

  /**
   * Get course by ID
   * @param {number} id - Course ID
   * @returns {Promise<Object>}
   */
  async getById(id) {
    const response = await api.get(`/courses/${id}`);
    // Interceptor already extracts data from unified format
    // Response is now the course object directly
    return response.data || response;
  },

  /**
   * Get admin dashboard courses with stats
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getDashboardData(params = {}) {
    const response = await api.get('/admin/dashboard', { params });
    // Interceptor already extracts data from unified format
    return response.data || response;
  },

  /**
   * Get admin courses list
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAdminCourses(params = {}) {
    const response = await api.get('/admin/courses', { params });
    // Interceptor already extracts data from unified format
    // Response is now the array directly, with meta attached if exists
    return response.data || response;
  },

  /**
   * Create course
   * @param {Object} payload - Course data
   * @returns {Promise<Object>}
   */
  async create(payload) {
    const response = await api.post('/admin/courses', payload);
    // Interceptor already extracts data from unified format
    // Response is now the course object directly
    return response.data || response;
  },

  /**
   * Update course
   * @param {number} id - Course ID
   * @param {Object} payload - Course data
   * @returns {Promise<Object>}
   */
  async update(id, payload) {
    const response = await api.put(`/admin/courses/${id}`, payload);
    // Interceptor already extracts data from unified format
    // Response is now the course object directly
    return response.data || response;
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
    const response = await api.get('/instructor/courses', { params });
    // Interceptor already extracts data from unified format
    // Response is now the array directly, with meta attached if exists
    return response.data || response;
  },

  /**
   * Get student courses
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getStudentCourses(params = {}) {
    const response = await api.get('/student/courses', { params });
    // Interceptor already extracts data from unified format
    // Response is now the array directly, with meta attached if exists
    return response.data || response;
  },
};

