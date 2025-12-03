<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">{{ $t('admin.community') || 'Community Management' }}</h2>
        <p class="text-sm text-slate-500">{{ $t('admin.communityDescription') || 'Manage community posts, comments, and reports' }}</p>
      </div>
      <div class="flex gap-2">
        <button
          @click="activeTab = 'posts'"
          :class="[
            'px-4 py-2 rounded-md',
            activeTab === 'posts' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-700'
          ]"
        >
          Posts
        </button>
        <button
          @click="activeTab = 'reports'"
          :class="[
            'px-4 py-2 rounded-md',
            activeTab === 'reports' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-700'
          ]"
        >
          Reports ({{ reports.length }})
        </button>
      </div>
    </div>

    <!-- Posts Tab -->
    <div v-if="activeTab === 'posts'">
      <div class="bg-white border border-slate-100 rounded-2xl shadow p-3 mb-4">
        <div class="flex flex-wrap gap-2 items-center">
          <input
            v-model="filters.search"
            class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg w-40"
            :placeholder="$t('common.search') || 'Search...'"
            @input="handleSearch"
          />
          <select
            v-model="filters.group_id"
            class="text-xs px-3 py-1.5 border border-slate-200 rounded-lg"
            @change="handleFilterChange"
          >
            <option value="">All Groups</option>
            <option v-for="group in groups" :key="group.id" :value="group.id">
              {{ group.name }}
            </option>
          </select>
        </div>
      </div>

      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="store.posts.length === 0" class="text-center py-20">
        <p class="text-slate-500 text-lg">{{ $t('admin.communityNoPosts') || 'No posts found' }}</p>
      </div>

      <div v-else class="space-y-4">
        <!-- Pinned Posts -->
        <div v-if="store.pinnedPosts.length > 0">
          <h3 class="text-sm font-semibold text-slate-700 mb-2">📌 Pinned</h3>
          <div v-for="post in store.pinnedPosts" :key="post.id" class="bg-white border border-slate-100 rounded-xl shadow p-4">
            <PostCard :post="post" @delete="handleDeletePost" @edit="handleEditPost" />
          </div>
        </div>

        <!-- Regular Posts -->
        <div v-for="post in store.regularPosts" :key="post.id" class="bg-white border border-slate-100 rounded-xl shadow p-4">
          <PostCard :post="post" @delete="handleDeletePost" @edit="handleEditPost" />
        </div>
      </div>

      <PaginationControls
        v-if="store.pagination.total > 0"
        :meta="store.pagination"
        @change-page="handlePageChange"
        @change-per-page="handlePerPageChange"
      />
    </div>

    <!-- Reports Tab -->
    <div v-if="activeTab === 'reports'">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-lg mx-auto mb-4"></div>
        <p class="text-slate-500">{{ $t('common.loading') || 'Loading...' }}</p>
      </div>

      <div v-else-if="reports.length === 0" class="text-center py-20">
        <p class="text-slate-500 text-lg">No reports found</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="report in reports" :key="report.id" class="bg-white border border-slate-100 rounded-xl shadow p-4">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-semibold text-red-600">REPORTED</span>
                <span class="text-xs text-slate-500">{{ formatDate(report.created_at) }}</span>
              </div>
              <p class="text-sm font-medium mb-1">Reason: {{ report.reason }}</p>
              <p class="text-xs text-slate-500">Reported by: {{ report.user?.name }}</p>
              <p v-if="report.reportable?.title" class="text-sm mt-2">{{ report.reportable.title }}</p>
              <p v-if="report.reportable?.body" class="text-sm text-slate-600 mt-1">{{ report.reportable.body }}</p>
            </div>
            <div class="flex gap-2">
              <button
                @click="handleDismissReport(report.id)"
                class="px-3 py-1 text-xs border rounded"
              >
                Dismiss
              </button>
              <button
                @click="handleDeleteReported(report)"
                class="px-3 py-1 text-xs bg-red-600 text-white rounded"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Post Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="showEditModal = false"
    >
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Edit Post</h3>
        <PostForm
          :post="editingPost"
          @submit="handleUpdatePost"
          @cancel="showEditModal = false"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useCommunityStore } from '@/stores/community';
import { groupService } from '@/services/api';
import PostCard from './Community/PostCard.vue';
import PostForm from './Community/PostForm.vue';

const store = useCommunityStore();
const groups = ref([]);
const reports = ref([]);
const loading = ref(false);
const activeTab = ref('posts');
const showEditModal = ref(false);
const editingPost = ref(null);

const filters = ref({
  search: '',
  group_id: '',
});

onMounted(async () => {
  await Promise.all([
    loadPosts(),
    loadGroups(),
    loadReports(),
  ]);
});

async function loadPosts() {
  try {
    await store.fetchPosts(filters.value);
  } catch (error) {
    console.error('Error loading posts:', error);
  }
}

async function loadGroups() {
  try {
    const response = await groupService.getAll();
    groups.value = response.data || [];
  } catch (error) {
    console.error('Error loading groups:', error);
  }
}

async function loadReports() {
  try {
    await store.fetchReports();
    reports.value = store.reports;
  } catch (error) {
    console.error('Error loading reports:', error);
  }
}

async function handleDeletePost(id) {
  if (!confirm('Are you sure you want to delete this post?')) return;
  try {
    await store.deletePost(id);
  } catch (error) {
    alert('Failed to delete post');
  }
}

function handleEditPost(post) {
  editingPost.value = post;
  showEditModal.value = true;
}

async function handleUpdatePost(postData) {
  try {
    await store.updatePost(editingPost.value.id, postData);
    showEditModal.value = false;
    editingPost.value = null;
  } catch (error) {
    alert('Failed to update post');
  }
}

async function handleDismissReport(reportId) {
  // TODO: Implement dismiss report endpoint
  alert('Dismiss report functionality to be implemented');
}

async function handleDeleteReported(report) {
  if (!confirm('Are you sure you want to delete this reported content?')) return;
  try {
    // Delete the reported item
    if (report.reportable_type === 'CommunityPost') {
      await store.deletePost(report.reportable_id);
    }
    // Remove report from list
    reports.value = reports.value.filter(r => r.id !== report.id);
  } catch (error) {
    alert('Failed to delete reported content');
  }
}

function handleSearch() {
  loadPosts();
}

function handleFilterChange() {
  store.setPage(1);
  loadPosts();
}

function handlePageChange(page) {
  store.setPage(page);
  loadPosts();
}

function handlePerPageChange(perPage) {
  store.setPerPage(perPage);
  loadPosts();
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString();
}
</script>

