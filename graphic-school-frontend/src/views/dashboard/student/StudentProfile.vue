<template>
  <div class="max-w-3xl space-y-6">
    <h2 class="text-2xl font-bold">ملفي الشخصي</h2>
    <form class="bg-white rounded-2xl border border-slate-100 shadow p-6 space-y-4" @submit.prevent="submit">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="label">رقم الطالب</label>
          <input :value="form.id" class="input disabled" disabled />
        </div>
        <div>
          <label class="label">الاسم</label>
          <input v-model="form.name" class="input" />
        </div>
        <div>
          <label class="label">البريد</label>
          <input :value="form.email" disabled class="input disabled" />
        </div>
        <div>
          <label class="label">الهاتف</label>
          <input v-model="form.phone" class="input" />
        </div>
        <div>
          <label class="label">العنوان</label>
          <input v-model="form.address" class="input" />
        </div>
        <div class="md:col-span-2">
          <label class="label">نبذة</label>
          <textarea v-model="form.bio" rows="3" class="input"></textarea>
        </div>
      </div>
      <button class="px-5 py-2 bg-primary text-white rounded-md" :disabled="saving">
        {{ saving ? 'جاري الحفظ...' : 'حفظ' }}
      </button>
      <p v-if="saved" class="text-green-600 text-sm">تم تحديث الملف بنجاح</p>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../../api';

const form = reactive({
  id: '',
  name: '',
  email: '',
  phone: '',
  address: '',
  bio: '',
});
const saving = ref(false);
const saved = ref(false);

async function loadProfile() {
  const { data } = await api.get('/student/profile');
  Object.assign(form, data);
}

async function submit() {
  saving.value = true;
  await api.put('/student/profile', {
    name: form.name,
    phone: form.phone,
    address: form.address,
    bio: form.bio,
  });
  saving.value = false;
  saved.value = true;
  setTimeout(() => (saved.value = false), 2000);
}

onMounted(loadProfile);
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.6rem 0.9rem;
}
.disabled {
  background: #f8fafc;
  color: #94a3b8;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

