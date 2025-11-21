/**
 * CHANGE-005: Messaging Service
 */
import api from './client';

export const messagingService = {
  /**
   * Get all conversations
   */
  async getConversations(params = {}) {
    const response = await api.get('/messaging/conversations', { params });
    return response.data;
  },

  /**
   * Get or create conversation
   */
  async getOrCreateConversation(data) {
    const response = await api.post('/messaging/conversations', data);
    return response.data;
  },

  /**
   * Get messages for a conversation
   */
  async getMessages(conversationId, params = {}) {
    const response = await api.get(`/messaging/conversations/${conversationId}/messages`, { params });
    return response.data;
  },

  /**
   * Send a message
   */
  async sendMessage(data) {
    const response = await api.post('/messaging/messages', data);
    return response.data;
  },

  /**
   * Archive conversation
   */
  async archiveConversation(conversationId) {
    const response = await api.put(`/messaging/conversations/${conversationId}/archive`);
    return response.data;
  },
};

