<template>
  <div class="py-8">
    <h2 class="text-2xl font-bold text-center mb-8">{{ block.config.title || 'Features' }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="(feature, index) in block.config.features || []" :key="index" class="text-center p-4">
        <i :class="feature.icon || 'fas fa-star'" class="text-4xl text-primary mb-4"></i>
        <h3 class="font-semibold mb-2">{{ feature.title }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ feature.description }}</p>
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

