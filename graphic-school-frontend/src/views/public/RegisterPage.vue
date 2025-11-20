<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ $t('auth.register') }}</h2>
      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.name') }}</label>
          <input v-model="form.name" type="text" required class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.email') }}</label>
          <input v-model="form.email" type="email" required class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.phone') }}</label>
          <input v-model="form.phone" type="text" class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">{{ $t('auth.password') }}</label>
          <input v-model="form.password" type="password" required class="input" />
        </div>
        <button
          class="w-full py-3 bg-primary text-white rounded-md font-semibold"
          :disabled="authStore.loading"
        >
          {{ authStore.loading ? $t('auth.registering') : $t('auth.registerButton') }}
        </button>
        <p v-if="authStore.error" class="text-center text-red-500 text-sm">
          {{ authStore.error }}
        </p>
      </form>
      <p class="text-center text-sm text-slate-500 mt-4">
        {{ $t('auth.haveAccount') }} <RouterLink to="/login" class="text-primary">{{ $t('auth.loginNow') }}</RouterLink>
      </p>
      <p class="text-center text-xs text-slate-400 mt-2">
        <RouterLink to="/" class="text-primary underline">{{ $t('auth.backToHome') }}</RouterLink>
      </p>
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

