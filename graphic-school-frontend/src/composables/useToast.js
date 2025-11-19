import { ref } from 'vue';

const toasts = ref([]);

/**
 * Toast Composable
 * Manages toast notifications
 */
export function useToast() {
  function show(message, type = 'info', duration = 3000) {
    const id = Date.now();
    const toast = { id, message, type, duration };
    
    toasts.value.push(toast);
    
    if (duration > 0) {
      setTimeout(() => {
        remove(id);
      }, duration);
    }
    
    return id;
  }
  
  function success(message, duration = 3000) {
    return show(message, 'success', duration);
  }
  
  function error(message, duration = 5000) {
    return show(message, 'error', duration);
  }
  
  function warning(message, duration = 4000) {
    return show(message, 'warning', duration);
  }
  
  function info(message, duration = 3000) {
    return show(message, 'info', duration);
  }
  
  function remove(id) {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  }
  
  function clear() {
    toasts.value = [];
  }
  
  // Listen for global toast events
  if (typeof window !== 'undefined') {
    window.addEventListener('toast', (event) => {
      const { message, type, duration } = event.detail;
      show(message, type, duration);
    });
  }
  
  return {
    toasts,
    show,
    success,
    error,
    warning,
    info,
    remove,
    clear,
  };
}

