/**
 * CHANGE-007: Advanced Reports Service
 */
import api from './client';

export const reportService = {
  // Advanced Reports
  async getTopStudentsByGrades(params = {}) {
    const response = await api.get('/admin/reports/advanced/top-students/grades', { params });
    return response.data;
  },

  async getTopStudentsByAttendance(params = {}) {
    const response = await api.get('/admin/reports/advanced/top-students/attendance', { params });
    return response.data;
  },

  async getTopStudentsByEngagement(params = {}) {
    const response = await api.get('/admin/reports/advanced/top-students/engagement', { params });
    return response.data;
  },

  async getAverageGradesByCourse(params = {}) {
    const response = await api.get('/admin/reports/advanced/average-grades/course', { params });
    return response.data;
  },

  async getAverageGradesByBatch(params = {}) {
    const response = await api.get('/admin/reports/advanced/average-grades/batch', { params });
    return response.data;
  },

  async getAverageGradesByInstructor(params = {}) {
    const response = await api.get('/admin/reports/advanced/average-grades/instructor', { params });
    return response.data;
  },

  async getAttendanceRateByCourse(params = {}) {
    const response = await api.get('/admin/reports/advanced/attendance-rate/course', { params });
    return response.data;
  },

  async getAttendanceRateByStudent(params = {}) {
    const response = await api.get('/admin/reports/advanced/attendance-rate/student', { params });
    return response.data;
  },

  async getEngagementQuality(params = {}) {
    const response = await api.get('/admin/reports/advanced/engagement-quality', { params });
    return response.data;
  },

  // Instructor Reports
  async getInstructorPerformance(params = {}) {
    const response = await api.get('/instructor/reports/performance', { params });
    return response.data;
  },
};

