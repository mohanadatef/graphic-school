<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Title *</label>
      <input
        v-model="formData.title"
        type="text"
        required
        class="w-full px-3 py-2 border rounded"
      />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Content *</label>
      <textarea
        v-model="formData.body"
        required
        rows="6"
        class="w-full px-3 py-2 border rounded"
      />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Group (Optional)</label>
      <select v-model="formData.group_id" class="w-full px-3 py-2 border rounded">
        <option value="">No Group</option>
        <option v-for="group in groups" :key="group.id" :value="group.id">
          {{ group.name }}
        </option>
      </select>
    </div>
    <div class="flex gap-2">
      <input
        v-model="formData.is_pinned"
        type="checkbox"
        id="pinned"
      />
      <label for="pinned" class="text-sm">Pin post</label>
    </div>
    <div class="flex gap-3">
      <button
        type="button"
        @click="$emit('cancel')"
        class="flex-1 px-4 py-2 border rounded"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="flex-1 px-4 py-2 bg-primary text-white rounded"
      >
        {{ post ? 'Update' : 'Create' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { groupService } from '@/services/api';

const props = defineProps({
  post: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['submit', 'cancel']);

const groups = ref([]);
const formData = ref({
  title: '',
  body: '',
  group_id: '',
  is_pinned: false,
});

onMounted(async () => {
  if (props.post) {
    formData.value = {
      title: props.post.title || '',
      body: props.post.body || '',
      group_id: props.post.group_id || '',
      is_pinned: props.post.is_pinned || false,
    };
  }
  await loadGroups();
});

async function loadGroups() {
  try {
    const response = await groupService.getAll();
    groups.value = response.data || [];
  } catch (error) {
    console.error('Error loading groups:', error);
  }
}

function handleSubmit() {
  emit('submit', { ...formData.value });
}
</script>

