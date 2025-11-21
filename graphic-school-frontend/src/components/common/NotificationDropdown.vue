<template>
  <div class="notification-dropdown">
    <button
      class="notification-icon"
      @click="toggleDropdown"
      :class="{ 'has-unread': unreadCount > 0 }"
    >
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span v-if="unreadCount > 0" class="badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
    </button>

    <div v-if="isOpen" class="dropdown-content">
      <NotificationCenter
        @close="isOpen = false"
        @notification-click="handleNotificationClick"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useNotificationStore } from '../../stores/notifications';
import NotificationCenter from './NotificationCenter.vue';

const notificationStore = useNotificationStore();
const isOpen = ref(false);
const unreadCount = computed(() => notificationStore.unreadCount);

onMounted(async () => {
  await notificationStore.fetchUnreadCount();
  // Poll for new notifications every 30 seconds
  const interval = setInterval(async () => {
    await notificationStore.fetchUnreadCount();
  }, 30000);
  onUnmounted(() => clearInterval(interval));
});

function toggleDropdown() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    notificationStore.fetchNotifications();
  }
}

function handleNotificationClick(notification) {
  // Handle notification click (navigate, etc.)
  console.log('Notification clicked:', notification);
}
</script>


<style scoped>
.notification-dropdown {
  position: relative;
}

.notification-icon {
  position: relative;
  padding: 8px;
  border: none;
  background: transparent;
  cursor: pointer;
  border-radius: 4px;
  transition: background 0.2s;
}

.notification-icon:hover {
  background: #f3f4f6;
}

.notification-icon.has-unread {
  color: #3b82f6;
}

.badge {
  position: absolute;
  top: 4px;
  right: 4px;
  background: #ef4444;
  color: white;
  border-radius: 10px;
  padding: 2px 6px;
  font-size: 10px;
  font-weight: 600;
  min-width: 18px;
  text-align: center;
}

.dropdown-content {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  z-index: 1000;
}
</style>

