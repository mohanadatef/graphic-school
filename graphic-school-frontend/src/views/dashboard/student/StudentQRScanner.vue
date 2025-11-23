<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('student.qr.title') || 'QR Check-In' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('student.qr.subtitle') || 'Scan QR code to mark attendance' }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div v-if="success" class="text-center py-8">
        <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-green-800 dark:text-green-400 mb-2">{{ $t('student.qr.success') || 'Attendance Confirmed!' }}</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('student.qr.successMessage') || 'Your attendance has been recorded successfully.' }}</p>
        <button @click="reset" class="mt-4 btn-primary">{{ $t('student.qr.scanAnother') || 'Scan Another' }}</button>
      </div>

      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('student.qr.enterToken') || 'Enter QR Token' }}</label>
          <input
            v-model="token"
            type="text"
            :placeholder="$t('student.qr.tokenPlaceholder') || 'Paste QR token here'"
            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white font-mono"
            maxlength="64"
          />
        </div>
        <button @click="checkIn" :disabled="!token || checking" class="btn-primary w-full">
          <span v-if="checking">{{ $t('common.checking') || 'Checking...' }}</span>
          <span v-else>{{ $t('student.qr.checkIn') || 'Check In' }}</span>
        </button>
      </div>

      <div v-if="error" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
        <p class="text-red-800 dark:text-red-400 text-sm">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const token = ref('');
const checking = ref(false);
const success = ref(false);
const error = ref('');

async function checkIn() {
  if (!token.value || token.value.length !== 64) {
    error.value = 'Invalid token format';
    return;
  }

  checking.value = true;
  error.value = '';

  try {
    await api.post('/student/qr-checkin', { token: token.value });
    success.value = true;
    toast.success('Attendance confirmed successfully');
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to check in';
    toast.error(error.value);
  } finally {
    checking.value = false;
  }
}

function reset() {
  token.value = '';
  success.value = false;
  error.value = '';
}
</script>

