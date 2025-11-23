<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.attendance.title') || 'Attendance Management' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.attendance.subtitle') || 'Manage attendance for your groups' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="sessions.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('instructor.attendance.noSessions') || 'No sessions found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="session in sessions" :key="session.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ session.title || session.name || 'Session' }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ formatDate(session.scheduled_at) }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ session.group?.code || session.group?.name }}</p>
          </div>
          <button
            @click="$router.push({ name: 'instructor-session-attendance', params: { id: session.id } })"
            class="btn-primary"
          >
            {{ $t('instructor.attendance.markAttendance') || 'Mark Attendance' }}
          </button>
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
const sessions = ref([]);

async function loadSessions() {
  loading.value = true;
  try {
    const response = await api.get('/instructor/sessions');
    sessions.value = response.data || [];
  } catch (error) {
    console.error('Error loading sessions:', error);
    toast.error('Failed to load sessions');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadSessions();
});
</script>
