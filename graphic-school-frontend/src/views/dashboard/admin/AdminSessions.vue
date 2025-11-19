<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h2 class="text-2xl font-bold">الجلسات</h2>
        <p class="text-sm text-slate-500">تعديل مواعيد المحاضرات.</p>
      </div>
      <button class="px-4 py-2 border rounded-md" @click="loadSessions">تحديث</button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <select v-model="filters.course_id" class="input w-48">
          <option value="">كل الكورسات</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
        </select>
        <select v-model="filters.status" class="input w-40">
          <option value="">كل الحالات</option>
          <option value="scheduled">مجدولة</option>
          <option value="completed">منتهية</option>
          <option value="cancelled">ملغاة</option>
        </select>
        <select v-model.number="filters.per_page" class="input w-32" @change="changePerPage(filters.per_page)">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
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
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const sessions = ref([]);
const courses = ref([]);
const dialogRef = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  course_id: '',
  status: '',
  page: 1,
  per_page: 10,
});

const form = reactive({
  id: null,
  title: '',
  session_date: '',
  status: 'scheduled',
  note: '',
});

async function loadCourses() {
  const { data } = await api.get('/admin/courses', { params: { per_page: 1000 } });
  courses.value = data.data;
}

async function loadSessions() {
  const { data } = await api.get('/admin/sessions', {
    params: {
      page: filters.page,
      per_page: filters.per_page,
      course_id: filters.course_id || undefined,
      status: filters.status || undefined,
    },
  });
  sessions.value = data.data;
  Object.assign(pagination, {
    current_page: data.meta.current_page,
    last_page: data.meta.last_page,
    per_page: data.meta.per_page,
    total: data.meta.total,
  });
}

function openModal(session) {
  form.id = session.id;
  form.title = session.title;
  form.session_date = session.session_date;
  form.status = session.status;
  form.note = session.note || '';
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

async function submit() {
  await api.put(`/admin/sessions/${form.id}`, form);
  closeModal();
  loadSessions();
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

function changePage(page) {
  filters.page = page;
  loadSessions();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadSessions();
}

watch(
  () => [filters.course_id, filters.status],
  () => {
    filters.page = 1;
    loadSessions();
  },
);

onMounted(async () => {
  await loadCourses();
  loadSessions();
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

