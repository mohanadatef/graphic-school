<template>
  <div class="relative">
    <select
      :value="modelValue"
      @change="handleChange"
      class="filter-dropdown"
      :class="{ 'filter-dropdown-active': modelValue }"
    >
      <option value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="getOptionValue(option)" :value="getOptionValue(option)">
        {{ getOptionLabel(option) }}
      </option>
    </select>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  options: {
    type: Array,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'الكل',
  },
  valueKey: {
    type: String,
    default: 'id',
  },
  labelKey: {
    type: String,
    default: 'name',
  },
});

const emit = defineEmits(['update:modelValue']);

function getOptionValue(option) {
  if (typeof option === 'object' && option !== null) {
    return option[props.valueKey];
  }
  return option;
}

function getOptionLabel(option) {
  if (typeof option === 'object' && option !== null) {
    return option[props.labelKey] || option.name || option.title || String(option[props.valueKey]);
  }
  return String(option);
}

function handleChange(event) {
  const value = event.target.value;
  // Convert to number if the original value was a number
  const numValue = Number(value);
  if (!isNaN(numValue) && value !== '' && String(numValue) === String(value)) {
    emit('update:modelValue', numValue);
  } else {
    emit('update:modelValue', value);
  }
}
</script>

<style scoped>
.filter-dropdown {
  @apply text-xs px-3 py-1.5 border border-slate-200 rounded-lg bg-white text-slate-700;
  @apply hover:border-slate-300 focus:outline-none focus:ring-2 focus:border-primary;
  @apply transition-all duration-200 cursor-pointer appearance-none;
  @apply bg-no-repeat pr-8;
  min-width: 120px;
  max-width: 180px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364758b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-position: right 0.5rem center;
  background-size: 16px;
}

.filter-dropdown:focus {
  --tw-ring-color: rgba(29, 78, 216, 0.2);
  box-shadow: 0 0 0 2px var(--tw-ring-color);
}

.filter-dropdown-active {
  @apply border-primary text-primary font-medium;
  background-color: rgba(29, 78, 216, 0.05);
}

.filter-dropdown option {
  @apply text-slate-700 bg-white;
  padding: 0.5rem;
}
</style>
