/**
 * Input Validation Utilities
 * Provides client-side validation functions
 */

export const validators = {
  email: (value) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(value) || 'البريد الإلكتروني غير صحيح';
  },

  required: (value) => {
    if (value === null || value === undefined || value === '') {
      return 'هذا الحقل مطلوب';
    }
    return true;
  },

  minLength: (min) => (value) => {
    if (value && value.length < min) {
      return `يجب أن يكون على الأقل ${min} أحرف`;
    }
    return true;
  },

  maxLength: (max) => (value) => {
    if (value && value.length > max) {
      return `يجب ألا يتجاوز ${max} حرف`;
    }
    return true;
  },

  password: (value) => {
    if (!value) return 'كلمة المرور مطلوبة';
    if (value.length < 8) return 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    if (!/[A-Z]/.test(value)) return 'يجب أن تحتوي على حرف كبير واحد على الأقل';
    if (!/[a-z]/.test(value)) return 'يجب أن تحتوي على حرف صغير واحد على الأقل';
    if (!/[0-9]/.test(value)) return 'يجب أن تحتوي على رقم واحد على الأقل';
    return true;
  },

  phone: (value) => {
    const phoneRegex = /^[0-9+\-\s()]+$/;
    return phoneRegex.test(value) || 'رقم الهاتف غير صحيح';
  },

  url: (value) => {
    try {
      new URL(value);
      return true;
    } catch {
      return 'الرابط غير صحيح';
    }
  },

  numeric: (value) => {
    return !isNaN(value) && !isNaN(parseFloat(value)) || 'يجب أن يكون رقماً';
  },

  positive: (value) => {
    const num = parseFloat(value);
    return num > 0 || 'يجب أن يكون رقماً موجباً';
  },
};

/**
 * Validate a value against multiple validators
 */
export function validate(value, rules) {
  if (!Array.isArray(rules)) {
    rules = [rules];
  }

  for (const rule of rules) {
    let result;
    if (typeof rule === 'function') {
      result = rule(value);
    } else if (typeof rule === 'string' && validators[rule]) {
      result = validators[rule](value);
    } else if (Array.isArray(rule)) {
      const [validatorName, ...args] = rule;
      if (validators[validatorName]) {
        result = validators[validatorName](...args)(value);
      }
    }

    if (result !== true) {
      return result;
    }
  }

  return true;
}

/**
 * Sanitize input to prevent XSS
 */
export function sanitize(input) {
  if (typeof input !== 'string') return input;
  
  const div = document.createElement('div');
  div.textContent = input;
  return div.innerHTML;
}

/**
 * Escape HTML special characters
 */
export function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

