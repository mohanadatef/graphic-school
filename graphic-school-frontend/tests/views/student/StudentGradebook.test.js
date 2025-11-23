import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../../utils/test-utils';
import StudentGradebook from '../../../src/views/dashboard/student/StudentGradebook.vue';

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

describe('StudentGradebook', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render gradebook data from API', async () => {
    const mockGradebook = [
      {
        id: 1,
        student_id: 1,
        program_id: 1,
        batch_id: 1,
        assignment_grade: 85.5,
        attendance_percentage: 90.0,
        participation_grade: 80.0,
        overall_grade: 85.0,
        program: {
          id: 1,
          title: 'Graphic Design Bootcamp',
        },
      },
    ];

    api.get.mockResolvedValue({
      data: {
        data: mockGradebook,
      },
    });

    const wrapper = mountWithPlugins(StudentGradebook, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(api.get).toHaveBeenCalledWith('/student/gradebook');
    expect(wrapper.text()).toContain('85');
    expect(wrapper.text()).toContain('90');
  });

  it('should display attendance percentage', async () => {
    const mockGradebook = [
      {
        id: 1,
        attendance_percentage: 95.5,
        overall_grade: 90.0,
      },
    ];

    api.get.mockResolvedValue({
      data: { data: mockGradebook },
    });

    const wrapper = mountWithPlugins(StudentGradebook, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toContain('95');
  });

  it('should display assignment grades', async () => {
    const mockGradebook = [
      {
        id: 1,
        assignment_grade: 88.5,
        overall_grade: 87.0,
      },
    ];

    api.get.mockResolvedValue({
      data: { data: mockGradebook },
    });

    const wrapper = mountWithPlugins(StudentGradebook, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toContain('88');
  });

  it('should display overall grade', async () => {
    const mockGradebook = [
      {
        id: 1,
        overall_grade: 92.5,
      },
    ];

    api.get.mockResolvedValue({
      data: { data: mockGradebook },
    });

    const wrapper = mountWithPlugins(StudentGradebook, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toContain('92');
  });

  it('should display translated labels', async () => {
    api.get.mockResolvedValue({
      data: { data: [] },
    });

    const wrapper = mountWithPlugins(StudentGradebook, {
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    });

    await waitFor(100);

    expect(wrapper.text()).toMatch(/gradebook|سجل/i);
  });
});

