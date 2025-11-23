import { useAuthStore } from '../stores/auth';

/**
 * Auth Middleware
 * Ensures user is authenticated
 * isAuthenticated relies ONLY on presence of token (no async API call)
 */
export function authMiddleware(to, from, next) {
  const authStore = useAuthStore();
  
  // Check token presence directly (synchronous, no API call)
  const hasToken = !!authStore.token || !!localStorage.getItem('gs_token') || !!localStorage.getItem('token');
  
  if (!hasToken) {
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }
  
  // If authenticated but trying to access login or register, redirect based on role
  if (hasToken && (to.path === '/login' || to.path === '/register')) {
    // Use centralized redirect function
    const redirectPath = authStore.afterLoginRedirect();
    return next(redirectPath);
  }
  
  next();
}

