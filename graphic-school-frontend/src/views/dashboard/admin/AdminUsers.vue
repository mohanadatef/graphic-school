<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">إدارة المستخدمين</h2>
        <p class="text-sm text-slate-500">إضافة / تعديل / تعطيل المستخدمين</p>
      </div>
      <button class="px-4 py-2 bg-primary text-white rounded-md" @click="openModal()">مستخدم جديد</button>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 p-4 space-y-3">
      <div class="flex flex-wrap gap-3">
        <input v-model="filters.search" class="input w-48" placeholder="بحث بالاسم أو البريد" />
        <select v-model="filters.role_id" class="input w-48">
          <option value="">كل الأدوار</option>
          <option v-for="role in roles" :key="role.id" :value="role.id">
            {{ role.name }}
          </option>
        </select>
        <select v-model.number="filters.per_page" class="input w-32" @change="changePerPage(filters.per_page)">
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button class="px-4 py-2 border rounded-md" @click="loadUsers">تحديث</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow border border-slate-100 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
          <tr>
            <th class="px-4 py-3 text-left">الاسم</th>
            <th class="px-4 py-3 text-left">الإيميل</th>
            <th class="px-4 py-3 text-left">الدور</th>
            <th class="px-4 py-3 text-left">الحالة</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-t border-slate-100">
            <td class="px-4 py-3 font-medium">{{ user.name }}</td>
            <td class="px-4 py-3">{{ user.email }}</td>
            <td class="px-4 py-3 text-xs uppercase">{{ user.role?.name || user.role_name }}</td>
            <td class="px-4 py-3">
              <span
                class="px-2 py-1 rounded-full text-xs"
                :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
              >
                {{ user.is_active ? 'مفعل' : 'موقوف' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right space-x-2 rtl:space-x-reverse">
              <button class="text-primary text-xs" @click="openModal(user)">تعديل</button>
              <button class="text-red-500 text-xs" @click="remove(user.id)">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!users.length" class="text-center py-6 text-sm text-slate-400">لا توجد بيانات.</p>
    </div>

    <PaginationControls
      v-if="pagination.total"
      :meta="pagination"
      @change-page="changePage"
      @change-per-page="changePerPage"
    />

    <dialog ref="dialogRef" class="rounded-2xl p-0 w-full max-w-lg">
      <form class="p-6 space-y-4" @submit.prevent="submit">
        <h3 class="text-xl font-semibold text-slate-900">{{ editing ? 'تعديل' : 'مستخدم جديد' }}</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="label">الاسم</label>
            <input v-model="form.name" type="text" required class="input" />
          </div>
          <div>
            <label class="label">الإيميل</label>
            <input v-model="form.email" type="email" required class="input" />
          </div>
          <div>
            <label class="label">الدور</label>
            <select v-model="form.role_id" class="input" required>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="label">الحالة</label>
            <select v-model="form.is_active" class="input">
              <option :value="true">مفعل</option>
              <option :value="false">موقوف</option>
            </select>
          </div>
          <div class="md:col-span-2" v-if="!editing">
            <label class="label">كلمة المرور</label>
            <input v-model="form.password" type="password" required class="input" />
          </div>
        </div>
        <div class="flex justify-end gap-3">
          <button type="button" class="px-4 py-2 border rounded-md" @click="closeModal">إلغاء</button>
          <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">
            حفظ
          </button>
        </div>
      </form>
    </dialog>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import api from '../../../api';
import PaginationControls from '../../../components/common/PaginationControls.vue';

const users = ref([]);
const roles = ref([]);
const dialogRef = ref(null);
const editing = ref(false);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});
const filters = reactive({
  search: '',
  role_id: '',
  page: 1,
  per_page: 10,
});

const form = reactive({
  id: null,
  name: '',
  email: '',
  password: '',
  role_id: null,
  is_active: true,
});

async function loadUsers() {
  const { data } = await api.get('/admin/users', {
    params: {
      page: filters.page,
      per_page: filters.per_page,
      search: filters.search || undefined,
      role_id: filters.role_id || undefined,
    },
  });
  users.value = data.data;
  Object.assign(pagination, {
    current_page: data.meta.current_page,
    last_page: data.meta.last_page,
    per_page: data.meta.per_page,
    total: data.meta.total,
  });
}

async function loadRoles() {
  const { data } = await api.get('/admin/roles');
  roles.value = data.map((role) => ({ id: role.id, name: role.name }));
  if (!form.role_id && roles.value.length) {
    form.role_id = roles.value[0].id;
  }
}

function openModal(user) {
  editing.value = Boolean(user);
  form.id = user?.id || null;
  form.name = user?.name || '';
  form.email = user?.email || '';
  form.password = '';
  form.role_id = user?.role_id || roles.value[0]?.id || null;
  form.is_active = user?.is_active ?? true;
  dialogRef.value.showModal();
}

function closeModal() {
  dialogRef.value.close();
}

async function submit() {
  if (editing.value) {
    await api.put(`/admin/users/${form.id}`, {
      name: form.name,
      email: form.email,
      role_id: form.role_id,
      is_active: form.is_active,
    });
  } else {
    await api.post('/admin/users', {
      name: form.name,
      email: form.email,
      password: form.password,
      role_id: form.role_id || roles.value[0]?.id,
      is_active: form.is_active,
    });
  }
  closeModal();
  loadUsers();
}

async function remove(id) {
  if (!confirm('هل أنت متأكد من الحذف؟')) return;
  await api.delete(`/admin/users/${id}`);
  loadUsers();
}

function changePage(page) {
  filters.page = page;
  loadUsers();
}

function changePerPage(perPage) {
  filters.per_page = perPage;
  filters.page = 1;
  loadUsers();
}

watch(
  () => [filters.search, filters.role_id],
  () => {
    filters.page = 1;
    loadUsers();
  },
);

onMounted(async () => {
  await loadRoles();
  await loadUsers();
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
dialog {
  border: none;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
}
</style>

