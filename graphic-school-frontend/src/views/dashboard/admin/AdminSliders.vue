<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">السلايدر</h2>
        <p class="text-sm text-slate-500">إدارة الشرائح الرئيسية للموقع.</p>
      </div>
      <RouterLink
        to="/dashboard/admin/sliders/new"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block"
      >
        شريحة جديدة
      </RouterLink>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <select v-model="filters.is_active" class="input w-40" @change="reload">
          <option value="">كل السلايدر</option>
          <option value="1">نشط</option>
          <option value="0">مخفي</option>
        </select>
        <select v-model.number="filters.per_page" class="input w-32" @change="changePerPage(filters.per_page)">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="20">20</option>
        </select>
      </div>
    </div>

    <div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3 text-left">الصورة</th>
            <th class="px-4 py-3 text-left">العنوان</th>
            <th class="px-4 py-3 text-left">الرابط</th>
            <th class="px-4 py-3 text-left">الترتيب</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slider in sliders" :key="slider.id" class="border-t border-slate-100">
            <td class="px-4 py-3">
              <img :src="slider.image_path" alt="slide" class="h-14 w-24 object-cover rounded" />
            </td>
            <td class="px-4 py-3">
              <p class="font-semibold text-slate-900">{{ slider.title || 'بدون عنوان' }}</p>
              <p class="text-xs text-slate-500">{{ slider.subtitle }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-primary">{{ slider.button_url || '-' }}</td>
            <td class="px-4 py-3">{{ slider.sort_order }}</td>
            <td class="px-4 py-3">
              <span :class="slider.is_active ? 'text-green-600' : 'text-slate-400'">
                {{ slider.is_active ? 'نشط' : 'مخفي' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-xs">
              <RouterLink
                :to="`/dashboard/admin/sliders/${slider.id}/edit`"
                class="text-primary mr-2 hover:underline"
              >
                تعديل
              </RouterLink>
              <button class="text-red-500" @click="remove(slider.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!sliders.length" class="text-center py-6 text-sm text-slate-400">لا توجد شرائح.</p>
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
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const sliders = ref([]);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});

const filters = reactive({
  page: 1,
  per_page: 10,
  is_active: '',
});

async function loadSliders() {
  try {
    const { data } = await api.get('/admin/sliders', {
      params: {
        page: filters.page,
        per_page: filters.per_page,
        is_active: filters.is_active === '' ? undefined : filters.is_active,
      },
    });

    // Handle unified API response format
    if (data && data.data) {
      sliders.value = Array.isArray(data.data) ? data.data : [];
      if (data.meta) {
        Object.assign(pagination, {
          current_page: data.meta.current_page || data.meta.pagination?.current_page || 1,
          last_page: data.meta.last_page || data.meta.pagination?.last_page || 1,
          per_page: data.meta.per_page || data.meta.pagination?.per_page || 10,
          total: data.meta.total || data.meta.pagination?.total || 0,
        });
      }
    } else if (Array.isArray(data)) {
      sliders.value = data;
    }
  } catch (err) {
    console.error('Error loading sliders:', err);
    sliders.value = [];
  }
}

function reload() {
  filters.page = 1;
  loadSliders();
}

function changePage(page) {
  filters.page = page;
  loadSliders();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadSliders();
}

async function remove(id) {
  if (!confirm('تأكيد الحذف؟')) return;
  await api.delete(`/admin/sliders/${id}`);
  loadSliders();
}

onMounted(loadSliders);
</script>

<style scoped>
.input {
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.9rem;
}
.label {
  display: block;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-bottom: 0.2rem;
}
</style>

