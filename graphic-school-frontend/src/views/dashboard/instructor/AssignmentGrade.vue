<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.assignments.grade') || 'Grade Assignment' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="submission" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ submission.student?.name }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $t('instructor.assignments.submittedAt') || 'Submitted At' }}: {{ formatDate(submission.submitted_at) }}</p>
        <p v-if="submission.text_submission" class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ submission.text_submission }}</p>
        <a v-if="submission.file_url" :href="submission.file_url" target="_blank" class="text-primary text-sm">{{ $t('instructor.assignments.downloadFile') || 'Download File' }}</a>
      </div>

      <form @submit.prevent="submitGrade" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.grade') || 'Grade' }} (0 - {{ submission.assignment?.max_grade || 100 }})</label>
          <input v-model.number="form.grade" type="number" min="0" :max="submission.assignment?.max_grade || 100" step="0.01" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('instructor.assignments.feedback') || 'Feedback' }}</label>
          <textarea v-model="form.feedback" rows="4" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"></textarea>
        </div>

        <div class="flex gap-3">
          <button type="submit" :disabled="grading" class="btn-primary flex-1">
            <span v-if="grading">{{ $t('common.saving') || 'Saving...' }}</span>
            <span v-else>{{ $t('common.save') || 'Save Grade' }}</span>
          </button>
          <button type="button" @click="$router.back()" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);
const grading = ref(false);
const submission = ref(null);
const form = reactive({
  grade: 0,
  feedback: '',
});

async function loadSubmission() {
  loading.value = true;
  try {
    // Load submission details - we'll need to get it from the assignment submissions endpoint
    // For now, we'll construct it from the ID
    const response = await api.get(`/instructor/assignments/${route.params.assignmentId}/submissions`);
    submission.value = response.data.find(s => s.id === parseInt(route.params.id));
    
    if (submission.value) {
      form.grade = submission.value.grade || 0;
      form.feedback = submission.value.feedback || '';
    }
  } catch (error) {
    console.error('Error loading submission:', error);
    toast.error('Failed to load submission');
  } finally {
    loading.value = false;
  }
}

async function submitGrade() {
  grading.value = true;
  try {
    await api.post(`/instructor/submissions/${route.params.id}/grade`, form);
    toast.success('Grade saved successfully');
    router.back();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to save grade');
  } finally {
    grading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleString();
}

onMounted(() => {
  loadSubmission();
});
</script>

