<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ assignment?.title || 'Assignment' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="assignment" class="space-y-6">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ assignment.title }}</h3>
        <p class="text-slate-600 dark:text-slate-400 mb-4">{{ assignment.description }}</p>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-slate-500 dark:text-slate-400">{{ $t('student.assignments.dueDate') || 'Due Date' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ formatDate(assignment.due_date) }}</p>
          </div>
          <div>
            <p class="text-slate-500 dark:text-slate-400">{{ $t('student.assignments.maxGrade') || 'Max Grade' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ assignment.max_grade }}</p>
          </div>
        </div>
      </div>

      <div v-if="submission" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h4 class="font-semibold text-slate-900 dark:text-white mb-4">{{ $t('student.assignments.submission') || 'Your Submission' }}</h4>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $t('student.assignments.submittedAt') || 'Submitted At' }}: {{ formatDate(submission.submitted_at) }}</p>
        <p v-if="submission.text_submission" class="text-slate-600 dark:text-slate-400 mb-2">{{ submission.text_submission }}</p>
        <a v-if="submission.file_url" :href="submission.file_url" target="_blank" class="text-primary text-sm">{{ $t('student.assignments.downloadFile') || 'Download File' }}</a>
        
        <div v-if="submission.isGraded" class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <p class="font-semibold text-green-800 dark:text-green-400 mb-2">{{ $t('student.assignments.grade') || 'Grade' }}: {{ submission.grade }}</p>
          <p v-if="submission.feedback" class="text-sm text-green-700 dark:text-green-300">{{ submission.feedback }}</p>
        </div>
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
const assignment = ref(null);
const submission = ref(null);

async function loadAssignment() {
  loading.value = true;
  try {
    const response = await api.get(`/student/assignments/${route.params.id}`);
    assignment.value = response.data;
    if (assignment.value.submissions && assignment.value.submissions.length > 0) {
      submission.value = assignment.value.submissions[0];
    }
  } catch (error) {
    console.error('Error loading assignment:', error);
    toast.error('Failed to load assignment');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleString();
}

onMounted(() => {
  loadAssignment();
});
</script>

