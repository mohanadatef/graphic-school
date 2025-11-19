import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://graphic-school-api.test/api',
});

// Request interceptor - attach token
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();
    const token = authStore.token;
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - handle errors globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const authStore = useAuthStore();
    
    // Handle 401 - Unauthorized
    if (error.response?.status === 401) {
      authStore.logout();
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
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

