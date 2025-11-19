<template>
  <Teleport to="body">
    <div
      v-if="toasts.length > 0"
      class="fixed top-4 right-4 z-50 space-y-2 max-w-md"
      :dir="isRTL ? 'rtl' : 'ltr'"
    >
      <TransitionGroup name="toast" tag="div">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'px-4 py-3 rounded-lg shadow-lg flex items-center justify-between gap-3 min-w-[300px]',
            toastClasses[toast.type],
          ]"
        >
          <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
          <button
            @click="remove(toast.id)"
            class="flex-shrink-0 text-current opacity-70 hover:opacity-100 transition-opacity"
            :aria-label="$t('common.close')"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { useToast } from '../../composables/useToast';
import { useI18n } from 'vue-i18n';

const { toasts, remove } = useToast();
const { locale } = useI18n();

const isRTL = computed(() => locale.value === 'ar');

const toastClasses = {
  success: 'bg-green-50 text-green-800 border border-green-200',
  error: 'bg-red-50 text-red-800 border border-red-200',
  warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
  info: 'bg-blue-50 text-blue-800 border border-blue-200',
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>

