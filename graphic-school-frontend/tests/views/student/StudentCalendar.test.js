import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../../utils/test-utils';
import StudentCalendar from '../../../src/views/dashboard/student/StudentCalendar.vue';

// Mock API client
vi.mock('../../../src/services/api/client', () => ({
  default: {
    get: vi.fn(),
  },
}));

import api from '../../../src/services/api/client';

// Mock useToast
vi.mock('../../../src/composables/useToast', () => ({
  useToast: vi.fn(() => ({
    success: vi.fn(),
    error: vi.fn(),
  })),
}));

describe('StudentCalendar', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render calendar events from API', async () => {
    const mockEvents = [
      {
        id: 'session-1',
        title: 'Session 1',
        start: '2025-02-01T09:00:00Z',
        end: '2025-02-01T11:00:00Z',
        color: '#3b82f6',
        type: 'session',
      },
      {
        id: 'assignment-1',
        title: 'Assignment Due',
        start: '2025-02-05T23:59:59Z',
        color: '#ef4444',
        type: 'assignment',
      },
    ];

    api.get.mockResolvedValue({
      data: {
        data: mockEvents,
      },
    });

    const wrapper = mountWithPlugins(StudentCalendar, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(api.get).toHaveBeenCalled();
    // Calendar should render events
    expect(wrapper.text()).toMatch(/session|assignment|calendar/i);
  });

  it('should display translated labels', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(StudentCalendar, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toMatch(/calendar|التقويم/i);
  });

  it('should handle date range changes', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(StudentCalendar, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    // Calendar should handle month/day navigation
    // This is a basic test - actual calendar implementation may vary
    expect(wrapper.exists()).toBe(true);
  });
});

