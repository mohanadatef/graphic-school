<template>
  <div class="py-12 text-center" style="background-color: var(--brand-primary, #6366f1); color: white; border-radius: 0.5rem;">
    <h2 class="text-3xl font-bold mb-4">{{ block.config.title || 'Call to Action' }}</h2>
    <p class="text-lg mb-6">{{ block.config.description || 'Description text' }}</p>
    <button v-if="block.config.button_text" class="bg-white text-primary px-6 py-3 rounded-lg font-semibold">
      {{ block.config.button_text }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});
</script>

