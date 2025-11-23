import { describe, it, expect, beforeEach, vi } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useAuthStore } from '../../src/stores/auth';
import { authService } from '../../src/services/api';

// Mock authService
vi.mock('../../src/services/api', () => ({
  authService: {
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
    getCurrentUser: vi.fn(),
  },
}));

// Mock i18n
vi.mock('../../src/i18n', () => ({
  default: {
    global: {
      t: (key) => key,
    },
    locale: 'ar',
    messages: {
      ar: {},
    },
  },
}));

describe('Auth Store', () => {
  beforeEach(() => {
    // Create a fresh Pinia instance for each test
    setActivePinia(createPinia());
    // Clear localStorage
    localStorage.clear();
  });

  describe('Initial State', () => {
    it('should initialize with null user and token', () => {
      const store = useAuthStore();
      expect(store.user).toBeNull();
      expect(store.token).toBeNull();
      expect(store.isAuthenticated).toBe(false);
    });

    it('should restore user and token from localStorage', () => {
      const mockUser = { id: 1, name: 'Test User', role: 'admin' };
      localStorage.setItem('gs_user', JSON.stringify(mockUser));
      localStorage.setItem('gs_token', 'test-token');

      const store = useAuthStore();
      expect(store.user).toEqual(mockUser);
      expect(store.token).toBe('test-token');
      expect(store.isAuthenticated).toBe(true);
    });
  });

  describe('Getters', () => {
    it('should return correct role name when role is a string', () => {
      const store = useAuthStore();
      store.setSession({ id: 1, role: 'admin' }, 'token');
      expect(store.roleName).toBe('admin');
      expect(store.isAdmin).toBe(true);
      expect(store.isInstructor).toBe(false);
      expect(store.isStudent).toBe(false);
    });

    it('should return correct role name when role is an object', () => {
      const store = useAuthStore();
      store.setSession({ id: 1, role: { name: 'student' } }, 'token');
      expect(store.roleName).toBe('student');
      expect(store.isStudent).toBe(true);
    });

    it('should return null role name when user is null', () => {
      const store = useAuthStore();
      expect(store.roleName).toBeNull();
    });
  });

  describe('Actions', () => {
    it('should set session correctly', () => {
      const store = useAuthStore();
      const user = { id: 1, name: 'Test User', role: 'admin' };
      const token = 'test-token';

      store.setSession(user, token);

      expect(store.user).toEqual(user);
      expect(store.token).toBe(token);
      expect(store.isAuthenticated).toBe(true);
      expect(localStorage.getItem('gs_user')).toBe(JSON.stringify(user));
      expect(localStorage.getItem('gs_token')).toBe(token);
    });

    it('should clear session correctly', () => {
      const store = useAuthStore();
      store.setSession({ id: 1, role: 'admin' }, 'token');
      store.clearSession();

      expect(store.user).toBeNull();
      expect(store.token).toBeNull();
      expect(store.isAuthenticated).toBe(false);
      expect(localStorage.getItem('gs_user')).toBeNull();
      expect(localStorage.getItem('gs_token')).toBeNull();
    });

    it('should login successfully', async () => {
      const store = useAuthStore();
      const mockUser = { id: 1, name: 'Test User', role: 'admin' };
      const mockToken = 'test-token';

      authService.login.mockResolvedValue({
        data: { user: mockUser, token: mockToken },
      });

      const result = await store.login({ email: 'test@example.com', password: 'password' });

      expect(authService.login).toHaveBeenCalledWith({
        email: 'test@example.com',
        password: 'password',
      });
      expect(store.user).toEqual(mockUser);
      expect(store.token).toBe(mockToken);
      expect(result).toEqual(mockUser);
      expect(store.loading).toBe(false);
    });

    it('should handle login error', async () => {
      const store = useAuthStore();
      const error = new Error('Invalid credentials');
      error.response = { data: { message: 'Invalid credentials' } };

      authService.login.mockRejectedValue(error);

      await expect(store.login({ email: 'test@example.com', password: 'wrong' })).rejects.toThrow();
      expect(store.error).toBe('Invalid credentials');
      expect(store.user).toBeNull();
      expect(store.loading).toBe(false);
    });

    it('should register successfully', async () => {
      const store = useAuthStore();
      const mockUser = { id: 2, name: 'New User', role: 'student' };
      const mockToken = 'test-token';

      authService.register.mockResolvedValue({
        data: { user: mockUser, token: mockToken },
      });

      const result = await store.register({
        name: 'New User',
        email: 'new@example.com',
        password: 'password',
      });

      expect(store.user).toEqual(mockUser);
      expect(store.token).toBe(mockToken);
      expect(result).toEqual(mockUser);
    });

    it('should logout successfully', async () => {
      const store = useAuthStore();
      store.setSession({ id: 1, role: 'admin' }, 'token');
      authService.logout.mockResolvedValue();

      await store.logout();

      expect(authService.logout).toHaveBeenCalled();
      expect(store.user).toBeNull();
      expect(store.token).toBeNull();
    });

    it('should clear session even if logout API fails', async () => {
      const store = useAuthStore();
      store.setSession({ id: 1, role: 'admin' }, 'token');
      const error = new Error('Network error');
      error.response = { status: 500 };
      authService.logout.mockRejectedValue(error);

      await store.logout();

      // Session should still be cleared
      expect(store.user).toBeNull();
      expect(store.token).toBeNull();
    });
  });
});

