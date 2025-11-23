<template>
  <div class="py-8" v-html="block.config.content || '<p>Custom HTML content</p>'"></div>
</template>

<script setup>
defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});
</script>

