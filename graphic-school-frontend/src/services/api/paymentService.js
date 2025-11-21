/**
 * CHANGE-004: Payment Timeline Service
 */
import api from './client';

export const paymentService = {
  /**
   * Get student payments
   */
  async getStudentPayments(params = {}) {
    const response = await api.get('/student/payments', { params });
    return response.data;
  },

  /**
   * Get all payments (Admin)
   */
  async getAllPayments(params = {}) {
    const response = await api.get('/admin/payments', { params });
    return response.data;
  },

  /**
   * Create payment (Admin)
   */
  async createPayment(data) {
    const response = await api.post('/admin/payments', data);
    return response.data;
  },

  /**
   * Update payment (Admin)
   */
  async updatePayment(id, data) {
    const response = await api.put(`/admin/payments/${id}`, data);
    return response.data;
  },

  /**
   * Get payment reports (Admin)
   */
  async getReports(params = {}) {
    const response = await api.get('/admin/payments/reports', { params });
    return response.data;
  },
};

