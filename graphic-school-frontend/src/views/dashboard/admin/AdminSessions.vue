<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold">الجلسات</h2>
        <p class="text-sm text-slate-500">تعديل مواعيد المحاضرات.</p>
      </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <select v-model="filters.course_id" class="input w-48" @change="handleFilterChange">
          <option value="">كل الكورسات</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
        </select>
        <select v-model="filters.status" class="input w-40" @change="handleFilterChange">
          <option value="">كل الحالات</option>
          <option value="scheduled">مجدولة</option>
          <option value="completed">منتهية</option>
          <option value="cancelled">ملغاة</option>
        </select>
        <select
          v-model.number="pagination.per_page"
          class="input w-32"
          @change="changePerPage(pagination.per_page)"
        >
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button class="px-4 py-2 border rounded-md" @click="loadItems">تحديث</button>
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
              <button class="text-primary text-xs" @click="openModal(session)">تعديل</button>
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

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-lg">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-xl font-semibold">تعديل الجلسة</h3>
        <div>
          <label class="label">العنوان</label>
          <input v-model="form.title" class="input" />
        </div>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">التاريخ</label>
            <input v-model="form.session_date" type="date" class="input" />
          </div>
          <div>
            <label class="label">الحالة</label>
            <select v-model="form.status" class="input">
              <option value="scheduled">مجدولة</option>
              <option value="completed">منتهية</option>
              <option value="cancelled">ملغاة</option>
            </select>
          </div>
        </div>
        <div>
          <label class="label">ملاحظة</label>
          <textarea v-model="form.note" rows="3" class="input"></textarea>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">حفظ</button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const courses = ref([]);
const dialogRef = ref(null);

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

const form = reactive({
  id: null,
  title: '',
  session_date: '',
  status: 'scheduled',
  note: '',
});

const { get, put } = useApi();

async function loadCourses() {
  try {
    const data = await get('/admin/courses', { params: { per_page: 1000 } });
    courses.value = data.data || [];
  } catch (err) {
    console.error('Error loading courses:', err);
    courses.value = [];
  }
}

function openModal(session) {
  form.id = session.id;
  form.title = session.title || '';
  form.session_date = session.session_date || '';
  form.status = session.status || 'scheduled';
  form.note = session.note || '';
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
  form.id = null;
  form.title = '';
  form.session_date = '';
  form.status = 'scheduled';
  form.note = '';
}

async function submit() {
  try {
    await updateItem(form.id, {
      title: form.title,
      session_date: form.session_date,
      status: form.status,
      note: form.note,
    });
    closeModal();
  } catch (err) {
    alert(error.value || 'حدث خطأ أثناء الحفظ');
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

