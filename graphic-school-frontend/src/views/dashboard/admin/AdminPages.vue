<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">إدارة الصفحات</h2>
        <p class="text-sm text-slate-500">إدارة صفحات الموقع (CMS Page Builder)</p>
      </div>
      <RouterLink
        to="/dashboard/admin/pages/new"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block hover:bg-primary-dark transition"
      >
        + صفحة جديدة
      </RouterLink>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <input
          v-model="filters.search"
          type="text"
          placeholder="بحث..."
          class="input flex-1 min-w-64"
          @input="debounceSearch"
        />
        <select v-model="filters.is_active" class="input w-40" @change="reload">
          <option value="">كل الصفحات</option>
          <option value="1">نشطة</option>
          <option value="0">مخفية</option>
        </select>
        <select v-model.number="filters.per_page" class="input w-32" @change="changePerPage(filters.per_page)">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
      </div>
    </div>

    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">الرابط (Slug)</th>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">القالب</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3 text-left">تاريخ الإنشاء</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="page in pages" :key="page.id" class="border-t border-slate-100 hover:bg-slate-50">
            <td class="px-4 py-3">
              <code class="text-xs bg-slate-100 px-2 py-1 rounded">{{ page.slug }}</code>
            </td>
            <td class="px-4 py-3">
              <p class="font-semibold text-slate-900">{{ page.title }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-slate-600">{{ page.template || 'default' }}</td>
            <td class="px-4 py-3">
              <span
                :class="
                  page.is_active
                    ? 'px-2 py-1 rounded text-xs bg-green-100 text-green-700'
                    : 'px-2 py-1 rounded text-xs bg-slate-100 text-slate-600'
                "
              >
                {{ page.is_active ? 'نشطة' : 'مخفية' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-500">
              {{ formatDate(page.created_at) }}
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <RouterLink
                :to="`/dashboard/admin/pages/${page.id}/edit`"
                class="text-primary mr-2 hover:underline"
              >
                تعديل
              </RouterLink>
              <a
                :href="`/${page.slug}`"
                target="_blank"
                class="text-blue-500 mr-2 hover:underline"
              >
                عرض
              </a>
              <button class="text-red-500 hover:underline" @click="remove(page.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!pages.length && !loading" class="text-center py-6 text-sm text-slate-400">
        لا توجد صفحات.
      </p>
      <div v-if="loading" class="text-center py-6">
        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
      </div>
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
import { onMounted, reactive, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { cmsService } from '../../../services/api/cmsService';
import { useToast } from '../../../composables/useToast';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const toast = useToast();
const pages = ref([]);
const loading = ref(false);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  page: 1,
  per_page: 10,
  search: '',
  is_active: '',
});

let searchTimeout = null;

const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    filters.page = 1;
    reload();
  }, 500);
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const reload = async () => {
  loading.value = true;
  try {
    const params = {
      page: filters.page,
      per_page: filters.per_page,
    };

    if (filters.search) {
      params.search = filters.search;
    }

    if (filters.is_active !== '') {
      params.is_active = filters.is_active;
    }

    const response = await cmsService.getPages(params);

    if (response.success && response.data) {
      // Handle both paginated and non-paginated responses
      const data = response.data.data || response.data;
      pages.value = Array.isArray(data) ? data : [];
      
      // Pagination meta
      if (response.data.meta) {
        pagination.current_page = response.data.meta.current_page || 1;
        pagination.last_page = response.data.meta.last_page || 1;
        pagination.per_page = response.data.meta.per_page || 10;
        pagination.total = response.data.meta.total || 0;
      } else if (response.data.current_page) {
        pagination.current_page = response.data.current_page || 1;
        pagination.last_page = response.data.last_page || 1;
        pagination.per_page = response.data.per_page || 10;
        pagination.total = response.data.total || 0;
      }
    }
  } catch (error) {
    console.error('Error loading pages:', error);
    toast.error('حدث خطأ أثناء تحميل الصفحات');
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  filters.page = page;
  reload();
};

const changePerPage = (perPage) => {
  filters.per_page = perPage;
  filters.page = 1;
  reload();
};

const remove = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذه الصفحة؟')) {
    return;
  }

  try {
    await cmsService.deletePage(id);
    toast.success('تم حذف الصفحة بنجاح');
    reload();
  } catch (error) {
    console.error('Error deleting page:', error);
    toast.error('حدث خطأ أثناء حذف الصفحة');
  }
};

onMounted(() => {
  reload();
});
</script>

