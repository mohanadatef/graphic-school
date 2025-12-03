<template>
  <div class="mt-4 border-t pt-4">
    <div class="mb-4">
      <h4 class="text-sm font-semibold mb-2">Comments</h4>
      <div v-if="loadingComments" class="text-sm text-slate-500">Loading comments...</div>
      <div v-else-if="comments.length === 0" class="text-sm text-slate-500">No comments yet</div>
      <div v-else class="space-y-3">
        <div v-for="comment in comments" :key="comment.id" class="bg-slate-50 rounded p-3">
          <div class="flex justify-between items-start mb-2">
            <div>
              <span class="text-xs font-medium">{{ comment.user?.name }}</span>
              <span class="text-xs text-slate-500 ml-2">{{ formatDate(comment.created_at) }}</span>
            </div>
            <button
              v-if="canEdit(comment)"
              @click="$emit('delete-comment', comment.id)"
              class="text-xs text-red-600"
            >
              Delete
            </button>
          </div>
          <p class="text-sm text-slate-700">{{ comment.body }}</p>
          <div class="mt-2 flex items-center gap-4 text-xs">
            <button @click="toggleReplyForm(comment.id)" class="text-primary">
              Reply ({{ comment.replies_count || 0 }})
            </button>
            <button @click="handleToggleLike('comments', comment.id)" class="text-primary">
              ❤️ {{ comment.likes_count || 0 }}
            </button>
          </div>
          <!-- Replies -->
          <div v-if="comment.replies && comment.replies.length > 0" class="mt-2 ml-4 space-y-2">
            <div v-for="reply in comment.replies" :key="reply.id" class="bg-white rounded p-2 text-xs">
              <div class="flex justify-between">
                <span class="font-medium">{{ reply.user?.name }}</span>
                <span class="text-slate-500">{{ formatDate(reply.created_at) }}</span>
              </div>
              <p class="mt-1">{{ reply.body }}</p>
            </div>
          </div>
          <!-- Reply Form -->
          <div v-if="replyingTo === comment.id" class="mt-2">
            <textarea
              v-model="replyText"
              class="w-full text-xs p-2 border rounded"
              placeholder="Write a reply..."
              rows="2"
            />
            <div class="flex gap-2 mt-1">
              <button
                @click="handleSubmitReply(comment.id)"
                class="text-xs px-3 py-1 bg-primary text-white rounded"
              >
                Reply
              </button>
              <button
                @click="cancelReply"
                class="text-xs px-3 py-1 border rounded"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Add Comment Form -->
    <div class="border-t pt-4">
      <textarea
        v-model="newComment"
        class="w-full text-sm p-2 border rounded"
        placeholder="Write a comment..."
        rows="3"
      />
      <button
        @click="handleSubmitComment"
        class="mt-2 px-4 py-2 bg-primary text-white rounded text-sm"
      >
        Post Comment
      </button>
    </div>
    <button
      @click="$emit('close')"
      class="mt-4 text-sm text-slate-500 hover:underline"
    >
      Close
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCommunityStore } from '@/stores/community';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
  postId: {
    type: Number,
    required: true,
  },
  comments: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['close', 'delete-comment']);

const store = useCommunityStore();
const authStore = useAuthStore();
const loadingComments = ref(false);
const newComment = ref('');
const replyText = ref('');
const replyingTo = ref(null);

onMounted(async () => {
  if (props.comments.length === 0) {
    await loadComments();
  }
});

async function loadComments() {
  loadingComments.value = true;
  try {
    await store.fetchComments(props.postId);
  } catch (error) {
    console.error('Error loading comments:', error);
  } finally {
    loadingComments.value = false;
  }
}

async function handleSubmitComment() {
  if (!newComment.value.trim()) return;
  try {
    await store.createComment(props.postId, { body: newComment.value });
    newComment.value = '';
    await loadComments();
  } catch (error) {
    alert('Failed to post comment');
  }
}

async function handleSubmitReply(commentId) {
  if (!replyText.value.trim()) return;
  try {
    await store.createReply(commentId, { body: replyText.value });
    replyText.value = '';
    replyingTo.value = null;
    await loadComments();
  } catch (error) {
    alert('Failed to post reply');
  }
}

function toggleReplyForm(commentId) {
  replyingTo.value = replyingTo.value === commentId ? null : commentId;
}

function cancelReply() {
  replyText.value = '';
  replyingTo.value = null;
}

async function handleToggleLike(type, id) {
  try {
    await store.toggleLike(type, id);
    await loadComments();
  } catch (error) {
    console.error('Error toggling like:', error);
  }
}

function canEdit(comment) {
  return comment.user_id === authStore.user?.id || authStore.roleName === 'admin';
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>

