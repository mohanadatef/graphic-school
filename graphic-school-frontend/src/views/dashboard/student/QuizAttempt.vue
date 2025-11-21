<template>
  <div class="space-y-6">
    <div class="card-premium p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2">{{ quiz?.title }}</h1>
          <p class="text-slate-600 dark:text-slate-400">{{ quiz?.description }}</p>
        </div>
        <div v-if="quiz?.time_limit" class="text-right">
          <p class="text-sm text-slate-500 dark:text-slate-400">الوقت المتبقي</p>
          <p class="text-2xl font-black text-primary">{{ formatTime(timeRemaining) }}</p>
        </div>
      </div>
      <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
        <span>النجاح: {{ quiz?.passing_score }}%</span>
        <span v-if="quiz?.max_attempts">المحاولات: {{ attemptsCount }} / {{ quiz.max_attempts }}</span>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
    </div>

    <form v-else @submit.prevent="submitQuiz" class="space-y-6">
      <div
        v-for="(question, index) in quiz?.questions || []"
        :key="question.id"
        class="card-premium p-6"
      >
        <div class="mb-4">
          <div class="flex items-start justify-between mb-2">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
              سؤال {{ index + 1 }}: {{ question.question }}
            </h3>
            <span class="text-sm text-slate-500 dark:text-slate-400">({{ question.points }} نقطة)</span>
          </div>
        </div>

        <!-- Multiple Choice -->
        <div v-if="question.type === 'multiple_choice'" class="space-y-2">
          <label
            v-for="(option, optIndex) in question.options"
            :key="optIndex"
            class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
            :class="answers[question.id] === optIndex
              ? 'border-primary bg-primary/5'
              : 'border-slate-200 dark:border-slate-700 hover:border-primary/50'"
          >
            <input
              type="radio"
              :name="`question_${question.id}`"
              :value="optIndex"
              v-model="answers[question.id]"
              class="w-5 h-5 text-primary"
            />
            <span class="flex-1 text-slate-700 dark:text-slate-300">{{ option }}</span>
          </label>
        </div>

        <!-- True/False -->
        <div v-else-if="question.type === 'true_false'" class="space-y-2">
          <label
            v-for="option in ['صحيح', 'خطأ']"
            :key="option"
            class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
            :class="answers[question.id] === option
              ? 'border-primary bg-primary/5'
              : 'border-slate-200 dark:border-slate-700 hover:border-primary/50'"
          >
            <input
              type="radio"
              :name="`question_${question.id}`"
              :value="option"
              v-model="answers[question.id]"
              class="w-5 h-5 text-primary"
            />
            <span class="flex-1 text-slate-700 dark:text-slate-300">{{ option }}</span>
          </label>
        </div>

        <!-- Short Answer -->
        <div v-else-if="question.type === 'short_answer'">
          <textarea
            v-model="answers[question.id]"
            rows="3"
            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:text-white"
            placeholder="اكتب إجابتك هنا..."
          ></textarea>
        </div>

        <!-- Essay -->
        <div v-else-if="question.type === 'essay'">
          <textarea
            v-model="answers[question.id]"
            rows="6"
            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:text-white"
            placeholder="اكتب إجابتك التفصيلية هنا..."
          ></textarea>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <button
          type="button"
          @click="$router.back()"
          class="btn-secondary"
        >
          إلغاء
        </button>
        <button
          type="submit"
          :disabled="submitting"
          class="btn-primary"
        >
          <span v-if="submitting">جاري الإرسال...</span>
          <span v-else>تقديم الاختبار</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const { get, post } = useApi();
const toast = useToast();

const quizId = ref(parseInt(route.params.quizId));
const loading = ref(false);
const submitting = ref(false);
const quiz = ref(null);
const answers = ref({});
const timeRemaining = ref(0);
const attemptsCount = ref(0);
let timer = null;

async function loadQuiz() {
  try {
    loading.value = true;
    const response = await get(`/student/quizzes/${quizId.value}`);
    quiz.value = response?.data || response;
    
    // Load attempts count
    try {
      const attemptsResponse = await get(`/student/quizzes/${quizId.value}/attempts`);
      attemptsCount.value = attemptsResponse?.data?.attempts_count || attemptsResponse?.attempts_count || 0;
    } catch (err) {
      console.warn('Could not load attempts:', err);
    }

    // Start timer if time limit exists
    if (quiz.value?.time_limit) {
      timeRemaining.value = quiz.value.time_limit * 60; // Convert to seconds
      startTimer();
    }
  } catch (err) {
    console.error('Error loading quiz:', err);
    toast.error('حدث خطأ أثناء تحميل الاختبار');
  } finally {
    loading.value = false;
  }
}

function startTimer() {
  timer = setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--;
    } else {
      clearInterval(timer);
      submitQuiz(true); // Auto submit when time runs out
    }
  }, 1000);
}

function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
}

async function submitQuiz(autoSubmit = false) {
  if (autoSubmit) {
    toast.warning('انتهى الوقت! سيتم تقديم الاختبار تلقائياً');
  }

  try {
    submitting.value = true;
    if (timer) {
      clearInterval(timer);
    }

    const response = await post(`/student/quizzes/${quizId.value}/submit`, {
      answers: answers.value,
    });

    const attempt = response?.data || response;
    
    if (attempt.is_passed) {
      toast.success(`مبروك! نجحت في الاختبار بنتيجة ${attempt.percentage}%`);
    } else {
      toast.warning(`لم تنجح في الاختبار. نتيجتك: ${attempt.percentage}%`);
    }

    // Navigate back after 2 seconds
    setTimeout(() => {
      router.push('/dashboard/student/quizzes');
    }, 2000);
  } catch (err) {
    console.error('Error submitting quiz:', err);
    toast.error(err.response?.data?.message || 'حدث خطأ أثناء تقديم الاختبار');
  } finally {
    submitting.value = false;
  }
}

onMounted(loadQuiz);
onUnmounted(() => {
  if (timer) {
    clearInterval(timer);
  }
});
</script>

