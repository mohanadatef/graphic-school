import api from './client';

/**
 * User Service
 * Handles all user-related API calls
 */
export const userService = {
  /**
   * Get admin users list
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAdminUsers(params = {}) {
    const { data } = await api.get('/admin/users', { params });
    return data;
  },

  /**
   * Get user by ID
   * @param {number} id - User ID
   * @returns {Promise<Object>}
   */
  async getById(id) {
    const { data } = await api.get(`/admin/users/${id}`);
    return data;
  },

  /**
   * Create user
   * @param {Object} payload - User data
   * @returns {Promise<Object>}
   */
  async create(payload) {
    const { data } = await api.post('/admin/users', payload);
    return data;
  },

  /**
   * Update user
   * @param {number} id - User ID
   * @param {Object} payload - User data
   * @returns {Promise<Object>}
   */
  async update(id, payload) {
    const { data } = await api.put(`/admin/users/${id}`, payload);
    return data;
  },

  /**
   * Delete user
   * @param {number} id - User ID
   * @returns {Promise<void>}
   */
  async delete(id) {
    await api.delete(`/admin/users/${id}`);
  },
};

