/**
 * CHANGE-003: In-App Notifications Service
 */
import api from './client';

export const notificationService = {
  /**
   * Get all notifications
   */
  async getNotifications(params = {}) {
    const response = await api.get('/notifications', { params });
    return response.data;
  },

  /**
   * Get unread count
   */
  async getUnreadCount() {
    const response = await api.get('/notifications/unread-count');
    return response.data;
  },

  /**
   * Mark notification as read
   */
  async markAsRead(id) {
    const response = await api.put(`/notifications/${id}/read`);
    return response.data;
  },

  /**
   * Mark all notifications as read
   */
  async markAllAsRead() {
    const response = await api.put('/notifications/read-all');
    return response.data;
  },

  /**
   * Delete notification
   */
  async delete(id) {
    const response = await api.delete(`/notifications/${id}`);
    return response.data;
  },
};

