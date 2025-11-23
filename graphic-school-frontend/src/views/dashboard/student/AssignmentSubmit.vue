<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.assignments.submit') || 'Submit Assignment' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="assignment" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div class="mb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ assignment.title }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ assignment.description }}</p>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('student.assignments.dueDate') || 'Due Date' }}: {{ formatDate(assignment.due_date) }}</p>
      </div>

      <form @submit.prevent="submitAssignment" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('student.assignments.file') || 'File Upload' }}</label>
          <input type="file" @change="handleFileChange" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white" />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('student.assignments.textSubmission') || 'Text Submission' }}</label>
          <textarea v-model="form.text_submission" rows="6" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"></textarea>
        </div>

        <div class="flex gap-3">
          <button type="submit" :disabled="submitting" class="btn-primary flex-1">
            <span v-if="submitting">{{ $t('common.submitting') || 'Submitting...' }}</span>
            <span v-else>{{ $t('student.assignments.submit') || 'Submit' }}</span>
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
const submitting = ref(false);
const assignment = ref(null);
const form = reactive({
  text_submission: '',
});
const file = ref(null);

async function loadAssignment() {
  loading.value = true;
  try {
    const response = await api.get(`/student/assignments/${route.params.id}`);
    assignment.value = response.data;
  } catch (error) {
    console.error('Error loading assignment:', error);
    toast.error('Failed to load assignment');
  } finally {
    loading.value = false;
  }
}

function handleFileChange(event) {
  file.value = event.target.files[0];
}

async function submitAssignment() {
  if (!file.value && !form.text_submission) {
    toast.error('Please provide either a file or text submission');
    return;
  }

  submitting.value = true;
  try {
    const formData = new FormData();
    if (file.value) {
      formData.append('file', file.value);
    }
    if (form.text_submission) {
      formData.append('text_submission', form.text_submission);
    }

    await api.post(`/student/assignments/${route.params.id}/submit`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    
    toast.success('Assignment submitted successfully');
    router.push({ name: 'student-assignments' });
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to submit assignment');
  } finally {
    submitting.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadAssignment();
});
</script>

