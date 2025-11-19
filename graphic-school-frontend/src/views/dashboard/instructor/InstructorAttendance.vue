<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold">تسجيل الحضور</h2>

    <div class="grid md:grid-cols-3 gap-4">
      <select v-model="selectedCourse" class="input">
        <option value="">اختر كورس</option>
        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
      </select>
      <select v-model="selectedSession" class="input">
        <option value="">اختر جلسة</option>
        <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.title }}</option>
      </select>
      <div class="flex gap-2">
        <button class="px-4 py-2 border rounded-md w-full" @click="markAll('absent')">جعل الجميع غياب</button>
        <button class="px-4 py-2 border rounded-md w-full" @click="markAll('present')">جعل الجميع حضور</button>
      </div>
    </div>

    <div v-if="selectedSession" class="bg-white rounded-2xl border border-slate-100 shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">الطالب</th>
            <th class="px-4 py-3 text-left">الإيميل</th>
            <th class="px-4 py-3 text-left">الحضور</th>
            <th class="px-4 py-3 text-left">ملاحظة</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="student in students" :key="student.student_id" class="border-t border-slate-100">
            <td class="px-4 py-3">{{ student.name }}</td>
            <td class="px-4 py-3 text-xs text-slate-500">{{ student.email }}</td>
            <td class="px-4 py-3">
              <select v-model="student.status" class="input">
                <option value="present">حاضر</option>
                <option value="absent">غائب</option>
              </select>
            </td>
            <td class="px-4 py-3">
              <input v-model="student.note" class="input" placeholder="تعليق" />
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!students.length" class="text-center py-6 text-sm text-slate-400">لا يوجد طلاب مسجلون.</p>
      <div class="p-4 flex justify-end border-t border-slate-100">
        <button class="px-5 py-2 bg-primary text-white rounded-md" :disabled="!students.length" @click="saveAttendance">
          حفظ الحضور
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../../api';

const courses = ref([]);
const sessions = ref([]);
const students = ref([]);
const selectedCourse = ref('');
const selectedSession = ref('');
const route = useRoute();

async function loadCourses() {
  const { data } = await api.get('/instructor/courses');
  courses.value = data;
}

watch(selectedCourse, async () => {
  selectedSession.value = '';
  students.value = [];
  if (!selectedCourse.value) {
    sessions.value = [];
    return;
  }
  const { data } = await api.get(`/instructor/courses/${selectedCourse.value}/sessions`);
  sessions.value = data;
  const targetSession = route.query.session;
  if (targetSession && data.some((session) => session.id === Number(targetSession))) {
    selectedSession.value = Number(targetSession);
  }
});

watch(selectedSession, loadAttendanceList);

async function loadAttendanceList() {
  students.value = [];
  if (!selectedSession.value) return;
  const { data } = await api.get(`/instructor/attendance/${selectedSession.value}`);
  students.value = data.map((record) => ({
    student_id: record.student_id,
    name: record.name,
    email: record.email,
    status: record.status || 'absent',
    note: record.note || '',
  }));
}

function markAll(status) {
  students.value = students.value.map((student) => ({ ...student, status }));
}

async function saveAttendance() {
  await api.post('/instructor/attendance', {
    session_id: selectedSession.value,
    records: students.value.map((student) => ({
      student_id: student.student_id,
      status: student.status,
      note: student.note,
    })),
  });
  alert('تم حفظ الحضور');
}

onMounted(async () => {
  await loadCourses();
  if (route.query.course) {
    selectedCourse.value = Number(route.query.course);
  }
  if (route.query.session && !selectedCourse.value) {
    selectedSession.value = Number(route.query.session);
  }
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.9rem;
}
</style>
