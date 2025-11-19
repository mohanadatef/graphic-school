import api from './client';

/**
 * Authentication Service
 * Handles all authentication-related API calls
 */
export const authService = {
  /**
   * Login user
   * @param {Object} credentials - { email, password }
   * @returns {Promise<{user: Object, token: string}>}
   */
  async login(credentials) {
    const { data } = await api.post('/login', credentials);
    return data;
  },

  /**
   * Register new user
   * @param {Object} payload - User registration data
   * @returns {Promise<{user: Object, token: string}>}
   */
  async register(payload) {
    const { data } = await api.post('/register', payload);
    return data;
  },

  /**
   * Logout user
   * @returns {Promise<void>}
   */
  async logout() {
    await api.post('/logout');
  },

  /**
   * Get current authenticated user
   * @returns {Promise<Object>}
   */
  async getCurrentUser() {
    const { data } = await api.get('/user');
    return data;
  },
};

