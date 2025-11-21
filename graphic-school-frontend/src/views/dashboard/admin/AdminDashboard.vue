<template>
  <div class="space-y-8">
    <!-- Header with Reports Link -->
    <div class="flex items-center justify-between flex-wrap gap-4">
      <h1 class="text-3xl font-black text-slate-900 dark:text-white">{{ $t('dashboard.quickStats') }}</h1>
      <div class="flex gap-2">
        <RouterLink
          to="/dashboard/admin/reports"
          class="btn-secondary inline-flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          التقارير الشاملة
        </RouterLink>
        <RouterLink
          to="/dashboard/admin/strategic-reports"
          class="btn-primary inline-flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          التقارير الاستراتيجية
        </RouterLink>
      </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div>
      <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div
          v-for="card in cards"
          :key="card.key"
          class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 group"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="p-2 rounded-lg bg-gradient-to-br" :class="card.gradient">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="card.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                <path v-else-if="card.icon === 'instructors'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                <path v-else-if="card.icon === 'courses'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                <path v-else-if="card.icon === 'sessions'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="text-right">
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ $t(card.labelKey) }}</p>
              <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:scale-105 transition-transform duration-300">
                {{ stats[card.key] ?? 0 }}
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Additional Stats -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
        <div
          v-for="stat in additionalStats"
          :key="stat.key"
          class="card-premium p-5 hover-lift"
        >
          <div class="flex items-center justify-between mb-3">
            <div class="p-2 rounded-lg bg-gradient-to-br" :class="stat.gradient">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="stat.icon === 'check'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else-if="stat.icon === 'calendar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <path v-else-if="stat.icon === 'money'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6 12H3.98M18 12h2.02M4.212 17.788l-1.424 1.424M19.192 5.01l1.424-1.424M6.344 7.656l-1.424-1.424M17.656 16.344l1.424 1.424" />
                <path v-else-if="stat.icon === 'pending'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else-if="stat.icon === 'chart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                <path v-else-if="stat.icon === 'clock'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else-if="stat.icon === 'check-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ stat.label }}</p>
          <p class="text-2xl font-black text-slate-900 dark:text-white">
            {{ stat.format === 'currency' ? formatCurrency(stats[stat.key] || 0) : (stats[stat.key] ?? 0) }}{{ stat.suffix || '' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Monthly Revenue Trend -->
    <div v-if="trends?.monthly_revenue?.length" class="card-premium p-6">
      <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">اتجاه الإيرادات الشهرية</h2>
      <div class="grid md:grid-cols-6 gap-4">
        <div
          v-for="month in trends.monthly_revenue"
          :key="month.month"
          class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900"
        >
          <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">{{ month.month_name }}</p>
          <p class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ formatCurrency(month.revenue || 0) }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ month.count || 0 }} تسجيل</p>
        </div>
      </div>
    </div>

    <!-- Top Courses -->
    <div v-if="topCourses?.length" class="card-premium p-6">
      <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">أفضل الكورسات أداءً</h2>
      <div class="grid md:grid-cols-5 gap-4">
        <div
          v-for="(course, index) in topCourses"
          :key="course.id"
          class="text-center p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 hover:scale-105 transition-transform duration-300"
        >
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary/80 text-white font-black text-lg mb-3">
            {{ index + 1 }}
          </div>
          <p class="font-bold text-slate-900 dark:text-white mb-2 text-sm line-clamp-2">{{ course.title }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">{{ course.students_count || 0 }} طالب</p>
          <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ formatCurrency(course.revenue || 0) }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow duration-300 p-6 space-y-4">
      <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $t('dashboard.coursePerformance') }}</h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('dashboard.filterByCategory') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
          <FilterDropdown
            v-model="filtersState.category_id"
            :options="filters.categories"
            placeholder="كل التصنيفات"
            @update:modelValue="resetAndReload"
          />
          <FilterDropdown
            v-model="filtersState.instructor_id"
            :options="filters.instructors"
            placeholder="كل المدربين"
            @update:modelValue="resetAndReload"
          />
          <FilterDropdown
            v-model="filtersState.status"
            :options="filters.statuses.map(s => ({ id: s, name: s }))"
            placeholder="كل الحالات"
            @update:modelValue="resetAndReload"
          />
        </div>
      </div>

      <div v-if="courseStore.loading" class="text-center py-8">
        <p class="text-slate-500">{{ $t('common.loading') }}</p>
      </div>

      <div v-else class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
        <table class="w-full text-sm">
          <thead class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 text-xs uppercase text-slate-600 dark:text-slate-400 font-semibold">
            <tr>
              <th class="px-6 py-4 text-left">{{ $t('courses.title') }}</th>
              <th class="px-6 py-4 text-left">{{ $t('courses.category') }}</th>
              <th class="px-6 py-4 text-left">{{ $t('courses.students') }}</th>
              <th class="px-6 py-4 text-left">{{ $t('courses.paidTotal') }}</th>
              <th class="px-6 py-4 text-left">{{ $t('courses.totalSessions') }}</th>
              <th class="px-6 py-4 text-left">{{ $t('courses.completed') }}</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
            <tr
              v-for="course in courses"
              :key="course.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 cursor-pointer"
            >
              <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ course.title }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                  {{ course.category?.name || '-' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ course.students_count ?? 0 }}</span>
              </td>
              <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(course.paid_total ?? 0) }}</td>
              <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ course.sessions_total ?? 0 }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full" :class="course.sessions_completed >= course.sessions_total ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'">
                  {{ course.sessions_completed ?? 0 }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationControls
        v-if="meta.total"
        :meta="meta"
        @change-page="changePage"
        @change-per-page="changePerPage"
      />
      <p v-else class="text-center text-sm text-slate-400 py-4">
        {{ $t('common.noData') }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { useCourseStore } from '../../../stores/course';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';
import { useI18n } from '../../../composables/useI18n';

const courseStore = useCourseStore();
const { t, locale } = useI18n();

const stats = ref({});
const filters = reactive({
  categories: [],
  instructors: [],
  statuses: [],
});
const filtersState = reactive({
  category_id: '',
  instructor_id: '',
  status: '',
  page: 1,
  per_page: 10,
});

const courses = ref([]);
const trends = ref({});
const topCourses = ref([]);
const meta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const cards = computed(() => [
  { labelKey: 'dashboard.students', key: 'students_count', icon: 'users', gradient: 'from-blue-500 to-blue-600' },
  { labelKey: 'dashboard.instructors', key: 'instructors_count', icon: 'instructors', gradient: 'from-purple-500 to-purple-600' },
  { labelKey: 'dashboard.activeCourses', key: 'active_courses', icon: 'courses', gradient: 'from-emerald-500 to-emerald-600' },
  { labelKey: 'dashboard.sessions', key: 'sessions_count', icon: 'sessions', gradient: 'from-orange-500 to-orange-600' },
  { labelKey: 'dashboard.attendanceRate', key: 'attendance_rate', icon: 'attendance', gradient: 'from-indigo-500 to-indigo-600', suffix: '%' },
]);

const additionalStats = computed(() => [
  { label: 'الجلسات المكتملة', key: 'sessions_completed', icon: 'check', gradient: 'from-green-500 to-green-600' },
  { label: 'الجلسات القادمة', key: 'sessions_upcoming', icon: 'calendar', gradient: 'from-blue-500 to-blue-600' },
  { label: 'إجمالي المبلغ', key: 'total_amount', icon: 'money', gradient: 'from-emerald-500 to-emerald-600', format: 'currency' },
  { label: 'المتبقي', key: 'pending_amount', icon: 'pending', gradient: 'from-orange-500 to-orange-600', format: 'currency' },
  { label: 'معدل التحصيل', key: 'collection_rate', icon: 'chart', gradient: 'from-purple-500 to-purple-600', suffix: '%' },
  { label: 'تسجيلات قيد الانتظار', key: 'enrollments_pending', icon: 'clock', gradient: 'from-yellow-500 to-yellow-600' },
  { label: 'تسجيلات معتمدة', key: 'enrollments_approved', icon: 'check-circle', gradient: 'from-green-500 to-green-600' },
  { label: 'تسجيلات مرفوضة', key: 'enrollments_rejected', icon: 'x-circle', gradient: 'from-red-500 to-red-600' },
]);

async function loadDashboard() {
  try {
    const data = await courseStore.fetchDashboardData({
      category_id: filtersState.category_id || undefined,
      instructor_id: filtersState.instructor_id || undefined,
      status: filtersState.status || undefined,
      page: filtersState.page,
      per_page: filtersState.per_page,
    });

    stats.value = data.stats || {};
    filters.categories = data.filters?.categories || [];
    filters.instructors = data.filters?.instructors || [];
    filters.statuses = data.filters?.statuses || [];
    courses.value = data.courses || [];
    trends.value = data.trends || {};
    topCourses.value = data.top_courses || [];
    Object.assign(meta, data.pagination || {});
  } catch (error) {
    // Error handled in store
  }
}

function changePage(page) {
  filtersState.page = page;
  loadDashboard();
}

function changePerPage(perPage) {
  filtersState.per_page = perPage;
  filtersState.page = 1;
  loadDashboard();
}

function formatCurrency(value) {
  return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
    style: 'currency',
    currency: 'EGP',
  }).format(value);
}

function resetAndReload() {
  filtersState.page = 1;
  loadDashboard();
}

watch(
  () => [filtersState.category_id, filtersState.instructor_id, filtersState.status],
  () => resetAndReload()
);

onMounted(loadDashboard);
</script>

