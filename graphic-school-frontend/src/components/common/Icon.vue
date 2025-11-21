<template>
  <svg
    :class="['icon', sizeClass, colorClass]"
    :width="size"
    :height="size"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    viewBox="0 0 24 24"
  >
    <component :is="iconComponent" />
  </svg>
</template>

<script setup>
import { computed, h } from 'vue';

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  size: {
    type: [String, Number],
    default: 20,
  },
  color: {
    type: String,
    default: 'current',
  },
});

const sizeClass = computed(() => {
  const s = typeof props.size === 'number' ? props.size : parseInt(props.size);
  return `w-${s} h-${s}`;
});

const colorClass = computed(() => {
  const colors = {
    current: 'text-current',
    primary: 'text-primary',
    slate: 'text-slate-600',
    white: 'text-white',
    red: 'text-red-500',
    green: 'text-green-500',
    blue: 'text-blue-500',
  };
  return colors[props.color] || colors.current;
});

const icons = {
  dashboard: () => h('path', { d: 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z' }),
  users: () => h('path', { d: 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2' }), h('circle', { cx: 9, cy: 7, r: 4 }), h('path', { d: 'M23 21v-2a4 4 0 0 0-3-3.87' }), h('path', { d: 'M16 3.13a4 4 0 0 1 0 7.75' }),
  roles: () => h('path', { d: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z' }),
  courses: () => h('path', { d: 'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z' }), h('path', { d: 'M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z' }),
  sessions: () => h('circle', { cx: 12, cy: 12, r: 10 }), h('polyline', { points: '12 6 12 12 16 14' }),
  enrollments: () => h('path', { d: 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2' }), h('circle', { cx: 8.5, cy: 7, r: 4 }), h('path', { d: 'M20 8v6' }), h('path', { d: 'M23 11h-6' }),
  attendance: () => h('path', { d: 'M9 11l3 3L22 4' }), h('path', { d: 'M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11' }),
  settings: () => h('circle', { cx: 12, cy: 12, r: 3 }), h('path', { d: 'M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24-4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24 4.24' }),
  sliders: () => h('rect', { x: 3, y: 3, width: 18, height: 18, rx: 2, ry: 2 }), h('line', { x1: 9, y1: 3, x2: 9, y2: 21 }),
  contacts: () => h('path', { d: 'M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z' }), h('polyline', { points: '22,6 12,13 2,6' }),
  add: () => h('line', { x1: 12, y1: 5, x2: 12, y2: 19 }), h('line', { x1: 5, y1: 12, x2: 19, y2: 12 }),
  edit: () => h('path', { d: 'M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7' }), h('path', { d: 'M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z' }),
  delete: () => h('polyline', { points: '3 6 5 6 21 6' }), h('path', { d: 'M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2' }),
  search: () => h('circle', { cx: 11, cy: 11, r: 8 }), h('path', { d: 'M21 21l-4.35-4.35' }),
  filter: () => h('polygon', { points: '22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3' }),
  chevronRight: () => h('polyline', { points: '9 18 15 12 9 6' }),
  chevronLeft: () => h('polyline', { points: '15 18 9 12 15 6' }),
  arrowLeft: () => h('line', { x1: 19, y1: 12, x2: 5, y2: 12 }), h('polyline', { points: '12 19 5 12 12 5' }),
  check: () => h('polyline', { points: '20 6 9 17 4 12' }),
  x: () => h('line', { x1: 18, y1: 6, x2: 6, y2: 18 }), h('line', { x1: 6, y1: 6, x2: 18, y2: 18 }),
  loading: () => h('circle', { cx: 12, cy: 12, r: 10, opacity: 0.25 }), h('path', { d: 'M12 2a10 10 0 0 1 10 10', opacity: 0.75 }),
};

const iconComponent = computed(() => {
  return icons[props.name] || icons.dashboard;
});
</script>

<style scoped>
.icon {
  display: inline-block;
  flex-shrink: 0;
}
</style>

