import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../utils/test-utils';
import AdminPayments from '../../src/views/dashboard/admin/AdminPayments.vue';

// Mock useListPage composable
vi.mock('../../src/composables/useListPage', () => ({
  useListPage: vi.fn(() => ({
    items: {
      value: [
        {
          id: 1,
          student: { name: 'Test Student' },
          course: { title: 'Test Course' },
          amount: 1000,
          remaining_amount: 500,
          payment_date: '2024-01-15',
          payment_method: 'cash',
          status: 'completed',
        },
      ],
    },
    loading: { value: false },
    error: { value: null },
    filters: {
      value: {
        search: '',
        status: '',
        from_date: '',
        to_date: '',
      },
    },
    pagination: {
      value: {
        current_page: 1,
        per_page: 10,
        total: 1,
        last_page: 1,
      },
    },
    changePage: vi.fn(),
    changePerPage: vi.fn(),
    loadItems: vi.fn(),
    loadItemsDebounced: vi.fn(),
    applyFilters: vi.fn(),
  })),
}));

// Mock useApi
vi.mock('../../src/composables/useApi', () => ({
  useApi: vi.fn(() => ({
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
  })),
}));

// Mock useToast
vi.mock('../../src/composables/useToast', () => ({
  useToast: vi.fn(() => ({
    success: vi.fn(),
    error: vi.fn(),
  })),
}));

describe('AdminPayments', () => {
  it('should render payment list', () => {
    const wrapper = mountWithPlugins(AdminPayments, {
      global: {
        stubs: {
          RouterLink: true,
          PaginationControls: true,
          FilterDropdown: true,
        },
      },
    });

    expect(wrapper.text()).toContain('إدارة المدفوعات');
    expect(wrapper.text()).toContain('Test Student');
    expect(wrapper.text()).toContain('Test Course');
  });

  it('should display payment amount and status', () => {
    const wrapper = mountWithPlugins(AdminPayments, {
      global: {
        stubs: {
          RouterLink: true,
          PaginationControls: true,
          FilterDropdown: true,
        },
      },
    });

    expect(wrapper.text()).toContain('1000');
    expect(wrapper.text()).toContain('500');
    expect(wrapper.text()).toContain('مكتمل');
  });

  it('should show create payment button', () => {
    const wrapper = mountWithPlugins(AdminPayments, {
      global: {
        stubs: {
          RouterLink: true,
          PaginationControls: true,
          FilterDropdown: true,
        },
      },
    });

    const createButton = wrapper.find('button');
    expect(createButton.exists()).toBe(true);
  });
});

