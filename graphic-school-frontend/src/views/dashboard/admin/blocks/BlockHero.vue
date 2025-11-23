<template>
  <div class="relative min-h-[400px] flex items-center justify-center bg-slate-200 dark:bg-slate-700 rounded-lg" :style="heroStyle">
    <div class="text-center z-10 px-4">
      <h1 class="text-4xl font-bold text-white mb-4">{{ block.config.title || 'Hero Title' }}</h1>
      <p class="text-xl text-white mb-6">{{ block.config.subtitle || 'Hero Subtitle' }}</p>
      <button v-if="block.config.button_text" class="btn-primary bg-white text-primary">
        {{ block.config.button_text }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});

const heroStyle = computed(() => {
  if (props.block.config.background_image) {
    return {
      backgroundImage: `url(${props.block.config.background_image})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    };
  }
  return {
    backgroundColor: 'var(--brand-primary, #6366f1)',
  };
});
</script>

