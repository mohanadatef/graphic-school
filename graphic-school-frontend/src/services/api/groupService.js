import api from './client';

/**
 * Group API Service
 * Handles all group-related API calls
 */
export const groupService = {
  /**
   * Get all groups
   */
  async getAll(filters = {}) {
    const { data } = await api.get('/admin/groups', { params: filters });
    return data;
  },

  /**
   * Get group by ID
   */
  async getById(id) {
    const { data } = await api.get(`/admin/groups/${id}`);
    return data;
  },

  /**
   * Create a new group
   */
  async create(groupData) {
    const { data } = await api.post('/admin/groups', groupData);
    return data;
  },

  /**
   * Update group
   */
  async update(id, groupData) {
    const { data } = await api.put(`/admin/groups/${id}`, groupData);
    return data;
  },

  /**
   * Delete group
   */
  async delete(id) {
    const { data } = await api.delete(`/admin/groups/${id}`);
    return data;
  },

  /**
   * Get instructor groups
   */
  async getInstructorGroups() {
    const { data } = await api.get('/instructor/my-groups');
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
   * Get group students
   */
  async getGroupStudents(groupId) {
    const { data } = await api.get(`/instructor/groups/${groupId}/students`);
    return data;
  },
};

