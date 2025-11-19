import api from './client';

/**
 * Category Service
 * Handles all category-related API calls
 */
export const categoryService = {
  /**
   * Get all categories
   * @returns {Promise<Array>}
   */
  async getAll() {
    const { data } = await api.get('/categories');
    return data;
  },

  /**
   * Get category by ID
   * @param {number} id - Category ID
   * @returns {Promise<Object>}
   */
  async getById(id) {
    const { data } = await api.get(`/categories/${id}`);
    return data;
  },

  /**
   * Get admin categories list
   * @param {Object} params - Query parameters
   * @returns {Promise<Object>}
   */
  async getAdminCategories(params = {}) {
    const { data } = await api.get('/admin/categories', { params });
    return data;
  },

  /**
   * Create category
   * @param {Object} payload - Category data
   * @returns {Promise<Object>}
   */
  async create(payload) {
    const { data } = await api.post('/admin/categories', payload);
    return data;
  },

  /**
   * Update category
   * @param {number} id - Category ID
   * @param {Object} payload - Category data
   * @returns {Promise<Object>}
   */
  async update(id, payload) {
    const { data } = await api.put(`/admin/categories/${id}`, payload);
    return data;
  },

  /**
   * Delete category
   * @param {number} id - Category ID
   * @returns {Promise<void>}
   */
  async delete(id) {
    await api.delete(`/admin/categories/${id}`);
  },
};

