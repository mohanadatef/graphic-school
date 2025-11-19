<template>
  <div class="space-y-6 max-w-3xl">
    <div>
      <h2 class="text-2xl font-bold">إعدادات الموقع</h2>
      <p class="text-sm text-slate-500">تحديث بيانات الاتصال والهوية.</p>
    </div>

    <form class="bg-white rounded-2xl shadow border border-slate-100 p-6 space-y-4" @submit.prevent="submit">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="label">اسم الموقع</label>
          <input v-model="form.site_name" class="input" />
        </div>
        <div>
          <label class="label">البريد الإلكتروني</label>
          <input v-model="form.email" class="input" />
        </div>
        <div>
          <label class="label">رقم الهاتف</label>
          <input v-model="form.phone" class="input" />
        </div>
        <div>
          <label class="label">العنوان</label>
          <input v-model="form.address" class="input" />
        </div>
        <div>
          <label class="label">اللون الأساسي</label>
          <input v-model="form.primary_color" type="color" class="input h-12" />
        </div>
        <div>
          <label class="label">اللون الثانوي</label>
          <input v-model="form.secondary_color" type="color" class="input h-12" />
        </div>
        <div class="md:col-span-2">
          <label class="label">عن الأكاديمية</label>
          <textarea v-model="form.about_us" rows="4" class="input"></textarea>
        </div>
        <div class="md:col-span-2">
          <label class="label">شعار الموقع</label>
          <div class="flex items-center gap-4">
            <img v-if="logoPreview" :src="logoPreview" alt="logo" class="h-16 w-16 rounded-lg object-cover border" />
            <input type="file" accept="image/*" class="input" @change="handleLogoChange" />
          </div>
          <p class="text-xs text-slate-400 mt-1">يدعم صور PNG / JPG بحد أقصى 4MB</p>
        </div>
      </div>
      <button class="px-5 py-3 bg-primary text-white rounded-md">حفظ التغييرات</button>
      <p v-if="saved" class="text-green-600 text-sm mt-2">تم التحديث بنجاح</p>
    </form>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import api from '../../../api';

const form = reactive({
  site_name: '',
  email: '',
  phone: '',
  address: '',
  about_us: '',
  primary_color: '#1d4ed8',
  secondary_color: '#f97316',
});
const saved = ref(false);
const logoPreview = ref('');
const logoFile = ref(null);

async function loadSettings() {
  const { data } = await api.get('/admin/settings');
  form.site_name = data.site_name || '';
  form.email = data.email || '';
  form.phone = data.phone || '';
  form.address = data.address || '';
  form.about_us = data.about_us || '';
  form.primary_color = data.primary_color || form.primary_color;
  form.secondary_color = data.secondary_color || form.secondary_color;
  logoPreview.value = data.logo_url || data.logo || '';
}

function handleLogoChange(event) {
  const file = event.target.files?.[0];
  if (!file) {
    logoFile.value = null;
    return;
  }
  logoFile.value = file;
  logoPreview.value = URL.createObjectURL(file);
}

async function submit() {
  const payload = new FormData();
  Object.entries(form).forEach(([key, value]) => {
    payload.append(key, value ?? '');
  });
  if (logoFile.value) {
    payload.append('logo_image', logoFile.value);
  }
  await api.post('/admin/settings', payload, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  saved.value = true;
  setTimeout(() => (saved.value = false), 2000);
}

onMounted(loadSettings);
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

