import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import { monitorApiCall, logError } from '../../utils/monitoring';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://graphic-school.test/api',
  timeout: 30000, // 30 seconds timeout
});

// Request interceptor - attach token and track performance
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();
    const token = authStore.token;
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add locale header
    const locale = localStorage.getItem('locale') || 'ar';
    config.headers['Accept-Language'] = locale;
    
    // Add performance tracking
    config.metadata = {
      startTime: performance.now(),
      url: config.url,
      method: config.method,
    };
    
    return config;
  },
  (error) => {
    logError(error, { type: 'request_interceptor' });
    return Promise.reject(error);
  }
);

// Response interceptor - handle unified API response format
api.interceptors.response.use(
  (response) => {
    // Track API performance
    if (response.config?.metadata) {
      const duration = performance.now() - response.config.metadata.startTime;
      monitorApiCall(
        response.config.metadata.url,
        response.config.metadata.method,
        duration,
        response.status
      );
    }

    // Backend now returns unified format: { success, message, data, errors, status, meta }
    // Extract the actual data from the unified response
    if (response.data && typeof response.data === 'object' && 'success' in response.data) {
      // This is a unified API response
      // Return the data property directly, but keep meta if exists
      const unifiedResponse = response.data;
      response.data = unifiedResponse.data;
      
      // Attach meta to response if exists
      if (unifiedResponse.meta) {
        response.meta = unifiedResponse.meta;
      }
      
      // Attach message if needed
      if (unifiedResponse.message) {
        response.message = unifiedResponse.message;
      }
    }
    
    return response;
  },
  (error) => {
    const authStore = useAuthStore();
    
    // Track API error performance
    if (error.config?.metadata) {
      const duration = performance.now() - error.config.metadata.startTime;
      monitorApiCall(
        error.config.metadata.url,
        error.config.metadata.method,
        duration,
        error.response?.status || 0
      );
    }

    // Log error for monitoring
    logError(error, {
      type: 'api_error',
      url: error.config?.url,
      method: error.config?.method,
      status: error.response?.status,
    });
    
    // Handle unified error format
    if (error.response?.data && typeof error.response.data === 'object' && 'success' in error.response.data) {
      // This is a unified error response
      const errorData = error.response.data;
      error.response.data = {
        message: errorData.message || 'An error occurred',
        errors: errorData.errors || null,
      };
    }
    
    // Handle 401 - Unauthorized
    if (error.response?.status === 401) {
      // Don't call logout API if we're already getting 401 (to avoid infinite loop)
      // Just clear the session locally
      if (authStore.token) {
        authStore.clearSession();
        // Only redirect if not already on login/register page
        if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
          window.location.href = '/login';
        }
      }
    }
    
    // Handle 403 - Forbidden
    if (error.response?.status === 403) {
      // Redirect to appropriate dashboard based on role
      const role = authStore.user?.role_name || authStore.user?.role?.name;
      if (role) {
        window.location.href = `/dashboard/${role}`;
      }
    }
    
    return Promise.reject(error);
  }
);

export default api;
