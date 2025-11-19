import { defineStore } from 'pinia';
import { ref } from 'vue';
import { categoryService } from '../services/api';

export const useCategoryStore = defineStore('category', () => {
  // State
  const categories = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Actions
  async function fetchAll() {
    loading.value = true;
    error.value = null;
    try {
      const data = await categoryService.getAll();
      categories.value = Array.isArray(data) ? data : data.data || [];
      return categories.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch categories';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createCategory(payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await categoryService.create(payload);
      categories.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create category';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateCategory(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await categoryService.update(id, payload);
      const index = categories.value.findIndex((c) => c.id === id);
      if (index !== -1) {
        categories.value[index] = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update category';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteCategory(id) {
    loading.value = true;
    error.value = null;
    try {
      await categoryService.delete(id);
      categories.value = categories.value.filter((c) => c.id !== id);
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete category';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    // State
    categories,
    loading,
    error,
    // Actions
    fetchAll,
    createCategory,
    updateCategory,
    deleteCategory,
  };
});

