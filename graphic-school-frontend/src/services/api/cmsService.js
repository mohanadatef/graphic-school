/**
 * CHANGE-002: CMS Services
 */
import api from './client';

export const cmsService = {
  // Pages
  async getPages(params = {}) {
    const response = await api.get('/admin/pages', { params });
    return response.data;
  },

  async getPage(slug) {
    const response = await api.get(`/pages/${slug}`);
    return response.data;
  },

  /**
   * Get public page by slug (no auth required)
   */
  async getPublicPage(slug, locale = null) {
    const params = locale ? { locale } : {};
    const response = await api.get(`/public/pages/${slug}`, { params });
    return response.data;
  },

  async createPage(data) {
    const response = await api.post('/admin/pages', data);
    return response.data;
  },

  async updatePage(id, data) {
    const response = await api.put(`/admin/pages/${id}`, data);
    return response.data;
  },

  async deletePage(id) {
    const response = await api.delete(`/admin/pages/${id}`);
    return response.data;
  },

  // FAQ
  async getFAQs(params = {}) {
    const response = await api.get('/faqs', { params });
    return response.data;
  },

  async getAdminFAQs(params = {}) {
    const response = await api.get('/admin/faqs', { params });
    return response.data;
  },

  async createFAQ(data) {
    const response = await api.post('/admin/faqs', data);
    return response.data;
  },

  async updateFAQ(id, data) {
    const response = await api.put(`/admin/faqs/${id}`, data);
    return response.data;
  },

  async deleteFAQ(id) {
    const response = await api.delete(`/admin/faqs/${id}`);
    return response.data;
  },

  // Media
  async getMedia(params = {}) {
    const response = await api.get('/admin/media', { params });
    return response.data;
  },

  async uploadMedia(file, data = {}) {
    const formData = new FormData();
    formData.append('file', file);
    if (data.alt_text) formData.append('alt_text', data.alt_text);
    if (data.description) formData.append('description', data.description);

    const response = await api.post('/admin/media', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  async updateMedia(id, data) {
    const response = await api.put(`/admin/media/${id}`, data);
    return response.data;
  },

  async deleteMedia(id) {
    const response = await api.delete(`/admin/media/${id}`);
    return response.data;
  },
};

