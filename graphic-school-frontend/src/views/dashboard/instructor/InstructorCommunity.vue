<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('community.feed.title') || 'Community Feed' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('community.feed.subtitle') || 'Share, discuss, and learn together' }}</p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary">{{ $t('community.feed.createPost') || 'Create Post' }}</button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-4">
        <select v-model="filters.sort" @change="loadPosts" class="px-4 py-2 border rounded-lg">
          <option value="latest">{{ $t('community.feed.sort.latest') || 'Latest' }}</option>
          <option value="trending">{{ $t('community.feed.sort.trending') || 'Trending' }}</option>
          <option value="most_liked">{{ $t('community.feed.sort.mostLiked') || 'Most Liked' }}</option>
        </select>
        <input v-model="filters.tag" @input="loadPosts" type="text" :placeholder="$t('community.feed.filterByTag') || 'Filter by tag'" class="px-4 py-2 border rounded-lg flex-1 max-w-xs">
      </div>
    </div>

    <!-- Posts List -->
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('community.feed.noPosts') || 'No posts found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="post in posts" :key="post.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-semibold">
              {{ post.user?.name?.charAt(0) || 'U' }}
            </div>
            <div>
              <p class="font-medium text-slate-900 dark:text-white">{{ post.user?.name }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(post.created_at) }}</p>
            </div>
          </div>
          <div v-if="post.is_pinned" class="text-primary">📌</div>
        </div>

        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ post.title }}</h3>
        <p class="text-slate-600 dark:text-slate-300 mb-4 whitespace-pre-wrap">{{ post.body }}</p>

        <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
          <span v-for="tag in post.tags" :key="tag.id" class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs text-slate-600 dark:text-slate-300">
            #{{ tag.name }}
          </span>
        </div>

        <div class="flex items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
          <button @click="toggleLike('post', post.id)" class="flex items-center gap-1 hover:text-primary">
            <span>{{ post.is_liked ? '❤️' : '🤍' }}</span>
            <span>{{ post.likes_count || 0 }}</span>
          </button>
          <button @click="viewPost(post.id)" class="flex items-center gap-1 hover:text-primary">
            💬 {{ post.comments_count || 0 }}
          </button>
        </div>
      </div>
    </div>

    <!-- Create Post Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showCreateModal = false">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">{{ $t('community.createPost.title') || 'Create Post' }}</h3>
        <CommunityCreatePost @created="handlePostCreated" @cancel="showCreateModal = false" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../api';
import { useToast } from '../../../composables/useToast';
import CommunityCreatePost from '../student/CommunityCreatePost.vue';

const router = useRouter();
const toast = useToast();
const loading = ref(false);
const posts = ref([]);
const showCreateModal = ref(false);
const filters = ref({
  sort: 'latest',
  tag: '',
});

async function loadPosts() {
  loading.value = true;
  try {
    const params = { sort: filters.value.sort };
    if (filters.value.tag) params.tag = filters.value.tag;
    const response = await api.get('/instructor/community/posts', { params });
    posts.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading posts:', error);
    toast.error('Failed to load posts');
  } finally {
    loading.value = false;
  }
}

async function toggleLike(type, id) {
  try {
    const response = await api.post('/instructor/community/like', { type, id });
    const result = response.data.data;
    const post = posts.value.find(p => p.id === id);
    if (post) {
      post.is_liked = result.liked;
      post.likes_count = result.likes_count;
    }
  } catch (error) {
    toast.error('Failed to toggle like');
  }
}

function viewPost(id) {
  // Navigate to post view if route exists, otherwise show in modal
  router.push({ name: 'community-post-view', params: { id } }).catch(() => {
    // Route might not exist for instructor, just log
    console.log('Post view route not available for instructor');
  });
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

function handlePostCreated() {
  showCreateModal.value = false;
  loadPosts();
}

onMounted(() => {
  loadPosts();
});
</script>

