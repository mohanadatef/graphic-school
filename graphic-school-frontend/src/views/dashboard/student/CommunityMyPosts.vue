<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('community.myPosts.title') || 'My Posts' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('community.myPosts.subtitle') || 'Posts you created' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('community.myPosts.noPosts') || 'You haven\'t created any posts yet' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="post in posts" :key="post.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ post.title }}</h3>
          <div v-if="post.is_pinned" class="text-primary">📌</div>
        </div>
        <p class="text-slate-600 dark:text-slate-300 mb-4 line-clamp-3">{{ post.body }}</p>
        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
          <span>{{ formatDate(post.created_at) }}</span>
          <span>💬 {{ post.comments_count || 0 }}</span>
          <span>❤️ {{ post.likes_count || 0 }}</span>
          <router-link :to="{ name: 'community-post-view', params: { id: post.id } }" class="text-primary hover:underline">
            {{ $t('community.myPosts.view') || 'View' }}
          </router-link>
        </div>
      </div>
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
    const response = await api.get('/student/community/posts/my-posts');
    posts.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading posts:', error);
    toast.error('Failed to load posts');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadPosts();
});
</script>

