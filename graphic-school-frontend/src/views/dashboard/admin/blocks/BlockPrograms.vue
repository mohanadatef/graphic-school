<template>
  <div class="py-8">
    <h2 class="text-2xl font-bold text-center mb-8">{{ block.config.title || 'Our Programs' }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
        <h3 class="font-semibold mb-2">Program Name</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Program description will be loaded dynamically</p>
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

