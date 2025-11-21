<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :aria-label="ariaLabel || label"
    :aria-busy="loading"
    :class="[
      'btn',
      variant,
      size,
      {
        'opacity-50 cursor-not-allowed': disabled || loading,
        'loading': loading,
      },
    ]"
    @click="handleClick"
  >
    <span v-if="loading" class="spinner-sm mr-2" aria-hidden="true"></span>
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'success'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: '',
  },
  ariaLabel: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['click']);

function handleClick(event) {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
}
</script>

<style scoped>
.btn {
  @apply font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn:focus-visible {
  @apply ring-2 ring-primary ring-offset-2;
}

.btn.primary {
  @apply bg-primary text-white hover:bg-primary-dark;
}

.btn.secondary {
  @apply bg-slate-200 text-slate-700 hover:bg-slate-300;
}

.btn.danger {
  @apply bg-red-600 text-white hover:bg-red-700;
}

.btn.success {
  @apply bg-green-600 text-white hover:bg-green-700;
}

.btn.sm {
  @apply px-3 py-1.5 text-sm;
}

.btn.md {
  @apply px-4 py-2 text-base;
}

.btn.lg {
  @apply px-6 py-3 text-lg;
}

.spinner-sm {
  @apply inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin;
}
</style>

