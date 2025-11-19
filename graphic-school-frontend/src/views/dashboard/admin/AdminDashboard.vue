<template>
  <div class="space-y-8">
    <div>
      <h1 class="text-2xl font-bold text-slate-900 mb-6">تقارير سريعة</h1>
      <div class="grid md:grid-cols-5 gap-4">
        <div
          v-for="card in cards"
          :key="card.label"
          class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm"
        >
          <p class="text-sm text-slate-500 mb-2">{{ card.label }}</p>
          <p class="text-3xl font-bold text-slate-900">{{ stats[card.key] ?? 0 }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow p-6 space-y-4">
      <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
          <h2 class="text-xl font-semibold">أداء الكورسات</h2>
          <p class="text-sm text-slate-500">فلترة حسب التصنيف أو المدرب أو الحالة.</p>
        </div>
        <div class="flex flex-wrap gap-2 text-sm">
          <select v-model="filtersState.category_id" class="input-filter w-40">
            <option value="">كل التصنيفات</option>
            <option v-for="category in filters.categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <select v-model="filtersState.instructor_id" class="input-filter w-48">
            <option value="">كل المدربين</option>
            <option v-for="instructor in filters.instructors" :key="instructor.id" :value="instructor.id">
              {{ instructor.name }}
            </option>
          </select>
          <select v-model="filtersState.status" class="input-filter w-36">
            <option value="">كل الحالات</option>
            <option v-for="status in filters.statuses" :key="status" :value="status">{{ status }}</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th class="px-4 py-3 text-left">الكورس</th>
              <th class="px-4 py-3 text-left">التصنيف</th>
              <th class="px-4 py-3 text-left">الطلاب</th>
              <th class="px-4 py-3 text-left">المبلغ المدفوع</th>
              <th class="px-4 py-3 text-left">إجمالي الجلسات</th>
              <th class="px-4 py-3 text-left">المكتمل</th>
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
      <p v-else class="text-center text-sm text-slate-400 py-4">لا توجد بيانات مطابقة للفلاتر الحالية.</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const stats = reactive({});
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

const meta = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const courses = ref([]);

const cards = [
  { label: 'الطلاب', key: 'students_count' },
  { label: 'المدربون', key: 'instructors_count' },
  { label: 'الكورسات النشطة', key: 'active_courses' },
  { label: 'الجلسات', key: 'sessions_count' },
  { label: 'نسبة الحضور %', key: 'attendance_rate' },
];

async function loadDashboard() {
  const { data } = await api.get('/admin/dashboard', {
    params: {
      category_id: filtersState.category_id || undefined,
      instructor_id: filtersState.instructor_id || undefined,
      status: filtersState.status || undefined,
      page: filtersState.page,
      per_page: filtersState.per_page,
    },
  });

  Object.assign(stats, data.stats);
  filters.categories = data.filters.categories;
  filters.instructors = data.filters.instructors;
  filters.statuses = data.filters.statuses;
  courses.value = data.courses || [];
  Object.assign(meta, data.pagination);
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
  return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'EGP' }).format(value);
}

function resetAndReload() {
  filtersState.page = 1;
  loadDashboard();
}

['category_id', 'instructor_id', 'status'].forEach((key) => {
  watch(
    () => filtersState[key],
    () => resetAndReload(),
  );
});

onMounted(loadDashboard);
</script>

<style scoped>
.input-filter {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.45rem 0.75rem;
  background: #fff;
}
</style>
