<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold">كورساتي</h2>
    <div class="grid md:grid-cols-2 gap-4">
      <article
        v-for="course in courses"
        :key="course.id"
        class="bg-white rounded-2xl border border-slate-100 p-5 shadow"
      >
        <h3 class="text-lg font-semibold text-slate-900">{{ course.title }}</h3>
        <p class="text-sm text-slate-500">{{ course.category?.name }}</p>
        <p class="text-sm text-slate-600 mt-3 line-clamp-3">{{ course.description }}</p>
        <p class="text-xs text-slate-400 mt-2">يبدأ في: {{ formatDate(course.start_date) }}</p>
      </article>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../../../api';

const courses = ref([]);

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

onMounted(async () => {
  const { data } = await api.get('/instructor/courses');
  courses.value = data;
});
</script>

