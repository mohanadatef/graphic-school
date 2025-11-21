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
    try {
      await api.post('/logout');
    } catch (error) {
      // If logout fails (e.g., token already invalid), that's okay
      // The session will be cleared anyway
      if (error.response?.status !== 401) {
        throw error;
      }
    }
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

