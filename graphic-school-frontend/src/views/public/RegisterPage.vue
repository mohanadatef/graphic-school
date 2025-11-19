<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow p-6">
      <h2 class="text-2xl font-bold text-slate-900 mb-4">حساب طالب جديد</h2>
      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm text-slate-500 block mb-1">الاسم الكامل</label>
          <input v-model="form.name" type="text" required class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">البريد الإلكتروني</label>
          <input v-model="form.email" type="email" required class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">رقم الهاتف</label>
          <input v-model="form.phone" type="text" class="input" />
        </div>
        <div>
          <label class="text-sm text-slate-500 block mb-1">كلمة المرور</label>
          <input v-model="form.password" type="password" required class="input" />
        </div>
        <button
          class="w-full py-3 bg-primary text-white rounded-md font-semibold"
          :disabled="auth.state.loading"
        >
          {{ auth.state.loading ? 'جاري إنشاء الحساب...' : 'تسجيل' }}
        </button>
        <p v-if="auth.state.error" class="text-center text-red-500 text-sm">
          {{ auth.state.error }}
        </p>
      </form>
      <p class="text-center text-sm text-slate-500 mt-4">
        لديك حساب بالفعل؟ <RouterLink to="/login" class="text-primary">تسجيل الدخول</RouterLink>
      </p>
      <p class="text-center text-xs text-slate-400 mt-2">
        <RouterLink to="/" class="text-primary underline">العودة للرئيسية</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

const auth = useAuth();
const router = useRouter();
const form = reactive({
  name: '',
  email: '',
  password: '',
  phone: '',
});

async function submit() {
  try {
    const user = await auth.register(form);
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

