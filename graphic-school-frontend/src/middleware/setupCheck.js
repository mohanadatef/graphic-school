import { useSetupWizardStore } from '../stores/setupWizard';

/**
 * Middleware to check if website is activated
 * Redirects to /setup if not activated
 */
export async function setupCheckMiddleware(to, from, next) {
  // Bypass setup check during Cypress E2E tests
  if (typeof window !== 'undefined' && window.Cypress) {
    return next();
  }

  // Routes that should NOT be checked
  const excludedRoutes = [
    '/setup',
    '/login',
    '/register',
    '/dashboard',
  ];

  // Check if current route is excluded
  const isExcluded = excludedRoutes.some(route => to.path.startsWith(route));
  
  if (isExcluded) {
    return next();
  }

  // Check if route is API or static asset
  if (to.path.startsWith('/api') || 
      to.path.startsWith('/_') || 
      to.path.match(/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/)) {
    return next();
  }

  try {
    const store = useSetupWizardStore();
    
    // Load status if not already loaded
    if (store.isActivated === null || store.shouldRunSetup === null) {
      try {
        await store.loadStatus();
      } catch (err) {
        // If API fails, assume setup is needed
        console.warn('Failed to load setup status, redirecting to setup:', err);
        return next('/setup');
      }
    }

    // If not activated and should run setup, redirect to /setup
    if (!store.isActivated && store.shouldRunSetup) {
      return next('/setup');
    }

    // Website is activated, proceed
    next();
  } catch (error) {
    console.error('Error checking setup status:', error);
    // On error, redirect to setup to be safe
    return next('/setup');
  }
}

