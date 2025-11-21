/**
 * CHANGE-003: Notifications Store
 */
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { notificationService } from '../services/api/notificationService';

export const useNotificationStore = defineStore('notifications', () => {
  const notifications = ref([]);
  const unreadCount = ref(0);
  const loading = ref(false);
  const error = ref(null);

  /**
   * Fetch notifications
   */
  async function fetchNotifications(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await notificationService.getNotifications(params);
      notifications.value = response.data?.data || [];
      return response;
    } catch (err) {
      error.value = err.message;
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Fetch unread count
   */
  async function fetchUnreadCount() {
    try {
      const response = await notificationService.getUnreadCount();
      unreadCount.value = response.data?.count || 0;
      return response;
    } catch (err) {
      console.error('Failed to fetch unread count:', err);
    }
  }

  /**
   * Mark notification as read
   */
  async function markAsRead(id) {
    try {
      await notificationService.markAsRead(id);
      const notification = notifications.value.find(n => n.id === id);
      if (notification) {
        notification.read_at = new Date().toISOString();
      }
      if (unreadCount.value > 0) {
        unreadCount.value--;
      }
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  }

  /**
   * Mark all as read
   */
  async function markAllAsRead() {
    try {
      await notificationService.markAllAsRead();
      notifications.value.forEach(n => {
        n.read_at = new Date().toISOString();
      });
      unreadCount.value = 0;
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  }

  /**
   * Delete notification
   */
  async function deleteNotification(id) {
    try {
      await notificationService.delete(id);
      notifications.value = notifications.value.filter(n => n.id !== id);
      // Re-fetch unread count
      await fetchUnreadCount();
    } catch (err) {
      error.value = err.message;
      throw err;
    }
  }

  /**
   * Get unread notifications
   */
  const unreadNotifications = computed(() => {
    return notifications.value.filter(n => !n.read_at);
  });

  /**
   * Get read notifications
   */
  const readNotifications = computed(() => {
    return notifications.value.filter(n => n.read_at);
  });

  return {
    notifications,
    unreadCount,
    loading,
    error,
    unreadNotifications,
    readNotifications,
    fetchNotifications,
    fetchUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification,
  };
});

