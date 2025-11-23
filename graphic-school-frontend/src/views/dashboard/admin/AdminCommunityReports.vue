<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.community.reports.title') || 'Community Reports' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.community.reports.subtitle') || 'Moderation queue' }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-700">
          <tr>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Reported By</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Type</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Reason</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-for="report in reports" :key="report.id">
            <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ report.user?.name }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ report.reportable_type }}</td>
            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ report.reason }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="{
                'text-yellow-600': report.status === 'pending',
                'text-green-600': report.status === 'reviewed',
                'text-red-600': report.status === 'rejected',
              }">{{ report.status }}</span>
            </td>
            <td class="px-6 py-4 text-sm space-x-2">
              <button v-if="report.status === 'pending'" @click="resolveReport(report.id, 'reviewed')" class="text-green-600">Approve</button>
              <button v-if="report.status === 'pending'" @click="resolveReport(report.id, 'rejected')" class="text-red-600">Reject</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const reports = ref([]);

async function loadReports() {
  loading.value = true;
  try {
    const response = await api.get('/admin/community/reports');
    reports.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading reports:', error);
    toast.error('Failed to load reports');
  } finally {
    loading.value = false;
  }
}

async function resolveReport(id, status) {
  try {
    await api.put(`/admin/community/reports/${id}/resolve`, { status });
    toast.success('Report resolved successfully');
    loadReports();
  } catch (error) {
    toast.error('Failed to resolve report');
  }
}

onMounted(() => {
  loadReports();
});
</script>

