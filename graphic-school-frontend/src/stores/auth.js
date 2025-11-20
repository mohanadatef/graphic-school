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

  // Initialize from localStorage
  const savedUser = localStorage.getItem('gs_user');
  const savedToken = localStorage.getItem('gs_token');
  
  if (savedUser) {
    user.value = JSON.parse(savedUser);
  }
  if (savedToken) {
    token.value = savedToken;
  }

  // Getters
  const isAuthenticated = computed(() => !!token.value);
  const roleName = computed(() => user.value?.role_name || user.value?.role?.name);
  const isAdmin = computed(() => roleName.value === 'admin');
  const isInstructor = computed(() => roleName.value === 'instructor');
  const isStudent = computed(() => roleName.value === 'student');

  // Actions
  function setSession(userData, tokenData) {
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
        setSession(data.user, data.token);
        return data.user;
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
      await authService.logout();
    } catch (err) {
      // Ignore logout errors
    } finally {
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
  };
});

