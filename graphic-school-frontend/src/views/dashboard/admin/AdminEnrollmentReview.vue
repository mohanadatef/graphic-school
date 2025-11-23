<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.enrollments.review') || 'Enrollment Review' }}</h2>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="enrollment" class="space-y-6">
      <!-- Enrollment Details -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.enrollments.details') || 'Enrollment Details' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.student') || 'Student' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.student?.name }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ enrollment.student?.email }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.program') || 'Program' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.program?.title || enrollment.program?.name }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.batch') || 'Batch' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.batch?.code || '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.group') || 'Group' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ enrollment.group?.code || enrollment.group?.name || '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.status') || 'Status' }}</p>
            <span
              :class="{
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': enrollment.status === 'pending',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': enrollment.status === 'approved',
                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': enrollment.status === 'rejected',
              }"
              class="px-2 py-1 text-xs font-semibold rounded-full"
            >
              {{ $t(`admin.enrollments.${enrollment.status}`) || enrollment.status }}
            </span>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.enrollments.createdAt') || 'Created At' }}</p>
            <p class="font-medium text-slate-900 dark:text-white">{{ formatDate(enrollment.created_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Enrollment Timeline -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('admin.enrollments.timeline') || 'Enrollment Timeline' }}</h3>
        <div class="space-y-4">
          <div v-for="log in logs" :key="log.id" class="flex gap-4">
            <div class="flex-shrink-0">
              <div class="w-2 h-2 bg-primary rounded-full mt-2"></div>
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $t(`admin.enrollments.actions.${log.action}`) || log.action }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(log.created_at) }}</p>
              <p v-if="log.admin" class="text-xs text-slate-600 dark:text-slate-400">{{ $t('admin.enrollments.by') || 'By' }}: {{ log.admin?.name }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div v-if="enrollment.status === 'pending'" class="flex gap-3">
        <button @click="approveEnrollment" class="btn-primary flex-1">
          {{ $t('admin.enrollments.approve') || 'Approve' }}
        </button>
        <button @click="rejectEnrollment" class="btn-danger flex-1">
          {{ $t('admin.enrollments.reject') || 'Reject' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const loading = ref(false);
const enrollment = ref(null);
const logs = ref([]);

async function loadEnrollment() {
  loading.value = true;
  try {
    const response = await api.get(`/admin/enrollments/${route.params.id}`);
    enrollment.value = response.data;
    
    // Load logs
    const logsResponse = await api.get(`/admin/enrollments/${route.params.id}/logs`);
    logs.value = logsResponse.data || [];
  } catch (error) {
    console.error('Error loading enrollment:', error);
    toast.error('Failed to load enrollment');
  } finally {
    loading.value = false;
  }
}

async function approveEnrollment() {
  try {
    await api.post(`/admin/enrollments/${route.params.id}/approve`);
    toast.success('Enrollment approved successfully');
    loadEnrollment();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to approve enrollment');
  }
}

async function rejectEnrollment() {
  if (!confirm('Are you sure you want to reject this enrollment?')) return;
  try {
    await api.post(`/admin/enrollments/${route.params.id}/reject`);
    toast.success('Enrollment rejected successfully');
    loadEnrollment();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to reject enrollment');
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadEnrollment();
});
</script>

