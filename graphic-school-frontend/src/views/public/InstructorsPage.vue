<template>
  <div class="max-w-6xl mx-auto px-4 py-10">
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">طاقم المدربين والمشرفين</h2>
        <p class="text-slate-500 text-sm">يمكن تعيين أكثر من مدرب ومشرف لكل كورس.</p>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <article
          v-for="instructor in instructors"
          :key="instructor.id"
          class="bg-white border border-slate-100 rounded-xl p-5 shadow hover:-translate-y-1 transition cursor-pointer"
          @click="$router.push({ name: 'instructor-details', params: { id: instructor.id } })"
        >
          <div class="flex items-center gap-3 mb-3">
            <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-lg font-bold">
              {{ instructor.name?.charAt(0) }}
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-slate-900">{{ instructor.name }}</h3>
              <p class="text-xs text-slate-500">{{ instructor.email }}</p>
            </div>
          </div>
          <p class="text-sm text-slate-600 line-clamp-4 mb-4">
            {{ instructor.bio || 'مدرب معتمد في Graphic School' }}
          </p>
          <div class="flex items-center justify-between text-xs text-slate-500 mb-3">
            <span>التقييم: {{ instructor.average_rating }}</span>
            <span>عدد الآراء: {{ instructor.reviews_count }}</span>
          </div>
          <RouterLink
            :to="{ name: 'instructor-details', params: { id: instructor.id } }"
            class="text-sm text-primary hover:underline font-medium"
            @click.stop
          >
            عرض الملف الشخصي →
          </RouterLink>
        </article>
      </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../api';

const instructors = ref([]);

onMounted(async () => {
  try {
    const response = await api.get('/instructors');
    
    // Handle unified API response format
    if (response.data) {
      if (Array.isArray(response.data)) {
        instructors.value = response.data;
      } else if (response.data.data && Array.isArray(response.data.data)) {
        instructors.value = response.data.data;
      }
    }
    
    // Debug: Log instructors to check if they have IDs
    console.log('Instructors loaded:', instructors.value);
  } catch (error) {
    console.error('Error loading instructors:', error);
    instructors.value = [];
  }
});
</script>

