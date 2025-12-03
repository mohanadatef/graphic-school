<template>
  <section class="py-24 bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-950 relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
      <div class="absolute top-20 left-10 w-72 h-72 bg-primary rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Section Header -->
      <div class="text-center mb-16 space-y-4">
        <div v-if="block.config?.badge" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
          <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
          {{ getLocalized(block.config.badge) }}
        </div>
        
        <h2 v-if="block.title" class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white mb-6">
          <span class="bg-gradient-to-r from-slate-900 via-slate-700 to-primary dark:from-white dark:via-slate-200 dark:to-primary bg-clip-text text-transparent">
            {{ getLocalized(block.title) }}
          </span>
        </h2>
        
        <p v-if="block.content" class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed">
          {{ getLocalized(block.content) }}
        </p>
      </div>
      
      <!-- Features Grid -->
      <div v-if="block.config?.features && block.config.features.length" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="(feature, index) in block.config.features"
          :key="index"
          class="group relative p-8 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/10"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <!-- Icon Container -->
          <div class="mb-6 relative">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/10 dark:from-primary/30 dark:to-primary/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
              <svg v-if="!feature.icon" class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <img v-else :src="feature.icon" :alt="getLocalized(feature.title)" class="w-8 h-8 object-contain" />
            </div>
            <!-- Decorative Circle -->
            <div class="absolute -top-2 -right-2 w-6 h-6 bg-primary/20 rounded-full blur-sm group-hover:bg-primary/40 transition-colors duration-300"></div>
          </div>
          
          <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 group-hover:text-primary transition-colors duration-300">
            {{ getLocalized(feature.title) }}
          </h3>
          
          <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
            {{ getLocalized(feature.description) }}
          </p>
          
          <!-- Link if available -->
          <a
            v-if="feature.link"
            :href="feature.link"
            class="inline-flex items-center gap-2 text-primary font-semibold hover:gap-3 transition-all duration-300 group/link"
          >
            <span>{{ getLocalized(feature.link_text) || 'Learn more' }}</span>
            <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
          
          <!-- Hover Effect Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300 pointer-events-none"></div>
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

.group {
  animation: fade-in-up 0.6s ease-out forwards;
  opacity: 0;
}
</style>

