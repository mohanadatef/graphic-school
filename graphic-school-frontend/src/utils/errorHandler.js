import i18n from '../i18n';

/**
 * Global Error Handler
 * Handles API errors and displays user-friendly messages
 */
export class ErrorHandler {
  static handle(error, showToast = true) {
    // In legacy mode, access i18n instance directly
    const t = (key, params) => {
      // Try i18n.global.t (composition API mode)
      if (i18n.global && typeof i18n.global.t === 'function') {
        return i18n.global.t(key, params);
      }
      // Try i18n.t (legacy mode)
      if (typeof i18n.t === 'function') {
        return i18n.t(key, params);
      }
      // Fallback: manual translation lookup
      const locale = i18n.locale || 'ar';
      const messages = i18n.messages?.[locale] || i18n.messages?.ar || {};
      const keys = key.split('.');
      let value = messages;
      for (const k of keys) {
        value = value?.[k];
        if (value === undefined) break;
      }
      return value || key;
    };
    
    let message = t('errors.generic');
    
    if (error.response) {
      const status = error.response.status;
      const data = error.response.data;
      
      // Handle unified error format: { success: false, message, errors, status }
      const errorMessage = data?.message || (typeof data === 'string' ? data : null);
      const errorErrors = data?.errors;
      
      switch (status) {
        case 400:
          message = errorMessage || t('errors.badRequest');
          break;
        case 401:
          message = errorMessage || t('errors.unauthorized');
          break;
        case 403:
          message = errorMessage || t('errors.forbidden');
          break;
        case 404:
          message = errorMessage || t('errors.notFound');
          break;
        case 422:
          message = errorMessage || t('errors.validation');
          if (errorErrors) {
            // Handle validation errors from unified format
            const firstError = Object.values(errorErrors)[0];
            if (Array.isArray(firstError) && firstError.length > 0) {
              message = firstError[0];
            } else if (typeof firstError === 'string') {
              message = firstError;
            }
          }
          break;
        case 500:
          message = errorMessage || t('errors.serverError');
          break;
        default:
          message = errorMessage || t('errors.generic');
      }
    } else if (error.request) {
      message = t('errors.networkError');
    } else {
      message = error.message || t('errors.generic');
    }
    
    if (showToast) {
      // Emit toast event (will be handled by toast composable)
      window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type: 'error' }
      }));
    }
    
    return message;
  }
}

