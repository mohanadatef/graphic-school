<template>
  <div class="language-picker">
    <button
      @click="toggleLocale"
      class="flex items-center gap-2 px-3 py-2 rounded-md border border-slate-200 hover:bg-slate-50 transition-colors"
      :aria-label="$t('common.language')"
    >
      <span class="text-sm font-medium">{{ currentLocale === 'ar' ? 'العربية' : 'English' }}</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useLocale } from '../../composables/useLocale';

const { locale, toggleLocale } = useLocale();

const currentLocale = computed(() => locale.value);
</script>

<style scoped>
.language-picker {
  display: inline-block;
}
</style>

