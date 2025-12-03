import api from './client';

/**
 * Community API Service
 * Handles all community-related API calls (Posts, Comments, Replies)
 */
export const communityService = {
  /**
   * Get all posts
   */
  async getPosts(filters = {}) {
    const { data } = await api.get('/community/posts', { params: filters });
    return data;
  },

  /**
   * Get post by ID
   */
  async getPost(id) {
    const { data } = await api.get(`/community/posts/${id}`);
    return data;
  },

  /**
   * Create a new post
   */
  async createPost(postData) {
    const { data } = await api.post('/community/posts', postData);
    return data;
  },

  /**
   * Update post
   */
  async updatePost(id, postData) {
    const { data } = await api.put(`/community/posts/${id}`, postData);
    return data;
  },

  /**
   * Delete post
   */
  async deletePost(id) {
    const { data } = await api.delete(`/community/posts/${id}`);
    return data;
  },

  /**
   * Get post comments
   */
  async getComments(postId) {
    const { data } = await api.get(`/community/posts/${postId}/comments`);
    return data;
  },

  /**
   * Create comment
   */
  async createComment(postId, commentData) {
    const { data } = await api.post(`/community/posts/${postId}/comments`, commentData);
    return data;
  },

  /**
   * Get comment replies
   */
  async getReplies(commentId) {
    const { data } = await api.get(`/community/comments/${commentId}/replies`);
    return data;
  },

  /**
   * Create reply
   */
  async createReply(commentId, replyData) {
    const { data } = await api.post(`/community/comments/${commentId}/replies`, replyData);
    return data;
  },

  /**
   * Like/unlike post or comment
   */
  async toggleLike(type, id) {
    const { data } = await api.post(`/community/${type}/${id}/like`);
    return data;
  },
};

