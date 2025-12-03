<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="close">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">
            {{ currency ? ($t('admin.currencies.edit') || 'Edit Currency') : ($t('admin.currencies.create') || 'Add Currency') }}
          </h3>
          <button
            @click="close"
            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="label">{{ $t('admin.currencies.code') || 'Code' }} <span class="text-red-500">*</span></label>
              <input
                v-model="form.code"
                type="text"
                required
                maxlength="10"
                class="input"
                :placeholder="$t('admin.currencies.codePlaceholder') || 'e.g., EGP, USD, EUR'"
                :disabled="!!currency"
              />
              <p class="text-xs text-slate-500 mt-1">{{ $t('admin.currencies.codeHint') || 'ISO currency code (3 letters)' }}</p>
            </div>

            <div>
              <label class="label">{{ $t('admin.currencies.symbol') || 'Symbol' }} <span class="text-red-500">*</span></label>
              <input
                v-model="form.symbol"
                type="text"
                required
                maxlength="10"
                class="input"
                :placeholder="$t('admin.currencies.symbolPlaceholder') || 'e.g., £, $, €'"
              />
            </div>

            <div class="md:col-span-2">
              <label class="label">{{ $t('admin.currencies.name') || 'Name' }} <span class="text-red-500">*</span></label>
              <input
                v-model="form.name"
                type="text"
                required
                class="input"
                :placeholder="$t('admin.currencies.namePlaceholder') || 'e.g., Egyptian Pound'"
              />
            </div>

            <div>
              <label class="label">{{ $t('admin.currencies.sortOrder') || 'Sort Order' }}</label>
              <input
                v-model.number="form.sort_order"
                type="number"
                min="0"
                class="input"
              />
            </div>
          </div>

          <div class="space-y-3">
            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="w-4 h-4" />
              <span>{{ $t('common.active') || 'Active' }}</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input v-model="form.is_default" type="checkbox" class="w-4 h-4" />
              <span>{{ $t('admin.currencies.setAsDefault') || 'Set as Default' }}</span>
            </label>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
            >
              {{ $t('common.cancel') || 'Cancel' }}
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              {{ loading ? ($t('common.saving') || 'Saving...') : ($t('common.save') || 'Save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useApi } from '../../composables/useApi';
import { useToast } from '../../composables/useToast';
import { useI18n } from '../../composables/useI18n';

const props = defineProps({
  currency: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'saved']);

const { post, put } = useApi();
const toast = useToast();
const { t } = useI18n();

const loading = ref(false);
const form = reactive({
  code: '',
  name: '',
  symbol: '',
  is_active: true,
  is_default: false,
  sort_order: 0,
});

watch(() => props.currency, (newCurrency) => {
  if (newCurrency) {
    Object.assign(form, {
      code: newCurrency.code || '',
      name: newCurrency.name || '',
      symbol: newCurrency.symbol || '',
      is_active: newCurrency.is_active ?? true,
      is_default: newCurrency.is_default ?? false,
      sort_order: newCurrency.sort_order || 0,
    });
  } else {
    // Reset form for new currency
    Object.assign(form, {
      code: '',
      name: '',
      symbol: '',
      is_active: true,
      is_default: false,
      sort_order: 0,
    });
  }
}, { immediate: true });

async function submit() {
  try {
    loading.value = true;
    
    if (props.currency) {
      await put(`/admin/currencies/${props.currency.id}`, form);
      toast.success(t('admin.currencies.updated') || 'Currency updated successfully');
    } else {
      await post('/admin/currencies', form);
      toast.success(t('admin.currencies.created') || 'Currency created successfully');
    }
    
    emit('saved');
  } catch (err) {
    toast.error(err.response?.data?.message || t('errors.saveError') || 'Failed to save currency');
  } finally {
    loading.value = false;
  }
}

function close() {
  emit('close');
}
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
  background: white;
}
.dark .input {
  background: #1e293b;
  border-color: #334155;
  color: #f1f5f9;
}
.label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #475569;
  margin-bottom: 0.5rem;
}
.dark .label {
  color: #cbd5e1;
}
</style>

