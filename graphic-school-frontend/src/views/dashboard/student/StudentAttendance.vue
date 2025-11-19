<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <h2 class="text-2xl font-bold">نسبة الحضور</h2>
      <select v-model="selectedCourse" class="input">
        <option value="">اختر كورس</option>
        <option v-for="course in courses" :key="course.course_id" :value="course.course_id">
          {{ course.course?.title }}
        </option>
      </select>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow overflow-hidden">
      <ul>
        <li
          v-for="record in attendance"
          :key="record.id"
          class="border-b border-slate-100 p-4 flex items-center justify-between"
        >
          <div>
            <p class="font-semibold text-slate-900">{{ record.session.title }}</p>
            <p class="text-xs text-slate-400">{{ formatDate(record.session.session_date) }}</p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs"
            :class="record.status === 'present' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
          >
            {{ record.status === 'present' ? 'حاضر' : 'غائب' }}
          </span>
        </li>
      </ul>
      <p v-if="!attendance.length" class="text-center py-6 text-sm text-slate-400">
        لا توجد سجلات بعد.
      </p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import api from '../../../api';

const courses = ref([]);
const attendance = ref([]);
const selectedCourse = ref('');

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('ar-EG');
}

async function loadCourses() {
  const { data } = await api.get('/student/courses');
  courses.value = data;
}

async function loadAttendance() {
  if (!selectedCourse.value) {
    attendance.value = [];
    return;
  }
  const { data } = await api.get(`/student/courses/${selectedCourse.value}/attendance`);
  attendance.value = data;
}

watch(selectedCourse, loadAttendance);
onMounted(loadCourses);
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
}
</style>

