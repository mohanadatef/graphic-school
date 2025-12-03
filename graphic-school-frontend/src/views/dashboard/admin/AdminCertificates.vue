<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ $t('admin.certificates') || 'Certificates' }}</h2>
        <p class="text-sm text-slate-500">{{ $t('admin.certificatesDescription') || 'Manage and issue certificates for students' }}</p>
      </div>
      <button
        @click="showIssueModal = true"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block"
      >
        {{ $t('admin.certificatesIssue') || 'Issue Certificate' }}
      </button>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-3">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
          :placeholder="$t('common.search') || 'Search...'"
          @input="handleSearch"
        />
        <select
          v-model="filters.course_id"
          class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg"
          @change="handleFilterChange"
        >
          <option value="">All Courses</option>
          <option v-for="course in courses" :key="course.id" :value="course.id">
            {{ course.title }}
          </option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <p class="text-red-800">{{ error }}</p>
    </div>

    <div v-else-if="items.length === 0" class="text-center py-20">
      <p class="text-slate-500 text-lg">{{ $t('admin.certificatesNoCertificates') || 'No certificates found' }}</p>
    </div>

    <div v-else class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">Student</th>
            <th class="px-4 py-3 text-left">Course</th>
            <th class="px-4 py-3 text-left">Group</th>
            <th class="px-4 py-3 text-left">Issued Date</th>
            <th class="px-4 py-3 text-left">Verification Code</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="cert in items" :key="cert.id" class="hover:bg-slate-50">
            <td class="px-4 py-3">{{ cert.student?.name }}</td>
            <td class="px-4 py-3">{{ cert.course?.title }}</td>
            <td class="px-4 py-3">{{ cert.group?.name || '-' }}</td>
            <td class="px-4 py-3">{{ formatDate(cert.issued_date) }}</td>
            <td class="px-4 py-3">
              <code class="text-xs bg-slate-100 px-2 py-1 rounded">{{ cert.verification_code }}</code>
            </td>
            <td class="px-4 py-3">
              <button
                @click="viewCertificate(cert.id)"
                class="text-primary hover:underline mr-3"
              >
                View
              </button>
              <button
                @click="deleteCertificate(cert.id)"
                class="text-red-600 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Pagination
      v-if="pagination.total > 0"
      :meta="pagination"
      @change-page="handlePageChange"
      @change-per-page="handlePerPageChange"
    />

    <!-- Issue Certificate Modal -->
    <div
      v-if="showIssueModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="showIssueModal = false"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4">{{ $t('admin.certificatesIssue') || 'Issue Certificate' }}</h3>
        <form @submit.prevent="handleIssueCertificate">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Student *</label>
              <select v-model="issueForm.student_id" required class="w-full px-3 py-2 border rounded">
                <option value="">Select Student</option>
                <option v-for="student in students" :key="student.id" :value="student.id">
                  {{ student.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Course *</label>
              <select v-model="issueForm.course_id" required class="w-full px-3 py-2 border rounded">
                <option value="">Select Course</option>
                <option v-for="course in courses" :key="course.id" :value="course.id">
                  {{ course.title }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Group</label>
              <select v-model="issueForm.group_id" class="w-full px-3 py-2 border rounded">
                <option value="">Select Group</option>
                <option v-for="group in availableGroups" :key="group.id" :value="group.id">
                  {{ group.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Instructor</label>
              <select v-model="issueForm.instructor_id" class="w-full px-3 py-2 border rounded">
                <option value="">Select Instructor</option>
                <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                  {{ instructor.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="flex gap-3 mt-6">
            <button
              type="button"
              @click="showIssueModal = false"
              class="flex-1 px-4 py-2 border rounded"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-primary text-white rounded"
              :disabled="loading"
            >
              <span v-if="loading">Issuing...</span>
              <span v-else>Issue Certificate</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useCertificateStore } from '@/stores/certificate';
import { useCourseStore } from '@/stores/course';
import { useGroupStore } from '@/stores/group';
import { userService } from '@/services/api/userService';
import Pagination from '@/components/common/PaginationControls.vue';

const certificateStore = useCertificateStore();
const courseStore = useCourseStore();
const groupStore = useGroupStore();

const { items, loading, error, pagination } = storeToRefs(certificateStore);

const courses = ref([]);
const students = ref([]);
const instructors = ref([]);
const groups = ref([]);
const showIssueModal = ref(false);
const filters = ref({
  search: '',
  course_id: '',
});

const issueForm = ref({
  student_id: '',
  course_id: '',
  group_id: '',
  instructor_id: '',
});

const availableGroups = computed(() => {
  if (!issueForm.value.course_id) return [];
  return groups.value.filter(g => g.course_id == issueForm.value.course_id);
});

onMounted(async () => {
  await Promise.all([
    certificateStore.fetchAll(filters.value),
    loadCourses(),
    loadStudents(),
    loadInstructors(),
    loadGroups(),
  ]);
});

async function loadCourses() {
  try {
    await courseStore.fetchAll({ per_page: -1 });
    courses.value = courseStore.items || [];
  } catch (error) {
    console.error('Error loading courses:', error);
  }
}

async function loadStudents() {
  try {
    // Using admin users endpoint with role filter
    const response = await userService.getAdminUsers({ role: 'student', per_page: -1 });
    students.value = response.data || [];
  } catch (error) {
    console.error('Error loading students:', error);
  }
}

async function loadInstructors() {
  try {
    // Using admin users endpoint with role filter
    const response = await userService.getAdminUsers({ role: 'instructor', per_page: -1 });
    instructors.value = response.data || [];
  } catch (error) {
    console.error('Error loading instructors:', error);
  }
}

async function loadGroups() {
  try {
    await groupStore.fetchAll({ per_page: -1 });
    groups.value = groupStore.items || [];
  } catch (error) {
    console.error('Error loading groups:', error);
  }
}

async function handleIssueCertificate() {
  try {
    await certificateStore.create(issueForm.value);
    showIssueModal.value = false;
    issueForm.value = { student_id: '', course_id: '', group_id: '', instructor_id: '' };
    await certificateStore.fetchAll(filters.value);
  } catch (error) {
    console.error('Error issuing certificate:', error);
    alert(error.response?.data?.message || 'Failed to issue certificate');
  }
}

async function viewCertificate(id) {
  try {
    await certificateStore.fetchOne(id);
    // Navigate to certificate details or show in modal
    // Implementation depends on your routing/UI requirements
  } catch (error) {
    console.error('Error viewing certificate:', error);
  }
}

async function deleteCertificate(id) {
  if (!confirm('Are you sure you want to delete this certificate?')) return;
  try {
    await certificateStore.deleteItem(id);
    await certificateStore.fetchAll(filters.value);
  } catch (error) {
    console.error('Error deleting certificate:', error);
    alert('Failed to delete certificate');
  }
}

function handleSearch() {
  certificateStore.fetchAll(filters.value);
}

function handleFilterChange() {
  certificateStore.fetchAll(filters.value);
}

function handlePageChange(page) {
  certificateStore.setPage(page);
  certificateStore.fetchAll(filters.value);
}

function handlePerPageChange(perPage) {
  certificateStore.setPerPage(perPage);
  certificateStore.fetchAll(filters.value);
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>
