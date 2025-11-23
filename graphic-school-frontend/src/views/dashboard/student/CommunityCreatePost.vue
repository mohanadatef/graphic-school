<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-2">{{ $t('community.createPost.title') || 'Title' }}</label>
      <input v-model="form.title" type="text" class="w-full px-4 py-2 border rounded-lg" required>
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">{{ $t('community.createPost.body') || 'Content' }}</label>
      <textarea v-model="form.body" rows="6" class="w-full px-4 py-2 border rounded-lg" required></textarea>
    </div>

    <div>
      <label class="block text-sm font-medium mb-2">{{ $t('community.createPost.tags') || 'Tags (comma separated)' }}</label>
      <input v-model="tagsInput" type="text" :placeholder="$t('community.createPost.tagsPlaceholder') || 'e.g., question, help, discussion'" class="w-full px-4 py-2 border rounded-lg">
    </div>

    <div class="flex gap-4">
      <button @click="$emit('cancel')" class="btn-secondary flex-1">{{ $t('common.cancel') || 'Cancel' }}</button>
      <button @click="submit" :disabled="submitting" class="btn-primary flex-1">
        <span v-if="submitting">{{ $t('common.submitting') || 'Submitting...' }}</span>
        <span v-else>{{ $t('common.submit') || 'Submit' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const emit = defineEmits(['created', 'cancel']);
const toast = useToast();
const submitting = ref(false);
const form = ref({
  title: '',
  body: '',
  program_id: null,
  batch_id: null,
  group_id: null,
});
const tagsInput = ref('');

async function submit() {
  if (!form.value.title || !form.value.body) {
    toast.error('Title and body are required');
    return;
  }

  submitting.value = true;
  try {
    const data = { ...form.value };
    if (tagsInput.value) {
      data.tags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
    }
    await api.post('/student/community/posts', data);
    toast.success('Post created successfully');
    emit('created');
    form.value = { title: '', body: '', program_id: null, batch_id: null, group_id: null };
    tagsInput.value = '';
  } catch (error) {
    toast.error('Failed to create post');
  } finally {
    submitting.value = false;
  }
}
</script>

