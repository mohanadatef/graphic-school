<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">الكورسات</h2>
        <p class="text-sm text-slate-500">إنشاء كورسات جديدة وتعيين المدربين والتحكم في الجلسات.</p>
      </div>
      <RouterLink
        to="/dashboard/admin/courses/new"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block"
      >
        كورس جديد
      </RouterLink>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-3">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
          placeholder="بحث..."
          @input="handleSearch"
        />
        <FilterDropdown
          v-model="filters.status"
          :options="[
            { id: 'draft', name: 'مسودة' },
            { id: 'upcoming', name: 'قادمة' },
            { id: 'running', name: 'قيد التنفيذ' },
            { id: 'completed', name: 'منتهية' }
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

    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">التصنيف</th>
            <th class="px-4 py-3 text-left">البداية</th>
            <th class="px-4 py-3 text-left">الوقت</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">المدربين</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="course in courses" :key="course.id" class="border-t border-slate-100">
            <td class="px-4 py-3 font-medium text-slate-900">{{ course.title }}</td>
            <td class="px-4 py-3">{{ course.category?.name }}</td>
            <td class="px-4 py-3">{{ formatDate(course.start_date) }}</td>
            <td class="px-4 py-3 text-xs">
              {{ formatTime(course.default_start_time) }} - {{ formatTime(course.default_end_time) }}
            </td>
            <td class="px-4 py-3 text-xs uppercase">{{ course.status }}</td>
            <td class="px-4 py-3 text-xs">
              <span
                v-for="inst in course.instructors"
                :key="inst.id"
                class="inline-block bg-slate-100 px-2 py-1 rounded-full mr-1 mb-1"
              >
                {{ inst.name }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <RouterLink
                :to="`/dashboard/admin/courses/${course.id}/edit`"
                class="text-primary mr-2 hover:underline"
              >
                تعديل
              </RouterLink>
              <button class="text-red-500" @click="remove(course.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
      <p v-else-if="!courses.length" class="text-center py-6 text-sm text-slate-400">لا توجد بيانات.</p>
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

// Use unified list page composable
const {
  items: courses,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
  createItem,
  updateItem,
  deleteItem,
} = useListPage({
  endpoint: '/admin/courses',
  initialFilters: {
    status: '',
    search: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // Manual filter application
});


async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  try {
    await deleteItem(id);
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحذف');
  }
}

function formatDate(date) {
  if (!date) return 'غير محدد';
  return new Date(date).toLocaleDateString('ar-EG');
}

function formatTime(time) {
  if (!time) return '--:--';
  return time.slice(0, 5);
}

// Handle search with debounce
function handleSearch() {
  loadItemsDebounced();
}

// Handle filter change (status) - manual apply
function handleFilterChange() {
  applyFilters();
}

onMounted(async () => {
  await loadOptions();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>
