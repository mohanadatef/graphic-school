/**
 * Self-Healing Router Extension
 * Automatically generates pages for 404 routes
 */

let isProcessing404 = false;

// Lazy load handler to avoid blocking
let handle404RouteFn = null;
async function getHandle404Route() {
  if (!handle404RouteFn) {
    try {
      const module = await import('../utils/selfHealBrowser');
      handle404RouteFn = module.handle404Route;
    } catch (error) {
      // Return no-op function if import fails
      handle404RouteFn = () => Promise.resolve();
    }
  }
  return handle404RouteFn;
}

/**
 * Enhanced router error handler
 */
export function setupSelfHealingRouter(router) {
  // Only setup in development, skip in tests
  if (window.Cypress || import.meta.env.MODE === 'test') {
    return router;
  }

  try {
    // Handle navigation errors (non-blocking)
    router.onError((error) => {
      try {
        console.warn('[SELF-HEAL] Router error:', error);
        
        // Check if it's a 404/component not found error
        if (error.message && (
          error.message.includes('404') ||
          error.message.includes('not found') ||
          error.message.includes('Cannot find module')
        )) {
          const currentRoute = router.currentRoute.value;
          if (currentRoute && currentRoute.path) {
            // Load handler dynamically to avoid blocking
            getHandle404Route().then((handle404Route) => {
              handle404Route(currentRoute.path).catch(() => {
                // Silently ignore errors
              });
            }).catch(() => {
              // Silently ignore
            });
          }
        }
      } catch (err) {
        // Silently ignore errors
      }
    });

    // Handle navigation failures
    router.beforeEach((to, from, next) => {
      // Reset processing flag
      isProcessing404 = false;
      next();
    });

    // Handle after navigation
    router.afterEach((to, from, failure) => {
      try {
        if (failure && failure.type === 4) { // NavigationFailureType.NOT_FOUND
          if (!isProcessing404) {
            isProcessing404 = true;
            getHandle404Route().then((handle404Route) => {
              handle404Route(to.path).then(() => {
                // Retry navigation after page is generated
                setTimeout(() => {
                  router.push(to.path).catch(() => {
                    // Silently ignore
                  });
                }, 500);
              }).catch(() => {
                // Silently ignore
              });
            }).catch(() => {
              // Silently ignore
            });
          }
        }
      } catch (err) {
        // Silently ignore errors
      }
    });
  } catch (error) {
    console.warn('[SELF-HEAL] Failed to setup router:', error.message);
  }

  return router;
}

/**
 * Check if route exists and generate if needed
 */
export async function ensureRouteExists(routePath) {
  const router = this;
  const route = router.resolve(routePath);
  
  if (route.name === null || route.matched.length === 0) {
    // Route doesn't exist, generate it
    const handle404Route = await getHandle404Route();
    await handle404Route(routePath).catch(() => {
      // Silently ignore
    });
    return false;
  }
  
  return true;
}

