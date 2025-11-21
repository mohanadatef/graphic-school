<template>
  <div class="notification-center">
    <div class="notification-header">
      <h3>{{ $t('notifications.title') }}</h3>
      <div class="header-actions">
        <button
          v-if="unreadCount > 0"
          @click="markAllAsRead"
          class="btn-mark-all"
        >
          {{ $t('notifications.markAllRead') }}
        </button>
        <button @click="$emit('close')" class="btn-close">×</button>
      </div>
    </div>

    <div class="notification-filters">
      <button
        :class="['filter-btn', { active: filter === 'all' }]"
        @click="filter = 'all'"
      >
        {{ $t('notifications.all') }}
      </button>
      <button
        :class="['filter-btn', { active: filter === 'unread' }]"
        @click="filter = 'unread'"
      >
        {{ $t('notifications.unread') }} ({{ unreadCount }})
      </button>
    </div>

    <div v-if="loading" class="loading">
      {{ $t('common.loading') }}
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else-if="filteredNotifications.length === 0" class="empty">
      {{ $t('notifications.noNotifications') }}
    </div>

    <div v-else class="notification-list">
      <div
        v-for="notification in filteredNotifications"
        :key="notification.id"
        :class="['notification-item', { unread: !notification.read_at }]"
        @click="handleNotificationClick(notification)"
      >
        <div class="notification-content">
          <div class="notification-title">{{ notification.title }}</div>
          <div class="notification-message">{{ notification.message }}</div>
          <div class="notification-time">
            {{ formatTime(notification.created_at) }}
          </div>
        </div>
        <div class="notification-actions">
          <button
            v-if="!notification.read_at"
            @click.stop="markAsRead(notification.id)"
            class="btn-mark-read"
            :title="$t('notifications.markAsRead')"
          >
            ✓
          </button>
          <button
            @click.stop="deleteNotification(notification.id)"
            class="btn-delete"
            :title="$t('common.delete')"
          >
            ×
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNotificationStore } from '../../stores/notifications';
import { useLocale } from '../../composables/useLocale';

const emit = defineEmits(['close', 'notification-click']);

const { t } = useLocale();
const notificationStore = useNotificationStore();

const filter = ref('all');
const loading = computed(() => notificationStore.loading);
const error = computed(() => notificationStore.error);
const unreadCount = computed(() => notificationStore.unreadCount);
const notifications = computed(() => notificationStore.notifications);

const filteredNotifications = computed(() => {
  if (filter.value === 'unread') {
    return notificationStore.unreadNotifications;
  }
  return notifications.value;
});

onMounted(async () => {
  await notificationStore.fetchNotifications();
  await notificationStore.fetchUnreadCount();
});

function formatTime(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (minutes < 1) return t('common.justNow');
  if (minutes < 60) return `${minutes} ${t('common.minutesAgo')}`;
  if (hours < 24) return `${hours} ${t('common.hoursAgo')}`;
  if (days < 7) return `${days} ${t('common.daysAgo')}`;
  return date.toLocaleDateString();
}

async function markAsRead(id) {
  await notificationStore.markAsRead(id);
}

async function markAllAsRead() {
  await notificationStore.markAllAsRead();
}

async function deleteNotification(id) {
  await notificationStore.deleteNotification(id);
}

function handleNotificationClick(notification) {
  emit('notification-click', notification);
  if (!notification.read_at) {
    markAsRead(notification.id);
  }
}
</script>

<style scoped>
.notification-center {
  width: 400px;
  max-height: 600px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
}

.notification-header {
  padding: 16px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.notification-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.btn-mark-all,
.btn-close {
  padding: 4px 12px;
  border: none;
  background: #f3f4f6;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-mark-all:hover {
  background: #e5e7eb;
}

.btn-close {
  font-size: 20px;
  line-height: 1;
}

.notification-filters {
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  gap: 8px;
}

.filter-btn {
  padding: 6px 12px;
  border: none;
  background: #f3f4f6;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.filter-btn.active {
  background: #3b82f6;
  color: white;
}

.notification-list {
  flex: 1;
  overflow-y: auto;
}

.notification-item {
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  cursor: pointer;
  transition: background 0.2s;
}

.notification-item:hover {
  background: #f9fafb;
}

.notification-item.unread {
  background: #eff6ff;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: 600;
  margin-bottom: 4px;
  color: #111827;
}

.notification-message {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.notification-time {
  font-size: 12px;
  color: #9ca3af;
}

.notification-actions {
  display: flex;
  gap: 4px;
}

.btn-mark-read,
.btn-delete {
  width: 24px;
  height: 24px;
  border: none;
  background: transparent;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-mark-read:hover {
  background: #dbeafe;
  color: #3b82f6;
}

.btn-delete:hover {
  background: #fee2e2;
  color: #ef4444;
}

.loading,
.error,
.empty {
  padding: 32px;
  text-align: center;
  color: #6b7280;
}

.error {
  color: #ef4444;
}
</style>

