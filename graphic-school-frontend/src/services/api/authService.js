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
    const response = await api.post('/login', credentials);
    // Interceptor already extracts data from unified format
    // Response is now { user, token } directly
    return response.data || response;
  },

  /**
   * Register new user
   * @param {Object} payload - User registration data
   * @returns {Promise<{user: Object, token: string}>}
   */
  async register(payload) {
    const response = await api.post('/register', payload);
    // Interceptor already extracts data from unified format
    // Response is now { user, token } directly
    return response.data || response;
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

