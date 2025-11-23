<template>
  <div class="py-8">
    <h2 v-if="block.config.title" class="text-2xl font-bold text-center mb-4">{{ block.config.title }}</h2>
    <div class="aspect-video bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center">
      <p class="text-slate-500">{{ block.config.url || 'Video URL will be embedded here' }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});
</script>

