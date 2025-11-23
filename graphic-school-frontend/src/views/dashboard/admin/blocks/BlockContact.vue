<template>
  <div class="py-8">
    <h2 class="text-2xl font-bold text-center mb-8">{{ block.config.title || 'Contact Us' }}</h2>
    <div class="max-w-md mx-auto space-y-4">
      <div v-if="block.config.email" class="flex items-center gap-3">
        <i class="fas fa-envelope text-primary"></i>
        <span>{{ block.config.email }}</span>
      </div>
      <div v-if="block.config.phone" class="flex items-center gap-3">
        <i class="fas fa-phone text-primary"></i>
        <span>{{ block.config.phone }}</span>
      </div>
      <div v-if="block.config.location" class="flex items-center gap-3">
        <i class="fas fa-map-marker-alt text-primary"></i>
        <span>{{ block.config.location }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  block: { type: Object, required: true },
  editing: { type: Boolean, default: false },
});
</script>

