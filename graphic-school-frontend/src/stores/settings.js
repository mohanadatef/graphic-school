import { defineStore } from 'pinia';
import { ref } from 'vue';
import { settingsService } from '../services/api';

export const useSettingsStore = defineStore('settings', () => {
  // State
  const settings = ref([]);
  const loading = ref(false);
  const error = ref(null);

  // Actions
  async function fetchAll() {
    loading.value = true;
    error.value = null;
    try {
      const data = await settingsService.getAll();
      settings.value = Array.isArray(data) ? data : data.data || [];
      return settings.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch settings';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateSetting(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await settingsService.update(id, payload);
      const index = settings.value.findIndex((s) => s.id === id);
      if (index !== -1) {
        settings.value[index] = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update setting';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateMultiple(settingsArray) {
    loading.value = true;
    error.value = null;
    try {
      const data = await settingsService.updateMultiple(settingsArray);
      await fetchAll(); // Refresh all settings
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update settings';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    // State
    settings,
    loading,
    error,
    // Actions
    fetchAll,
    updateSetting,
    updateMultiple,
  };
});

