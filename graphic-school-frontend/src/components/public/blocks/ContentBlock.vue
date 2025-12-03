<template>
  <section class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 v-if="block.title" class="text-3xl font-bold text-slate-900 dark:text-white mb-6">
        {{ block.title }}
      </h2>
      <div v-if="block.content" class="prose prose-lg dark:prose-invert max-w-none">
        <div v-html="formatContent(block.content)"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  block: {
    type: Object,
    required: true,
  },
});

function formatContent(content) {
  if (!content) return '';
  return content.replace(/\n/g, '<br>');
}
</script>

