<template>
  <div class="max-w-4xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-slate-900 mb-4">عن أكاديمية جرافيك سكول</h1>
    <p class="text-slate-600 leading-relaxed whitespace-pre-line">
      {{ settings.about_us || defaultAbout }}
    </p>
    <div class="grid md:grid-cols-3 gap-6 mt-10">
      <div class="bg-white p-5 rounded-2xl shadow">
        <p class="text-sm text-slate-400">الهاتف</p>
        <p class="text-lg font-semibold text-slate-900">{{ settings.phone }}</p>
      </div>
      <div class="bg-white p-5 rounded-2xl shadow">
        <p class="text-sm text-slate-400">البريد الإلكتروني</p>
        <p class="text-lg font-semibold text-slate-900">{{ settings.email }}</p>
      </div>
      <div class="bg-white p-5 rounded-2xl shadow">
        <p class="text-sm text-slate-400">العنوان</p>
        <p class="text-lg font-semibold text-slate-900">{{ settings.address }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import api from '../../api';
import { useBrandingStore } from '../../stores/branding';

const settings = reactive({});
const brandingStore = useBrandingStore();
const defaultAbout = computed(() => 
  `${brandingStore.displayName} هو مركز تدريبي متخصص في تعليم التصميم الجرافيكي والبراندنج، مع متابعة دقيقة للحضور والواجبات لكل طالب، وتوفير تقييمات للمدربين بعد انتهاء كل كورس.`
);

onMounted(async () => {
  const { data } = await api.get('/settings');
  Object.assign(settings, data);
});
</script>

