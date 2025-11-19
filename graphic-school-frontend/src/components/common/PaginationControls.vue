<template>
  <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-slate-600">
    <div class="flex items-center gap-2">
      <span>صفحة {{ meta.current_page }} من {{ meta.last_page }}</span>
      <span class="text-slate-400">|</span>
      <span>الإجمالي: {{ meta.total }}</span>
    </div>
    <div class="flex items-center gap-2">
      <label class="text-xs text-slate-400">عدد الصفوف</label>
      <select class="input w-20" :value="meta.per_page" @change="onPerPageChange">
        <option v-for="option in perPageOptions" :key="option" :value="option">
          {{ option }}
        </option>
      </select>
      <div class="flex items-center gap-2">
        <button class="pager-btn" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">
          السابق
        </button>
        <button class="pager-btn" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">
          التالي
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
}
.pager-btn {
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.35rem 0.9rem;
  transition: all 0.2s;
}
.pager-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>


