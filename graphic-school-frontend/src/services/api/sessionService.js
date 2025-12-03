import api from './client';

/**
 * Session API Service
 * Handles all session-related API calls
 */
export const sessionService = {
  /**
   * Get all sessions (Admin)
   */
  async getAll(filters = {}) {
    const { data } = await api.get('/admin/sessions', { params: filters });
    return data;
  },

  /**
   * Get session by ID
   */
  async getById(id) {
    const { data } = await api.get(`/admin/sessions/${id}`);
    return data;
  },

  /**
   * Update session
   */
  async update(id, sessionData) {
    const { data } = await api.put(`/admin/sessions/${id}`, sessionData);
    return data;
  },

  /**
   * Delete session
   */
  async delete(id) {
    const { data } = await api.delete(`/admin/sessions/${id}`);
    return data;
  },

  /**
   * Get instructor sessions
   */
  async getInstructorSessions() {
    const { data } = await api.get('/instructor/sessions');
    return data;
  },

  /**
   * Get group sessions
   */
  async getGroupSessions(groupId) {
    const { data } = await api.get(`/instructor/groups/${groupId}/sessions`);
    return data;
  },

  /**
   * Get student sessions
   */
  async getStudentSessions() {
    const { data } = await api.get('/student/my-sessions');
    return data;
  },
};

