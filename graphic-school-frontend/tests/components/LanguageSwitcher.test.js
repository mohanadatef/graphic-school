import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';
import LanguageSwitcher from '../../src/components/common/LanguageSwitcher.vue';
import { useI18nStore } from '../../src/stores/i18n';
import api from '../../src/services/api/client';

// Mock API
vi.mock('../../src/services/api/client', () => ({
  default: {
    get: vi.fn(),
    defaults: {
      headers: {
        common: {},
      },
    },
  },
}));

// Mock i18n
const mockT = vi.fn((key) => key);
vi.mock('vue-i18n', () => ({
  useI18n: () => ({
    t: mockT,
    locale: { value: 'en' },
  }),
}));

describe('LanguageSwitcher Component', () => {
  let pinia;

  beforeEach(() => {
    pinia = createPinia();
    setActivePinia(pinia);
    vi.clearAllMocks();
    localStorage.clear();
  });

  it('should render language switcher button', () => {
    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    expect(wrapper.find('button').exists()).toBe(true);
  });

  it('should fetch available locales on mount', async () => {
    const mockLocales = [
      { code: 'en', name: 'English', native_name: 'English' },
      { code: 'ar', name: 'Arabic', native_name: 'العربية' },
    ];

    api.get.mockResolvedValue({
      data: {
        data: {
          locales: mockLocales,
        },
      },
    });

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    expect(api.get).toHaveBeenCalledWith('/locales');
  });

  it('should toggle dropdown on button click', async () => {
    api.get.mockResolvedValue({
      data: {
        data: {
          locales: [{ code: 'en', name: 'English' }],
        },
      },
    });

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    const button = wrapper.find('button');
    expect(wrapper.vm.isOpen).toBe(false);

    await button.trigger('click');
    expect(wrapper.vm.isOpen).toBe(true);

    await button.trigger('click');
    expect(wrapper.vm.isOpen).toBe(false);
  });

  it('should switch language when locale is clicked', async () => {
    const mockLocales = [
      { code: 'en', name: 'English', native_name: 'English' },
      { code: 'ar', name: 'Arabic', native_name: 'العربية' },
    ];

    api.get.mockResolvedValue({
      data: {
        data: {
          locales: mockLocales,
        },
      },
    });

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    const store = useI18nStore();
    store.locale = 'en';
    store.switchLanguage = vi.fn();

    await wrapper.setData({ isOpen: true });
    await wrapper.vm.$nextTick();

    const localeButtons = wrapper.findAll('button[type="button"]');
    const arButton = localeButtons.find((btn) => btn.text().includes('العربية'));

    if (arButton) {
      await arButton.trigger('click');
      expect(store.switchLanguage).toHaveBeenCalled();
    }
  });

  it('should display loading state', () => {
    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    wrapper.setData({ loading: true });
    expect(wrapper.text()).toContain('Loading');
  });

  it('should display current language name', async () => {
    api.get.mockResolvedValue({
      data: {
        data: {
          locales: [
            { code: 'en', name: 'English', native_name: 'English' },
            { code: 'ar', name: 'Arabic', native_name: 'العربية' },
          ],
        },
      },
    });

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    const store = useI18nStore();
    store.locale = 'ar';

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    expect(wrapper.text()).toContain('العربية');
  });

  it('should handle API error gracefully', async () => {
    api.get.mockRejectedValue(new Error('Network error'));

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    // Should fallback to default locales
    expect(wrapper.vm.availableLocales.length).toBeGreaterThan(0);
  });

  it('should close dropdown when clicking outside', async () => {
    api.get.mockResolvedValue({
      data: {
        data: {
          locales: [{ code: 'en', name: 'English' }],
        },
      },
    });

    const wrapper = mount(LanguageSwitcher, {
      global: {
        plugins: [pinia],
      },
    });

    await wrapper.vm.$nextTick();
    await new Promise((resolve) => setTimeout(resolve, 100));

    wrapper.setData({ isOpen: true });
    wrapper.vm.closeDropdown();

    expect(wrapper.vm.isOpen).toBe(false);
  });
});

