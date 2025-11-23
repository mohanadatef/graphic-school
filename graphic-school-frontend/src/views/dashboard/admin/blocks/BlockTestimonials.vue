<template>
  <div class="py-8">
    <h2 class="text-2xl font-bold text-center mb-8">{{ block.config.title || 'Testimonials' }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="i in 3" :key="i" class="bg-slate-50 dark:bg-slate-700 p-6 rounded-lg">
        <p class="text-slate-700 dark:text-slate-300 mb-4">"Testimonial text will be loaded dynamically"</p>
        <p class="font-semibold">Student Name</p>
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

