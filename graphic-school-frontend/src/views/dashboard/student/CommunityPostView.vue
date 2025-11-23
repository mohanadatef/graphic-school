<template>
  <div class="space-y-6">
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="post" class="space-y-6">
      <!-- Post -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
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

        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ post.title }}</h1>
        <p class="text-slate-600 dark:text-slate-300 whitespace-pre-wrap mb-4">{{ post.body }}</p>

        <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
          <span v-for="tag in post.tags" :key="tag.id" class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs">
            #{{ tag.name }}
          </span>
        </div>

        <div class="flex items-center gap-6 text-sm">
          <button @click="toggleLike('post', post.id)" class="flex items-center gap-1 hover:text-primary">
            <span>{{ post.is_liked ? '❤️' : '🤍' }}</span>
            <span>{{ post.likes_count || 0 }}</span>
          </button>
          <span class="text-slate-500 dark:text-slate-400">💬 {{ post.comments_count || 0 }}</span>
          <button @click="reportContent('post', post.id)" class="hover:text-red-600">🚩</button>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('community.post.comments') || 'Comments' }}</h3>

        <!-- Add Comment -->
        <div class="mb-6">
          <textarea v-model="newComment" rows="3" :placeholder="$t('community.post.addComment') || 'Add a comment...'" class="w-full px-4 py-2 border rounded-lg mb-2"></textarea>
          <button @click="submitComment" class="btn-primary">{{ $t('community.post.submitComment') || 'Submit Comment' }}</button>
        </div>

        <!-- Comments List -->
        <div v-if="post.comments && post.comments.length > 0" class="space-y-4">
          <div v-for="comment in post.comments" :key="comment.id" class="border-b border-slate-200 dark:border-slate-700 pb-4 last:border-0">
            <div class="flex items-start gap-3 mb-2">
              <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm">
                {{ comment.user?.name?.charAt(0) || 'U' }}
              </div>
              <div class="flex-1">
                <p class="font-medium text-sm">{{ comment.user?.name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(comment.created_at) }}</p>
                <p class="mt-2 text-slate-700 dark:text-slate-300">{{ comment.body }}</p>
                <div class="flex items-center gap-4 mt-2 text-sm">
                  <button @click="toggleLike('comment', comment.id)" class="flex items-center gap-1 hover:text-primary">
                    <span>{{ comment.is_liked ? '❤️' : '🤍' }}</span>
                    <span>{{ comment.likes_count || 0 }}</span>
                  </button>
                  <button @click="showReplyInput(comment.id)" class="hover:text-primary">💬 {{ $t('community.post.reply') || 'Reply' }}</button>
                </div>

                <!-- Replies -->
                <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 mr-8 space-y-3">
                  <div v-for="reply in comment.replies" :key="reply.id" class="bg-slate-50 dark:bg-slate-900 p-3 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                      <span class="font-medium text-sm">{{ reply.user?.name }}</span>
                      <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(reply.created_at) }}</span>
                    </div>
                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ reply.body }}</p>
                  </div>
                </div>

                <!-- Reply Input -->
                <div v-if="replyingTo === comment.id" class="mt-3">
                  <textarea v-model="newReply" rows="2" :placeholder="$t('community.post.addReply') || 'Add a reply...'" class="w-full px-4 py-2 border rounded-lg mb-2"></textarea>
                  <div class="flex gap-2">
                    <button @click="submitReply(comment.id)" class="btn-primary text-sm">{{ $t('community.post.submitReply') || 'Submit' }}</button>
                    <button @click="replyingTo = null" class="btn-secondary text-sm">{{ $t('common.cancel') || 'Cancel' }}</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-slate-500 dark:text-slate-400">
          {{ $t('community.post.noComments') || 'No comments yet' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const toast = useToast();
const loading = ref(false);
const post = ref(null);
const newComment = ref('');
const newReply = ref('');
const replyingTo = ref(null);

async function loadPost() {
  loading.value = true;
  try {
    const response = await api.get(`/student/community/posts/${route.params.id}`);
    post.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error loading post:', error);
    toast.error('Failed to load post');
  } finally {
    loading.value = false;
  }
}

async function submitComment() {
  if (!newComment.value.trim()) return;
  
  try {
    await api.post('/student/community/comments', {
      post_id: route.params.id,
      body: newComment.value,
    });
    toast.success('Comment added successfully');
    newComment.value = '';
    loadPost();
  } catch (error) {
    toast.error('Failed to add comment');
  }
}

async function submitReply(commentId) {
  if (!newReply.value.trim()) return;
  
  try {
    await api.post('/student/community/replies', {
      comment_id: commentId,
      body: newReply.value,
    });
    toast.success('Reply added successfully');
    newReply.value = '';
    replyingTo.value = null;
    loadPost();
  } catch (error) {
    toast.error('Failed to add reply');
  }
}

function showReplyInput(commentId) {
  replyingTo.value = commentId;
}

async function toggleLike(type, id) {
  try {
    const response = await api.post('/student/community/like', { type, id });
    const result = response.data.data;
    if (type === 'post') {
      post.value.is_liked = result.liked;
      post.value.likes_count = result.likes_count;
    } else {
      // Update comment like status
      const comment = post.value.comments?.find(c => c.id === id);
      if (comment) {
        comment.is_liked = result.liked;
        comment.likes_count = result.likes_count;
      }
    }
  } catch (error) {
    toast.error('Failed to toggle like');
  }
}

async function reportContent(type, id) {
  const reason = prompt('Reason for reporting:');
  if (!reason) return;
  
  try {
    await api.post('/student/community/report', { type, id, reason });
    toast.success('Content reported successfully');
  } catch (error) {
    toast.error('Failed to report content');
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadPost();
});
</script>

