<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">الأسئلة الشائعة</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">إدارة الأسئلة الشائعة</p>
      </div>
      <button
        @click="showCreateModal = true"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        سؤال جديد
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
            placeholder="بحث..."
            @input="handleSearch"
          />
        </div>
        <FilterDropdown
          v-model="filters.category"
          :options="categoryOptions"
          placeholder="كل الفئات"
          @update:modelValue="handleFilterChange"
        />
        <FilterDropdown
          v-model.number="pagination.per_page"
          :options="[
            { id: 10, name: '10' },
            { id: 20, name: '20' },
            { id: 50, name: '50' }
          ]"
          placeholder="عدد الصفحات"
          @update:modelValue="changePerPage"
        />
      </div>
    </div>

    <!-- FAQs List -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
      <div class="divide-y divide-slate-100 dark:divide-slate-700">
        <div
          v-for="faq in faqs"
          :key="faq.id"
          class="p-6 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="font-semibold text-slate-900 dark:text-white">{{ faq.question }}</h3>
                <span
                  class="px-2 py-1 text-xs rounded-full"
                  :class="faq.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'"
                >
                  {{ faq.is_active ? 'نشط' : 'غير نشط' }}
                </span>
                <span v-if="faq.category" class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full">
                  {{ faq.category }}
                </span>
              </div>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ faq.answer }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="editFAQ(faq)"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل
              </button>
              <button
                @click="removeFAQ(faq.id)"
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                حذف
              </button>
            </div>
          </div>
        </div>
      </div>
      <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
      <p v-else-if="!faqs.length" class="text-center py-6 text-sm text-slate-400">لا توجد أسئلة.</p>
      <p v-if="error" class="text-center py-6 text-sm text-red-500">{{ error }}</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingFAQ" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">
            {{ editingFAQ ? 'تعديل سؤال' : 'سؤال جديد' }}
          </h3>
        </div>
        <form @submit.prevent="saveFAQ" class="p-6 space-y-4">
          <div>
            <label class="label">السؤال</label>
            <input v-model="formData.question" type="text" class="input" required maxlength="500" />
          </div>
          <div>
            <label class="label">الإجابة</label>
            <textarea v-model="formData.answer" class="input" rows="5" required maxlength="2000"></textarea>
          </div>
          <div>
            <label class="label">الفئة</label>
            <input v-model="formData.category" type="text" class="input" placeholder="عام، تقني، إلخ" />
          </div>
          <div>
            <label class="label">ترتيب العرض</label>
            <input v-model.number="formData.sort_order" type="number" class="input" />
          </div>
          <div>
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="formData.is_active" type="checkbox" class="w-4 h-4 text-primary rounded" />
              <span class="text-sm text-slate-600 dark:text-slate-400">نشط</span>
            </label>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1">حفظ</button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">إلغاء</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed } from 'vue';
import { useListPage } from '../../../composables/useListPage';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import PaginationControls from '../../../components/common/PaginationControls.vue';
import FilterDropdown from '../../../components/common/FilterDropdown.vue';

const { get, post, put, delete: del } = useApi();
const toast = useToast();
const showCreateModal = ref(false);
const editingFAQ = ref(null);

const formData = reactive({
  question: '',
  answer: '',
  category: '',
  sort_order: 0,
  is_active: true,
});

const {
  items: faqs,
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
  endpoint: '/admin/faqs',
  initialFilters: {
    search: '',
    category: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false,
});

const categoryOptions = computed(() => {
  const categories = new Set();
  faqs.value.forEach(faq => {
    if (faq.category) categories.add(faq.category);
  });
  return Array.from(categories).map(cat => ({ id: cat, name: cat }));
});

function handleSearch() {
  loadItemsDebounced();
}

function handleFilterChange() {
  applyFilters();
}

function editFAQ(faq) {
  editingFAQ.value = faq;
  formData.question = faq.question;
  formData.answer = faq.answer;
  formData.category = faq.category || '';
  formData.sort_order = faq.sort_order || 0;
  formData.is_active = faq.is_active !== false;
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingFAQ.value = null;
  Object.assign(formData, {
    question: '',
    answer: '',
    category: '',
    sort_order: 0,
    is_active: true,
  });
}

async function saveFAQ() {
  try {
    if (editingFAQ.value) {
      await put(`/admin/faqs/${editingFAQ.value.id}`, formData);
      toast.success('تم تحديث السؤال بنجاح');
    } else {
      await post('/admin/faqs', formData);
      toast.success('تم إضافة السؤال بنجاح');
    }
    closeModal();
    await loadItems();
  } catch (err) {
    toast.error(error.value || 'حدث خطأ أثناء الحفظ');
  }
}

async function removeFAQ(id) {
  if (!confirm('هل أنت متأكد من حذف هذا السؤال؟')) return;
  try {
    await del(`/admin/faqs/${id}`);
    toast.success('تم حذف السؤال بنجاح');
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
</style>

