<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
    <div class="max-w-6xl mx-auto px-4 py-10">
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-3xl font-bold text-slate-900">كل الكورسات</h2>
          <p class="text-slate-500 text-sm">اختر التصنيف أو ابحث عن كورس معين.</p>
        </div>
        <select v-model="selectedCategory" class="border border-slate-200 rounded-md px-3 py-2 text-sm">
          <option value="">كل التصنيفات</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="grid md:grid-cols-3 gap-6">
        <article
          v-for="course in filteredCourses"
          :key="course.id"
          class="bg-white rounded-xl border border-slate-100 shadow-sm flex flex-col overflow-hidden"
        >
          <div class="h-40 bg-slate-100 flex items-center justify-center text-slate-400">
            {{ course.category?.name }}
          </div>
          <div class="p-4 flex-1 flex flex-col">
            <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ course.title }}</h3>
            <p class="text-sm text-slate-600 line-clamp-3 flex-1">{{ course.description }}</p>
            <div class="text-sm text-slate-500 mt-3">
              عدد المدربين: {{ course.instructors?.length || 0 }}
            </div>
            <div class="mt-4 flex items-center justify-between">
              <span class="font-semibold text-primary">{{ course.price }} ج.م</span>
              <RouterLink :to="`/courses/${course.id}`" class="text-sm text-primary">التفاصيل</RouterLink>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../api';

const courses = ref([]);
const categories = ref([]);
const selectedCategory = ref('');

const filteredCourses = computed(() => {
  if (!selectedCategory.value) return courses.value;
  return courses.value.filter((course) => course.category_id === Number(selectedCategory.value));
});

async function loadCourses() {
  const { data } = await api.get('/courses', {
    params: {
      category_id: selectedCategory.value || undefined,
    },
  });
  courses.value = data;
}

onMounted(async () => {
  const [{ data: courseData }, { data: categoryData }] = await Promise.all([
    api.get('/courses'),
    api.get('/categories'),
  ]);
  courses.value = courseData;
  categories.value = categoryData;
});

watch(selectedCategory, loadCourses);
</script>

