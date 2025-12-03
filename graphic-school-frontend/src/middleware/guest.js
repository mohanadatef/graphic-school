import { useAuthStore } from '../stores/auth';

/**
 * Guest Middleware
 * Ensures user is NOT authenticated (for login/register pages)
 */
export async function guestMiddleware(to, from, next) {
  const authStore = useAuthStore();
  
  if (authStore.isAuthenticated) {
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
      
      // For guest pages (login/register), don't make API calls
      // Only use localStorage data to check if user is already logged in
      // If token exists but user data is missing from localStorage, token is likely invalid
      // Clear the invalid token silently
      if (!authStore.user && !savedUser && authStore.token) {
        // Token exists but no user data in localStorage - token is likely invalid
        // Clear it silently without making API call
        if (authStore.clearSession) {
          authStore.clearSession();
        }
      }
    }
    
    const role = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
    
    // Validate role before redirecting
    if (role && ['admin', 'super_admin', 'instructor', 'student'].includes(role)) {
      return next({ path: `/dashboard/${role}` });
    } else {
      // If role is invalid or missing, clear session and allow access to login/register
      // This happens when token exists but user data is incomplete or invalid
      if (authStore.clearSession) {
        authStore.clearSession();
      }
      // Allow access to the public page (login/register) - don't redirect
      // The user should be able to log in again
      return next();
    }
  }
  
  next();
}

