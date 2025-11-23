<template>
  <div class="py-8">
    <h2 class="text-2xl font-bold text-center mb-8">{{ block.config.title || 'Frequently Asked Questions' }}</h2>
    <div class="space-y-4 max-w-2xl mx-auto">
      <div v-for="(item, index) in block.config.items || []" :key="index" class="bg-white dark:bg-slate-800 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">{{ item.question }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ item.answer }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});
</script>

