<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ $t('student.community') || 'Community' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          {{ $t('student.communityDescription') || 'Connect with your classmates and instructors' }}
        </p>
      </div>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-primary text-white rounded-md inline-block"
      >
        {{ $t('student.communityCreatePost') || 'Create Post' }}
      </button>
    </div>

    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl shadow p-3 mb-4">
      <div class="flex flex-wrap gap-2 items-center">
        <input
          v-model="filters.search"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg w-40"
          :placeholder="$t('common.search') || 'Search...'"
          @input="handleSearch"
        />
        <select
          v-model="filters.group_id"
          class="text-xs px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg"
          @change="handleFilterChange"
        >
          <option value="">All Groups</option>
          <option v-for="group in myGroups" :key="group.id" :value="group.id">
            {{ group.name }}
          </option>
        </select>
      </div>
    </div>

    <div v-if="store.loading" class="text-center py-20">
      <div class="spinner-lg mx-auto mb-4"></div>
      <p class="text-slate-500 dark:text-slate-400">{{ $t('common.loading') || 'Loading...' }}</p>
    </div>

    <div v-else-if="store.posts.length === 0" class="text-center py-20">
      <p class="text-slate-500 dark:text-slate-400 text-lg">
        {{ $t('student.communityNoPosts') || 'No posts yet' }}
      </p>
    </div>

    <div v-else class="space-y-4">
      <!-- Pinned Posts -->
      <div v-if="store.pinnedPosts.length > 0">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">📌 Pinned</h3>
        <div v-for="post in store.pinnedPosts" :key="post.id" class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow p-4">
          <PostCard
            :post="post"
            :can-edit="post.user_id === currentUser?.id"
            @delete="handleDeletePost"
            @edit="handleEditPost"
            @view-comments="handleViewPost"
            @toggle-like="handleToggleLike"
          />
          <PostComments
            v-if="viewingPostId === post.id"
            :post-id="post.id"
            :comments="post.comments || []"
            @close="viewingPostId = null"
          />
        </div>
      </div>

      <!-- Regular Posts -->
      <div v-for="post in store.regularPosts" :key="post.id" class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow p-4">
        <PostCard
          :post="post"
          :can-edit="post.user_id === currentUser?.id"
          @delete="handleDeletePost"
          @edit="handleEditPost"
          @view-comments="handleViewPost"
          @toggle-like="handleToggleLike"
        />
        <PostComments
          v-if="viewingPostId === post.id"
          :post-id="post.id"
          :comments="post.comments || []"
          @close="viewingPostId = null"
        />
      </div>
    </div>

    <PaginationControls
      v-if="store.pagination.total > 0"
      :meta="store.pagination"
      @change-page="handlePageChange"
      @change-per-page="handlePerPageChange"
    />

    <!-- Create/Edit Post Modal -->
    <div
      v-if="showCreateModal || showEditModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4 dark:text-white">{{ editingPost ? 'Edit Post' : 'Create Post' }}</h3>
        <PostForm
          :post="editingPost"
          :groups="myGroups"
          @submit="handleSubmitPost"
          @cancel="closeModal"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useCommunityStore } from '@/stores/community';
import { useAuthStore } from '@/stores/auth';
import { groupService } from '@/services/api';
import PostCard from '@/views/dashboard/admin/Community/PostCard.vue';
import PostForm from '@/views/dashboard/admin/Community/PostForm.vue';
import PostComments from '@/views/dashboard/admin/Community/PostComments.vue';

const store = useCommunityStore();
const authStore = useAuthStore();
const myGroups = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingPost = ref(null);
const viewingPostId = ref(null);

const currentUser = computed(() => authStore.user);

const filters = ref({
  search: '',
  group_id: '',
});

onMounted(async () => {
  await Promise.all([
    loadMyGroups(),
    loadPosts(),
  ]);
});

async function loadMyGroups() {
  try {
    // Students should see groups they're enrolled in
    // This might need a specific endpoint
    const response = await groupService.getAll();
    myGroups.value = response.data || [];
  } catch (error) {
    console.error('Error loading groups:', error);
  }
}

async function loadPosts() {
  try {
    await store.fetchPosts(filters.value);
  } catch (error) {
    console.error('Error loading posts:', error);
  }
}

async function handleSubmitPost(postData) {
  try {
    if (editingPost.value) {
      await store.updatePost(editingPost.value.id, postData);
    } else {
      await store.createPost(postData);
    }
    closeModal();
  } catch (error) {
    alert('Failed to save post');
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

function handleViewPost(postId) {
  viewingPostId.value = viewingPostId.value === postId ? null : postId;
}

async function handleToggleLike(postId) {
  try {
    await store.toggleLike('posts', postId);
  } catch (error) {
    console.error('Error toggling like:', error);
  }
}

function closeModal() {
  showCreateModal.value = false;
  showEditModal.value = false;
  editingPost.value = null;
}

function handleSearch() {
  store.setPage(1);
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
</script>

