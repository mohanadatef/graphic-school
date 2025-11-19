import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { courseService } from '../services/api';

export const useCourseStore = defineStore('course', () => {
  // State
  const courses = ref([]);
  const currentCourse = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  });
  const filters = ref({
    category_id: '',
    instructor_id: '',
    status: '',
  });

  // Getters
  const filteredCourses = computed(() => {
    if (!filters.value.category_id) return courses.value;
    return courses.value.filter(
      (course) => course.category_id === Number(filters.value.category_id)
    );
  });

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getAll(params);
      courses.value = Array.isArray(data) ? data : data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return courses.value;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch courses';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchById(id) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getById(id);
      currentCourse.value = data;
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch course';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchDashboardData(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getDashboardData(params);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch dashboard data';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchAdminCourses(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getAdminCourses(params);
      courses.value = data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch courses';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function createCourse(payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.create(payload);
      courses.value.push(data);
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create course';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateCourse(id, payload) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.update(id, payload);
      const index = courses.value.findIndex((c) => c.id === id);
      if (index !== -1) {
        courses.value[index] = data;
      }
      if (currentCourse.value?.id === id) {
        currentCourse.value = data;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update course';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteCourse(id) {
    loading.value = true;
    error.value = null;
    try {
      await courseService.delete(id);
      courses.value = courses.value.filter((c) => c.id !== id);
      if (currentCourse.value?.id === id) {
        currentCourse.value = null;
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete course';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchInstructorCourses(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getInstructorCourses(params);
      courses.value = data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch courses';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchStudentCourses(params = {}) {
    loading.value = true;
    error.value = null;
    try {
      const data = await courseService.getStudentCourses(params);
      courses.value = data.data || [];
      if (data.pagination) {
        pagination.value = data.pagination;
      }
      return data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch courses';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function setFilters(newFilters) {
    filters.value = { ...filters.value, ...newFilters };
  }

  function resetFilters() {
    filters.value = {
      category_id: '',
      instructor_id: '',
      status: '',
    };
  }

  return {
    // State
    courses,
    currentCourse,
    loading,
    error,
    pagination,
    filters,
    // Getters
    filteredCourses,
    // Actions
    fetchAll,
    fetchById,
    fetchDashboardData,
    fetchAdminCourses,
    createCourse,
    updateCourse,
    deleteCourse,
    fetchInstructorCourses,
    fetchStudentCourses,
    setFilters,
    resetFilters,
  };
});

