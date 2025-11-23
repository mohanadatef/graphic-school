<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.gradebook.title') || 'Gradebook Overview' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.gradebook.subtitle') || 'View all student academic performance' }}</p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <select
          v-model="filters.program_id"
          class="px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          @change="loadGradebook"
        >
          <option value="">{{ $t('admin.gradebook.allPrograms') || 'All Programs' }}</option>
          <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.title || program.name }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="entries.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.gradebook.noEntries') || 'No gradebook entries found' }}</p>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
          <tr>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.gradebook.student') || 'Student' }}</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.gradebook.program') || 'Program' }}</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.gradebook.assignmentGrade') || 'Assignments' }}</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.gradebook.attendance') || 'Attendance %' }}</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.gradebook.overall') || 'Overall Grade' }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="entry in entries" :key="entry.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
            <td class="px-4 py-3 text-slate-900 dark:text-white">{{ entry.student?.name }}</td>
            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ entry.program?.title || entry.program?.name }}</td>
            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ entry.assignment_grade.toFixed(2) }}</td>
            <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ entry.attendance_percentage.toFixed(2) }}%</td>
            <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ entry.overall_grade.toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const entries = ref([]);
const programs = ref([]);
const filters = reactive({
  program_id: '',
});

async function loadPrograms() {
  try {
    const response = await api.get('/programs');
    programs.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading programs:', error);
  }
}

async function loadGradebook() {
  loading.value = true;
  try {
    const params = { ...filters };
    const response = await api.get('/admin/gradebook', { params });
    entries.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Error loading gradebook:', error);
    toast.error('Failed to load gradebook');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadPrograms();
  loadGradebook();
});
</script>

