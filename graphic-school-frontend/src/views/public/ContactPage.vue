<template>
  <div class="max-w-4xl mx-auto px-4 py-12 grid md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-3xl font-bold text-slate-900 mb-4">تواصل معنا</h2>
      <p class="text-slate-600 mb-6">
        أرسل سؤالك أو استفسارك وسيصل مباشرة إلى لوحة التحكم الخاصة بالإدارة.
      </p>
      <ul class="space-y-4 text-sm text-slate-600">
        <li><span class="font-semibold text-slate-900">الهاتف:</span> {{ settings.phone }}</li>
        <li><span class="font-semibold text-slate-900">الإيميل:</span> {{ settings.email }}</li>
        <li><span class="font-semibold text-slate-900">العنوان:</span> {{ settings.address }}</li>
      </ul>
    </div>
    <form class="bg-white rounded-2xl shadow p-6 space-y-4" @submit.prevent="submit">
      <div>
        <label class="text-sm text-slate-500 block mb-1">الاسم</label>
        <input v-model="form.name" type="text" required class="input" />
      </div>
      <div>
        <label class="text-sm text-slate-500 block mb-1">البريد الإلكتروني</label>
        <input v-model="form.email" type="email" required class="input" />
      </div>
      <div>
        <label class="text-sm text-slate-500 block mb-1">الهاتف</label>
        <input v-model="form.phone" type="text" class="input" />
      </div>
      <div>
        <label class="text-sm text-slate-500 block mb-1">الرسالة</label>
        <textarea v-model="form.message" required rows="4" class="input"></textarea>
      </div>
      <button
        class="w-full py-3 bg-primary text-white rounded-md font-semibold"
        :disabled="submitting"
      >
        {{ submitting ? 'جاري الإرسال...' : 'إرسال' }}
      </button>
      <p v-if="success" class="text-green-600 text-sm text-center">تم الإرسال بنجاح</p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import api from '../../api';

const settings = reactive({});
const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: '',
});
const submitting = ref(false);
const success = ref(false);

async function submit() {
  submitting.value = true;
  success.value = false;
  try {
    await api.post('/contact', form);
    success.value = true;
    form.name = '';
    form.email = '';
    form.phone = '';
    form.message = '';
  } catch (error) {
    alert(error.response?.data?.message || 'حدث خطأ اثناء الارسال');
  } finally {
    submitting.value = false;
  }
}

onMounted(async () => {
  const { data } = await api.get('/settings');
  Object.assign(settings, data);
});
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

