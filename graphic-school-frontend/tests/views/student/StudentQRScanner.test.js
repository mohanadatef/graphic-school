import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mountWithPlugins, waitFor } from '../../utils/test-utils';
import StudentQRScanner from '../../../src/views/dashboard/student/StudentQRScanner.vue';

// Mock API client
vi.mock('../../../src/services/api/client', () => ({
  default: {
    post: vi.fn(),
  },
}));

import api from '../../../src/services/api/client';

// Mock useToast
const mockToastSuccess = vi.fn();
const mockToastError = vi.fn();
vi.mock('../../../src/composables/useToast', () => ({
  useToast: vi.fn(() => ({
    success: mockToastSuccess,
    error: mockToastError,
  })),
}));

describe('StudentQRScanner', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should render QR scanner interface', () => {
    const wrapper = mountWithPlugins(StudentQRScanner);

    expect(wrapper.text()).toMatch(/qr|check.*in|مسح/i);
  });

  it('should handle valid QR token check-in', async () => {
    const mockResponse = {
      data: {
        data: {
          id: 1,
          session_id: 1,
          student_id: 1,
          status: 'present',
        },
      },
    };

    api.post.mockResolvedValue(mockResponse);

    const wrapper = mountWithPlugins(StudentQRScanner);

    const tokenInput = wrapper.find('input[type="text"]');
    const checkInButton = wrapper.find('button');

    await tokenInput.setValue('a'.repeat(64)); // Valid 64-char token
    await checkInButton.trigger('click');
    await waitFor(100);

    expect(api.post).toHaveBeenCalledWith('/student/qr-checkin', {
      token: 'a'.repeat(64),
    });
    expect(mockToastSuccess).toHaveBeenCalled();
  });

  it('should show error for invalid token format', async () => {
    const wrapper = mountWithPlugins(StudentQRScanner);

    const tokenInput = wrapper.find('input[type="text"]');
    const checkInButton = wrapper.find('button');

    await tokenInput.setValue('short-token'); // Invalid token
    await checkInButton.trigger('click');
    await waitFor(100);

    expect(api.post).not.toHaveBeenCalled();
    expect(wrapper.text()).toMatch(/invalid|خطأ/i);
  });

  it('should show success message after check-in', async () => {
    api.post.mockResolvedValue({
      data: {
        data: { status: 'present' },
      },
    });

    const wrapper = mountWithPlugins(StudentQRScanner);

    const tokenInput = wrapper.find('input[type="text"]');
    const checkInButton = wrapper.find('button');

    await tokenInput.setValue('a'.repeat(64));
    await checkInButton.trigger('click');
    await waitFor(200);

    expect(wrapper.text()).toMatch(/success|confirmed|نجح/i);
  });

  it('should handle API error gracefully', async () => {
    api.post.mockRejectedValue({
      response: {
        data: {
          message: 'Token expired',
        },
      },
    });

    const wrapper = mountWithPlugins(StudentQRScanner);

    const tokenInput = wrapper.find('input[type="text"]');
    const checkInButton = wrapper.find('button');

    await tokenInput.setValue('a'.repeat(64));
    await checkInButton.trigger('click');
    await waitFor(300);

    expect(mockToastError).toHaveBeenCalled();
    // Error message should be displayed in the component
    const errorDiv = wrapper.find('.bg-red-50, .bg-red-900\\/20');
    expect(errorDiv.exists()).toBe(true);
    expect(errorDiv.text()).toContain('Token expired');
  });
});

