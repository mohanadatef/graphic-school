import { useAuthStore } from '../stores/auth';

/**
 * Auth Middleware
 * Ensures user is authenticated
 */
export function authMiddleware(to, from, next) {
  const authStore = useAuthStore();
  
  if (!authStore.isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } });
  }
  
  next();
}

