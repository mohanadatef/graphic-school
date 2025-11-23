<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('subscription.usage.title') || 'Usage Overview' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('subscription.usage.subtitle') || 'Track your resource usage' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="usage.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('subscription.usage.noData') || 'No usage data available' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="item in usage" :key="item.key" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white capitalize">{{ item.key.replace('_', ' ') }}</h3>
          <span v-if="item.is_exceeded" class="text-red-600 text-sm font-medium">Exceeded</span>
        </div>
        
        <div class="mb-2">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-slate-600 dark:text-slate-400">Used</span>
            <span class="font-semibold">{{ item.used }} / {{ item.limit }}</span>
          </div>
          <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
            <div 
              :class="getProgressBarClass(item.percentage)"
              class="h-2 rounded-full transition-all"
              :style="{ width: Math.min(100, item.percentage) + '%' }"
            ></div>
          </div>
        </div>
        
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ item.percentage.toFixed(1) }}% used</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const usage = ref([]);

async function loadUsage() {
  loading.value = true;
  try {
    const response = await api.get('/academy/subscription/usage');
    usage.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading usage:', error);
    toast.error('Failed to load usage data');
  } finally {
    loading.value = false;
  }
}

function getProgressBarClass(percentage) {
  if (percentage >= 100) return 'bg-red-500';
  if (percentage >= 80) return 'bg-yellow-500';
  return 'bg-green-500';
}

onMounted(() => {
  loadUsage();
});
</script>

