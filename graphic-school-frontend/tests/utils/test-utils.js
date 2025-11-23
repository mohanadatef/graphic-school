import { mount, shallowMount } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import { createRouter, createWebHistory } from 'vue-router';
import ar from '../../src/i18n/locales/ar.json';
import en from '../../src/i18n/locales/en.json';

/**
 * Create a test Pinia instance
 */
export function createTestPinia() {
  const pinia = createPinia();
  setActivePinia(pinia);
  return pinia;
}

/**
 * Create a test i18n instance
 */
export function createTestI18n(locale = 'ar') {
  return createI18n({
    locale,
    fallbackLocale: 'ar',
    messages: {
      ar,
      en,
    },
    legacy: false,
  });
}

/**
 * Create a test router instance
 */
export function createTestRouter() {
  return createRouter({
    history: createWebHistory(),
    routes: [
      {
        path: '/',
        component: { template: '<div>Home</div>' },
      },
      {
        path: '/dashboard',
        component: { template: '<div>Dashboard</div>' },
      },
    ],
  });
}

/**
 * Mount a component with all necessary plugins
 */
export function mountWithPlugins(component, options = {}) {
  const pinia = options.pinia || createTestPinia();
  const i18n = options.i18n || createTestI18n();
  const router = options.router || createTestRouter();

  return mount(component, {
    global: {
      plugins: [pinia, i18n, router],
      mocks: {
        $t: (key) => key,
        ...options.mocks,
      },
      stubs: {
        RouterLink: true,
        RouterView: true,
        ...options.stubs,
      },
    },
    ...options,
  });
}

/**
 * Shallow mount a component with all necessary plugins
 */
export function shallowMountWithPlugins(component, options = {}) {
  const pinia = options.pinia || createTestPinia();
  const i18n = options.i18n || createTestI18n();
  const router = options.router || createTestRouter();

  return shallowMount(component, {
    global: {
      plugins: [pinia, i18n, router],
      mocks: {
        $t: (key) => key,
        ...options.mocks,
      },
      stubs: {
        RouterLink: true,
        RouterView: true,
        ...options.stubs,
      },
    },
    ...options,
  });
}

/**
 * Wait for next tick
 */
export function waitForNextTick() {
  return new Promise((resolve) => {
    setTimeout(resolve, 0);
  });
}

/**
 * Wait for a specific amount of time
 */
export function waitFor(ms = 100) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Create mock user data
 */
export function createMockUser(overrides = {}) {
  return {
    id: 1,
    name: 'Test User',
    email: 'test@example.com',
    role: 'admin',
    role_name: 'admin',
    ...overrides,
  };
}

/**
 * Create mock payment data
 */
export function createMockPayment(overrides = {}) {
  return {
    id: 1,
    student_id: 1,
    course_id: 1,
    amount: 1000,
    remaining_amount: 500,
    payment_method: 'cash',
    payment_date: '2024-01-15',
    status: 'completed',
    ...overrides,
  };
}

