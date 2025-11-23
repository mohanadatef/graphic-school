<template>
  <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border-2 border-slate-200 dark:border-slate-700 p-8 md:p-10">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-primary/80 mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">{{ $t('auth.login') }}</h2>
          <p class="text-slate-600 dark:text-slate-400">مرحباً بعودتك! سجّل دخولك للمتابعة</p>
        </div>
        
        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label for="email" class="text-sm font-semibold text-slate-700 block">
              {{ $t('auth.email') }}
            </label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <input
                id="email"
                v-model="email"
                type="email"
                required
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.email')"
                autocomplete="email"
              />
            </div>
          </div>
          <div class="space-y-2">
            <label for="password" class="text-sm font-semibold text-slate-700 block">
              {{ $t('auth.password') }}
            </label>
            <div class="relative">
              <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <input
                id="password"
                v-model="password"
                type="password"
                required
                class="input-enhanced pl-4 pr-12"
                :placeholder="$t('auth.password')"
                autocomplete="current-password"
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
              {{ $t('common.loading') }}
            </span>
            <span v-else class="inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              {{ $t('auth.login') }}
            </span>
          </button>
          <div v-if="authStore.error" class="p-4 rounded-xl bg-red-50 border-2 border-red-200">
            <p class="text-sm font-medium text-red-700 text-center">{{ authStore.error }}</p>
          </div>
        </form>
        
        <div class="mt-8 pt-6 border-t border-slate-200 space-y-4">
          <p class="text-center text-sm text-slate-600">
            {{ $t('auth.noAccount') }}
            <RouterLink to="/register" class="font-semibold text-primary hover:underline">
              {{ $t('auth.register') }}
            </RouterLink>
          </p>
          <RouterLink
            to="/"
            class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-primary transition-colors duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ $t('nav.home') || 'العودة للرئيسية' }}
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';
import { useI18n } from '../../composables/useI18n';

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();

const email = ref('');
const password = ref('');

// Redirect if already authenticated
onMounted(() => {
  if (authStore.isAuthenticated) {
    const redirectPath = authStore.afterLoginRedirect();
    router.replace(redirectPath);
  }
});

async function handleSubmit() {
  try {
    await authStore.login({
      email: email.value,
      password: password.value,
    });

    // Use the centralized redirect function
    const redirectPath = authStore.afterLoginRedirect();
    router.push(redirectPath);
  } catch (e) {
    // error handled by store
  }
}
</script>

