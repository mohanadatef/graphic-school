import { describe, it, expect, beforeEach, vi } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useBrandingStore } from '../../../src/stores/branding';
import api from '../../../src/services/api/client';

vi.mock('../../../src/services/api/client', () => ({
  default: {
    get: vi.fn(),
  },
}));

describe('Branding Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  it('should fetch branding from API', async () => {
    const mockBranding = {
      'branding.name.display': 'Test Academy',
      'branding.colors.primary': '#3b82f6',
      'branding.colors.secondary': '#0ea5e9',
    };

    api.get.mockResolvedValue({
      data: {
        success: true,
        data: mockBranding,
      },
    });

    const store = useBrandingStore();
    await store.fetchBranding();

    expect(api.get).toHaveBeenCalledWith('/branding/frontend');
    expect(store.branding).toEqual(mockBranding);
  });

  it('should apply branding to DOM', async () => {
    const mockBranding = {
      'branding.name.display': 'Test Academy',
      'branding.colors.primary': '#ff0000',
      'branding.colors.secondary': '#00ff00',
      'branding.fonts.main': 'Roboto',
    };

    api.get.mockResolvedValue({
      data: {
        success: true,
        data: mockBranding,
      },
    });

    const store = useBrandingStore();
    await store.fetchBranding();

    const root = document.documentElement;
    expect(root.style.getPropertyValue('--primary')).toBe('#ff0000');
    expect(root.style.getPropertyValue('--secondary')).toBe('#00ff00');
    expect(root.style.getPropertyValue('--font-main')).toBe('"Roboto", sans-serif');
  });

  it('should use default branding on API failure', async () => {
    api.get.mockRejectedValue(new Error('API Error'));

    const store = useBrandingStore();
    
    try {
      await store.fetchBranding();
    } catch (error) {
      // Expected to throw
    }

    // Should still have default branding applied
    const root = document.documentElement;
    expect(root.style.getPropertyValue('--primary')).toBeTruthy();
  });

  it('should get branding value by key', () => {
    const store = useBrandingStore();
    store.branding = {
      'branding.name.display': 'Test Academy',
    };

    expect(store.get('branding.name.display')).toBe('Test Academy');
    expect(store.get('branding.name.display', 'Default')).toBe('Test Academy');
    expect(store.get('non.existent.key', 'Default')).toBe('Default');
  });

  it('should compute display name', () => {
    const store = useBrandingStore();
    store.branding = {
      'branding.name.display': 'Test Academy',
    };

    expect(store.displayName).toBe('Test Academy');
  });
});

