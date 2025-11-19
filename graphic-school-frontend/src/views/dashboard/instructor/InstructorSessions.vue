<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
      <h2 class="text-2xl font-bold">الجلسات</h2>
      <select v-model="filters.course_id" class="input w-56">
        <option value="">كل الكورسات</option>
        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
      </select>
      <select v-model="filters.status" class="input w-40">
        <option value="">كل الحالات</option>
        <option value="scheduled">مجدولة</option>
        <option value="completed">منتهية</option>
        <option value="cancelled">ملغاة</option>
      </select>
      <input v-model="filters.from_date" type="date" class="input" placeholder="من تاريخ" />
      <input v-model="filters.to_date" type="date" class="input" placeholder="إلى تاريخ" />
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">الكورس</th>
            <th class="px-4 py-3 text-left">الجلسة</th>
            <th class="px-4 py-3 text-left">التاريخ والوقت</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">إجراءات</th>
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
              <button class="text-primary text-sm" @click="goToAttendance(session)">إدارة الحضور</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!sessions.length" class="text-center py-6 text-sm text-slate-400">لا توجد جلسات مطابقة.</p>
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
import { useRouter } from 'vue-router';
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
  status: '',
  from_date: '',
  to_date: '',
  page: 1,
  per_page: 10,
});

const router = useRouter();

async function loadCourses() {
  const { data } = await api.get('/instructor/courses');
  courses.value = data;
}

async function loadSessions() {
  const { data } = await api.get('/instructor/sessions', {
    params: {
      course_id: filters.course_id || undefined,
      status: filters.status || undefined,
      from_date: filters.from_date || undefined,
      to_date: filters.to_date || undefined,
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

function changePage(page) {
  filters.page = page;
  loadSessions();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadSessions();
}

function goToAttendance(session) {
  router.push({ name: 'instructor-attendance', query: { session: session.id, course: session.course_id } });
}

watch(
  () => [filters.course_id, filters.status, filters.from_date, filters.to_date],
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
