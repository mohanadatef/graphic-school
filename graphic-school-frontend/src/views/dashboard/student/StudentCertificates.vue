<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.certificates.title') || 'My Certificates' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.certificates.subtitle') || 'View your issued certificates' }}</p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="certificates.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('student.certificates.noCertificates') || 'No certificates found' }}</p>
    </div>

    <div v-else class="space-y-4">
      <div v-for="cert in certificates" :key="cert.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ cert.program?.title || cert.program?.name }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ $t('student.certificates.issuedAt') || 'Issued At' }}: {{ formatDate(cert.issued_at) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $t('student.certificates.verificationCode') || 'Verification Code' }}: {{ cert.verification_code }}</p>
          </div>
          <button
            @click="downloadCertificate(cert.id)"
            class="btn-primary"
          >
            {{ $t('student.certificates.download') || 'Download' }}
          </button>
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
const certificates = ref([]);

async function loadCertificates() {
  loading.value = true;
  try {
    const response = await api.get('/student/certificates');
    certificates.value = response.data || [];
  } catch (error) {
    console.error('Error loading certificates:', error);
    toast.error('Failed to load certificates');
  } finally {
    loading.value = false;
  }
}

async function downloadCertificate(id) {
  try {
    const response = await api.get(`/student/certificates/${id}/download`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `certificate-${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    toast.success('Certificate downloaded');
  } catch (error) {
    toast.error('Failed to download certificate');
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
