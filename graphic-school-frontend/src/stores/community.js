import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { communityService } from '../services/api';

export const useCommunityStore = defineStore('community', () => {
  // State
  const posts = ref([]);
  const currentPost = ref(null);
  const comments = ref([]);
  const reports = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
  });

  // Getters
  const pinnedPosts = computed(() => posts.value.filter(p => p.is_pinned));
  const regularPosts = computed(() => posts.value.filter(p => !p.is_pinned));

  // Actions - Posts
  async function fetchPosts(filters = {}) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.getPosts({
        page: pagination.value.current_page,
        per_page: pagination.value.per_page,
        ...filters,
      });
      posts.value = response.data || [];
      if (response.meta) {
        pagination.value = {
          current_page: response.meta.current_page || 1,
          last_page: response.meta.last_page || 1,
          per_page: response.meta.per_page || 15,
          total: response.meta.total || 0,
        };
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load posts';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchPost(id) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.getPost(id);
      currentPost.value = response.data;
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load post';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createPost(postData) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.createPost(postData);
      if (response.data) {
        posts.value.unshift(response.data);
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create post';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updatePost(id, postData) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.updatePost(id, postData);
      const index = posts.value.findIndex(p => p.id === id);
      if (index !== -1 && response.data) {
        posts.value[index] = response.data;
      }
      if (currentPost.value?.id === id) {
        currentPost.value = response.data;
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update post';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deletePost(id) {
    loading.value = true;
    error.value = null;
    try {
      await communityService.deletePost(id);
      posts.value = posts.value.filter(p => p.id !== id);
      if (currentPost.value?.id === id) {
        currentPost.value = null;
      }
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete post';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // Actions - Comments
  async function fetchComments(postId) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.getComments(postId);
      comments.value = response.data || [];
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load comments';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createComment(postId, commentData) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.createComment(postId, commentData);
      if (response.data) {
        comments.value.push(response.data);
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create comment';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // Actions - Replies
  async function fetchReplies(commentId) {
    try {
      const response = await communityService.getReplies(commentId);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load replies';
      throw err;
    }
  }

  async function createReply(commentId, replyData) {
    loading.value = true;
    error.value = null;
    try {
      const response = await communityService.createReply(commentId, replyData);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create reply';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // Actions - Likes
  async function toggleLike(type, id) {
    try {
      const response = await communityService.toggleLike(type, id);
      // Update post/comment in state
      if (type === 'posts') {
        const post = posts.value.find(p => p.id === id);
        if (post) {
          post.likes_count = response.data?.likes_count || post.likes_count || 0;
          post.is_liked = response.data?.is_liked;
        }
        if (currentPost.value?.id === id) {
          currentPost.value.likes_count = response.data?.likes_count || currentPost.value.likes_count || 0;
          currentPost.value.is_liked = response.data?.is_liked;
        }
      }
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to toggle like';
      throw err;
    }
  }

  // Actions - Reports (Admin only)
  async function fetchReports() {
    loading.value = true;
    error.value = null;
    try {
      // This endpoint needs to be implemented in backend
      // const response = await api.get('/admin/community/reports');
      // reports.value = response.data || [];
      // return response;
      return { data: [] };
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load reports';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // Utility
  function clearStore() {
    posts.value = [];
    currentPost.value = null;
    comments.value = [];
    reports.value = [];
    error.value = null;
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
    };
  }

  function setPage(page) {
    pagination.value.current_page = page;
  }

  function setPerPage(perPage) {
    pagination.value.per_page = perPage;
    pagination.value.current_page = 1;
  }

  return {
    // State
    posts,
    currentPost,
    comments,
    reports,
    loading,
    error,
    pagination,
    // Getters
    pinnedPosts,
    regularPosts,
    // Actions
    fetchPosts,
    fetchPost,
    createPost,
    updatePost,
    deletePost,
    fetchComments,
    createComment,
    fetchReplies,
    createReply,
    toggleLike,
    fetchReports,
    clearStore,
    setPage,
    setPerPage,
  };
});

