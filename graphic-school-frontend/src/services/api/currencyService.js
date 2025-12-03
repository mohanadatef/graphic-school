import api from './client';

/**
 * Currency API Service
 * Handles all currency-related API calls
 */
export const currencyService = {
  /**
   * Get all currencies (Admin)
   */
  async getAll(filters = {}) {
    const { data } = await api.get('/admin/currencies', { params: filters });
    return data;
  },

  /**
   * Get currency by ID
   */
  async getById(id) {
    const { data } = await api.get(`/admin/currencies/${id}`);
    return data;
  },

  /**
   * Create a new currency (Admin)
   */
  async create(currencyData) {
    const { data } = await api.post('/admin/currencies', currencyData);
    return data;
  },

  /**
   * Update currency (Admin)
   */
  async update(id, currencyData) {
    const { data } = await api.put(`/admin/currencies/${id}`, currencyData);
    return data;
  },

  /**
   * Delete currency (Admin)
   */
  async delete(id) {
    const { data } = await api.delete(`/admin/currencies/${id}`);
    return data;
  },

  /**
   * Get active currencies
   */
  async getActive() {
    const { data } = await api.get('/admin/currencies/active');
    return data;
  },
};

