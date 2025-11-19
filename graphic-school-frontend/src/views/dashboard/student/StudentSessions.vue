<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4 flex-wrap">
      <h2 class="text-2xl font-bold">جدول المحاضرات</h2>
      <select v-model="filters.course_id" class="input w-48">
        <option value="">كل الكورسات</option>
        <option v-for="course in courses" :key="course.course_id" :value="course.course_id">
          {{ course.course?.title }}
        </option>
      </select>
      <select v-model="filters.attendance_status" class="input w-40">
        <option value="">كل الحضور</option>
        <option value="present">حضرت</option>
        <option value="absent">تغيبت</option>
      </select>
      <select v-model="filters.status" class="input w-40">
        <option value="">كل الحالات</option>
        <option value="scheduled">مجدولة</option>
        <option value="completed">منتهية</option>
        <option value="cancelled">ملغاة</option>
      </select>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">الجلسة</th>
            <th class="px-4 py-3 text-left">التاريخ والوقت</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">الحضور</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="session in sessions" :key="session.id" class="border-b border-slate-100">
            <td class="px-4 py-3 font-semibold text-slate-900">{{ session.course?.title }}</td>
            <td class="px-4 py-3">{{ session.title }}</td>
            <td class="px-4 py-3 text-xs text-slate-500">
              {{ formatDateTime(session.session_date, session.start_time) }}
            </td>
            <td class="px-4 py-3 text-xs uppercase">{{ session.status }}</td>
            <td class="px-4 py-3">
              <span :class="attendanceBadgeClass(session)" class="px-3 py-1 rounded-full text-xs">
                {{ attendanceLabel(session) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!sessions.length" class="text-center py-6 text-sm text-slate-400">لا توجد محاضرات حالياً.</p>
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
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const courses = ref([]);
const sessions = ref([]);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  course_id: '',
  attendance_status: '',
  status: '',
  page: 1,
  per_page: 10,
});

async function loadCourses() {
  const { data } = await api.get('/student/courses');
  courses.value = data;
}

async function loadSessions() {
  const { data } = await api.get('/student/sessions', {
    params: {
      course_id: filters.course_id || undefined,
      attendance_status: filters.attendance_status || undefined,
      status: filters.status || undefined,
      page: filters.page,
      per_page: filters.per_page,
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

function formatDateTime(date, time) {
  if (!date) return 'غير محدد';
  const formattedDate = new Date(date).toLocaleDateString('ar-EG');
  const formattedTime = time ? time.slice(0, 5) : '--:--';
  return `${formattedDate} - ${formattedTime}`;
}

function attendanceLabel(session) {
  const status = session.attendance?.[0]?.status;
  return status === 'present' ? 'حاضر' : 'غائب';
}

function attendanceBadgeClass(session) {
  const status = session.attendance?.[0]?.status;
  return status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
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
  () => [filters.course_id, filters.attendance_status, filters.status],
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
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
}
</style>
