<template>
  <div class="space-y-3">
    <label v-if="label" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Single Language Mode -->
    <div v-if="!hasMultipleLanguages">
      <textarea
        :value="singleValue"
        @input="updateSingleValue"
        :required="required"
        :placeholder="placeholder"
        :rows="rows"
        class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
      ></textarea>
    </div>

    <!-- Multi-Language Mode -->
    <div v-else class="space-y-3">
      <div
        v-for="language in activeLanguages"
        :key="language.code"
        class="space-y-1"
      >
        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400">
          {{ language.native_name || language.name }}
          <span v-if="language.is_default" class="text-xs text-blue-600 dark:text-blue-400">({{ $t('admin.languages.default') || 'Default' }})</span>
        </label>
        <textarea
          :value="getValue(language.code)"
          @input="updateValue(language.code, $event.target.value)"
          :required="required && language.is_default"
          :placeholder="placeholder"
          :rows="rows"
          :dir="language.is_rtl ? 'rtl' : 'ltr'"
          class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent"
        ></textarea>
      </div>
    </div>

    <p v-if="hint" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useLanguageStore } from '../../stores/language';

const props = defineProps({
  modelValue: {
    type: [String, Object],
    default: '',
  },
  fieldName: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  rows: {
    type: Number,
    default: 3,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);

const languageStore = useLanguageStore();
const hasMultipleLanguages = computed(() => languageStore.hasMultipleLanguages);
const activeLanguages = computed(() => languageStore.activeLanguages);

const singleValue = computed(() => {
  if (typeof props.modelValue === 'string') {
    return props.modelValue;
  }
  if (typeof props.modelValue === 'object' && props.modelValue) {
    const defaultLang = languageStore.getDefaultLanguage;
    return props.modelValue[defaultLang?.code || 'en'] || '';
  }
  return '';
});

function getValue(languageCode) {
  if (typeof props.modelValue === 'object' && props.modelValue) {
    return props.modelValue[languageCode] || '';
  }
  if (languageCode === (languageStore.getDefaultLanguage?.code || 'en')) {
    return typeof props.modelValue === 'string' ? props.modelValue : '';
  }
  return '';
}

function updateSingleValue(event) {
  emit('update:modelValue', event.target.value);
}

function updateValue(languageCode, value) {
  if (!hasMultipleLanguages.value) {
    emit('update:modelValue', value);
    return;
  }

  const currentValue = typeof props.modelValue === 'object' && props.modelValue ? { ...props.modelValue } : {};
  currentValue[languageCode] = value;
  emit('update:modelValue', currentValue);
}
</script>

