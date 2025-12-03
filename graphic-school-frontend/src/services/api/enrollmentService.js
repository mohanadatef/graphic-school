import api from './client';

/**
 * Enrollment API Service
 * Handles all enrollment-related API calls
 */
export const enrollmentService = {
  /**
   * Get all enrollments (Admin)
   */
  async getAll(filters = {}) {
    const { data } = await api.get('/admin/enrollments', { params: filters });
    return data;
  },

  /**
   * Get enrollment by ID
   */
  async getById(id) {
    const { data } = await api.get(`/admin/enrollments/${id}`);
    return data;
  },

  /**
   * Create enrollment (Admin)
   */
  async create(enrollmentData) {
    const { data } = await api.post('/admin/enrollments', enrollmentData);
    return data;
  },

  /**
   * Update enrollment
   */
  async update(id, enrollmentData) {
    const { data } = await api.put(`/admin/enrollments/${id}`, enrollmentData);
    return data;
  },

  /**
   * Approve enrollment and assign to group
   */
  async approve(id, groupId = null) {
    const { data } = await api.post(`/admin/enrollments/${id}/approve`, { group_id: groupId });
    return data;
  },

  /**
   * Reject enrollment
   */
  async reject(id, reason = '') {
    const { data } = await api.post(`/admin/enrollments/${id}/reject`, { reason });
    return data;
  },

  /**
   * Withdraw enrollment
   */
  async withdraw(id) {
    const { data } = await api.post(`/admin/enrollments/${id}/withdraw`);
    return data;
  },

  /**
   * Public enrollment (creates student + enrollment)
   */
  async publicEnroll(enrollmentData) {
    const { data } = await api.post('/enroll', enrollmentData);
    return data;
  },

  /**
   * Student enroll in course
   */
  async studentEnroll(courseId, groupId = null) {
    const { data } = await api.post('/student/enroll', {
      course_id: courseId,
      group_id: groupId,
    });
    return data;
  },

  /**
   * Get student enrollments
   */
  async getStudentEnrollments(filters = {}) {
    const { data } = await api.get('/student/enrollments', { params: filters });
    return data;
  },
};

