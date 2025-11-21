<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">الاختبارات</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
    </div>

    <div v-else-if="quizzes.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">لا توجد اختبارات متاحة</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="quiz in quizzes"
        :key="quiz.id"
        class="card-premium p-6 hover-lift"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ quiz.title }}</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-4">{{ quiz.description }}</p>
            <div class="flex items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
              <span v-if="quiz.time_limit">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ quiz.time_limit }} دقيقة
              </span>
              <span>
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                النجاح: {{ quiz.passing_score }}%
              </span>
              <span v-if="quiz.max_attempts">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ quiz.max_attempts }} محاولة
              </span>
            </div>
          </div>
          <div class="ml-4">
            <button
              @click="startQuiz(quiz)"
              class="btn-primary"
            >
              بدء الاختبار
            </button>
          </div>
        </div>
        <div v-if="quiz.attempts && quiz.attempts.length > 0" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">المحاولات السابقة:</p>
          <div class="space-y-2">
            <div
              v-for="attempt in quiz.attempts"
              :key="attempt.id"
              class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg"
            >
              <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                  النتيجة: {{ attempt.percentage || 0 }}% ({{ attempt.score || 0 }}/{{ attempt.total_points || 0 }})
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                  {{ formatDate(attempt.completed_at) }}
                </p>
              </div>
              <span
                :class="attempt.is_passed
                  ? 'px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                  : 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
              >
                {{ attempt.is_passed ? 'نجح' : 'فشل' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';

const router = useRouter();
const { get } = useApi();

const loading = ref(false);
const quizzes = ref([]);

async function loadQuizzes() {
  try {
    loading.value = true;
    const response = await get('/student/quizzes');
    quizzes.value = response?.data || response || [];
  } catch (err) {
    console.error('Error loading quizzes:', err);
    quizzes.value = [];
  } finally {
    loading.value = false;
  }
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}

function startQuiz(quiz) {
  router.push(`/dashboard/student/quizzes/${quiz.id}/attempt`);
}

onMounted(loadQuizzes);
</script>

