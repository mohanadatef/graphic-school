import { translate } from '../i18n';

/**
 * Global Error Handler
 * Handles API errors and displays user-friendly messages
 */
export class ErrorHandler {
  static handle(error, showToast = true) {
    let message = translate('errors.generic');
    
    if (error.response) {
      const status = error.response.status;
      const data = error.response.data;
      
      // Handle unified error format: { success: false, message, errors, status }
      const errorMessage = data?.message || (typeof data === 'string' ? data : null);
      const errorErrors = data?.errors;
      
      switch (status) {
        case 400:
          message = errorMessage || translate('errors.badRequest');
          break;
        case 401:
          message = errorMessage || translate('errors.unauthorized');
          break;
        case 403:
          message = errorMessage || translate('errors.forbidden');
          break;
        case 404:
          message = errorMessage || translate('errors.notFound');
          break;
        case 422:
          message = errorMessage || translate('errors.validation');
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
          message = errorMessage || translate('errors.serverError');
          break;
        default:
          message = errorMessage || translate('errors.generic');
      }
    } else if (error.request) {
      message = translate('errors.networkError');
    } else {
      message = error.message || translate('errors.generic');
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

