<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.assignments.title') || 'My Assignments' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.assignments.subtitle') || 'View and submit your assignments' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="assignments.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.assignments.noAssignments') || 'No assignments found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="assignment in assignments" :key="assignment.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ assignment.title }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ assignment.description }}</p>
            <div class="flex gap-4 text-sm">
              <div>
                <span class="text-slate-500 dark:text-slate-400">{{ $t('student.assignments.dueDate') || 'Due Date' }}:</span>
                <span class="text-slate-900 dark:text-white ml-2" :class="{ 'text-red-600 dark:text-red-400': assignment.is_overdue }">{{ formatDate(assignment.due_date) }}</span>
              </div>
              <div>
                <span class="text-slate-500 dark:text-slate-400">{{ $t('student.assignments.maxGrade') || 'Max Grade' }}:</span>
                <span class="text-slate-900 dark:text-white ml-2">{{ assignment.max_grade }}</span>
              </div>
            </div>
            <div v-if="assignment.submissions && assignment.submissions.length > 0" class="mt-4">
              <div v-for="submission in assignment.submissions" :key="submission.id" class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('student.assignments.submittedAt') || 'Submitted' }}: {{ formatDate(submission.submitted_at) }}</p>
                <p v-if="submission.isGraded" class="text-sm font-semibold text-slate-900 dark:text-white mt-1">{{ $t('student.assignments.grade') || 'Grade' }}: {{ submission.grade }}</p>
                <p v-if="submission.feedback" class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ submission.feedback }}</p>
              </div>
            </div>
          </div>
          <div class="ml-4">
            <button
              v-if="!assignment.submissions || assignment.submissions.length === 0"
              @click="$router.push({ name: 'student-assignment-submit', params: { id: assignment.id } })"
              class="btn-primary"
            >
              {{ $t('student.assignments.submit') || 'Submit' }}
            </button>
            <button
              v-else
              @click="$router.push({ name: 'student-assignment-view', params: { id: assignment.id } })"
              class="btn-secondary"
            >
              {{ $t('common.view') || 'View' }}
            </button>
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
const assignments = ref([]);

async function loadAssignments() {
  loading.value = true;
  try {
    const response = await api.get('/student/assignments');
    assignments.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading assignments:', error);
    toast.error('Failed to load assignments');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadAssignments();
});
</script>

