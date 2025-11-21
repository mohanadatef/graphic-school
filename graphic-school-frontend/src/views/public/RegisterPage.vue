<template>
  <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border-2 border-slate-200 dark:border-slate-700 p-8 md:p-10">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-primary/80 mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">{{ $t('auth.register') }}</h2>
          <p class="text-slate-600 dark:text-slate-400">أنشئ حسابك وابدأ رحلتك التعليمية معنا</p>
        </div>
        
        <form class="space-y-5" @submit.prevent="submit">
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 block">{{ $t('auth.name') }}</label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <input
                v-model="form.name"
                type="text"
                required
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.name')"
                autocomplete="name"
              />
            </div>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 block">{{ $t('auth.email') }}</label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <input
                v-model="form.email"
                type="email"
                required
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.email')"
                autocomplete="email"
              />
            </div>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 block">{{ $t('auth.phone') }}</label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <input
                v-model="form.phone"
                type="tel"
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.phone')"
                autocomplete="tel"
              />
            </div>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 block">{{ $t('auth.password') }}</label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <input
                v-model="form.password"
                type="password"
                required
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.password')"
                autocomplete="new-password"
              />
            </div>
          </div>
          <button
            type="submit"
            class="w-full btn-primary py-4 text-lg"
            :disabled="authStore.loading"
          >
            <span v-if="authStore.loading" class="inline-flex items-center gap-2">
              <span class="spinner"></span>
              {{ $t('auth.registering') }}
            </span>
            <span v-else class="inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              {{ $t('auth.registerButton') }}
            </span>
          </button>
          <div v-if="authStore.error" class="p-4 rounded-xl bg-red-50 border-2 border-red-200">
            <p class="text-sm font-medium text-red-700 text-center">{{ authStore.error }}</p>
          </div>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-200 space-y-4">
          <p class="text-center text-sm text-slate-600">
            {{ $t('auth.haveAccount') }}
            <RouterLink to="/login" class="font-semibold text-primary hover:underline">
              {{ $t('auth.loginNow') }}
            </RouterLink>
          </p>
          <RouterLink
            to="/"
            class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-primary transition-colors duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $t('auth.backToHome') || 'العودة للرئيسية' }}
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';
import { useI18n } from '../../composables/useI18n';

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();

const form = reactive({
  name: '',
  email: '',
  password: '',
  phone: '',
});

async function submit() {
  try {
    const user = await authStore.register(form);
    
    toast.success(t('auth.registerSuccess') || 'تم التسجيل بنجاح');
    
    // Get role from authStore or user
    const role = authStore.roleName || user?.role_name || user?.role?.name;
    
    if (role) {
      router.push(`/dashboard/${role}`);
    } else {
      console.warn('No role found, redirecting to home');
      router.push('/');
    }
  } catch (error) {
    // Error is handled in store and displayed
    console.error('Registration error:', error);
  }
}
</script>


