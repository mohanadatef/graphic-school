<template>
  <div id="app" :dir="isRTL ? 'rtl' : 'ltr'">
    <router-view v-slot="{ Component, route }">
      <keep-alive :include="keepAliveRoutes">
        <component :is="Component" :key="route.fullPath" />
      </keep-alive>
    </router-view>
    <ToastContainer />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useLocale } from './composables/useLocale';
import ToastContainer from './components/common/ToastContainer.vue';

const { isRTL } = useLocale();

// Routes that should be kept alive for performance
const keepAliveRoutes = computed(() => [
  'AdminDashboard',
  'AdminCourses',
  'AdminUsers',
  'InstructorCourses',
  'StudentCourses',
]);
</script>

<style>
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
