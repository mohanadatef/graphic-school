<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <button @click="$router.back()" class="text-primary mb-2">← {{ $t('common.back') || 'Back' }}</button>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('instructor.qr.title') || 'Generate QR Code' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('instructor.qr.subtitle') || 'Generate QR code for session attendance' }}</p>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="qrData" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div class="text-center">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('instructor.qr.generated') || 'QR Code Generated' }}</h3>
        <div class="mb-4">
          <img :src="qrData.qr_url" alt="QR Code" class="mx-auto border-2 border-slate-200 dark:border-slate-700 rounded-lg" />
        </div>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
          {{ $t('instructor.qr.expiresAt') || 'Expires at' }}: {{ formatDate(qrData.expires_at) }}
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ qrData.token }}</p>
        <div class="mt-6">
          <button @click="generateNew" class="btn-secondary">{{ $t('instructor.qr.generateNew') || 'Generate New' }}</button>
        </div>
      </div>
    </div>

    <div v-else class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <button @click="generateQR" :disabled="generating" class="btn-primary w-full">
        <span v-if="generating">{{ $t('common.generating') || 'Generating...' }}</span>
        <span v-else>{{ $t('instructor.qr.generate') || 'Generate QR Code' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const toast = useToast();
const loading = ref(false);
const generating = ref(false);
const qrData = ref(null);

async function generateQR() {
  generating.value = true;
  try {
    const response = await api.post(`/instructor/sessions/${route.params.id}/qr-generate`);
    qrData.value = response.data;
    toast.success('QR code generated successfully');
  } catch (error) {
    console.error('Error generating QR:', error);
    toast.error(error.response?.data?.message || 'Failed to generate QR code');
  } finally {
    generating.value = false;
  }
}

async function generateNew() {
  qrData.value = null;
  await generateQR();
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleString();
}

onMounted(() => {
  // Optionally load existing QR if available
});
</script>

