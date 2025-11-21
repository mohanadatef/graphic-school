<template>
  <div class="space-y-6">
    <!-- Lesson Header -->
    <div class="card-premium p-6">
      <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2">
            <RouterLink
              :to="`/dashboard/student/courses/${courseId}?enrollment=${enrollmentId}`"
              class="text-primary hover:underline text-sm"
            >
              ← العودة للكورس
            </RouterLink>
          </div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2">{{ lesson?.title }}</h1>
          <p class="text-slate-600 dark:text-slate-400">{{ lesson?.description }}</p>
        </div>
        <div v-if="lesson?.is_preview" class="px-4 py-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold">
          معاينة مجانية
        </div>
      </div>
    </div>

    <!-- Video Player -->
    <div v-if="lesson?.lesson_type === 'video' && lesson?.video_url" class="card-premium p-6">
      <div class="aspect-video bg-slate-900 rounded-lg overflow-hidden">
        <iframe
          v-if="lesson.video_provider === 'youtube'"
          :src="getYouTubeEmbedUrl(lesson.video_url)"
          class="w-full h-full"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
        <video
          v-else
          :src="lesson.video_url"
          controls
          class="w-full h-full"
          @timeupdate="updateProgress"
          @ended="markComplete"
        ></video>
      </div>
    </div>

    <!-- Text Content -->
    <div v-if="lesson?.content" class="card-premium p-6">
      <div class="prose dark:prose-invert max-w-none" v-html="lesson.content"></div>
    </div>

    <!-- Resources -->
    <div v-if="lesson?.resources && lesson.resources.length > 0" class="card-premium p-6">
      <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">الموارد المرفقة</h3>
      <div class="space-y-3">
        <div
          v-for="resource in lesson.resources"
          :key="resource.id"
          class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-lg"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg bg-primary/10">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
              </svg>
            </div>
            <div>
              <p class="font-semibold text-slate-900 dark:text-white">{{ resource.title }}</p>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ resource.description }}</p>
            </div>
          </div>
          <a
            v-if="resource.external_url"
            :href="resource.external_url"
            target="_blank"
            class="btn-primary text-sm"
          >
            فتح الرابط
          </a>
          <a
            v-else-if="resource.file_path && resource.is_downloadable"
            :href="resource.file_path"
            download
            class="btn-primary text-sm"
          >
            تحميل
          </a>
        </div>
      </div>
    </div>

    <!-- Quiz Link -->
    <div v-if="lesson?.has_quiz || lesson?.quiz" class="card-premium p-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">اختبار الدرس</h3>
          <p class="text-slate-600 dark:text-slate-400">اختبر فهمك للدرس من خلال الاختبار</p>
        </div>
        <button
          @click="openQuiz"
          class="btn-primary"
        >
          بدء الاختبار
        </button>
      </div>
    </div>

    <!-- Project Submission -->
    <div v-if="lesson?.lesson_type === 'project'" class="card-premium p-6">
      <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">تقديم المشروع</h3>
      <p class="text-slate-600 dark:text-slate-400 mb-4">قم بتقديم مشروعك للدرس</p>
      <button
        @click="openProjectSubmission"
        class="btn-primary"
      >
        تقديم المشروع
      </button>
    </div>

    <!-- Navigation -->
    <div class="flex items-center justify-between">
      <button
        v-if="previousLesson"
        @click="goToLesson(previousLesson)"
        class="btn-secondary"
      >
        ← الدرس السابق
      </button>
      <div v-else></div>
      
      <button
        v-if="!isCompleted"
        @click="markComplete"
        class="btn-primary"
      >
        إكمال الدرس
      </button>
      <div
        v-else
        class="px-6 py-3 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-semibold flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        مكتمل
      </div>

      <button
        v-if="nextLesson"
        @click="goToLesson(nextLesson)"
        class="btn-primary"
      >
        الدرس التالي →
      </button>
      <div v-else></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const { get, post, put } = useApi();
const toast = useToast();

const lessonId = computed(() => route.params.id);
const courseId = computed(() => route.query.course);
const enrollmentId = computed(() => route.query.enrollment);

const loading = ref(false);
const lesson = ref(null);
const curriculum = ref(null);
const isCompleted = ref(false);
const currentProgress = ref(0);

const previousLesson = computed(() => {
  if (!curriculum.value || !lesson.value) return null;
  
  const modules = curriculum.value?.modules || curriculum.value?.data?.modules || [];
  const allLessons = modules.flatMap(m => m.lessons || []);
  const currentIndex = allLessons.findIndex(l => l.id === lesson.value.id);
  
  if (currentIndex > 0) {
    return allLessons[currentIndex - 1];
  }
  return null;
});

const nextLesson = computed(() => {
  if (!curriculum.value || !lesson.value) return null;
  
  const modules = curriculum.value?.modules || curriculum.value?.data?.modules || [];
  const allLessons = modules.flatMap(m => m.lessons || []);
  const currentIndex = allLessons.findIndex(l => l.id === lesson.value.id);
  
  if (currentIndex >= 0 && currentIndex < allLessons.length - 1) {
    return allLessons[currentIndex + 1];
  }
  return null;
});

async function loadLesson() {
  try {
    loading.value = true;
    
    // Load curriculum to get lesson
    const curriculumResponse = await get(`/student/courses/${courseId.value}/curriculum`);
    const curriculumData = curriculumResponse?.data || curriculumResponse;
    curriculum.value = curriculumData;
    
    // Find lesson from modules
    const modules = curriculumData?.modules || curriculumData?.data?.modules || [];
    const allLessons = modules.flatMap(m => {
      const moduleLessons = m.lessons || [];
      return Array.isArray(moduleLessons) ? moduleLessons : [];
    });
    lesson.value = allLessons.find(l => l.id === parseInt(lessonId.value));
    
    if (!lesson.value) {
      toast.error('الدرس غير موجود');
      return;
    }
    
    // Load progress
    if (enrollmentId.value) {
      try {
        const progressResponse = await get(`/student/enrollments/${enrollmentId.value}/progress`);
        const progress = progressResponse?.data || progressResponse;
        const progressByModule = progress.progress_by_module || [];
        
        // Check if lesson is completed
        isCompleted.value = progressByModule
          .flatMap(m => {
            // Handle different data structures
            if (m.lessons && Array.isArray(m.lessons)) {
              return m.lessons.map(l => typeof l === 'object' ? l : { lesson_id: l, is_completed: false });
            }
            return [];
          })
          .some(l => l.lesson_id === parseInt(lessonId.value) && l.is_completed);
      } catch (progressErr) {
        console.warn('Could not load progress:', progressErr);
        isCompleted.value = false;
      }
    }
  } catch (err) {
    console.error('Error loading lesson:', err);
    toast.error('حدث خطأ أثناء تحميل الدرس');
  } finally {
    loading.value = false;
  }
}

async function updateProgress(event) {
  if (!enrollmentId.value || !lesson.value) return;
  
  const video = event.target;
  const percentage = Math.floor((video.currentTime / video.duration) * 100);
  
  if (percentage > currentProgress.value) {
    currentProgress.value = percentage;
    
    try {
      await put(`/student/enrollments/${enrollmentId.value}/lessons/${lessonId.value}/progress`, {
        percentage,
        time_spent: Math.floor(video.currentTime),
      });
    } catch (err) {
      console.error('Error updating progress:', err);
    }
  }
}

async function markComplete() {
  if (!enrollmentId.value || !lesson.value) return;
  
  try {
    await post(`/student/enrollments/${enrollmentId.value}/lessons/${lessonId.value}/complete`);
    isCompleted.value = true;
    toast.success('تم إكمال الدرس بنجاح');
    
    // Auto navigate to next lesson
    if (nextLesson.value) {
      setTimeout(() => {
        goToLesson(nextLesson.value);
      }, 1500);
    }
  } catch (err) {
    console.error('Error marking lesson complete:', err);
    toast.error('حدث خطأ أثناء إكمال الدرس');
  }
}

function goToLesson(lesson) {
  router.push(`/dashboard/student/lessons/${lesson.id}?enrollment=${enrollmentId.value}&course=${courseId.value}`);
}

function openQuiz() {
  if (lesson.value?.quiz?.id) {
    router.push(`/dashboard/student/quizzes/${lesson.value.quiz.id}/attempt`);
  } else {
    toast.warning('لا يوجد اختبار لهذا الدرس');
  }
}

function openProjectSubmission() {
  router.push(`/dashboard/student/projects?lesson=${lesson.value?.id}&course=${courseId.value}`);
}

function getYouTubeEmbedUrl(url) {
  const videoId = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/)?.[1];
  return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
}

onMounted(loadLesson);
</script>

