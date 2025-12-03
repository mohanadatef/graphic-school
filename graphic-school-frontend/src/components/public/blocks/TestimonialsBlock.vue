<template>
  <section class="py-16 bg-slate-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 v-if="block.title" class="text-3xl md:text-4xl font-bold text-center text-slate-900 dark:text-white mb-12">
        {{ block.title }}
      </h2>
      <div v-if="block.config?.testimonials && block.config.testimonials.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(testimonial, index) in block.config.testimonials"
          :key="index"
          class="p-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700"
        >
          <p class="text-slate-600 dark:text-slate-400 mb-4">
            {{ getLocalized(testimonial.content) }}
          </p>
          <div class="flex items-center gap-3">
            <div v-if="testimonial.avatar" class="w-12 h-12 rounded-full overflow-hidden">
              <img :src="testimonial.avatar" :alt="getLocalized(testimonial.name)" class="w-full h-full object-cover" />
            </div>
            <div>
              <p class="font-medium text-slate-900 dark:text-white">
                {{ getLocalized(testimonial.name) }}
              </p>
              <p v-if="testimonial.role" class="text-sm text-slate-500 dark:text-slate-400">
                {{ getLocalized(testimonial.role) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useI18n } from '../../../composables/useI18n';

const { locale } = useI18n();

defineProps({
  block: {
    type: Object,
    required: true,
  },
});

function getLocalized(value) {
  if (typeof value === 'string') return value;
  if (typeof value === 'object' && value) {
    return value[locale.value] || value.en || value[Object.keys(value)[0]] || '';
  }
  return '';
}
</script>

