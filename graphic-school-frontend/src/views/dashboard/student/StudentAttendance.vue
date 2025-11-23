<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.attendance.title') || 'My Attendance' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.attendance.subtitle') || 'View your attendance records' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="attendance.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.attendance.noRecords') || 'No attendance records found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="record in attendance" :key="record.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ record.session?.title || record.session?.name || 'Session' }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ formatDate(record.session?.scheduled_at) }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ record.session?.group?.code || record.session?.group?.name }}</p>
          </div>
          <span
            :class="{
              'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': record.status === 'present',
              'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': record.status === 'absent',
              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': record.status === 'late',
              'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': record.status === 'excused',
            }"
            class="px-3 py-1 text-sm font-semibold rounded-full"
          >
            {{ $t(`student.attendance.${record.status}`) || record.status }}
          </span>
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
const attendance = ref([]);

async function loadAttendance() {
  loading.value = true;
  try {
    const response = await api.get('/student/attendance');
    attendance.value = response.data || [];
  } catch (error) {
    console.error('Error loading attendance:', error);
    toast.error('Failed to load attendance');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadAttendance();
});
</script>
