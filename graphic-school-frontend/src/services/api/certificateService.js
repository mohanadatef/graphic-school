import api from './client';

/**
 * Certificate API Service
 * Handles all certificate-related API calls
 */
export const certificateService = {
  /**
   * Get all certificates (Admin)
   */
  async getAll(filters = {}) {
    const { data } = await api.get('/admin/certificates', { params: filters });
    return data;
  },

  /**
   * Get certificate by ID
   */
  async getById(id) {
    const { data } = await api.get(`/admin/certificates/${id}`);
    return data;
  },

  /**
   * Issue a new certificate (Admin)
   */
  async issue(certificateData) {
    const { data } = await api.post('/admin/certificates', certificateData);
    return data;
  },

  /**
   * Delete certificate (Admin)
   */
  async delete(id) {
    const { data } = await api.delete(`/admin/certificates/${id}`);
    return data;
  },

  /**
   * Get student certificates
   */
  async getStudentCertificates() {
    const { data } = await api.get('/student/certificates');
    return data;
  },

  /**
   * Get student certificate by ID
   */
  async getStudentCertificate(id) {
    const { data } = await api.get(`/student/certificates/${id}`);
    return data;
  },

  /**
   * Verify certificate by code (Public)
   */
  async verify(code) {
    const { data } = await api.get(`/certificates/verify/${code}`);
    return data;
  },
};

