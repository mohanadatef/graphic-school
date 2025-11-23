<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.leaderboard.title') || 'Leaderboard' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.leaderboard.subtitle') || 'Top students by points' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">{{ $t('student.leaderboard.rank') || 'Rank' }}</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">{{ $t('student.leaderboard.name') || 'Name' }}</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">{{ $t('student.leaderboard.level') || 'Level' }}</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">{{ $t('student.leaderboard.points') || 'Points' }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="entry in leaderboard" :key="entry.user.id" class="hover:bg-slate-50 dark:hover:bg-slate-700">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">#{{ entry.rank }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ entry.user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span v-if="entry.level" class="inline-flex items-center gap-1" :style="{ color: entry.level.color }">
                <span>{{ entry.level.icon || '⭐' }}</span>
                <span>{{ entry.level.name }}</span>
              </span>
              <span v-else class="text-slate-500 dark:text-slate-400">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white">{{ entry.total_points }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const leaderboard = ref([]);

async function loadLeaderboard() {
  loading.value = true;
  try {
    const response = await api.get('/student/gamification/leaderboard');
    leaderboard.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading leaderboard:', error);
    toast.error('Failed to load leaderboard');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadLeaderboard();
});
</script>

