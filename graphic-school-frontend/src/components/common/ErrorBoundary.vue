<template>
  <div v-if="hasError" class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 p-4">
    <div class="max-w-md w-full text-center">
      <div class="mb-6">
        <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ t('errors.somethingWentWrong') }}</h1>
      <p class="text-slate-600 dark:text-slate-400 mb-6">{{ errorMessage }}</p>
      <div class="flex gap-3 justify-center">
        <button @click="retry" class="btn-primary">
          {{ t('common.retry') }}
        </button>
        <button @click="goHome" class="btn-secondary">
          {{ t('common.goHome') }}
        </button>
      </div>
      <details v-if="showDetails" class="mt-6 text-left">
        <summary class="cursor-pointer text-sm text-slate-500 dark:text-slate-400 mb-2">
          {{ t('errors.technicalDetails') }}
        </summary>
        <pre class="text-xs bg-slate-100 dark:bg-slate-800 p-4 rounded overflow-auto max-h-40">{{ errorDetails }}</pre>
      </details>
    </div>
  </div>
  <slot v-else />
</template>

<script setup>
import { ref, onErrorCaptured } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from '../../composables/useI18n';

const router = useRouter();
const { t } = useI18n();
const hasError = ref(false);
const errorMessage = ref('');
const errorDetails = ref('');
const showDetails = ref(false);

const props = defineProps({
  fallback: {
    type: String,
    default: null,
  },
});

function retry() {
  hasError.value = false;
  errorMessage.value = '';
  errorDetails.value = '';
  window.location.reload();
}

function goHome() {
  router.push('/');
}

onErrorCaptured((err, instance, info) => {
  hasError.value = true;
  errorMessage.value = err.message || 'حدث خطأ غير متوقع';
  errorDetails.value = `${err.stack}\n\nComponent: ${instance?.$?.type?.name || 'Unknown'}\nInfo: ${info}`;
  
  // Log to console in development
  if (import.meta.env.DEV) {
    console.error('Error caught by ErrorBoundary:', err, info);
  }
  
  // Prevent error from propagating
  return false;
});
</script>

