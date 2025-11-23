import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://graphic-school.test/api',
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('gs_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  
  // Add locale header
  const locale = localStorage.getItem('locale') || 'ar';
  config.headers['Accept-Language'] = locale;
  
  return config;
});

api.interceptors.response.use(
  (response) => {
    // Handle unified API response format: { success, message, data, errors, status, meta }
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
    // Handle unified error format
    if (error.response?.data && typeof error.response.data === 'object' && 'success' in error.response.data) {
      // This is a unified error response
      const errorData = error.response.data;
      error.response.data = {
        message: errorData.message || 'An error occurred',
        errors: errorData.errors || null,
      };
    }
    
    if (error.response?.status === 401) {
      localStorage.removeItem('gs_token');
      localStorage.removeItem('gs_user');
    }
    return Promise.reject(error);
  },
);

export default api;

