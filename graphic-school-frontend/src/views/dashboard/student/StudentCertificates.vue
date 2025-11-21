<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">شهاداتي</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
    </div>

    <div v-else-if="certificates.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">لا توجد شهادات حتى الآن</p>
      <p class="text-slate-400 dark:text-slate-500 text-sm mt-2">أكمل الكورسات للحصول على شهادات</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="certificate in certificates"
        :key="certificate.id"
        class="card-premium p-6 hover-lift"
      >
        <div class="mb-4">
          <div class="w-full h-48 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-lg flex items-center justify-center border-4 border-yellow-300 dark:border-yellow-700">
            <svg class="w-24 h-24 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ certificate.course?.title || 'شهادة إتمام' }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
          صدرت في: {{ formatDate(certificate.issued_date) }}
        </p>
        <div class="flex items-center gap-2">
          <button
            @click="downloadCertificate(certificate)"
            class="btn-primary flex-1 text-sm"
          >
            تحميل الشهادة
          </button>
          <button
            @click="viewCertificate(certificate)"
            class="btn-secondary text-sm"
          >
            عرض
          </button>
        </div>
        <div v-if="certificate.verification_code" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
          <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">رمز التحقق:</p>
          <p class="text-sm font-mono text-slate-700 dark:text-slate-300">{{ certificate.verification_code }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const { get } = useApi();
const toast = useToast();

const loading = ref(false);
const certificates = ref([]);

async function loadCertificates() {
  try {
    loading.value = true;
    const response = await get('/student/certificates');
    certificates.value = response?.data || response || [];
  } catch (err) {
    console.error('Error loading certificates:', err);
    certificates.value = [];
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function downloadCertificate(certificate) {
  if (certificate.pdf_path) {
    window.open(certificate.pdf_path, '_blank');
  } else {
    toast.warning('الشهادة غير متاحة للتحميل حالياً');
  }
}

function viewCertificate(certificate) {
  if (certificate.verification_code) {
    window.open(`/certificates/verify/${certificate.verification_code}`, '_blank');
  } else {
    toast.warning('رمز التحقق غير متاح');
  }
}

onMounted(loadCertificates);
</script>

