<template>
  <div class="post-card">
    <div class="flex justify-between items-start mb-2">
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <span v-if="post.is_pinned" class="text-xs text-primary">📌</span>
          <h4 class="font-semibold text-slate-900">{{ post.title }}</h4>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <span>{{ post.user?.name }}</span>
          <span>•</span>
          <span>{{ formatDate(post.created_at) }}</span>
          <span v-if="post.group">•</span>
          <span v-if="post.group">{{ post.group.name }}</span>
        </div>
      </div>
      <div class="flex gap-2">
        <button
          @click="$emit('edit', post)"
          class="text-xs text-primary hover:underline"
        >
          Edit
        </button>
        <button
          @click="$emit('delete', post.id)"
          class="text-xs text-red-600 hover:underline"
        >
          Delete
        </button>
      </div>
    </div>
    <p class="text-sm text-slate-600 mb-3">{{ post.body }}</p>
    <div class="flex items-center gap-4 text-xs text-slate-500">
      <button @click="$emit('toggle-like', post.id)" class="flex items-center gap-1">
        ❤️ {{ post.likes_count || 0 }}
      </button>
      <button @click="$emit('view-comments', post.id)" class="flex items-center gap-1">
        💬 {{ post.comments_count || 0 }}
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  post: {
    type: Object,
    required: true,
  },
});

defineEmits(['edit', 'delete', 'toggle-like', 'view-comments']);

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>

