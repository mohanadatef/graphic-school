# دليل النظام الموحد لاستدعاءات API والفلاتر

تم توحيد جميع استدعاءات API والفلاتر في composables موحدة لضمان عمل جميع الصفحات بنفس الطريقة.

## Composables المتوفرة

### 1. `useApi` - استدعاءات API موحدة
معالجة موحدة للأخطاء وحالة التحميل.

```javascript
import { useApi } from '@/composables/useApi';

const { loading, error, get, post, put, delete: del, clearError } = useApi();

// GET request
const data = await get('/admin/users');

// POST request
await post('/admin/users', { name: 'John' });

// PUT request
await put('/admin/users/1', { name: 'Jane' });

// DELETE request
await del('/admin/users/1');
```

### 2. `useFilters` - إدارة الفلاتر
فلاتر تعمل عند الحاجة فقط (ليس تلقائياً).

```javascript
import { useFilters } from '@/composables/useFilters';

const { filters, resetFilters, clearFilter, debounceSearch, buildParams } = useFilters({
  search: '',
  status: '',
}, {
  debounceMs: 500, // وقت الانتظار للبحث
  autoApply: false, // لا تطبق تلقائياً
});

// البحث مع debounce
function handleSearch() {
  debounceSearch(() => {
    loadItems();
  });
}

// تطبيق الفلاتر يدوياً
function applyFilters() {
  loadItems();
}
```

### 3. `usePagination` - إدارة الصفحات
إدارة موحدة للصفحات.

```javascript
import { usePagination } from '@/composables/usePagination';

const { pagination, updatePagination, changePage, changePerPage } = usePagination(10);
```

### 4. `useListPage` - Composable شامل
يجمع كل شيء في مكان واحد.

```javascript
import { useListPage } from '@/composables/useListPage';

const {
  items,           // البيانات
  loading,         // حالة التحميل
  error,           // الأخطاء
  filters,         // الفلاتر
  pagination,      // الصفحات
  changePage,      // تغيير الصفحة
  changePerPage,   // تغيير عدد العناصر
  loadItems,       // تحميل البيانات
  loadItemsDebounced, // تحميل مع debounce
  applyFilters,    // تطبيق الفلاتر
  createItem,      // إنشاء عنصر
  updateItem,      // تحديث عنصر
  deleteItem,      // حذف عنصر
  refresh,         // تحديث الصفحة الحالية
} = useListPage({
  endpoint: '/admin/users',
  initialFilters: {
    search: '',
    role_id: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // الفلاتر لا تعمل تلقائياً
});
```

## مثال كامل - صفحة إدارة المستخدمين

```vue
<template>
  <div>
    <!-- الفلاتر -->
    <input
      v-model="filters.search"
      @input="handleSearch"
      placeholder="بحث..."
    />
    <select v-model="filters.role_id" @change="handleFilterChange">
      <option value="">كل الأدوار</option>
    </select>
    <button @click="loadItems">تحديث</button>

    <!-- البيانات -->
    <div v-if="loading">جاري التحميل...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <table v-else>
      <tr v-for="user in items" :key="user.id">
        <td>{{ user.name }}</td>
      </tr>
    </table>

    <!-- الصفحات -->
    <PaginationControls
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />
  </div>
</template>

<script setup>
import { useListPage } from '@/composables/useListPage';
import PaginationControls from '@/components/common/PaginationControls.vue';

const {
  items,
  loading,
  error,
  filters,
  pagination,
  changePage,
  changePerPage,
  loadItems,
  loadItemsDebounced,
  applyFilters,
  createItem,
  updateItem,
  deleteItem,
} = useListPage({
  endpoint: '/admin/users',
  initialFilters: {
    search: '',
    role_id: '',
  },
  perPage: 10,
  debounceMs: 500,
  autoApplyFilters: false, // مهم: الفلاتر لا تعمل تلقائياً
});

// البحث مع debounce
function handleSearch() {
  loadItemsDebounced();
}

// تطبيق الفلاتر يدوياً
function handleFilterChange() {
  applyFilters();
}
</script>
```

## المبادئ الأساسية

1. **الفلاتر لا تعمل تلقائياً**: يجب الضغط على زر "تحديث" أو استخدام `applyFilters()` أو `loadItemsDebounced()` للبحث
2. **البحث مع debounce**: استخدم `loadItemsDebounced()` للبحث لتقليل عدد الطلبات
3. **معالجة الأخطاء**: جميع الأخطاء معالجة تلقائياً في `useApi`
4. **حالة التحميل**: `loading` متاح تلقائياً
5. **توحيد البنية**: جميع الصفحات تستخدم نفس البنية

## تحديث الصفحات الموجودة

للتحديث من النظام القديم:

1. استبدل `api.get/post/put/delete` بـ `useApi` أو `useListPage`
2. استبدل `watch` للفلاتر بـ `applyFilters()` أو `loadItemsDebounced()`
3. أضف `loading` و `error` للعرض
4. استخدم `useListPage` للصفحات التي تعرض قوائم

