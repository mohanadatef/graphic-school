<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 relative">
    <header class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md shadow-sm sticky top-0 z-50 relative w-full border-b border-slate-200/50 dark:border-slate-700/50" style="pointer-events: auto;">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-4">
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div v-if="settings.logo" class="h-12 w-12 rounded-xl overflow-hidden shadow-md group-hover:shadow-lg transition-shadow duration-300">
            <img :src="settings.logo" alt="logo" class="h-full w-full object-cover" />
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors duration-200">
              {{ brandingStore.displayName }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ settings.address }}</p>
          </div>
        </RouterLink>
        <nav class="flex flex-wrap items-center gap-2 sm:gap-4 text-sm font-medium relative z-40" style="pointer-events: auto;">
          <RouterLink
            v-if="enabledPages.home !== false"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/"
          >
            {{ $t('navigation.home') }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.programs !== false"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/programs"
          >
            {{ $t('navigation.programs') || 'Programs' }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.about !== false"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/about"
          >
            {{ $t('navigation.about') }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.contact !== false"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/contact"
          >
            {{ $t('navigation.contact') }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.community !== false"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/community"
          >
            {{ $t('navigation.community') || 'Community' }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <RouterLink
            v-if="enabledPages.faq === true"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all duration-200 relative group text-slate-700 dark:text-slate-300"
            active-class="text-primary bg-primary/10 dark:bg-primary/20"
            to="/faq"
          >
            {{ $t('navigation.faq') || 'FAQ' }}
            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
          </RouterLink>
          <div class="relative z-50" style="pointer-events: auto;">
            <ThemeToggle />
          </div>
          <div class="relative z-50" style="pointer-events: auto;">
            <LanguageSwitcher />
          </div>
          <RouterLink 
            v-if="!authStore.isAuthenticated" 
            class="px-4 py-2 bg-gradient-to-r from-primary to-primary/90 text-white rounded-lg font-medium hover:shadow-lg hover:scale-105 transition-all duration-200 relative z-50 shadow-md"
            style="pointer-events: auto; position: relative;"
            to="/login"
          >
            {{ $t('auth.login') }}
          </RouterLink>
          <button
            v-else
            type="button"
            id="dashboard-button"
            class="px-4 py-2 bg-gradient-to-r from-slate-100 to-slate-50 text-slate-700 rounded-lg font-medium hover:from-slate-200 hover:to-slate-100 active:scale-95 transition-all duration-200 cursor-pointer relative z-50 shadow-sm hover:shadow-md"
            style="pointer-events: auto; position: relative; touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
            @click.stop.prevent="goToDashboard"
            @mousedown.stop.prevent="goToDashboard"
            @touchstart.stop.prevent="goToDashboard"
            @mouseup.stop
          >
            <span class="inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              {{ $t('navigation.dashboard') }}
            </span>
          </button>
        </nav>
      </div>
    </header>

    <main class="flex-1">
      <RouterView />
    </main>

    <footer class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-slate-100 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid gap-8 md:grid-cols-4 text-sm">
        <div class="md:col-span-2">
          <div class="flex items-center gap-3 mb-4">
            <div v-if="settings.logo" class="h-12 w-12 rounded-xl overflow-hidden">
              <img :src="settings.logo" alt="logo" class="h-full w-full object-cover" />
            </div>
            <h3 class="text-xl font-bold">{{ brandingStore.displayName }}</h3>
          </div>
          <p class="text-slate-300 leading-relaxed max-w-md">
            {{ settings.about_us || 'أكاديمية متخصصة في تصميم الجرافيك والبراندنج. نقدم تجربة تعليمية شاملة تجمع بين الإبداع والتكنولوجيا.' }}
          </p>
        </div>
        <div>
          <h3 class="font-semibold mb-4 text-white">معلومات التواصل</h3>
          <ul class="space-y-2 text-slate-300">
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              {{ settings.phone || '010000000' }}
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              {{ settings.email || `info@${brandingStore.displayName.toLowerCase().replace(/\s/g, '')}.com` }}
            </li>
            <li class="flex items-center gap-2">
              <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ settings.address || 'القاهرة - مصر' }}
            </li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold mb-4 text-white">روابط سريعة</h3>
          <ul class="space-y-2">
            <li>
              <RouterLink to="/courses" class="text-slate-300 hover:text-primary transition-colors duration-200">
                الكورسات
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/instructors" class="text-slate-300 hover:text-primary transition-colors duration-200">
                المدربين
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/about" class="text-slate-300 hover:text-primary transition-colors duration-200">
                من نحن
              </RouterLink>
            </li>
            <li>
              <RouterLink to="/contact" class="text-slate-300 hover:text-primary transition-colors duration-200">
                اتصل بنا
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>
      <div class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
          <p>جميع الحقوق محفوظة © {{ new Date().getFullYear() }} {{ brandingStore.displayName }}</p>
          <div class="flex items-center gap-4">
            <a href="#" class="hover:text-primary transition-colors duration-200">سياسة الخصوصية</a>
            <a href="#" class="hover:text-primary transition-colors duration-200">شروط الاستخدام</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, reactive, computed } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useBrandingStore } from '../../stores/branding';
import { useWebsiteSettingsStore } from '../../stores/websiteSettings';
import LanguageSwitcher from '../common/LanguageSwitcher.vue';
import ThemeToggle from '../common/ThemeToggle.vue';

const authStore = useAuthStore();
const brandingStore = useBrandingStore();
const websiteStore = useWebsiteSettingsStore();
const router = useRouter();

const settings = computed(() => websiteStore.settings?.general_info || {});
const enabledPages = computed(() => websiteStore.settings?.enabled_pages || {});

function goToDashboard(event) {
  // Prevent any default behavior
  if (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
  }
  
  console.log('Dashboard button clicked', event);
  console.log('Auth store state:', {
    isAuthenticated: authStore.isAuthenticated,
    roleName: authStore.roleName,
    user: authStore.user,
    token: authStore.token ? 'exists' : 'missing',
  });
  
  // Force immediate execution
  setTimeout(() => {
    try {
      // Check if user is authenticated
      if (!authStore.isAuthenticated) {
        console.warn('User not authenticated, redirecting to login');
        router.push('/login');
        return;
      }
      
      // Get role from auth store
      const role = authStore.roleName || authStore.user?.role_name || authStore.user?.role?.name;
      
      console.log('User role:', role);
      
      if (!role) {
        console.warn('No role found for user, redirecting to login');
        router.push('/login');
        return;
      }
      
      // Navigate to dashboard based on role
      const dashboardPath = `/dashboard/${role}`;
      console.log('Navigating to:', dashboardPath);
      router.push(dashboardPath).catch(err => {
        console.error('Navigation error:', err);
        router.push('/login');
      });
    } catch (error) {
      console.error('Error navigating to dashboard:', error);
      router.push('/login');
    }
  }, 0);
}

onMounted(async () => {
  // Load website settings
  await websiteStore.loadSettings();
  
  // Load branding
  await brandingStore.fetchBranding();
  
  // Add direct event listener as fallback
  const dashboardButton = document.getElementById('dashboard-button');
  if (dashboardButton) {
    dashboardButton.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log('Direct event listener triggered');
      goToDashboard(e);
    }, { capture: true });
    
    dashboardButton.addEventListener('mousedown', (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log('Direct mousedown listener triggered');
      goToDashboard(e);
    }, { capture: true });
  }
});
</script>

