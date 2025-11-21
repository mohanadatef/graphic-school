<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">مشاريعي</h1>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">جاري التحميل...</p>
    </div>

    <div v-else-if="projects.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-slate-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <p class="text-slate-500 dark:text-slate-400 text-lg">لا توجد مشاريع حتى الآن</p>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="project in projects"
        :key="project.id"
        class="card-premium p-6 hover-lift"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ project.title }}</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-2">{{ project.description }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">
              الكورس: {{ project.course?.title }}
            </p>
          </div>
          <span
            :class="getStatusClass(project.status)"
            class="px-3 py-1 rounded-full text-xs font-semibold"
          >
            {{ getStatusText(project.status) }}
          </span>
        </div>
        
        <div v-if="project.score !== null" class="mb-4 p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
          <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">النتيجة:</span>
            <span class="text-2xl font-black text-primary">{{ project.score }}%</span>
          </div>
        </div>
        
        <div v-if="project.instructor_feedback" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">ملاحظات المدرب:</p>
          <p class="text-sm text-slate-600 dark:text-slate-400">{{ project.instructor_feedback }}</p>
        </div>
        
        <div class="flex items-center gap-2">
          <span class="text-xs text-slate-500 dark:text-slate-400">
            تم التقديم: {{ formatDate(project.submitted_at) }}
          </span>
          <span v-if="project.reviewed_at" class="text-xs text-slate-500 dark:text-slate-400">
            تم المراجعة: {{ formatDate(project.reviewed_at) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '../../../composables/useApi';

const { get } = useApi();

const loading = ref(false);
const projects = ref([]);

async function loadProjects() {
  try {
    loading.value = true;
    const response = await get('/student/projects');
    projects.value = response?.data || response || [];
  } catch (err) {
    console.error('Error loading projects:', err);
    projects.value = [];
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

function getStatusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    submitted: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    in_review: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
    approved: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
    needs_revision: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
    rejected: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
  };
  return classes[status] || classes.pending;
}

function getStatusText(status) {
  const texts = {
    pending: 'قيد الانتظار',
    submitted: 'تم التقديم',
    in_review: 'قيد المراجعة',
    approved: 'مقبول',
    needs_revision: 'يحتاج مراجعة',
    rejected: 'مرفوض',
  };
  return texts[status] || status;
}

onMounted(loadProjects);
</script>

