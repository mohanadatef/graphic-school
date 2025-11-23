<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Title</label>
      <input v-model="localConfig.title" @input="update" type="text" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Subtitle</label>
      <input v-model="localConfig.subtitle" @input="update" type="text" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Button Text</label>
      <input v-model="localConfig.button_text" @input="update" type="text" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Button Link</label>
      <input v-model="localConfig.button_link" @input="update" type="text" class="w-full px-3 py-2 border rounded-lg">
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  block: { type: Object, required: true },
});

const emit = defineEmits(['update']);

const localConfig = ref({ ...props.block.config });

watch(() => props.block.config, (newConfig) => {
  localConfig.value = { ...newConfig };
}, { deep: true });

function update() {
  emit('update', { config: { ...localConfig.value } });
}
</script>

