<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.attendance.markAttendance') || 'Mark Attendance' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="attendance.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('instructor.attendance.noStudents') || 'No students found' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ session?.title || session?.name || 'Session' }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ formatDate(session?.scheduled_at) }}</p>
      </div>

      <div class="space-y-3 mb-6">
        <div v-for="record in attendance" :key="record.id" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
          <div>
            <p class="font-medium text-slate-900 dark:text-white">{{ record.student?.name }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ record.student?.email }}</p>
          </div>
          <select
            v-model="record.status"
            class="px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          >
            <option value="present">{{ $t('instructor.attendance.present') || 'Present' }}</option>
            <option value="absent">{{ $t('instructor.attendance.absent') || 'Absent' }}</option>
            <option value="late">{{ $t('instructor.attendance.late') || 'Late' }}</option>
            <option value="excused">{{ $t('instructor.attendance.excused') || 'Excused' }}</option>
          </select>
        </div>
      </div>

      <div class="flex gap-3">
        <button @click="updateAttendance" :disabled="saving" class="btn-primary flex-1">
          <span v-if="saving">{{ $t('common.saving') || 'Saving...' }}</span>
          <span v-else>{{ $t('common.save') || 'Save' }}</span>
        </button>
        <button @click="$router.back()" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
      </div>
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
const saving = ref(false);
const session = ref(null);
const attendance = ref([]);

async function loadAttendance() {
  loading.value = true;
  try {
    const sessionResponse = await api.get(`/instructor/sessions/${route.params.id}`);
    session.value = sessionResponse.data;
    
    const attendanceResponse = await api.get(`/instructor/sessions/${route.params.id}/attendance`);
    attendance.value = attendanceResponse.data || [];
  } catch (error) {
    console.error('Error loading attendance:', error);
    toast.error('Failed to load attendance');
  } finally {
    loading.value = false;
  }
}

async function updateAttendance() {
  saving.value = true;
  try {
    const attendanceData = attendance.value.map(record => ({
      student_id: record.student_id,
      status: record.status,
    }));
    
    await api.post(`/instructor/sessions/${route.params.id}/attendance/update`, {
      attendance: attendanceData,
    });
    toast.success('Attendance updated successfully');
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to update attendance');
  } finally {
    saving.value = false;
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

