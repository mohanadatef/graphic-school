<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.assignments.title') || 'My Assignments' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.assignments.subtitle') || 'Manage your assignments' }}</p>
      </div>
      <button
        @click="$router.push({ name: 'instructor-assignment-create' })"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('instructor.assignments.create') || 'Create Assignment' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="assignments.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('instructor.assignments.noAssignments') || 'No assignments found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="assignment in assignments" :key="assignment.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ assignment.title }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ assignment.description }}</p>
            <div class="flex gap-4 text-sm">
              <div>
                <span class="text-slate-500 dark:text-slate-400">{{ $t('instructor.assignments.dueDate') || 'Due Date' }}:</span>
                <span class="text-slate-900 dark:text-white ml-2">{{ formatDate(assignment.due_date) }}</span>
              </div>
              <div>
                <span class="text-slate-500 dark:text-slate-400">{{ $t('instructor.assignments.maxGrade') || 'Max Grade' }}:</span>
                <span class="text-slate-900 dark:text-white ml-2">{{ assignment.max_grade }}</span>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <button
              @click="$router.push({ name: 'instructor-assignment-submissions', params: { id: assignment.id } })"
              class="btn-primary"
            >
              {{ $t('instructor.assignments.viewSubmissions') || 'View Submissions' }}
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
    const response = await api.get('/instructor/assignments');
    assignments.value = response.data.data || response.data;
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

