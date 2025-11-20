<template>
  <div class="space-y-8">
    <div>
      <h1 class="text-2xl font-bold text-slate-900 mb-6">{{ $t('dashboard.quickStats') }}</h1>
      <div class="grid md:grid-cols-5 gap-4">
        <div
          v-for="card in cards"
          :key="card.key"
          class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm"
        >
          <p class="text-sm text-slate-500 mb-2">{{ $t(card.labelKey) }}</p>
          <p class="text-3xl font-bold text-slate-900">{{ stats[card.key] ?? 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow p-6 space-y-4">
      <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-semibold">{{ $t('dashboard.coursePerformance') }}</h2>
          <p class="text-sm text-slate-500">{{ $t('dashboard.filterByCategory') }}</p>
        </div>
        <div class="flex flex-wrap gap-2 text-sm">
          <select
            v-model="filtersState.category_id"
            class="input-filter w-40"
            :aria-label="$t('courses.category')"
          >
            <option value="">{{ $t('courses.allCategories') }}</option>
            <option
              v-for="category in filters.categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
          <select
            v-model="filtersState.instructor_id"
            class="input-filter w-48"
            :aria-label="$t('courses.instructors')"
          >
            <option value="">{{ $t('dashboard.allInstructors') }}</option>
            <option
              v-for="instructor in filters.instructors"
              :key="instructor.id"
              :value="instructor.id"
            >
              {{ instructor.name }}
            </option>
          </select>
          <select
            v-model="filtersState.status"
            class="input-filter w-36"
            :aria-label="$t('common.status')"
          >
            <option value="">{{ $t('dashboard.allStatuses') }}</option>
            <option v-for="status in filters.statuses" :key="status" :value="status">
              {{ status }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="courseStore.loading" class="text-center py-8">
        <p class="text-slate-500">{{ $t('common.loading') }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th class="px-4 py-3 text-left">{{ $t('courses.title') }}</th>
              <th class="px-4 py-3 text-left">{{ $t('courses.category') }}</th>
              <th class="px-4 py-3 text-left">{{ $t('courses.students') }}</th>
              <th class="px-4 py-3 text-left">{{ $t('courses.paidTotal') }}</th>
              <th class="px-4 py-3 text-left">{{ $t('courses.totalSessions') }}</th>
              <th class="px-4 py-3 text-left">{{ $t('courses.completed') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="course in courses" :key="course.id" class="border-b border-slate-100">
              <td class="px-4 py-3 font-semibold text-slate-900">{{ course.title }}</td>
              <td class="px-4 py-3 text-slate-500">{{ course.category?.name || '-' }}</td>
              <td class="px-4 py-3">{{ course.students_count ?? 0 }}</td>
              <td class="px-4 py-3">{{ formatCurrency(course.paid_total ?? 0) }}</td>
              <td class="px-4 py-3">{{ course.sessions_total ?? 0 }}</td>
              <td class="px-4 py-3">{{ course.sessions_completed ?? 0 }}</td>
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
import { useCourseStore } from '../../../stores/course';
import PaginationControls from '../../../components/common/PaginationControls.vue';
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
const meta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const cards = computed(() => [
  { labelKey: 'dashboard.students', key: 'students_count' },
  { labelKey: 'dashboard.instructors', key: 'instructors_count' },
  { labelKey: 'dashboard.activeCourses', key: 'active_courses' },
  { labelKey: 'dashboard.sessions', key: 'sessions_count' },
  { labelKey: 'dashboard.attendanceRate', key: 'attendance_rate' },
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

<style scoped>
.input-filter {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.45rem 0.75rem;
  background: #fff;
  outline: none;
  transition: border-color 0.2s;
}

.input-filter:focus {
  border-color: var(--primary-color, #1d4ed8);
}
</style>
