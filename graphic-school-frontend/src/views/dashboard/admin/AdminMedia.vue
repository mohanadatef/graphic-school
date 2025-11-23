<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">مكتبة الوسائط</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">إدارة جميع الملفات والصور</p>
      </div>
      <button
        @click="showUploadModal = true"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        رفع ملف
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
          <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="filters.search"
            class="w-full pl-10 pr-4 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
            placeholder="بحث بالاسم..."
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.type"
          :options="typeOptions"
          placeholder="كل الأنواع"
          @update:modelValue="handleFilterChange"
        />
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            v-model="filters.images_only"
            type="checkbox"
            class="w-4 h-4 text-primary rounded"
            @change="handleFilterChange"
          />
          <span class="text-sm text-slate-600 dark:text-slate-400">صور فقط</span>
        </label>
        <FilterDropdown
          v-model.number="pagination.per_page"
          :options="[
            { id: 12, name: '12' },
            { id: 24, name: '24' },
            { id: 48, name: '48' }
          ]"
          placeholder="عدد الصفحات"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <!-- Media Grid -->
    <div v-if="loading" class="text-center py-12 text-slate-400 dark:text-slate-500">
      <div class="spinner mx-auto mb-4"></div>
      جاري التحميل...
    </div>
    <div v-else-if="error" class="text-center py-12 text-red-500 dark:text-red-400">{{ error }}</div>
    <div v-else-if="!media.length" class="text-center py-12 text-slate-400 dark:text-slate-500">
      <p class="mb-4">لا توجد ملفات.</p>
      <button @click="showUploadModal = true" class="btn-primary inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        رفع أول ملف
      </button>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <div
        v-for="item in media"
        :key="item.id"
        class="group relative bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-all duration-200"
      >
        <div class="aspect-square bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden">
          <img
            v-if="item.type === 'image'"
            :src="getMediaUrl(item.file_path)"
            :alt="item.alt_text || item.name"
            class="w-full h-full object-cover"
          />
          <div v-else class="text-slate-400">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <div class="p-2">
          <p class="text-xs font-semibold text-slate-900 dark:text-white truncate" :title="item.name">
            {{ item.name }}
          </p>
          <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatFileSize(item.file_size) }}</p>
        </div>
        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2">
          <button
            @click="editMedia(item)"
            class="p-2 bg-white rounded-lg text-slate-900 hover:bg-slate-100"
            title="تعديل"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button
            @click="deleteMedia(item.id)"
            class="p-2 bg-red-500 rounded-lg text-white hover:bg-red-600"
            title="حذف"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">رفع ملف</h3>
        </div>
        <form @submit.prevent="uploadFile" class="p-6 space-y-4">
          <div>
            <label class="label">الملف</label>
            <input
              ref="fileInput"
              type="file"
              class="input"
              @change="handleFileSelect"
              required
            />
          </div>
          <div>
            <label class="label">النص البديل (للصور)</label>
            <input v-model="uploadForm.alt_text" type="text" class="input" />
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="uploadForm.description" class="input" rows="3"></textarea>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1" :disabled="uploading">
              {{ uploading ? 'جاري الرفع...' : 'رفع' }}
            </button>
            <button type="button" @click="closeUploadModal" class="btn-secondary flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="editingMedia" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">تعديل الملف</h3>
        </div>
        <form @submit.prevent="saveMedia" class="p-6 space-y-4">
          <div>
            <label class="label">الاسم</label>
            <input v-model="editForm.name" type="text" class="input" required />
          </div>
          <div>
            <label class="label">النص البديل</label>
            <input v-model="editForm.alt_text" type="text" class="input" />
          </div>
          <div>
            <label class="label">الوصف</label>
            <textarea v-model="editForm.description" class="input" rows="3"></textarea>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1">حفظ</button>
            <button type="button" @click="editingMedia = null" class="btn-secondary flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { get, post, put, delete: del } = useApi();
const toast = useToast();
const showUploadModal = ref(false);
const editingMedia = ref(null);
const uploading = ref(false);
const fileInput = ref(null);

const typeOptions = [
  { id: 'image', name: 'صورة' },
  { id: 'video', name: 'فيديو' },
  { id: 'document', name: 'مستند' },
];

const uploadForm = reactive({
  file: null,
  alt_text: '',
  description: '',
});

const editForm = reactive({
  name: '',
  alt_text: '',
  description: '',
});

const {
  items: media,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
} = useListPage({
  endpoint: '/admin/media',
  initialFilters: {
    search: '',
    type: '',
    images_only: false,
  },
  perPage: 24,
  debounceMs: 500,
  autoApplyFilters: false,
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function handleFileSelect(event) {
  uploadForm.file = event.target.files[0];
}

function getMediaUrl(path) {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://graphic-school.test/api';
  // Remove /api from base URL for storage paths
  const baseUrl = apiBaseUrl.replace('/api', '');
  return `${baseUrl}/storage/${path}`;
}

function formatFileSize(bytes) {
  if (!bytes) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function closeUploadModal() {
  showUploadModal.value = false;
  uploadForm.file = null;
  uploadForm.alt_text = '';
  uploadForm.description = '';
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

async function uploadFile() {
  if (!uploadForm.file) {
    toast.error('يرجى اختيار ملف');
    return;
  }

  uploading.value = true;
  try {
    const formData = new FormData();
    formData.append('file', uploadForm.file);
    if (uploadForm.alt_text) formData.append('alt_text', uploadForm.alt_text);
    if (uploadForm.description) formData.append('description', uploadForm.description);

    await post('/admin/media', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    toast.success('تم رفع الملف بنجاح');
    closeUploadModal();
    await loadItems();
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء الرفع');
  } finally {
    uploading.value = false;
  }
}

function editMedia(item) {
  editingMedia.value = item;
  editForm.name = item.name;
  editForm.alt_text = item.alt_text || '';
  editForm.description = item.description || '';
}

async function saveMedia() {
  try {
    await put(`/admin/media/${editingMedia.value.id}`, editForm);
    toast.success('تم تحديث الملف بنجاح');
    editingMedia.value = null;
    await loadItems();
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء التحديث');
  }
}

async function deleteMedia(id) {
  if (!confirm('هل أنت متأكد من حذف هذا الملف؟')) return;
  try {
    await del(`/admin/media/${id}`);
    toast.success('تم حذف الملف بنجاح');
    await loadItems();
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء الحذف');
  }
}

onMounted(async () => {
  await loadItems();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.15rem;
}
.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #1d4ed8;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>

