<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.assignments.submissions') || 'Assignment Submissions' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="submissions.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('instructor.assignments.noSubmissions') || 'No submissions found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="submission in submissions" :key="submission.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ submission.student?.name }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $t('instructor.assignments.submittedAt') || 'Submitted At' }}: {{ formatDate(submission.submitted_at) }}</p>
            <p v-if="submission.text_submission" class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ submission.text_submission }}</p>
            <a v-if="submission.file_url" :href="submission.file_url" target="_blank" class="text-primary text-sm">{{ $t('instructor.assignments.downloadFile') || 'Download File' }}</a>
          </div>
          <div class="ml-4">
            <span
              :class="{
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': submission.status === 'submitted',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': submission.status === 'graded',
                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': submission.status === 'late',
              }"
              class="px-3 py-1 text-sm font-semibold rounded-full"
            >
              {{ $t(`instructor.assignments.${submission.status}`) || submission.status }}
            </span>
          </div>
        </div>

        <div v-if="submission.status === 'submitted' || submission.status === 'late'" class="mt-4">
          <button
            @click="$router.push({ name: 'instructor-assignment-grade', params: { id: submission.id } })"
            class="btn-primary"
          >
            {{ $t('instructor.assignments.grade') || 'Grade' }}
          </button>
        </div>

        <div v-if="submission.isGraded" class="mt-4 p-4 bg-slate-50 dark:bg-slate-900 rounded-lg">
          <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('instructor.assignments.grade') || 'Grade' }}: <span class="font-semibold">{{ submission.grade }}</span></p>
          <p v-if="submission.feedback" class="text-sm text-slate-600 dark:text-slate-400 mt-2">{{ submission.feedback }}</p>
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
const submissions = ref([]);

async function loadSubmissions() {
  loading.value = true;
  try {
    const response = await api.get(`/instructor/assignments/${route.params.id}/submissions`);
    submissions.value = response.data || [];
  } catch (error) {
    console.error('Error loading submissions:', error);
    toast.error('Failed to load submissions');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleString();
}

onMounted(() => {
  loadSubmissions();
});
</script>

