<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <header class="bg-white shadow-sm sticky top-0 z-30">
      <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <img v-if="settings.logo" :src="settings.logo" alt="logo" class="h-10 w-auto" />
          <div>
            <h1 class="text-xl font-bold text-slate-900">{{ settings.site_name || 'Graphic School' }}</h1>
            <p class="text-xs text-slate-500">{{ settings.address }}</p>
          </div>
        </div>
        <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
          <RouterLink class="hover:text-primary" to="/">{{ $t('navigation.home') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/courses">{{ $t('navigation.courses') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/instructors">{{ $t('navigation.instructors') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/about">{{ $t('navigation.about') }}</RouterLink>
          <RouterLink class="hover:text-primary" to="/contact">{{ $t('navigation.contact') }}</RouterLink>
          <LanguageSwitcher />
          <RouterLink v-if="!auth.state.token" class="px-3 py-2 bg-primary text-white rounded-md" to="/login">
            {{ $t('auth.login') }}
          </RouterLink>
          <button
            v-else
            class="px-3 py-2 bg-slate-100 text-slate-700 rounded-md"
            @click="goToDashboard"
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
import { useAuth } from '../../composables/useAuth';
import LanguageSwitcher from '../common/LanguageSwitcher.vue';

const settings = reactive({});
const auth = useAuth();
const router = useRouter();

function goToDashboard() {
  const role = auth.roleName?.value;
  if (!role) return;
  router.push(`/dashboard/${role}`);
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
});
</script>

