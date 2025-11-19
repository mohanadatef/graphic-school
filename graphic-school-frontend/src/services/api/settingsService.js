import api from './client';

/**
 * Settings Service
 * Handles all settings-related API calls
 */
export const settingsService = {
  /**
   * Get all settings
   * @returns {Promise<Array>}
   */
  async getAll() {
    const { data } = await api.get('/admin/settings');
    return data;
  },

  /**
   * Update setting
   * @param {number} id - Setting ID
   * @param {Object} payload - Setting data
   * @returns {Promise<Object>}
   */
  async update(id, payload) {
    const { data } = await api.put(`/admin/settings/${id}`, payload);
    return data;
  },

  /**
   * Update multiple settings
   * @param {Array} settings - Array of settings to update
   * @returns {Promise<Object>}
   */
  async updateMultiple(settings) {
    const { data } = await api.put('/admin/settings', { settings });
    return data;
  },
};

