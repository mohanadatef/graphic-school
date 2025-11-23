<template>
  <div class="py-8">
    <h2 v-if="block.config.title" class="text-2xl font-bold text-center mb-8">{{ block.config.title }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="aspect-square bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center">
        <p class="text-slate-500 text-sm">Image {{ i }}</p>
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

