import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../../utils/test-utils';
import InstructorAssignments from '../../../src/views/dashboard/instructor/InstructorAssignments.vue';

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

// Mock router
const mockRouterPush = vi.fn();
const mockRouter = {
  push: mockRouterPush,
};

describe('InstructorAssignments', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render assignments list from API', async () => {
    const mockAssignments = [
      {
        id: 1,
        title: 'Assignment 1',
        description: 'Test assignment',
        due_date: '2025-02-01',
        max_grade: 100,
      },
    ];

    api.get.mockResolvedValue({
      data: {
        data: mockAssignments,
      },
    });

    const wrapper = mountWithPlugins(InstructorAssignments, {
      global: {
        mocks: {
          $router: mockRouter,
        },
      },
    });

    await waitFor(100);

    expect(api.get).toHaveBeenCalledWith('/instructor/assignments');
    expect(wrapper.text()).toContain('Assignment 1');
  });

  it('should show create assignment button', () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(InstructorAssignments, {
      global: {
        mocks: {
          $router: mockRouter,
        },
      },
    });

    const createButton = wrapper.find('button');
    expect(createButton.exists()).toBe(true);
  });

  it('should navigate to create page on button click', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(InstructorAssignments, {
      global: {
        mocks: {
          $router: mockRouter,
        },
      },
    });

    const createButton = wrapper.find('button');
    await createButton.trigger('click');

    expect(mockRouterPush).toHaveBeenCalledWith({
      name: 'instructor-assignment-create',
    });
  });

  it('should display translated labels', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(InstructorAssignments, {
      global: {
        mocks: {
          $router: mockRouter,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toMatch(/assignments|الواجبات/i);
  });
});

