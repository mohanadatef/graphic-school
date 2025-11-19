import { useAuthStore } from '../stores/auth';

/**
 * Role Middleware
 * Ensures user has required role
 * @param {string|string[]} allowedRoles - Role(s) allowed to access the route
 */
export function roleMiddleware(allowedRoles) {
  return (to, from, next) => {
    const authStore = useAuthStore();
    
    if (!authStore.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } });
    }
    
    const userRole = authStore.roleName;
    const roles = Array.isArray(allowedRoles) ? allowedRoles : [allowedRoles];
    
    if (!roles.includes(userRole)) {
      // Redirect to user's dashboard
      return next({ path: `/dashboard/${userRole}` });
    }
    
    next();
  };
}

