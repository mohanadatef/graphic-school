import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../../utils/test-utils';
import StudentAssignments from '../../../src/views/dashboard/student/StudentAssignments.vue';

// Mock API client
vi.mock('../../../src/services/api/client', () => ({
  default: {
    get: vi.fn(),
  },
}));

// Mock useToast
vi.mock('../../../src/composables/useToast', () => ({
  useToast: vi.fn(() => ({
    success: vi.fn(),
    error: vi.fn(),
  })),
}));

import api from '../../../src/services/api/client';

describe('StudentAssignments', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render assignments list from API', async () => {
    const mockAssignments = [
      {
        id: 1,
        title: 'Assignment 1',
        description: 'Complete this assignment',
        due_date: '2025-02-01',
        max_grade: 100,
        is_overdue: false,
        submissions: [],
      },
      {
        id: 2,
        title: 'Assignment 2',
        description: 'Another assignment',
        due_date: '2025-02-15',
        max_grade: 100,
        is_overdue: false,
        submissions: [],
      },
    ];

    api.get.mockResolvedValue({
      data: {
        data: mockAssignments,
      },
    });

    const wrapper = mountWithPlugins(StudentAssignments);

    await waitFor(100);

    expect(api.get).toHaveBeenCalledWith('/student/assignments');
    expect(wrapper.text()).toContain('Assignment 1');
    expect(wrapper.text()).toContain('Assignment 2');
  });

  it('should display translated labels', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(StudentAssignments);

    await waitFor(100);

    // Check for i18n keys or translated text
    expect(wrapper.text()).toMatch(/assignments|الواجبات/i);
  });

  it('should show submit button for unsubmitted assignments', async () => {
    const mockAssignments = [
      {
        id: 1,
        title: 'Assignment 1',
        due_date: '2025-02-01',
        max_grade: 100,
        submissions: [],
      },
    ];

    api.get.mockResolvedValue({
      data: { data: mockAssignments },
    });

    const wrapper = mountWithPlugins(StudentAssignments);

    await waitFor(100);

    const submitButton = wrapper.find('button');
    expect(submitButton.exists()).toBe(true);
  });

  it('should handle API error gracefully', async () => {
    api.get.mockRejectedValue(new Error('API Error'));

    const wrapper = mountWithPlugins(StudentAssignments);

    await waitFor(100);

    // Should show error state or empty state
    expect(wrapper.text()).toMatch(/no.*assignments|loading/i);
  });
});

