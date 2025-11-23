<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.groups.title') || 'Groups' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('admin.groups.subtitle') || 'Batch' }}: {{ batch?.code || batchId }}
        </p>
      </div>
      <button
        @click="showCreateModal = true"
        class="btn-primary inline-flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        {{ $t('admin.groups.create') || 'Create Group' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="groups.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('admin.groups.noGroups') || 'No groups found' }}</p>
    </div>

    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="group in groups"
        :key="group.id"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ group.name || group.code }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ group.code }}</p>
          </div>
          <span :class="group.is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'" class="px-2 py-1 text-xs font-medium rounded-full">
            {{ group.is_active ? ($t('common.active') || 'Active') : ($t('common.inactive') || 'Inactive') }}
          </span>
        </div>
        
        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400 mb-4">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>{{ $t('admin.groups.capacity') || 'Capacity' }}: {{ group.capacity }}</span>
          </div>
          <div v-if="group.room" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>{{ group.room }}</span>
          </div>
          <div v-if="group.students_count !== undefined" class="flex items-center gap-2">
            <span>{{ $t('admin.groups.students') || 'Students' }}: {{ group.students_count }}</span>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="$router.push({ name: 'admin-groups-view', params: { groupId: group.id } })"
            class="btn-secondary flex-1 text-sm"
          >
            {{ $t('common.view') || 'View' }}
          </button>
          <button
            @click="editGroup(group)"
            class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingGroup" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
          {{ editingGroup ? ($t('admin.groups.edit') || 'Edit Group') : ($t('admin.groups.create') || 'Create Group') }}
        </h3>
        <form @submit.prevent="saveGroup" class="space-y-4">
          <div>
            <label class="label">{{ $t('admin.groups.code') || 'Code' }}</label>
            <input v-model="groupForm.code" class="input" />
          </div>
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="label">{{ $t('admin.groups.capacity') || 'Capacity' }}</label>
              <input v-model.number="groupForm.capacity" type="number" min="1" class="input" />
            </div>
            <div>
              <label class="label">{{ $t('admin.groups.room') || 'Room' }}</label>
              <input v-model="groupForm.room" class="input" />
            </div>
          </div>
          <div>
            <label class="label">{{ $t('admin.groups.instructor') || 'Instructor' }}</label>
            <select v-model="groupForm.instructor_id" class="input">
              <option value="">{{ $t('common.none') || 'None' }}</option>
              <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                {{ instructor.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="label mb-4 block">{{ $t('admin.groups.translations') || 'Translations' }}</label>
            <div class="space-y-3">
              <div v-for="lang in availableLanguages" :key="lang.code" class="p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2 block">{{ lang.native_name }}</label>
                <input
                  v-model="groupForm.translations[lang.code].name"
                  class="input text-sm"
                  :placeholder="`${$t('admin.groups.name') || 'Name'} (${lang.native_name})`"
                  :required="lang.code === 'ar'"
                />
                <textarea
                  v-model="groupForm.translations[lang.code].description"
                  class="input text-sm mt-2"
                  rows="2"
                  :placeholder="`${$t('admin.groups.description') || 'Description'} (${lang.native_name})`"
                ></textarea>
              </div>
            </div>
          </div>
          <div>
            <label class="flex items-center gap-3 cursor-pointer">
              <input type="checkbox" v-model="groupForm.is_active" class="w-5 h-5 text-primary border-slate-300 rounded" />
              <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('common.active') || 'Active' }}</span>
            </label>
          </div>
          <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary flex-1" :disabled="saving">
              {{ saving ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
            </button>
            <button type="button" @click="closeModal" class="btn-secondary flex-1">
              {{ $t('common.cancel') || 'Cancel' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';

const route = useRoute();
const router = useRouter();
const { get, post, put } = useApi();
const toast = useToast();

const batchId = route.params.batchId || route.query.batch_id;
const loading = ref(false);
const saving = ref(false);
const batch = ref(null);
const groups = ref([]);
const instructors = ref([]);
const showCreateModal = ref(false);
const editingGroup = ref(null);
const availableLanguages = ref([]);

const groupForm = reactive({
  code: '',
  capacity: 20,
  room: '',
  instructor_id: null,
  is_active: true,
  translations: {},
});

onMounted(async () => {
  await loadLanguages();
  await loadBatch();
  await loadInstructors();
  await loadGroups();
});

async function loadLanguages() {
  try {
    const response = await get('/locales');
    let languages = [];
    if (response?.data?.locales) languages = response.data.locales;
    else if (response?.locales) languages = response.locales;
    else if (Array.isArray(response)) languages = response;
    
    if (languages.length === 0) {
      languages = [
        { code: 'en', name: 'English', native_name: 'English' },
        { code: 'ar', name: 'Arabic', native_name: 'العربية' },
      ];
    }
    availableLanguages.value = languages;
    
    languages.forEach(lang => {
      groupForm.translations[lang.code] = {
        locale: lang.code,
        name: '',
        description: '',
      };
    });
  } catch (error) {
    console.error('Error loading languages:', error);
  }
}

async function loadBatch() {
  if (!batchId) return;
  try {
    const response = await get(`/admin/batches/${batchId}`);
    batch.value = response?.data || response;
  } catch (error) {
    console.error('Error loading batch:', error);
  }
}

async function loadInstructors() {
  try {
    const response = await get('/admin/users?role=instructor');
    instructors.value = (response?.data || response || []).filter(u => u.role?.name === 'instructor');
  } catch (error) {
    console.error('Error loading instructors:', error);
  }
}

async function loadGroups() {
  try {
    loading.value = true;
    const response = await get(`/admin/groups?batch_id=${batchId}`);
    groups.value = response?.data || response || [];
  } catch (error) {
    toast.error('Failed to load groups');
    console.error(error);
  } finally {
    loading.value = false;
  }
}

function editGroup(group) {
  editingGroup.value = group;
  groupForm.code = group.code || '';
  groupForm.capacity = group.capacity || 20;
  groupForm.room = group.room || '';
  groupForm.instructor_id = group.instructor_id || null;
  groupForm.is_active = group.is_active;
  
  if (group.translations) {
    group.translations.forEach(trans => {
      if (groupForm.translations[trans.locale]) {
        groupForm.translations[trans.locale].name = trans.name || '';
        groupForm.translations[trans.locale].description = trans.description || '';
      }
    });
  }
  
  showCreateModal.value = true;
}

function closeModal() {
  showCreateModal.value = false;
  editingGroup.value = null;
  Object.keys(groupForm.translations).forEach(locale => {
    groupForm.translations[locale].name = '';
    groupForm.translations[locale].description = '';
  });
  groupForm.code = '';
  groupForm.capacity = 20;
  groupForm.room = '';
  groupForm.instructor_id = null;
  groupForm.is_active = true;
}

async function saveGroup() {
  try {
    saving.value = true;
    const translationsArray = availableLanguages.value.map(lang => groupForm.translations[lang.code]);
    
    const data = {
      batch_id: batchId,
      code: groupForm.code,
      capacity: groupForm.capacity,
      room: groupForm.room,
      instructor_id: groupForm.instructor_id || null,
      is_active: groupForm.is_active,
      translations: translationsArray,
    };
    
    if (editingGroup.value) {
      await put(`/admin/groups/${editingGroup.value.id}`, data);
      toast.success('Group updated successfully');
    } else {
      await post('/admin/groups', data);
      toast.success('Group created successfully');
    }
    
    closeModal();
    await loadGroups();
  } catch (error) {
    toast.error('Failed to save group');
    console.error(error);
  } finally {
    saving.value = false;
  }
}
</script>

