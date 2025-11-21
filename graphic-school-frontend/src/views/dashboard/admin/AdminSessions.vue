<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold">الجلسات</h2>
        <p class="text-sm text-slate-500">تعديل مواعيد المحاضرات.</p>
      </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-3">
      <div class="flex flex-wrap gap-2 items-center">
        <FilterDropdown
          v-model="filters.course_id"
          :options="courses"
          placeholder="كل الكورسات"
          label-key="title"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model="filters.status"
          :options="[
            { id: 'scheduled', name: 'مجدولة' },
            { id: 'completed', name: 'منتهية' },
            { id: 'cancelled', name: 'ملغاة' }
          ]"
          placeholder="كل الحالات"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model.number="pagination.per_page"
          :options="[
            { id: 10, name: '10' },
            { id: 20, name: '20' },
            { id: 50, name: '50' }
          ]"
          placeholder="عدد الصفحات"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">التاريخ</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="session in sessions" :key="session.id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ session.course?.title }}</td>
            <td class="px-4 py-3">{{ session.title }}</td>
            <td class="px-4 py-3">{{ formatDate(session.session_date) }}</td>
            <td class="px-4 py-3 text-xs uppercase">{{ session.status }}</td>
            <td class="px-4 py-3 text-right">
              <RouterLink
                :to="`/dashboard/admin/sessions/${session.id}/edit`"
                class="text-primary text-xs hover:underline"
              >
                تعديل
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
      <p v-else-if="!sessions.length" class="text-center py-6 text-sm text-slate-400">لا توجد بيانات.</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const courses = ref([]);

// Use unified list page composable
const {
  items: sessions,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  applyFilters,
  updateItem,
} = useListPage({
  endpoint: '/admin/sessions',
  initialFilters: {
    course_id: '',
    status: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // Manual filter application
});

const { get } = useApi();

async function loadCourses() {
  try {
    const data = await get('/admin/courses', { params: { per_page: 1000 } });
    courses.value = Array.isArray(data) ? data : (data.data || []);
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  }
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

// Handle filter change - manual apply
function handleFilterChange() {
  applyFilters();
}

onMounted(async () => {
  await loadCourses();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

