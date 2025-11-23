<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.community.posts.title') || 'Community Posts' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.community.posts.subtitle') || 'Manage all community posts' }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Title</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Author</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="post in posts" :key="post.id">
            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ post.title }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ post.user?.name }}</td>
            <td class="px-6 py-4 text-sm">
              <span v-if="post.is_pinned" class="text-primary">📌 Pinned</span>
              <span v-if="post.is_locked" class="text-red-600 ml-2">🔒 Locked</span>
            </td>
            <td class="px-6 py-4 text-sm space-x-2">
              <button @click="togglePin(post.id, !post.is_pinned)" class="text-primary">{{ post.is_pinned ? 'Unpin' : 'Pin' }}</button>
              <button @click="toggleLock(post.id, !post.is_locked)" class="text-yellow-600">{{ post.is_locked ? 'Unlock' : 'Lock' }}</button>
              <button @click="deletePost(post.id)" class="text-red-600">{{ $t('common.delete') || 'Delete' }}</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const posts = ref([]);

async function loadPosts() {
  loading.value = true;
  try {
    const response = await api.get('/admin/community/posts');
    posts.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading posts:', error);
    toast.error('Failed to load posts');
  } finally {
    loading.value = false;
  }
}

async function togglePin(id, pin) {
  try {
    await api.put(`/admin/community/posts/${id}/pin`, { pin });
    toast.success(pin ? 'Post pinned' : 'Post unpinned');
    loadPosts();
  } catch (error) {
    toast.error('Failed to update pin status');
  }
}

async function toggleLock(id, lock) {
  try {
    await api.put(`/admin/community/posts/${id}/lock`, { lock });
    toast.success(lock ? 'Post locked' : 'Post unlocked');
    loadPosts();
  } catch (error) {
    toast.error('Failed to update lock status');
  }
}

async function deletePost(id) {
  if (!confirm('Are you sure you want to delete this post?')) return;
  
  try {
    await api.delete(`/admin/community/posts/${id}`);
    toast.success('Post deleted successfully');
    loadPosts();
  } catch (error) {
    toast.error('Failed to delete post');
  }
}

onMounted(() => {
  loadPosts();
});
</script>

