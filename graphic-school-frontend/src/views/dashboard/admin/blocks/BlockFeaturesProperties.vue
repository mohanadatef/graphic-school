<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Title</label>
      <input v-model="localConfig.title" @input="update" type="text" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Features</label>
      <div v-for="(feature, index) in localConfig.features || []" :key="index" class="mb-2 p-2 border rounded">
        <input v-model="feature.title" @input="update" type="text" placeholder="Title" class="w-full mb-1 px-2 py-1 border rounded text-sm">
        <input v-model="feature.description" @input="update" type="text" placeholder="Description" class="w-full px-2 py-1 border rounded text-sm">
      </div>
      <button @click="addFeature" class="btn-secondary text-sm w-full mt-2">Add Feature</button>
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

if (!localConfig.value.features) {
  localConfig.value.features = [];
}

watch(() => props.block.config, (newConfig) => {
  localConfig.value = { ...newConfig };
  if (!localConfig.value.features) {
    localConfig.value.features = [];
  }
}, { deep: true });

function addFeature() {
  localConfig.value.features.push({ icon: 'fas fa-star', title: '', description: '' });
  update();
}

function update() {
  emit('update', { config: { ...localConfig.value } });
}
</script>

