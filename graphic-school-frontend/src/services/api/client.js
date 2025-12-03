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
    
    // Log request in development mode (for debugging)
    if (import.meta.env.DEV && (config.url?.includes('/login') || config.url?.includes('/register'))) {
      console.log('[API Request]', {
        method: config.method?.toUpperCase(),
        url: config.baseURL + config.url,
        data: config.data,
        headers: {
          'Content-Type': config.headers['Content-Type'],
          'Accept-Language': config.headers['Accept-Language'],
        },
      });
    }
    
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

    // Don't log expected errors (these are handled gracefully)
    const isPublicEndpoint = error.config?.url?.includes('/public/');
    const is404 = error.response?.status === 404;
    const is401 = error.response?.status === 401;
    const isUserEndpoint = error.config?.url === '/user' || error.config?.url?.endsWith('/user');
    const isGetUserRequest = isUserEndpoint && error.config?.method === 'get';
    
    // Skip logging for:
    // 1. 404 errors on public endpoints (expected behavior)
    // 2. 401 errors on GET /user (expected when token is invalid/expired - app handles this gracefully)
    const shouldSkipLogging = (isPublicEndpoint && is404) || (isGetUserRequest && is401);
    
    if (!shouldSkipLogging) {
      // Log error for monitoring (skip expected errors)
      logError(error, {
        type: 'api_error',
        url: error.config?.url,
        method: error.config?.method,
        status: error.response?.status,
      });
    }
    
    // Handle unified error format
    if (error.response?.data && typeof error.response.data === 'object' && 'success' in error.response.data) {
      // This is a unified error response
      // Preserve the original structure but make it easier to access
      const errorData = error.response.data;
      // Keep the original data but also add direct access to message
      error.response.data = {
        ...errorData, // Preserve all original fields
        message: errorData.message || 'An error occurred',
        errors: errorData.errors || null,
      };
      // Also attach message directly to error for easier access
      error.message = errorData.message || error.message || 'An error occurred';
    }
    
    // Handle 401 - Unauthorized
    if (error.response?.status === 401) {
      // Log detailed error in development mode
      if (import.meta.env.DEV) {
        console.error('[API 401 Error]', {
          url: error.config?.baseURL + error.config?.url,
          method: error.config?.method?.toUpperCase(),
          requestData: error.config?.data,
          responseData: error.response?.data,
          status: error.response?.status,
        });
      }
      
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
      const role = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
      const validRoles = ['admin', 'super_admin', 'instructor', 'student'];
      if (role && validRoles.includes(role)) {
        window.location.href = `/dashboard/${role}`;
      } else {
        // If role is invalid or missing, redirect to login
        window.location.href = '/login';
      }
    }
    
    return Promise.reject(error);
  }
);

export default api;
