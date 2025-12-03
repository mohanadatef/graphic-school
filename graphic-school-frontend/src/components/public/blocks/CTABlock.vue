<template>
  <section class="relative py-24 overflow-hidden">
    <!-- Gradient Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-blue-600 dark:from-primary dark:via-primary/80 dark:to-blue-700"></div>
    
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-white/5 rounded-full blur-3xl animate-pulse delay-2000"></div>
    </div>
    
    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff12_1px,transparent_1px),linear-gradient(to_bottom,#ffffff12_1px,transparent_1px)] bg-[size:24px_24px] opacity-30"></div>
    
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
      <div class="space-y-8 animate-fade-in-up">
        <!-- Badge -->
        <div v-if="block.config?.badge" class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-md rounded-full border border-white/30 text-sm font-semibold text-white mb-4">
          <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
          {{ getLocalized(block.config.badge) }}
        </div>
        
        <h2 v-if="block.title" class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight">
          {{ getLocalized(block.title) }}
        </h2>
        
        <p v-if="block.content" class="text-xl md:text-2xl text-white/90 mb-12 max-w-3xl mx-auto leading-relaxed font-light">
          {{ getLocalized(block.content) }}
        </p>
        
        <div v-if="block.config?.cta_text" class="flex flex-wrap justify-center gap-6 mt-12">
          <RouterLink
            :to="block.config.cta_link || '#'"
            class="group relative px-10 py-5 bg-white text-primary font-bold rounded-2xl hover:shadow-2xl hover:shadow-white/30 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 overflow-hidden"
          >
            <span class="relative z-10 flex items-center gap-3">
              {{ getLocalized(block.config.cta_text) }}
              <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 to-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </RouterLink>
          
          <RouterLink
            v-if="block.config?.secondary_cta_text"
            :to="block.config.secondary_cta_link || '#'"
            class="px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/40 text-white font-semibold rounded-2xl hover:bg-white/20 hover:border-white/60 transition-all duration-300 transform hover:scale-105"
          >
            {{ getLocalized(block.config.secondary_cta_text) }}
          </RouterLink>
        </div>
        
        <!-- Additional Info -->
        <div v-if="block.config?.additional_info" class="mt-12 text-white/80 text-sm">
          {{ getLocalized(block.config.additional_info) }}
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';
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

<style scoped>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.delay-1000 {
  animation-delay: 1s;
}

.delay-2000 {
  animation-delay: 2s;
}
</style>

