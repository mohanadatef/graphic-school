/**
 * Browser-side Self-Healing System
 * Detects issues and sends requests to Node.js backend for file generation
 */

/**
 * Log self-healing actions (browser console)
 */
export function logSelfHeal(action, details) {
  const timestamp = new Date().toISOString();
  console.log(`[SELF-HEAL] ${timestamp} - ${action}:`, details);
  
  // Store in localStorage for persistence
  try {
    const logs = JSON.parse(localStorage.getItem('selfHealLogs') || '[]');
    logs.push({ timestamp, action, details });
    // Keep only last 100 logs
    if (logs.length > 100) {
      logs.shift();
    }
    localStorage.setItem('selfHealLogs', JSON.stringify(logs));
  } catch (e) {
    // Ignore localStorage errors
  }
}

/**
 * Request page generation from backend
 */
export async function requestPageGeneration(routePath) {
  try {
    logSelfHeal('PAGE_GENERATION_REQUESTED', { routePath });
    
    // Try to call backend API if available
    const response = await fetch('/api/self-heal/generate-page', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ routePath }),
    }).catch(() => null);
    
    if (response && response.ok) {
      const data = await response.json();
      logSelfHeal('PAGE_GENERATION_SUCCESS', data);
      return true;
    }
    
    // Fallback: log for manual generation
    logSelfHeal('PAGE_GENERATION_PENDING', {
      routePath,
      message: 'Backend API not available. Page will be generated on next build.',
    });
    
    return false;
  } catch (error) {
    logSelfHeal('PAGE_GENERATION_ERROR', {
      routePath,
      error: error.message,
    });
    return false;
  }
}

// Cache for API availability check (prevents parallel requests)
let apiAvailabilityCheck = null;
let apiAvailable = null;

/**
 * Check if self-heal API is available (only check once per session)
 * Silently checks without logging errors to console
 */
async function checkApiAvailability() {
  // Return cached result if available
  const cached = sessionStorage.getItem('selfHealApiAvailable');
  if (cached === 'false') {
    apiAvailable = false;
    return false;
  }
  if (cached === 'true') {
    apiAvailable = true;
    return true;
  }
  
  // If a check is already in progress, wait for it
  if (apiAvailabilityCheck) {
    return apiAvailabilityCheck;
  }
  
  // Start a new check
  apiAvailabilityCheck = (async () => {
    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 200); // 200ms timeout
      
      // Use OPTIONS method to check if endpoint exists (less likely to cause console errors)
      // If OPTIONS fails, try a minimal POST request with very short timeout
      let response = null;
      
      try {
        response = await fetch('/api/self-heal/add-translation', {
          method: 'OPTIONS',
          signal: controller.signal,
        });
      } catch (e) {
        // OPTIONS failed, try POST with minimal data
        try {
          const postController = new AbortController();
          const postTimeoutId = setTimeout(() => postController.abort(), 100);
          
          response = await fetch('/api/self-heal/add-translation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ _check: true }), // Minimal check payload
            signal: postController.signal,
          }).catch(() => null);
          
          clearTimeout(postTimeoutId);
        } catch (e2) {
          // Both methods failed
          response = null;
        }
      }
      
      clearTimeout(timeoutId);
      
      // If we got a response (even 404), check the status
      if (response) {
        // 200-299, 405 (Method Not Allowed), or 400 (Bad Request) means endpoint exists
        if (response.ok || response.status === 405 || response.status === 400) {
          apiAvailable = true;
          sessionStorage.setItem('selfHealApiAvailable', 'true');
          return true;
        }
        // 404 means endpoint doesn't exist
        if (response.status === 404) {
          apiAvailable = false;
          sessionStorage.setItem('selfHealApiAvailable', 'false');
          return false;
        }
      }
      
      // No response or unexpected status - assume unavailable
      apiAvailable = false;
      sessionStorage.setItem('selfHealApiAvailable', 'false');
      return false;
    } catch (error) {
      // Silently handle errors - endpoint doesn't exist
      apiAvailable = false;
      sessionStorage.setItem('selfHealApiAvailable', 'false');
      return false;
    } finally {
      // Clear the check promise so we can check again if needed
      apiAvailabilityCheck = null;
    }
  })();
  
  return apiAvailabilityCheck;
}

/**
 * Request translation addition
 * Silently fails if endpoint doesn't exist (stores in localStorage instead)
 */
export async function requestTranslation(locale, key, value = null) {
  // Store in localStorage first (always works)
  try {
    const pendingTranslations = JSON.parse(
      localStorage.getItem('pendingTranslations') || '{}'
    );
    if (!pendingTranslations[locale]) {
      pendingTranslations[locale] = {};
    }
    pendingTranslations[locale][key] = value || key;
    localStorage.setItem('pendingTranslations', JSON.stringify(pendingTranslations));
  } catch (e) {
    // Ignore localStorage errors
  }
  
  // Check if API is available (cached per session)
  const isAvailable = await checkApiAvailability();
  if (!isAvailable) {
    // API endpoint doesn't exist, skip the request entirely
    return false;
  }
  
  // Try to send to backend API (only if available)
  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 300);
    
    // Use a silent fetch that won't log errors to console
    const response = await fetch('/api/self-heal/add-translation', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ locale, key, value }),
      signal: controller.signal,
    }).catch((error) => {
      // Silently handle fetch errors (network issues, CORS, etc.)
      // Mark API as unavailable
      apiAvailable = false;
      sessionStorage.setItem('selfHealApiAvailable', 'false');
      return null;
    });
    
    clearTimeout(timeoutId);
    
    if (response && response.ok) {
      const data = await response.json();
      if (import.meta.env.DEV) {
        logSelfHeal('TRANSLATION_SUCCESS', data);
      }
      return true;
    } else if (response) {
      // Any non-ok response (404, 500, etc.) means API is unavailable
      apiAvailable = false;
      sessionStorage.setItem('selfHealApiAvailable', 'false');
    }
  } catch (error) {
    // Silently handle any errors - mark API as unavailable
    apiAvailable = false;
    sessionStorage.setItem('selfHealApiAvailable', 'false');
  }
  
  return false;
}

/**
 * Request E2E test generation
 */
export async function requestTestGeneration(routePath) {
  try {
    logSelfHeal('TEST_GENERATION_REQUESTED', { routePath });
    
    const response = await fetch('/api/self-heal/generate-test', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ routePath }),
    }).catch(() => null);
    
    if (response && response.ok) {
      const data = await response.json();
      logSelfHeal('TEST_GENERATION_SUCCESS', data);
      return true;
    }
    
    logSelfHeal('TEST_GENERATION_PENDING', { routePath });
    return false;
  } catch (error) {
    logSelfHeal('TEST_GENERATION_ERROR', { routePath, error: error.message });
    return false;
  }
}

/**
 * Handle 404 route in browser
 */
export async function handle404Route(routePath) {
  logSelfHeal('404_DETECTED', { routePath });
  
  // Request page generation
  await requestPageGeneration(routePath);
  
  // Request test generation
  await requestTestGeneration(routePath);
  
  // Add default translations
  await requestTranslation('en', 'auto.page.title', `Page: ${routePath}`);
  await requestTranslation('en', 'auto.page.description', `Auto-generated page for route: ${routePath}`);
  await requestTranslation('ar', 'auto.page.title', `صفحة: ${routePath}`);
  await requestTranslation('ar', 'auto.page.description', `صفحة تم إنشاؤها تلقائياً للمسار: ${routePath}`);
  
  return true;
}

