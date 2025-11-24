<template>
  <div class="min-h-[60vh] flex items-center justify-center px-4" data-e2e-not-found="true">
    <div class="text-center max-w-2xl">
      <div class="mb-8">
        <h1 class="text-9xl font-black text-primary/20 dark:text-primary/10 mb-4">404</h1>
        <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">
          {{ $t('errors.pageNotFound') || 'Page Not Found' }}
        </h2>
        <p class="text-lg text-slate-600 dark:text-slate-400 mb-8">
          {{ $t('errors.pageNotFoundDescription') || 'The page you are looking for does not exist.' }}
        </p>
      </div>
      
      <div class="flex flex-wrap gap-4 justify-center">
        <RouterLink
          to="/"
          class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium"
        >
          {{ $t('errors.backToHome') || 'Back to Home' }}
        </RouterLink>
        <button
          @click="$router.go(-1)"
          class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors font-medium"
        >
          {{ $t('errors.goBack') || 'Go Back' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>

