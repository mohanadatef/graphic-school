import api from './client';

/**
 * Attendance API Service
 * Handles all attendance-related API calls
 */
export const attendanceService = {
  /**
   * Get attendance overview (Admin)
   */
  async getOverview(filters = {}) {
    const { data } = await api.get('/admin/attendance', { params: filters });
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
   * Get session attendance
   */
  async getSessionAttendance(sessionId) {
    const { data } = await api.get(`/instructor/sessions/${sessionId}/attendance`);
    return data;
  },

  /**
   * Update session attendance
   */
  async updateAttendance(sessionId, attendanceData) {
    const { data } = await api.post(`/instructor/sessions/${sessionId}/attendance/update`, attendanceData);
    return data;
  },

  /**
   * Take attendance (mark for a session)
   */
  async takeAttendance(sessionId, attendanceRecords) {
    const { data } = await api.post(`/instructor/sessions/${sessionId}/attendance`, {
      attendance: attendanceRecords,
    });
    return data;
  },

  /**
   * Get student attendance history
   */
  async getStudentAttendanceHistory() {
    const { data } = await api.get('/student/attendance-history');
    return data;
  },

  /**
   * Get student attendance
   */
  async getStudentAttendance() {
    const { data } = await api.get('/student/attendance');
    return data;
  },
};

