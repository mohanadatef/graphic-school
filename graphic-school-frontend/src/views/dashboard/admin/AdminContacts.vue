<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold">رسائل تواصل معنا</h2>
        <p class="text-sm text-slate-500">الوارد من صفحة التواصل.</p>
      </div>
      <button class="px-4 py-2 border rounded-md" @click="loadMessages">تحديث</button>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <article
        v-for="message in messages"
        :key="message.id"
        class="bg-white p-5 rounded-2xl border border-slate-100 shadow flex flex-col gap-2"
      >
        <div class="flex items-center justify-between">
          <p class="font-semibold text-slate-900">{{ message.name }}</p>
          <span class="text-xs text-slate-500">{{ formatDate(message.created_at) }}</span>
        </div>
        <p class="text-xs text-primary">{{ message.email }}</p>
        <p class="text-sm text-slate-600">{{ message.message }}</p>
        <div class="flex items-center justify-between text-xs">
          <span>{{ message.phone }}</span>
          <button
            class="px-3 py-1 rounded-full"
            :class="message.is_resolved ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
            @click="resolve(message)"
          >
            {{ message.is_resolved ? 'تم الرد' : 'تحديد كمكتمل' }}
          </button>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../../../api';

const messages = ref([]);

async function loadMessages() {
  const { data } = await api.get('/admin/contacts');
  messages.value = Array.isArray(data.data) ? data.data : data;
}

async function resolve(message) {
  if (message.is_resolved) return;
  await api.post(`/admin/contacts/${message.id}/resolve`);
  loadMessages();
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleString('ar-EG');
}

onMounted(loadMessages);
</script>

