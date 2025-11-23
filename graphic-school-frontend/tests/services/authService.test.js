import { describe, it, expect, beforeEach, vi } from 'vitest';
import { authService } from '../../src/services/api/authService';
import api from '../../src/api.js';

// Mock the API client
vi.mock('../../src/api.js', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
  },
}));

describe('AuthService', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('login', () => {
    it('should call API with correct credentials', async () => {
      const credentials = { email: 'test@example.com', password: 'password' };
      const mockResponse = {
        data: {
          success: true,
          data: {
            user: { id: 1, email: 'test@example.com' },
            token: 'mock-token',
          },
        },
      };

      api.post.mockResolvedValue(mockResponse);

      const result = await authService.login(credentials);

      expect(api.post).toHaveBeenCalledWith('/login', credentials);
      expect(result).toEqual(mockResponse.data);
    });

    it('should handle login errors', async () => {
      const credentials = { email: 'test@example.com', password: 'wrong' };
      const error = new Error('Invalid credentials');
      error.response = { data: { message: 'Invalid credentials' } };

      api.post.mockRejectedValue(error);

      await expect(authService.login(credentials)).rejects.toThrow('Invalid credentials');
    });
  });

  describe('register', () => {
    it('should call API with registration data', async () => {
      const payload = {
        name: 'New User',
        email: 'new@example.com',
        password: 'password',
      };
      const mockResponse = {
        data: {
          success: true,
          data: {
            user: { id: 2, ...payload },
            token: 'mock-token',
          },
        },
      };

      api.post.mockResolvedValue(mockResponse);

      const result = await authService.register(payload);

      expect(api.post).toHaveBeenCalledWith('/register', payload);
      expect(result).toEqual(mockResponse.data);
    });
  });

  describe('logout', () => {
    it('should call logout API', async () => {
      api.post.mockResolvedValue({ data: { success: true } });

      await authService.logout();

      expect(api.post).toHaveBeenCalledWith('/logout');
    });

    it('should not throw on 401 errors', async () => {
      const error = new Error('Unauthorized');
      error.response = { status: 401 };
      api.post.mockRejectedValue(error);

      // Should not throw
      await expect(authService.logout()).resolves.not.toThrow();
    });

    it('should throw on non-401 errors', async () => {
      const error = new Error('Server error');
      error.response = { status: 500 };
      api.post.mockRejectedValue(error);

      await expect(authService.logout()).rejects.toThrow('Server error');
    });
  });

  describe('getCurrentUser', () => {
    it('should fetch current user', async () => {
      const mockUser = { id: 1, name: 'Test User', email: 'test@example.com' };
      api.get.mockResolvedValue({ data: mockUser });

      const result = await authService.getCurrentUser();

      expect(api.get).toHaveBeenCalledWith('/user');
      expect(result).toEqual(mockUser);
    });
  });
});

