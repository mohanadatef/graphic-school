<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.leaderboard.title') || 'Group Leaderboard' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.leaderboard.subtitle') || 'Student rankings by points' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Rank</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Name</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Level</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Points</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="entry in leaderboard" :key="entry.user.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ entry.rank }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ entry.user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span v-if="entry.level" :style="{ color: entry.level.color }">{{ entry.level.name }}</span>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ entry.total_points }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const toast = useToast();
const loading = ref(false);
const leaderboard = ref([]);

async function loadLeaderboard() {
  loading.value = true;
  try {
    const groupId = route.query.group_id || route.params.groupId;
    const response = await api.get('/instructor/gamification/group-leaderboard', {
        params: { group_id: groupId }
      });
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

