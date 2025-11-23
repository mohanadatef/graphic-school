import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '../services/api';
import i18n from '../i18n';

// Helper function to get translation in legacy mode
const t = (key, params) => {
  // Try i18n.global.t (composition API mode)
  if (i18n.global && typeof i18n.global.t === 'function') {
    return i18n.global.t(key, params);
  }
  // Try i18n.t (legacy mode)
  if (typeof i18n.t === 'function') {
    return i18n.t(key, params);
  }
  // Fallback: manual translation lookup
  const locale = i18n.locale || 'ar';
  const messages = i18n.messages?.[locale] || i18n.messages?.ar || {};
  const keys = key.split('.');
  let value = messages;
  for (const k of keys) {
    value = value?.[k];
    if (value === undefined) break;
  }
  return value || key;
};

export const useAuthStore = defineStore('auth', () => {
  
  // State
  const user = ref(null);
  const token = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Initialize from localStorage ON APP START
  const savedUser = localStorage.getItem('gs_user') || localStorage.getItem('user');
  const savedToken = localStorage.getItem('gs_token') || localStorage.getItem('token');
  
  if (savedUser) {
    try {
      const parsedUser = JSON.parse(savedUser);
      // Ensure role_name is set if role is a string
      if (parsedUser && typeof parsedUser.role === 'string' && !parsedUser.role_name) {
        parsedUser.role_name = parsedUser.role;
      } else if (parsedUser && parsedUser.role && typeof parsedUser.role === 'object' && parsedUser.role.name && !parsedUser.role_name) {
        parsedUser.role_name = parsedUser.role.name;
        parsedUser.role = parsedUser.role.name; // Normalize to string
      }
      user.value = parsedUser;
    } catch (e) {
      console.error('[authStore] Error parsing saved user:', e);
      localStorage.removeItem('gs_user');
      localStorage.removeItem('user');
    }
  }
  if (savedToken) {
    token.value = savedToken;
    // Ensure both token keys are set for compatibility
    localStorage.setItem('gs_token', savedToken);
    localStorage.setItem('token', savedToken);
  }

  // Getters
  const isAuthenticated = computed(() => !!token.value);
  const roleName = computed(() => {
    if (!user.value) {
      return null;
    }
    
    // Try role as string first (from API response - most common case)
    if (typeof user.value.role === 'string' && user.value.role) {
      return user.value.role;
    }
    
    // Try role_name (if it's appended by the model)
    if (user.value.role_name) {
      return user.value.role_name;
    }
    
    // Try role as object with name property
    if (user.value.role && typeof user.value.role === 'object' && user.value.role.name) {
      return user.value.role.name;
    }
    
    // Fallback: try to get from nested role object
    if (user.value.role?.name) {
      return user.value.role.name;
    }
    
    return null;
  });
  const isAdmin = computed(() => {
    const role = roleName.value;
    return role === 'admin' || role === 'super_admin';
  });
  const isInstructor = computed(() => roleName.value === 'instructor');
  const isStudent = computed(() => roleName.value === 'student');

  // Actions
  function setSession(userData, tokenData) {
    // Ensure role is always a STRING
    if (userData) {
      // Normalize role to string
      if (userData.role && typeof userData.role === 'object' && userData.role.name) {
        // If role is an object, extract the name
        userData.role = userData.role.name;
        userData.role_name = userData.role;
      } else if (userData.role && typeof userData.role !== 'string') {
        // Convert to string if not already
        userData.role = String(userData.role);
      }
      
      // Ensure role_name is set if role exists
      if (userData.role && !userData.role_name) {
        userData.role_name = userData.role;
      }
    }
    
    user.value = userData;
    token.value = tokenData;
    localStorage.setItem('gs_user', JSON.stringify(userData));
    localStorage.setItem('gs_token', tokenData);
    error.value = null;
  }

  function clearSession() {
    user.value = null;
    token.value = null;
    localStorage.removeItem('gs_user');
    localStorage.removeItem('gs_token');
    error.value = null;
  }

  async function login(credentials) {
    loading.value = true;
    error.value = null;
    try {
      const response = await authService.login(credentials);
      // Backend returns unified format: { success, message, data: { user, token } }
      // The interceptor already extracts data, so response is { user, token }
      const data = response.data || response; // Support both formats for backward compatibility
      if (data && data.user && data.token) {
        // Ensure role is always a STRING
        if (data.user.role && typeof data.user.role !== 'string') {
          data.user.role = String(data.user.role);
        }
        // Ensure role_name is set if role exists
        if (data.user.role && !data.user.role_name) {
          data.user.role_name = data.user.role;
        }
        
        setSession(data.user, data.token);
        // Ensure token is persisted
        if (data.token) {
          localStorage.setItem('token', data.token);
          localStorage.setItem('gs_token', data.token);
        }
        // Return resolved promise with user data
        return Promise.resolve(data.user);
      } else {
        throw new Error('Invalid response format from server');
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || t('auth.loginError');
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function register(payload) {
    loading.value = true;
    error.value = null;
    try {
      const response = await authService.register(payload);
      // Backend returns unified format: { success, message, data: { user, token } }
      // The interceptor already extracts data, so response is { user, token }
      const data = response.data || response; // Support both formats for backward compatibility
      if (data && data.user && data.token) {
        // Ensure role is always a STRING
        if (data.user.role && typeof data.user.role !== 'string') {
          data.user.role = String(data.user.role);
        }
        // Ensure role_name is set if role exists
        if (data.user.role && !data.user.role_name) {
          data.user.role_name = data.user.role;
        }
        
        setSession(data.user, data.token);
        return data.user;
      } else {
        throw new Error('Invalid response format from server');
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message || t('auth.registerError');
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      // Only call logout API if we have a valid token
      if (token.value) {
        await authService.logout();
      }
    } catch (err) {
      // Ignore logout errors (especially 401 errors - token might already be invalid)
      // This is expected when token expires or is invalid
      if (err.response?.status !== 401) {
        console.warn('Logout API call failed:', err);
      }
    } finally {
      // Always clear session, even if API call fails
      clearSession();
    }
  }

  async function fetchCurrentUser() {
    if (!token.value) return;
    
    loading.value = true;
    try {
      const userData = await authService.getCurrentUser();
      user.value = userData;
      localStorage.setItem('gs_user', JSON.stringify(userData));
    } catch (err) {
      clearSession();
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Get the redirect path after login based on user role
   * @returns {string} The redirect path
   */
  function afterLoginRedirect() {
    const role = roleName.value;
    
    if (role === 'student') {
      return '/';
    } else if (role === 'instructor') {
      return '/dashboard/instructor';
    } else if (role === 'admin' || role === 'super_admin') {
      return '/dashboard/admin';
    }
    
    // Default fallback
    return '/dashboard/admin';
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    // Getters
    isAuthenticated,
    roleName,
    isAdmin,
    isInstructor,
    isStudent,
    // Actions
    login,
    register,
    logout,
    setSession,
    clearSession,
    fetchCurrentUser,
    afterLoginRedirect,
  };
});

