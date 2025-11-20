<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ $t('auth.login') }}</h2>
      <form class="space-y-4" @submit.prevent="handleSubmit">
        <div>
          <label for="email" class="text-sm text-slate-500 block mb-1">
            {{ $t('auth.email') }}
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="input"
            :placeholder="$t('auth.email')"
            autocomplete="email"
          />
        </div>
        <div>
          <label for="password" class="text-sm text-slate-500 block mb-1">
            {{ $t('auth.password') }}
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="input"
            :placeholder="$t('auth.password')"
            autocomplete="current-password"
          />
        </div>
        <button
          type="submit"
          class="w-full py-3 bg-primary text-white rounded-md font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="authStore.loading"
        >
          {{ authStore.loading ? $t('common.loading') : $t('auth.login') }}
        </button>
        <p v-if="authStore.error" class="text-center text-red-500 text-sm">
          {{ authStore.error }}
        </p>
      </form>
      <p class="text-center text-sm text-slate-500 mt-4">
        {{ $t('auth.noAccount') }}
        <RouterLink to="/register" class="text-primary hover:underline">
          {{ $t('auth.register') }}
        </RouterLink>
      </p>
      <p class="text-center text-xs text-slate-400 mt-2">
        <RouterLink to="/" class="text-primary underline hover:no-underline">
          {{ $t('nav.home') }}
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
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

async function handleSubmit() {
  try {
    const user = await authStore.login({
      email: email.value,
      password: password.value,
    });
    
    toast.success(t('auth.loginSuccess'));
    
    // Get role from user or authStore
    const role = authStore.roleName || user?.role_name || user?.role?.name;
    
    if (role) {
      router.push(`/dashboard/${role}`);
    } else {
      console.warn('No role found, redirecting to home');
      router.push('/');
    }
  } catch (error) {
    // Error is handled in store and displayed
  }
}
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.input:focus {
  border-color: var(--primary-color, #1d4ed8);
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
}
</style>
