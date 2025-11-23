<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.gamification.title') || 'Gamification' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gamification.subtitle') || 'Your points, levels, and achievements' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="summary" class="space-y-6">
      <!-- Points & Level Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('student.gamification.totalPoints') || 'Total Points' }}</p>
            <p class="text-4xl font-bold text-slate-900 dark:text-white">{{ summary.wallet?.total_points || 0 }}</p>
          </div>
          <div v-if="summary.wallet?.level" class="text-right">
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">{{ $t('student.gamification.level') || 'Level' }}</p>
            <div class="flex items-center gap-2">
              <span class="text-2xl">{{ summary.wallet.level.icon || '⭐' }}</span>
              <span class="text-2xl font-bold" :style="{ color: summary.wallet.level.color || '#3b82f6' }">
                {{ summary.wallet.level.name }}
              </span>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ $t('student.gamification.rank') || 'Rank' }}: #{{ summary.rank || 'N/A' }}
          </p>
        </div>
      </div>

      <!-- Badges Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.gamification.badges') || 'Badges' }}
        </h3>
        <div v-if="summary.badges && summary.badges.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="badge in summary.badges" :key="badge.id" class="text-center p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <div class="text-4xl mb-2">{{ badge.icon || '🏆' }}</div>
            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ badge.name }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ formatDate(badge.awarded_at) }}</p>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-slate-500 dark:text-slate-400">{{ $t('student.gamification.noBadges') || 'No badges earned yet' }}</p>
        </div>
      </div>

      <!-- Recent Events Card -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
          {{ $t('student.gamification.recentEvents') || 'Recent Events' }}
        </h3>
        <div v-if="summary.recent_events && summary.recent_events.length > 0" class="space-y-3">
          <div v-for="event in summary.recent_events" :key="event.id" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <div>
              <p class="text-sm font-medium text-slate-900 dark:text-white">{{ event.rule?.name || event.event_type }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(event.created_at) }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ event.points_awarded }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-slate-500 dark:text-slate-400">{{ $t('student.gamification.noEvents') || 'No events yet' }}</p>
        </div>
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
const summary = ref(null);

async function loadSummary() {
  loading.value = true;
  try {
    const response = await api.get('/student/gamification/summary');
    summary.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error loading gamification summary:', error);
    toast.error('Failed to load gamification summary');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadSummary();
});
</script>

