<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold">كورساتي</h2>
    <div class="grid md:grid-cols-2 gap-4">
      <article
        v-for="enrollment in courses"
        :key="enrollment.id"
        class="bg-white rounded-2xl border border-slate-100 p-5 shadow"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-primary">{{ enrollment.course.category?.name }}</p>
            <h3 class="text-lg font-semibold text-slate-900">{{ enrollment.course.title }}</h3>
          </div>
          <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">
            {{ enrollment.payment_status }}
          </span>
        </div>
        <p class="text-sm text-slate-600 mt-3 line-clamp-3">{{ enrollment.course.description }}</p>
        <p class="text-xs text-slate-400 mt-2">
          مواعيد: {{ (enrollment.course.days_of_week || []).join(' - ') }}
        </p>
      </article>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../../../api';

const courses = ref([]);

onMounted(async () => {
  const { data } = await api.get('/student/courses');
  courses.value = data;
});
</script>

