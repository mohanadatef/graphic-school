<template>
  <div class="space-y-6 max-w-3xl">
    <h2 class="text-2xl font-bold">ملاحظات المحاضرات</h2>
    <div class="grid gap-4 md:grid-cols-3">
      <select v-model="selectedCourse" class="input">
        <option value="">اختر كورس</option>
        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
      </select>
      <select v-model="selectedSession" class="input">
        <option value="">اختر جلسة</option>
        <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.title }}</option>
      </select>
      <input v-model="note.student_id" type="number" class="input" placeholder="ID الطالب (اختياري)" />
    </div>
    <textarea v-model="note.text" rows="4" class="input" placeholder="أضف ملاحظة يراها الطلاب"></textarea>
    <button class="px-5 py-2 bg-primary text-white rounded-md" @click="submit" :disabled="!selectedSession">
      حفظ الملاحظة
    </button>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import api from '../../../api';

const courses = ref([]);
const sessions = ref([]);
const selectedCourse = ref('');
const selectedSession = ref('');
const note = ref({ student_id: '', text: '' });

async function loadCourses() {
  const { data } = await api.get('/instructor/courses');
  courses.value = data;
}

watch(selectedCourse, async () => {
  selectedSession.value = '';
  sessions.value = [];
  if (!selectedCourse.value) return;
  const { data } = await api.get(`/instructor/courses/${selectedCourse.value}/sessions`);
  sessions.value = data;
});

async function submit() {
  await api.post('/instructor/session-note', {
    session_id: selectedSession.value,
    student_id: note.value.student_id || undefined,
    note: note.value.text,
  });
  alert('تم نشر الملاحظة');
  note.value = { student_id: '', text: '' };
}

onMounted(loadCourses);
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.6rem 0.9rem;
}
</style>

