<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.assignments.title') || 'Assignments Overview' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.assignments.subtitle') || 'View all assignments' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="assignments.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.assignments.noAssignments') || 'No assignments found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="assignment in assignments" :key="assignment.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ assignment.title }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ assignment.program?.title || assignment.program?.name }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('admin.assignments.dueDate') || 'Due Date' }}: {{ formatDate(assignment.due_date) }}</p>
          </div>
          <div>
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('admin.assignments.createdBy') || 'Created by' }}: {{ assignment.creator?.name }}</span>
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
    const response = await api.get('/admin/assignments');
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

