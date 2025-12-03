import { useAuthStore } from '../stores/auth';

/**
 * Role Middleware
 * Ensures user has required role
 * @param {string|string[]} allowedRoles - Role(s) allowed to access the route
 */
export function roleMiddleware(allowedRoles) {
  return async (to, from, next) => {
    const authStore = useAuthStore();
    
    if (!authStore.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } });
    }
    
    // If user data is missing or role is missing, try to load from localStorage first
    if (!authStore.user || !authStore.roleName) {
      // First, try to load from localStorage if available
      const savedUser = localStorage.getItem('gs_user') || localStorage.getItem('user');
      if (savedUser && !authStore.user) {
        try {
          const parsedUser = JSON.parse(savedUser);
          // Normalize role
          if (parsedUser.role && typeof parsedUser.role === 'object' && parsedUser.role.name) {
            parsedUser.role = parsedUser.role.name;
            parsedUser.role_name = parsedUser.role;
          }
          if (parsedUser.role && !parsedUser.role_name) {
            parsedUser.role_name = parsedUser.role;
          }
          // Use setSession to properly update the store
          if (authStore.setSession) {
            authStore.setSession(parsedUser, authStore.token);
          }
        } catch (e) {
          console.warn('Failed to parse saved user data:', e);
        }
      }
      
      // Only try to fetch from API if user is still missing
      if (!authStore.user && authStore.fetchCurrentUser) {
        try {
          await authStore.fetchCurrentUser();
        } catch (error) {
          // If 404, endpoint doesn't exist - that's okay, use localStorage data
          if (error.response?.status === 404) {
            console.warn('User endpoint not available, using localStorage data');
            // Don't redirect if we have data in localStorage
            if (!savedUser) {
              if (authStore.clearSession) {
                authStore.clearSession();
              }
              return next({ name: 'login', query: { redirect: to.fullPath } });
            }
          } else if (error.response?.status === 401) {
            // 401 means token is invalid
            console.warn('Token invalid, clearing session');
            if (authStore.clearSession) {
              authStore.clearSession();
            }
            return next({ name: 'login', query: { redirect: to.fullPath } });
          } else {
            console.warn('Failed to fetch user data in role middleware:', error);
            // For other errors, only redirect if no localStorage data
            if (!savedUser) {
              if (authStore.clearSession) {
                authStore.clearSession();
              }
              return next({ name: 'login', query: { redirect: to.fullPath } });
            }
          }
        }
      }
    }
    
    const userRole = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
    const roles = Array.isArray(allowedRoles) ? allowedRoles : [allowedRoles];
    
    if (!roles.includes(userRole)) {
      // Validate role before redirecting
      if (userRole && ['admin', 'super_admin', 'instructor', 'student'].includes(userRole)) {
        return next({ path: `/dashboard/${userRole}` });
      } else {
        // If role is invalid or missing, redirect to login
        console.warn('Invalid or missing role, redirecting to login');
        return next({ name: 'login', query: { redirect: to.fullPath } });
      }
    }
    
    next();
  };
}

