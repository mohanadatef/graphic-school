<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="text-center mb-12">
        <p class="text-sm font-semibold text-primary mb-3">فريقنا المتميز</p>
        <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">طاقم المدربين والمشرفين</h2>
        <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">خبراء في مجالاتهم، ملتزمون بمساعدتك في تحقيق أهدافك التعليمية</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="instructor in instructors"
          :key="instructor.id"
          class="group bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer overflow-hidden relative"
          @click="$router.push({ name: 'instructor-details', params: { id: instructor.id } })"
        >
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative">
            <div class="flex items-start gap-4 mb-4">
              <div class="relative">
                <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-2xl font-black text-white shadow-lg group-hover:scale-110 transition-transform duration-300">
                  {{ instructor.name?.charAt(0) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center">
                  <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 pt-2">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors duration-200">{{ instructor.name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3">{{ instructor.email }}</p>
                <div class="flex items-center gap-2 mb-2">
                  <div class="flex items-center">
                    <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= Math.round(instructor.average_rating || 0) ? 'text-amber-400' : 'text-slate-300'" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="text-sm font-semibold text-slate-700">{{ instructor.average_rating || '0.0' }}</span>
                  <span class="text-xs text-slate-500">({{ instructor.reviews_count || 0 }} تقييم)</span>
                </div>
              </div>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-3 mb-4 leading-relaxed">
              {{ instructor.bio || `مدرب معتمد في ${brandingStore.displayName} مع سنوات من الخبرة في مجال التصميم الجرافيكي` }}
            </p>
            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
              <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                  {{ instructor.courses_count || 0 }} كورس
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  {{ instructor.students_count || 0 }} طالب
                </span>
              </div>
              <RouterLink
                :to="{ name: 'instructor-details', params: { id: instructor.id } }"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary hover:bg-primary/10 rounded-lg transition-all duration-200"
                @click.stop
              >
                عرض الملف الشخصي
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </RouterLink>
            </div>
          </div>
        </article>
      </div>
      <div v-if="instructors.length === 0" class="text-center py-20">
        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <p class="text-lg text-slate-500">لا يوجد مدربين متاحين حالياً</p>
      </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../../api';
import { useBrandingStore } from '../../stores/branding';

const instructors = ref([]);
const brandingStore = useBrandingStore();

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

