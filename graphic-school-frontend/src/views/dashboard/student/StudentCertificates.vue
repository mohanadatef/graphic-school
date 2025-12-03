<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
        {{ $t('student.certificates.title') || 'My Certificates' }}
      </h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $t('student.certificates.subtitle') || 'View and download your certificates' }}
      </p>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <p class="text-red-800">{{ error }}</p>
    </div>

    <div v-else-if="items.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.certificates.noCertificates') || 'No certificates issued yet' }}
      </p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="cert in items"
        :key="cert.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
                {{ cert.course?.title }}
              </h3>
              <p v-if="cert.group" class="text-sm text-slate-600 dark:text-slate-400">
                {{ cert.group.name }}
              </p>
            </div>
            <div class="text-right">
              <div class="text-xs text-slate-500 dark:text-slate-400">
                {{ formatDate(cert.issued_date) }}
              </div>
            </div>
          </div>

          <div class="space-y-2 mb-4">
            <div class="text-sm">
              <span class="text-slate-500 dark:text-slate-400">Certificate #:</span>
              <span class="font-mono text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded ml-2">
                {{ cert.certificate_number }}
              </span>
            </div>
            <div v-if="cert.instructor" class="text-sm">
              <span class="text-slate-500 dark:text-slate-400">Instructor:</span>
              <span class="ml-2">{{ cert.instructor.name }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="viewCertificate(cert.id)"
              class="flex-1 px-4 py-2 bg-primary text-white rounded-md text-sm hover:bg-primary/90"
            >
              {{ $t('student.certificates.view') || 'View' }}
            </button>
            <button
              @click="copyVerificationLink(cert.verification_code)"
              class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md text-sm hover:bg-slate-50 dark:hover:bg-slate-700"
              :title="$t('student.certificates.copyLink') || 'Copy verification link'"
            >
              🔗
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useCertificateStore } from '@/stores/certificate';
import { useRouter } from 'vue-router';

const router = useRouter();
const certificateStore = useCertificateStore();
const { items, loading, error } = storeToRefs(certificateStore);

onMounted(async () => {
  await certificateStore.fetchStudentCertificates();
});

function viewCertificate(id) {
  router.push(`/dashboard/student/certificates/${id}`);
}

function copyVerificationLink(code) {
  const url = `${window.location.origin}/certificate/verify?code=${code}`;
  navigator.clipboard.writeText(url);
  alert('Verification link copied to clipboard!');
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>
