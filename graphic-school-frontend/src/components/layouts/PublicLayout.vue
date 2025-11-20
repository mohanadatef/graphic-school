<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 relative">
    <header class="bg-white shadow-sm sticky top-0 z-50 relative w-full" style="pointer-events: auto;">
      <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <img v-if="settings.logo" :src="settings.logo" alt="logo" class="h-10 w-auto" />
          <div>
            <h1 class="text-xl font-bold text-slate-900">{{ settings.site_name || 'Graphic School' }}</h1>
            <p class="text-xs text-slate-500">{{ settings.address }}</p>
          </div>
        </div>
        <nav class="flex flex-wrap items-center gap-4 text-sm font-medium relative z-40" style="pointer-events: auto;">
          <RouterLink class="hover:text-primary" to="/">{{ $t('navigation.home') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/courses">{{ $t('navigation.courses') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/instructors">{{ $t('navigation.instructors') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/about">{{ $t('navigation.about') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/contact">{{ $t('navigation.contact') }}</RouterLink>
          <div class="relative z-50" style="pointer-events: auto;">
            <LanguageSwitcher />
          </div>
          <RouterLink 
            v-if="!authStore.isAuthenticated" 
            class="px-3 py-2 bg-primary text-white rounded-md relative z-50"
            style="pointer-events: auto; position: relative;"
            to="/login"
          >
            {{ $t('auth.login') }}
          </RouterLink>
          <button
            v-else
            type="button"
            id="dashboard-button"
            class="px-3 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200 active:bg-slate-300 transition-colors cursor-pointer relative z-50"
            style="pointer-events: auto; position: relative; touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
            @click.stop.prevent="goToDashboard"
            @mousedown.stop.prevent="goToDashboard"
            @touchstart.stop.prevent="goToDashboard"
            @mouseup.stop
          >
            {{ $t('navigation.dashboard') }}
          </button>
        </nav>
      </div>
    </header>

    <main class="flex-1">
      <RouterView />
    </main>

    <footer class="bg-slate-900 text-slate-100 mt-12">
      <div class="max-w-6xl mx-auto px-4 py-8 grid gap-4 md:grid-cols-3 text-sm">
        <div>
          <h3 class="font-semibold mb-2">عن جرافيك سكول</h3>
          <p class="text-slate-300 text-sm">
            {{ settings.about_us || 'أكاديمية متخصصة في تصميم الجرافيك والبراندنج.' }}
          </p>
        </div>
        <div>
          <h3 class="font-semibold mb-2">معلومات التواصل</h3>
          <p>الهاتف: {{ settings.phone || '010000000' }}</p>
          <p>الإيميل: {{ settings.email || 'info@graphicschool.com' }}</p>
          <p>العنوان: {{ settings.address || 'القاهرة - مصر' }}</p>
        </div>
        <div>
          <h3 class="font-semibold mb-2">تابعنا</h3>
          <p class="text-slate-300">ألوان الهوية: {{ settings.primary_color }} / {{ settings.secondary_color }}</p>
        </div>
      </div>
      <div class="text-center text-xs text-slate-500 border-t border-slate-800 py-3">
        جميع الحقوق محفوظة © {{ new Date().getFullYear() }} Graphic School
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, reactive } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import api from '../../api';
import { useAuthStore } from '../../stores/auth';
import LanguageSwitcher from '../common/LanguageSwitcher.vue';

const settings = reactive({});
const authStore = useAuthStore();
const router = useRouter();

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
  try {
    const { data } = await api.get('/settings');
    Object.assign(settings, data);
    if (data.primary_color) {
      document.documentElement.style.setProperty('--primary-color', data.primary_color);
    }
    if (data.secondary_color) {
      document.documentElement.style.setProperty('--secondary-color', data.secondary_color);
    }
  } catch (error) {
    console.error(error);
  }
  
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

