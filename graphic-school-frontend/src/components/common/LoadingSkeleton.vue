<template>
  <div class="animate-pulse space-y-4">
    <!-- Card Skeleton -->
    <div v-if="type === 'card'" class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-3/4 mb-4"></div>
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-1/2 mb-2"></div>
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-2/3"></div>
    </div>

    <!-- Table Row Skeleton -->
    <div v-else-if="type === 'table-row'" class="flex items-center gap-4 py-4 border-b border-slate-200 dark:border-slate-700">
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded flex-1"></div>
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-24"></div>
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-32"></div>
      <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-20"></div>
    </div>

    <!-- List Item Skeleton -->
    <div v-else-if="type === 'list-item'" class="flex items-center gap-3 p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
      <div class="w-12 h-12 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
      <div class="flex-1 space-y-2">
        <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Custom Skeleton -->
    <div v-else class="space-y-3">
      <div v-for="i in lines" :key="i" class="h-4 bg-slate-200 dark:bg-slate-700 rounded" :style="{ width: widths[i - 1] || '100%' }"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'card',
    validator: (value) => ['card', 'table-row', 'list-item', 'custom'].includes(value),
  },
  lines: {
    type: Number,
    default: 3,
  },
  widths: {
    type: Array,
    default: () => ['100%', '80%', '60%'],
  },
});
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

