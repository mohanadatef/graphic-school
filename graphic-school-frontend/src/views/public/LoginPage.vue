<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ $t('auth.login') }}</h2>
      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.email') }}</label>
          <input v-model="email" type="email" required class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.password') }}</label>
          <input v-model="password" type="password" required class="input" />
        </div>
        <button
          class="w-full py-3 bg-primary text-white rounded-md font-semibold"
          :disabled="auth.state.loading"
        >
          {{ auth.state.loading ? $t('auth.loggingIn') : $t('auth.loginButton') }}
        </button>
        <p v-if="auth.state.error" class="text-center text-red-500 text-sm">
          {{ auth.state.error }}
        </p>
      </form>
      <p class="text-center text-sm text-slate-500 mt-4">
        {{ $t('auth.noAccount') }} <RouterLink to="/register" class="text-primary">{{ $t('auth.registerNow') }}</RouterLink>
      </p>
      <p class="text-center text-xs text-slate-400 mt-2">
        <RouterLink to="/" class="text-primary underline">{{ $t('auth.backToHome') }}</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

const auth = useAuth();
const router = useRouter();
const email = ref('');
const password = ref('');

async function submit() {
  try {
    const user = await auth.login({ email: email.value, password: password.value });
    router.push(`/dashboard/${user.role_name || user.role?.name}`);
  } catch (error) {
    // handled in auth
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
}

.input:focus {
  border-color: var(--primary-color, #1d4ed8);
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
}
</style>

