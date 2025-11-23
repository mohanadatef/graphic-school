import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useApi } from '../../src/composables/useApi';
import api from '../../src/api.js';

// Mock the API client
vi.mock('../../src/api.js', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}));

describe('useApi Composable', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('should initialize with loading false and no error', () => {
    const { loading, error } = useApi();
    expect(loading.value).toBe(false);
    expect(error.value).toBeNull();
  });

  describe('get', () => {
    it('should make GET request and return data', async () => {
      const { get, loading } = useApi();
      const mockData = { id: 1, name: 'Test' };
      api.get.mockResolvedValue({ data: mockData });

      const promise = get('/test');
      expect(loading.value).toBe(true);

      const result = await promise;

      expect(api.get).toHaveBeenCalledWith('/test', {});
      expect(result).toEqual(mockData);
      expect(loading.value).toBe(false);
      expect(error.value).toBeNull();
    });

    it('should handle errors correctly', async () => {
      const { get, loading, error } = useApi();
      const mockError = new Error('Network error');
      mockError.response = { data: { message: 'Network error' } };
      api.get.mockRejectedValue(mockError);

      await expect(get('/test')).rejects.toThrow();

      expect(loading.value).toBe(false);
      expect(error.value).toBe('Network error');
    });
  });

  describe('post', () => {
    it('should make POST request with data', async () => {
      const { post } = useApi();
      const mockData = { id: 1, name: 'Created' };
      api.post.mockResolvedValue({ data: mockData });

      const result = await post('/test', { name: 'Test' });

      expect(api.post).toHaveBeenCalledWith('/test', { name: 'Test' }, {});
      expect(result).toEqual(mockData);
    });
  });

  describe('put', () => {
    it('should make PUT request with data', async () => {
      const { put } = useApi();
      const mockData = { id: 1, name: 'Updated' };
      api.put.mockResolvedValue({ data: mockData });

      const result = await put('/test/1', { name: 'Updated' });

      expect(api.put).toHaveBeenCalledWith('/test/1', { name: 'Updated' }, {});
      expect(result).toEqual(mockData);
    });
  });

  describe('delete', () => {
    it('should make DELETE request', async () => {
      const { delete: del } = useApi();
      api.delete.mockResolvedValue({ data: { success: true } });

      await del('/test/1');

      expect(api.delete).toHaveBeenCalledWith('/test/1', {});
    });
  });

  describe('clearError', () => {
    it('should clear error', () => {
      const { error, clearError } = useApi();
      error.value = 'Some error';
      clearError();
      expect(error.value).toBeNull();
    });
  });
});

