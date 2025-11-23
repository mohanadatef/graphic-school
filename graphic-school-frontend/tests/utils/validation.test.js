import { describe, it, expect } from 'vitest';
import { validators, validate } from '../../src/utils/validation';

describe('Validation Utils', () => {
  describe('validators.email', () => {
    it('should validate correct email', () => {
      expect(validators.email('test@example.com')).toBe(true);
    });

    it('should reject invalid email', () => {
      expect(validators.email('invalid-email')).toBe('البريد الإلكتروني غير صحيح');
      expect(validators.email('test@')).toBe('البريد الإلكتروني غير صحيح');
      expect(validators.email('@example.com')).toBe('البريد الإلكتروني غير صحيح');
    });
  });

  describe('validators.required', () => {
    it('should validate non-empty values', () => {
      expect(validators.required('value')).toBe(true);
      expect(validators.required(0)).toBe(true);
      expect(validators.required(false)).toBe(true);
    });

    it('should reject empty values', () => {
      expect(validators.required('')).toBe('هذا الحقل مطلوب');
      expect(validators.required(null)).toBe('هذا الحقل مطلوب');
      expect(validators.required(undefined)).toBe('هذا الحقل مطلوب');
    });
  });

  describe('validators.minLength', () => {
    it('should validate minimum length', () => {
      const minLength5 = validators.minLength(5);
      expect(minLength5('12345')).toBe(true);
      expect(minLength5('123456')).toBe(true);
    });

    it('should reject values below minimum length', () => {
      const minLength5 = validators.minLength(5);
      expect(minLength5('1234')).toBe('يجب أن يكون على الأقل 5 أحرف');
    });
  });

  describe('validators.maxLength', () => {
    it('should validate maximum length', () => {
      const maxLength10 = validators.maxLength(10);
      expect(maxLength10('12345')).toBe(true);
      expect(maxLength10('1234567890')).toBe(true);
    });

    it('should reject values above maximum length', () => {
      const maxLength10 = validators.maxLength(10);
      expect(maxLength10('12345678901')).toBe('يجب ألا يتجاوز 10 حرف');
    });
  });

  describe('validators.password', () => {
    it('should validate strong password', () => {
      expect(validators.password('Password123')).toBe(true);
    });

    it('should reject weak passwords', () => {
      expect(validators.password('short')).toBe('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
      expect(validators.password('nouppercase123')).toBe('يجب أن تحتوي على حرف كبير واحد على الأقل');
      expect(validators.password('NOLOWERCASE123')).toBe('يجب أن تحتوي على حرف صغير واحد على الأقل');
      expect(validators.password('NoNumbers')).toBe('يجب أن تحتوي على رقم واحد على الأقل');
    });
  });

  describe('validators.phone', () => {
    it('should validate phone numbers', () => {
      expect(validators.phone('+1234567890')).toBe(true);
      expect(validators.phone('123-456-7890')).toBe(true);
      expect(validators.phone('(123) 456-7890')).toBe(true);
    });

    it('should reject invalid phone numbers', () => {
      expect(validators.phone('abc123')).toBe('رقم الهاتف غير صحيح');
    });
  });

  describe('validators.url', () => {
    it('should validate URLs', () => {
      expect(validators.url('https://example.com')).toBe(true);
      expect(validators.url('http://example.com')).toBe(true);
    });

    it('should reject invalid URLs', () => {
      expect(validators.url('not-a-url')).toBe('الرابط غير صحيح');
    });
  });

  describe('validators.numeric', () => {
    it('should validate numbers', () => {
      expect(validators.numeric('123')).toBe(true);
      expect(validators.numeric('123.45')).toBe(true);
    });

    it('should reject non-numeric values', () => {
      expect(validators.numeric('abc')).toBe('يجب أن يكون رقماً');
    });
  });

  describe('validators.positive', () => {
    it('should validate positive numbers', () => {
      expect(validators.positive('123')).toBe(true);
      expect(validators.positive('0.5')).toBe(true);
    });

    it('should reject zero and negative numbers', () => {
      expect(validators.positive('0')).toBe('يجب أن يكون رقماً موجباً');
      expect(validators.positive('-5')).toBe('يجب أن يكون رقماً موجباً');
    });
  });

  describe('validate function', () => {
    it('should validate with single rule', () => {
      const result = validate('test@example.com', validators.email);
      expect(result).toBe(true);
    });

    it('should validate with multiple rules', () => {
      const result = validate('Password123', [validators.required, validators.password]);
      expect(result).toBe(true);
    });

    it('should return first error when validation fails', () => {
      const result = validate('', [validators.required, validators.email]);
      expect(result).toBe('هذا الحقل مطلوب');
    });

    it('should handle array of rules', () => {
      const result = validate('test', [validators.required, validators.minLength(5)]);
      expect(result).toBe('يجب أن يكون على الأقل 5 أحرف');
    });
  });
});

