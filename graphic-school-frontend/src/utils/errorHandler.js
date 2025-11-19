import i18n from '../i18n';

/**
 * Global Error Handler
 * Handles API errors and displays user-friendly messages
 */
export class ErrorHandler {
  static handle(error, showToast = true) {
    const t = i18n.global.t;
    
    let message = t('errors.generic');
    
    if (error.response) {
      const status = error.response.status;
      const data = error.response.data;
      
      switch (status) {
        case 400:
          message = data.message || t('errors.badRequest');
          break;
        case 401:
          message = t('errors.unauthorized');
          break;
        case 403:
          message = t('errors.forbidden');
          break;
        case 404:
          message = t('errors.notFound');
          break;
        case 422:
          message = data.message || t('errors.validation');
          if (data.errors) {
            // Handle validation errors
            const firstError = Object.values(data.errors)[0];
            if (Array.isArray(firstError) && firstError.length > 0) {
              message = firstError[0];
            }
          }
          break;
        case 500:
          message = t('errors.serverError');
          break;
        default:
          message = data.message || t('errors.generic');
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

