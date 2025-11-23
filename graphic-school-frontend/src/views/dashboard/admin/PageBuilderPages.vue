<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('pageBuilder.pages.title') || 'Page Builder' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('pageBuilder.pages.subtitle') || 'Create and manage your website pages' }}</p>
      </div>
      <button @click="showCreateModal = true" class="btn-primary">{{ $t('pageBuilder.pages.createPage') || 'Create Page' }}</button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-4">
      <div class="flex flex-wrap gap-4">
        <select v-model="filters.status" @change="loadPages" class="px-4 py-2 border rounded-lg">
          <option value="">{{ $t('pageBuilder.pages.allStatuses') || 'All Statuses' }}</option>
          <option value="published">{{ $t('pageBuilder.pages.published') || 'Published' }}</option>
          <option value="draft">{{ $t('pageBuilder.pages.draft') || 'Draft' }}</option>
        </select>
        <input v-model="filters.search" @input="loadPages" type="text" :placeholder="$t('pageBuilder.pages.search') || 'Search pages...'" class="px-4 py-2 border rounded-lg flex-1 max-w-xs">
      </div>
    </div>

    <!-- Pages List -->
    <div v-if="loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="pages.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">{{ $t('pageBuilder.pages.noPages') || 'No pages found' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="page in pages" :key="page.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <div class="flex items-start justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ page.title }}</h3>
          <span :class="getStatusClass(page.status)" class="px-2 py-1 rounded-full text-xs font-medium">
            {{ page.status }}
          </span>
        </div>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ page.description || 'No description' }}</p>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-4">
          <span>{{ page.slug }}</span>
          <span>•</span>
          <span>{{ page.language.toUpperCase() }}</span>
        </div>
        <div class="flex gap-2">
          <router-link :to="{ name: 'page-builder-editor', params: { id: page.id } }" class="btn-primary flex-1 text-center text-sm">
            {{ $t('pageBuilder.pages.edit') || 'Edit' }}
          </router-link>
          <button @click="duplicatePage(page.id)" class="btn-secondary text-sm">{{ $t('pageBuilder.pages.duplicate') || 'Duplicate' }}</button>
          <button @click="deletePage(page.id)" class="btn-danger text-sm">{{ $t('common.delete') || 'Delete' }}</button>
        </div>
      </div>
    </div>

    <!-- Create Page Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showCreateModal = false">
      <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-lg w-full mx-4">
        <h3 class="text-xl font-bold mb-4">{{ $t('pageBuilder.pages.createPage') || 'Create Page' }}</h3>
        <form @submit.prevent="createPage" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('pageBuilder.pages.title') || 'Title' }}</label>
            <input v-model="pageForm.title" type="text" class="w-full px-4 py-2 border rounded-lg" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('pageBuilder.pages.slug') || 'Slug' }}</label>
            <input v-model="pageForm.slug" type="text" class="w-full px-4 py-2 border rounded-lg">
            <p class="text-xs text-slate-500 mt-1">{{ $t('pageBuilder.pages.slugHint') || 'Leave empty to auto-generate from title' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('pageBuilder.pages.description') || 'Description (SEO)' }}</label>
            <textarea v-model="pageForm.description" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">{{ $t('pageBuilder.pages.language') || 'Language' }}</label>
            <select v-model="pageForm.language" class="w-full px-4 py-2 border rounded-lg">
              <option value="en">English</option>
              <option value="ar">Arabic</option>
            </select>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="showCreateModal = false" class="btn-secondary">{{ $t('common.cancel') || 'Cancel' }}</button>
            <button type="submit" class="btn-primary" :disabled="creating">{{ creating ? ($t('common.creating') || 'Creating...') : ($t('common.create') || 'Create') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const router = useRouter();
const toast = useToast();
const loading = ref(false);
const creating = ref(false);
const pages = ref([]);
const showCreateModal = ref(false);
const filters = ref({
  status: '',
  search: '',
});
const pageForm = ref({
  title: '',
  slug: '',
  description: '',
  language: 'en',
});

async function loadPages() {
  loading.value = true;
  try {
    const params = {};
    if (filters.value.status) params.status = filters.value.status;
    if (filters.value.search) params.search = filters.value.search;
    const response = await api.get('/page-builder/pages', { params });
    pages.value = response.data.data?.data || response.data.data || [];
  } catch (error) {
    console.error('Error loading pages:', error);
    toast.error('Failed to load pages');
  } finally {
    loading.value = false;
  }
}

async function createPage() {
  creating.value = true;
  try {
    const response = await api.post('/page-builder/pages', pageForm.value);
    toast.success('Page created successfully');
    showCreateModal.value = false;
    pageForm.value = { title: '', slug: '', description: '', language: 'en' };
    await loadPages();
    router.push({ name: 'page-builder-editor', params: { id: response.data.data.id } });
  } catch (error) {
    console.error('Error creating page:', error);
    toast.error(error.response?.data?.message || 'Failed to create page');
  } finally {
    creating.value = false;
  }
}

async function duplicatePage(id) {
  try {
    const response = await api.post(`/page-builder/pages/${id}/duplicate`);
    toast.success('Page duplicated successfully');
    await loadPages();
  } catch (error) {
    toast.error('Failed to duplicate page');
  }
}

async function deletePage(id) {
  if (!confirm('Are you sure you want to delete this page?')) return;
  try {
    await api.delete(`/page-builder/pages/${id}`);
    toast.success('Page deleted successfully');
    await loadPages();
  } catch (error) {
    toast.error('Failed to delete page');
  }
}

function getStatusClass(status) {
  return status === 'published' 
    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
}

onMounted(() => {
  loadPages();
});
</script>

