/**
 * Monitoring & Logging Utilities
 * Provides client-side error tracking and performance monitoring
 */

/**
 * Log error to console and optionally send to server
 */
export function logError(error, context = {}) {
  const errorInfo = {
    message: error.message,
    stack: error.stack,
    context,
    timestamp: new Date().toISOString(),
    url: window.location.href,
    userAgent: navigator.userAgent,
  };

  // Log to console in development
  if (import.meta.env.DEV) {
    console.error('Error logged:', errorInfo);
  }

  // In production, you can send to error tracking service (e.g., Sentry)
  if (import.meta.env.PROD) {
    // Example: Send to API endpoint
    // fetch('/api/logs/error', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(errorInfo),
    // }).catch(() => {
    //   // Silently fail if logging fails
    // });
  }
}

/**
 * Track performance metrics
 */
export function trackPerformance(metricName, duration, metadata = {}) {
  const metric = {
    name: metricName,
    duration,
    metadata,
    timestamp: new Date().toISOString(),
  };

  // Log to console in development
  if (import.meta.env.DEV) {
    console.log('Performance metric:', metric);
  }

  // In production, send to analytics
  if (import.meta.env.PROD && window.gtag) {
    window.gtag('event', 'timing_complete', {
      name: metricName,
      value: Math.round(duration),
      ...metadata,
    });
  }
}

/**
 * Track user action
 */
export function trackEvent(eventName, eventData = {}) {
  const event = {
    name: eventName,
    data: eventData,
    timestamp: new Date().toISOString(),
    url: window.location.href,
  };

  // Log to console in development
  if (import.meta.env.DEV) {
    console.log('Event tracked:', event);
  }

  // In production, send to analytics
  if (import.meta.env.PROD && window.gtag) {
    window.gtag('event', eventName, eventData);
  }
}

/**
 * Monitor API call performance
 */
export function monitorApiCall(url, method, duration, status) {
  trackPerformance('api_call', duration, {
    url,
    method,
    status,
  });

  // Alert on slow API calls (> 2 seconds)
  if (duration > 2000) {
    console.warn(`Slow API call detected: ${method} ${url} took ${duration}ms`);
  }
}

