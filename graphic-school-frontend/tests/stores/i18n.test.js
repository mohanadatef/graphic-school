import { describe, it, expect, beforeEach, vi } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useI18nStore } from '../../src/stores/i18n';
import { loadTranslations } from '../../src/i18n/loader';

// Mock the loader
vi.mock('../../src/i18n/loader', () => ({
  loadTranslations: vi.fn(),
}));

describe('I18n Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    localStorage.clear();
    document.documentElement.lang = '';
    document.documentElement.dir = '';
    document.body.classList.remove('rtl', 'ltr');
  });

  describe('Initial State', () => {
    it('should initialize with default locale from localStorage', () => {
      localStorage.setItem('locale', 'en');
      const store = useI18nStore();
      expect(store.locale).toBe('en');
    });

    it('should initialize with "ar" if no locale in localStorage', () => {
      const store = useI18nStore();
      expect(store.locale).toBe('ar');
    });

    it('should initialize with empty messages', () => {
      const store = useI18nStore();
      expect(store.messages).toEqual({});
    });

    it('should initialize with loading false', () => {
      const store = useI18nStore();
      expect(store.loading).toBe(false);
    });

    it('should compute isRTL correctly', () => {
      const store = useI18nStore();
      store.locale = 'ar';
      expect(store.isRTL).toBe(true);
      
      store.locale = 'en';
      expect(store.isRTL).toBe(false);
    });

    it('should compute localeName correctly', () => {
      const store = useI18nStore();
      store.locale = 'ar';
      expect(store.localeName).toBe('العربية');
      
      store.locale = 'en';
      expect(store.localeName).toBe('English');
    });
  });

  describe('loadLocale', () => {
    it('should load translations successfully', async () => {
      const mockTranslations = {
        'common.welcome': 'Welcome',
        'common.hello': 'Hello',
      };
      
      loadTranslations.mockResolvedValue(mockTranslations);
      
      const store = useI18nStore();
      await store.loadLocale('en');
      
      expect(loadTranslations).toHaveBeenCalledWith('en');
      expect(store.messages).toEqual(mockTranslations);
      expect(store.locale).toBe('en');
      expect(localStorage.getItem('locale')).toBe('en');
      expect(document.documentElement.lang).toBe('en');
      expect(document.documentElement.dir).toBe('ltr');
      expect(document.body.classList.contains('ltr')).toBe(true);
    });

    it('should set RTL for Arabic locale', async () => {
      const mockTranslations = { 'common.welcome': 'مرحباً' };
      loadTranslations.mockResolvedValue(mockTranslations);
      
      const store = useI18nStore();
      await store.loadLocale('ar');
      
      expect(document.documentElement.lang).toBe('ar');
      expect(document.documentElement.dir).toBe('rtl');
      expect(document.body.classList.contains('rtl')).toBe(true);
      expect(document.body.classList.contains('ltr')).toBe(false);
    });

    it('should handle loading errors gracefully', async () => {
      const error = new Error('Failed to load translations');
      loadTranslations.mockRejectedValue(error);
      
      const store = useI18nStore();
      await store.loadLocale('en');
      
      expect(store.error).toBe('Failed to load translations');
      expect(store.loading).toBe(false);
    });

    it('should set loading state during load', async () => {
      let resolveLoad;
      const loadPromise = new Promise((resolve) => {
        resolveLoad = resolve;
      });
      
      loadTranslations.mockReturnValue(loadPromise);
      
      const store = useI18nStore();
      const loadPromise2 = store.loadLocale('en');
      
      expect(store.loading).toBe(true);
      
      resolveLoad({ 'common.welcome': 'Welcome' });
      await loadPromise2;
      
      expect(store.loading).toBe(false);
    });
  });

  describe('switchLanguage', () => {
    it('should switch language successfully', async () => {
      const mockTranslations = { 'common.welcome': 'Welcome' };
      loadTranslations.mockResolvedValue(mockTranslations);
      
      const store = useI18nStore();
      store.locale = 'ar';
      
      await store.switchLanguage('en');
      
      expect(loadTranslations).toHaveBeenCalledWith('en');
      expect(store.locale).toBe('en');
    });

    it('should not switch if already on the same locale', async () => {
      const store = useI18nStore();
      store.locale = 'en';
      
      await store.switchLanguage('en');
      
      expect(loadTranslations).not.toHaveBeenCalled();
    });

    it('should fallback to "ar" for unsupported locales', async () => {
      const mockTranslations = { 'common.welcome': 'مرحباً' };
      loadTranslations.mockResolvedValue(mockTranslations);
      
      const store = useI18nStore();
      store.locale = 'en';
      
      await store.switchLanguage('fr');
      
      expect(loadTranslations).toHaveBeenCalledWith('ar');
      expect(store.locale).toBe('ar');
    });
  });

  describe('t (translation function)', () => {
    it('should return translation for existing key', () => {
      const store = useI18nStore();
      store.messages = {
        'common.welcome': 'Welcome',
        'common.hello': 'Hello :name',
      };
      
      expect(store.t('common.welcome')).toBe('Welcome');
      expect(store.t('common.hello', { name: 'John' })).toBe('Hello John');
    });

    it('should return key if translation not found', () => {
      const store = useI18nStore();
      store.messages = {};
      
      expect(store.t('common.missing')).toBe('common.missing');
    });

    it('should replace multiple parameters', () => {
      const store = useI18nStore();
      store.messages = {
        'message': 'Hello :name, you have :count messages',
      };
      
      expect(store.t('message', { name: 'John', count: '5' })).toBe('Hello John, you have 5 messages');
    });
  });
});

