<template>
  <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-slate-600">
    <div class="flex items-center gap-2">
      <span>
        {{ $t('pagination.page') }} {{ meta.current_page }} {{ $t('pagination.of') }}
        {{ meta.last_page }}
      </span>
      <span class="text-slate-400">|</span>
      <span>{{ $t('pagination.total') }}: {{ meta.total }}</span>
    </div>
    <div class="flex items-center gap-2">
      <label for="per-page-select" class="text-xs text-slate-400">
        {{ $t('pagination.rowsPerPage') }}
      </label>
      <select
        id="per-page-select"
        class="input w-20"
        :value="meta.per_page"
        @change="onPerPageChange"
        :aria-label="$t('pagination.rowsPerPage')"
      >
        <option v-for="option in perPageOptions" :key="option" :value="option">
          {{ option }}
        </option>
      </select>
      <div class="flex items-center gap-2">
        <button
          class="pager-btn"
          :disabled="meta.current_page === 1"
          @click="go(meta.current_page - 1)"
          :aria-label="$t('pagination.previous')"
        >
          {{ $t('pagination.previous') }}
        </button>
        <button
          class="pager-btn"
          :disabled="meta.current_page === meta.last_page"
          @click="go(meta.current_page + 1)"
          :aria-label="$t('pagination.next')"
        >
          {{ $t('pagination.next') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  meta: {
    type: Object,
    required: true,
  },
  perPageOptions: {
    type: Array,
    default: () => [5, 10, 20, 50],
  },
});

const emit = defineEmits(['change-page', 'change-per-page']);

function go(page) {
  if (page < 1 || page > props.meta.last_page) return;
  emit('change-page', page);
}

function onPerPageChange(event) {
  emit('change-per-page', Number(event.target.value));
}
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.3rem 0.5rem;
  outline: none;
  transition: border-color 0.2s;
}

.input:focus {
  border-color: var(--primary-color, #1d4ed8);
}

.pager-btn {
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.35rem 0.9rem;
  transition: all 0.2s;
  background: white;
  cursor: pointer;
}

.pager-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.pager-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>
