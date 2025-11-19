import { useAuthStore } from '../stores/auth';

/**
 * Guest Middleware
 * Ensures user is NOT authenticated (for login/register pages)
 */
export function guestMiddleware(to, from, next) {
  const authStore = useAuthStore();
  
  if (authStore.isAuthenticated) {
    const role = authStore.roleName;
    return next({ path: `/dashboard/${role}` });
  }
  
  next();
}

