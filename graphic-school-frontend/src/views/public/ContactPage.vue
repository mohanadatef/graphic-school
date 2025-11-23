<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
      <p class="text-sm font-semibold text-primary mb-3">نحن هنا لمساعدتك</p>
      <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">تواصل معنا</h2>
      <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        أرسل سؤالك أو استفسارك وسيصل مباشرة إلى فريقنا. نحن نرد خلال 24 ساعة.
      </p>
    </div>
    
    <div class="grid lg:grid-cols-2 gap-12">
      <div class="space-y-8">
        <div>
          <h3 class="text-2xl font-bold text-slate-900 mb-6">معلومات التواصل</h3>
          <div class="space-y-6">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gradient-to-br from-slate-50 to-white border border-slate-200 hover:shadow-md transition-shadow duration-200">
              <div class="p-3 rounded-xl bg-primary/10">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">الهاتف</p>
                <a :href="`tel:${settings.phone}`" class="text-lg font-bold text-slate-900 dark:text-white hover:text-primary transition-colors duration-200">
                  {{ settings.phone || '010000000' }}
                </a>
              </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow duration-200">
              <div class="p-3 rounded-xl bg-primary/10 dark:bg-primary/20">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">البريد الإلكتروني</p>
                <a :href="`mailto:${settings.email}`" class="text-lg font-bold text-slate-900 dark:text-white hover:text-primary transition-colors duration-200">
                  {{ settings.email || `info@${brandingStore.displayName.toLowerCase().replace(/\s/g, '')}.com` }}
                </a>
              </div>
            </div>
            <div class="flex items-start gap-4 p-4 rounded-xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 hover:shadow-md transition-shadow duration-200">
              <div class="p-3 rounded-xl bg-primary/10">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">العنوان</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ settings.address || 'القاهرة - مصر' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <form class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border-2 border-slate-200 dark:border-slate-700 p-8 space-y-6" @submit.prevent="submit">
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-2">الاسم</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="input-enhanced"
            placeholder="أدخل اسمك الكامل"
          />
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-2">البريد الإلكتروني</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="input-enhanced"
            placeholder="example@email.com"
          />
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-2">الهاتف</label>
          <input
            v-model="form.phone"
            type="tel"
            class="input-enhanced"
            placeholder="01000000000"
          />
        </div>
        <div>
          <label class="text-sm font-semibold text-slate-700 block mb-2">الرسالة</label>
          <textarea
            v-model="form.message"
            required
            rows="5"
            class="input-enhanced resize-none"
            placeholder="اكتب رسالتك هنا..."
          ></textarea>
        </div>
        <button
          type="submit"
          class="w-full btn-primary py-4 text-lg"
          :disabled="submitting"
        >
          <span v-if="submitting" class="inline-flex items-center gap-2">
            <span class="spinner"></span>
            جاري الإرسال...
          </span>
          <span v-else class="inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            إرسال الرسالة
          </span>
        </button>
        <div v-if="success" class="p-4 rounded-xl bg-emerald-50 border-2 border-emerald-200">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-semibold text-emerald-700">تم إرسال رسالتك بنجاح! سنرد عليك قريباً.</p>
          </div>
        </div>
      </form>
    </div>
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


