<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.gradebook.title') || 'My Gradebook' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gradebook.subtitle') || 'View your academic performance' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="entries.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.gradebook.noEntries') || 'No gradebook entries found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="entry in entries" :key="entry.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ entry.program?.title || entry.program?.name }}</h3>
        <div class="grid md:grid-cols-4 gap-4">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gradebook.assignmentGrade') || 'Assignments' }}</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatGrade(entry.assignment_grade) }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gradebook.attendance') || 'Attendance' }}</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatGrade(entry.attendance_percentage) }}%</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gradebook.participation') || 'Participation' }}</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatGrade(entry.participation_grade) }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.gradebook.overall') || 'Overall Grade' }}</p>
            <p class="text-2xl font-bold text-primary">{{ formatGrade(entry.overall_grade) }}</p>
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
const entries = ref([]);

function formatGrade(grade) {
  if (grade === null || grade === undefined) return 'N/A';
  return parseFloat(grade).toFixed(2);
}

async function loadGradebook() {
  loading.value = true;
  try {
    const response = await api.get('/student/gradebook');
    entries.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading gradebook:', error);
    toast.error('Failed to load gradebook');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadGradebook();
});
</script>

