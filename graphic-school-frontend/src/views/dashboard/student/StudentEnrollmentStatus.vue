<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.enrollments.title') || 'My Enrollments' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.enrollments.subtitle') || 'View your enrollment status' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="enrollments.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.enrollments.noEnrollments') || 'No enrollments found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="enrollment in enrollments" :key="enrollment.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ enrollment.program?.title || enrollment.program?.name }}</h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('student.enrollments.batch') || 'Batch' }}</p>
                <p class="text-slate-900 dark:text-white">{{ enrollment.batch?.code || '-' }}</p>
              </div>
              <div>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('student.enrollments.group') || 'Group' }}</p>
                <p class="text-slate-900 dark:text-white">{{ enrollment.group?.code || enrollment.group?.name || '-' }}</p>
              </div>
            </div>
          </div>
          <span
            :class="{
              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': enrollment.status === 'pending',
              'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': enrollment.status === 'approved',
              'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': enrollment.status === 'rejected',
            }"
            class="px-3 py-1 text-sm font-semibold rounded-full"
          >
            {{ $t(`student.enrollments.${enrollment.status}`) || enrollment.status }}
          </span>
        </div>

        <div v-if="enrollment.status === 'approved'" class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
          <p class="text-sm text-green-800 dark:text-green-400">{{ $t('student.enrollments.nextSteps') || 'Next Steps: Complete payment to start attending sessions.' }}</p>
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
const enrollments = ref([]);

async function loadEnrollments() {
  loading.value = true;
  try {
    const response = await api.get('/student/enrollments');
    enrollments.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error loading enrollments:', error);
    toast.error('Failed to load enrollments');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadEnrollments();
});
</script>

