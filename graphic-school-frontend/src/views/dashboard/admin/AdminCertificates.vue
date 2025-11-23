<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.certificates.title') || 'Certificates Management' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.certificates.subtitle') || 'Manage issued certificates' }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <input
          v-model="filters.search"
          class="flex-1 min-w-[200px] px-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
          :placeholder="$t('common.search') || 'Search...'"
          @input="loadCertificates"
        />
      </div>
    </div>

    <!-- Certificates Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="certificates.length === 0" class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.certificates.noCertificates') || 'No certificates found' }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
            <tr>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.certificates.student') || 'Student' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.certificates.program') || 'Program' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.certificates.verificationCode') || 'Verification Code' }}</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">{{ $t('admin.certificates.issuedAt') || 'Issued At' }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            <tr v-for="cert in certificates" :key="cert.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="px-4 py-3 text-slate-900 dark:text-white">{{ cert.student?.name }}</td>
              <td class="px-4 py-3 text-slate-900 dark:text-white">{{ cert.program?.title || cert.program?.name }}</td>
              <td class="px-4 py-3 font-mono text-sm text-slate-600 dark:text-slate-400">{{ cert.verification_code }}</td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ formatDate(cert.issued_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const certificates = ref([]);
const filters = reactive({ search: '' });

async function loadCertificates() {
  loading.value = true;
  try {
    const params = { ...filters };
    const response = await api.get('/admin/certificates', { params });
    certificates.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error loading certificates:', error);
    toast.error('Failed to load certificates');
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}

onMounted(() => {
  loadCertificates();
});
</script>

