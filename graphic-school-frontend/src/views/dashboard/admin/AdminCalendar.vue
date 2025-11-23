<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('admin.calendar.title') || 'Calendar' }}</h2>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('admin.calendar.subtitle') || 'View all calendar events' }}</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div id="calendar" class="min-h-[600px]"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import api from '../../../services/api/client';
import { useToast } from '../../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const events = ref([]);
let calendarInstance = null;

async function loadEvents() {
  loading.value = true;
  try {
    const response = await api.get('/admin/calendar');
    events.value = response.data || [];
    
    // Initialize calendar (simplified - would use FullCalendar in production)
    renderCalendar();
  } catch (error) {
    console.error('Error loading events:', error);
    toast.error('Failed to load calendar events');
  } finally {
    loading.value = false;
  }
}

function renderCalendar() {
  // Simplified calendar rendering
  // In production, use FullCalendar library
  const calendarEl = document.getElementById('calendar');
  if (calendarEl) {
    calendarEl.innerHTML = `
      <div class="text-center py-20">
        <p class="text-slate-500 dark:text-slate-400">Calendar view will be implemented with FullCalendar library</p>
        <div class="mt-4 space-y-2">
          ${events.value.map(event => `
            <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-lg text-right">
              <p class="font-medium text-slate-900 dark:text-white">${event.title}</p>
              <p class="text-sm text-slate-600 dark:text-slate-400">${new Date(event.start_datetime).toLocaleString()}</p>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  }
}

onMounted(() => {
  loadEvents();
});
</script>

