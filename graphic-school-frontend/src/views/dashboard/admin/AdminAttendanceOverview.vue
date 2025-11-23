<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.attendance.title') || 'Attendance Overview' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.attendance.subtitle') || 'View attendance summaries' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else class="space-y-6">
      <div v-for="batch in batches" :key="batch.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ batch.program?.title }} - {{ batch.code }}</h3>
        <div v-for="group in batch.groups" :key="group.id" class="mb-4 last:mb-0">
          <h4 class="font-medium text-slate-900 dark:text-white mb-2">{{ group.code || group.name }}</h4>
          <div class="grid grid-cols-4 gap-4 text-sm">
            <div>
              <p class="text-slate-500 dark:text-slate-400">{{ $t('admin.attendance.totalSessions') || 'Total Sessions' }}</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ group.sessions_count || 0 }}</p>
            </div>
            <div>
              <p class="text-slate-500 dark:text-slate-400">{{ $t('admin.attendance.present') || 'Present' }}</p>
              <p class="font-medium text-green-600 dark:text-green-400">{{ group.attendance_stats?.present || 0 }}</p>
            </div>
            <div>
              <p class="text-slate-500 dark:text-slate-400">{{ $t('admin.attendance.absent') || 'Absent' }}</p>
              <p class="font-medium text-red-600 dark:text-red-400">{{ group.attendance_stats?.absent || 0 }}</p>
            </div>
            <div>
              <p class="text-slate-500 dark:text-slate-400">{{ $t('admin.attendance.attendanceRate') || 'Rate' }}</p>
              <p class="font-medium text-slate-900 dark:text-white">{{ group.attendance_stats?.rate || 0 }}%</p>
            </div>
          </div>
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
const batches = ref([]);

async function loadAttendance() {
  loading.value = true;
  try {
    const response = await api.get('/admin/attendance');
    // Group by batches
    const attendance = response.data.data || response.data;
    // Process and group data
    batches.value = attendance;
  } catch (error) {
    console.error('Error loading attendance:', error);
    toast.error('Failed to load attendance');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadAttendance();
});
</script>

