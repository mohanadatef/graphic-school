<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary/20 to-slate-900 dark:from-slate-950 dark:via-primary/30 dark:to-slate-950 text-white min-h-[85vh] flex items-center">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-primary/10 to-blue-500/10 rounded-full blur-3xl animate-pulse delay-2000"></div>
    </div>
    
    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 w-full">
      <div class="text-center space-y-8 animate-fade-in-up">
        <!-- Badge -->
        <div v-if="block.config?.badge" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-sm font-medium text-white/90 mb-4 animate-fade-in">
          <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
          {{ block.config.badge }}
        </div>
        
        <h2 v-if="block.title" class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight">
          <span class="bg-gradient-to-r from-white via-white to-primary/80 bg-clip-text text-transparent animate-gradient">
            {{ block.title }}
          </span>
        </h2>
        
        <p v-if="block.content" class="text-xl md:text-2xl lg:text-3xl text-slate-200 dark:text-slate-300 mb-12 max-w-4xl mx-auto leading-relaxed font-light">
          {{ block.content }}
        </p>
        
        <div v-if="block.config?.cta_text" class="flex flex-wrap justify-center gap-4 mt-12">
          <RouterLink
            :to="block.config.cta_link || '#'"
            class="group relative px-10 py-5 bg-gradient-to-r from-primary via-primary to-primary/90 text-white font-bold rounded-2xl hover:shadow-2xl hover:shadow-primary/50 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 overflow-hidden"
          >
            <span class="relative z-10 flex items-center gap-2">
              {{ block.config.cta_text }}
              <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-primary/80 to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </RouterLink>
          
          <RouterLink
            v-if="block.config?.secondary_cta_text"
            :to="block.config.secondary_cta_link || '#'"
            class="px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/30 text-white font-semibold rounded-2xl hover:bg-white/20 hover:border-white/50 transition-all duration-300 transform hover:scale-105"
          >
            {{ block.config.secondary_cta_text }}
          </RouterLink>
        </div>
        
        <!-- Stats or Features Preview -->
        <div v-if="block.config?.stats && block.config.stats.length" class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl mx-auto">
          <div v-for="(stat, index) in block.config.stats" :key="index" class="text-center animate-fade-in-up" :style="{ animationDelay: `${index * 100}ms` }">
            <div class="text-4xl md:text-5xl font-bold text-primary mb-2">{{ stat.value }}</div>
            <div class="text-sm md:text-base text-slate-300">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
      <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
      </svg>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';

defineProps({
  block: {
    type: Object,
    required: true,
  },
});
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

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes gradient {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.animate-fade-in {
  animation: fade-in 1s ease-out forwards;
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 3s ease infinite;
}

.delay-1000 {
  animation-delay: 1s;
}

.delay-2000 {
  animation-delay: 2s;
}
</style>

