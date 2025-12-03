<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ $t('public.certificate.title') || 'Verify Certificate' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ $t('public.certificate.subtitle') || 'Enter the verification code to verify a certificate' }}</p>

        <form @submit.prevent="verifyCertificate" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('public.certificate.verificationCode') || 'Verification Code' }}</label>
            <input
              v-model="verificationCode"
              type="text"
              required
              placeholder="CERT-XXXXXXXXXXXX"
              class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono"
            />
          </div>

          <button type="submit" :disabled="verifying" class="btn-primary w-full">
            <span v-if="verifying">{{ $t('common.verifying') || 'Verifying...' }}</span>
            <span v-else>{{ $t('public.certificate.verify') || 'Verify' }}</span>
          </button>
        </form>

        <!-- Certificate Details -->
        <div v-if="certificate" class="mt-6 p-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
          <h3 class="text-lg font-semibold text-green-800 dark:text-green-400 mb-4">{{ $t('public.certificate.valid') || 'Certificate Verified' }}</h3>
          <div class="space-y-3">
            <div>
              <p class="text-sm text-green-700 dark:text-green-300">{{ $t('public.certificate.student') || 'Student' }}</p>
              <p class="font-medium text-green-900 dark:text-green-200">{{ certificate.student?.name }}</p>
            </div>
            <div>
              <p class="text-sm text-green-700 dark:text-green-300">{{ $t('public.certificate.course') || 'Course' }}</p>
              <p class="font-medium text-green-900 dark:text-green-200">{{ certificate.course?.title }}</p>
            </div>
            <div>
              <p class="text-sm text-green-700 dark:text-green-300">{{ $t('public.certificate.issuedAt') || 'Issued At' }}</p>
              <p class="font-medium text-green-900 dark:text-green-200">{{ formatDate(certificate.issued_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Invalid Certificate -->
        <div v-if="invalid" class="mt-6 p-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
          <p class="text-red-800 dark:text-red-400">{{ $t('public.certificate.invalid') || 'Invalid verification code. Please check and try again.' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../services/api/client';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const verifying = ref(false);
const verificationCode = ref('');
const certificate = ref(null);
const invalid = ref(false);

async function verifyCertificate() {
  verifying.value = true;
  invalid.value = false;
  certificate.value = null;
  
  try {
    const response = await api.get('/certificates/verify', {
      params: { verification_code: verificationCode.value },
    });
    certificate.value = response.data;
    toast.success('Certificate verified successfully');
  } catch (error) {
    invalid.value = true;
    toast.error('Invalid verification code');
  } finally {
    verifying.value = false;
  }
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>

