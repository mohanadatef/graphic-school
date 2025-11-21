<template>
  <div id="app" :dir="isRTL ? 'rtl' : 'ltr'">
    <ErrorBoundary>
      <router-view v-slot="{ Component, route }">
        <keep-alive :include="keepAliveRoutes">
          <component :is="Component" :key="route.fullPath" />
        </keep-alive>
      </router-view>
      <ToastContainer />
    </ErrorBoundary>
  </div>
</template>

<script setup>
import { computed, onErrorCaptured } from 'vue';
import { useLocale } from './composables/useLocale';
import ToastContainer from './components/common/ToastContainer.vue';
import ErrorBoundary from './components/common/ErrorBoundary.vue';
import { logError } from './utils/monitoring';

const { isRTL } = useLocale();

// Routes that should be kept alive for performance
const keepAliveRoutes = computed(() => [
  'AdminDashboard',
  'AdminCourses',
  'AdminUsers',
  'InstructorCourses',
  'StudentCourses',
]);

// Global error handler
onErrorCaptured((err, instance, info) => {
  logError(err, {
    type: 'vue_error',
    component: instance?.$?.type?.name || 'Unknown',
    info,
  });
  
  // Return false to prevent error from propagating
  return false;
});

// Global unhandled error handler
if (typeof window !== 'undefined') {
  window.addEventListener('error', (event) => {
    logError(event.error || new Error(event.message), {
      type: 'global_error',
      filename: event.filename,
      lineno: event.lineno,
      colno: event.colno,
    });
  });

  window.addEventListener('unhandledrejection', (event) => {
    logError(event.reason || new Error('Unhandled Promise Rejection'), {
      type: 'unhandled_promise_rejection',
    });
  });
}
</script>

<style>
@import './style.css';

#app {
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* Prevent layout shifts */
* {
  box-sizing: border-box;
}
</style>
