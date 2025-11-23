<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">الرسائل</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">التواصل مع الطلاب</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Conversations List -->
      <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
          <h3 class="font-semibold text-slate-900 dark:text-white">المحادثات</h3>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[600px] overflow-y-auto">
          <div
            v-for="conversation in conversations"
            :key="conversation.id"
            @click="selectConversation(conversation)"
            class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-150 cursor-pointer"
            :class="{ 'bg-primary/10 border-r-4 border-primary': selectedConversation?.id === conversation.id }"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-900 dark:text-white truncate">
                  {{ conversation.student?.name || 'طالب' }}
                </p>
                <p class="text-sm text-slate-500 dark:text-slate-400 truncate">
                  {{ conversation.course?.title || 'كورس' }}
                </p>
                <p v-if="conversation.subject" class="text-xs text-slate-400 dark:text-slate-500 mt-1 truncate">
                  {{ conversation.subject }}
                </p>
              </div>
              <div v-if="conversation.unread_count > 0" class="flex-shrink-0">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-primary text-white text-xs font-bold rounded-full">
                  {{ conversation.unread_count }}
                </span>
              </div>
            </div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">
              {{ formatDate(conversation.last_message_at) }}
            </p>
          </div>
        </div>
        <p v-if="loading" class="text-center py-6 text-sm text-slate-400">جاري التحميل...</p>
        <p v-else-if="!conversations.length" class="text-center py-6 text-sm text-slate-400">لا توجد محادثات.</p>
      </div>

      <!-- Messages View -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
        <div v-if="selectedConversation" class="flex-1 flex flex-col">
          <!-- Conversation Header -->
          <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-semibold text-slate-900 dark:text-white">
                  {{ selectedConversation.student?.name || 'طالب' }}
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                  {{ selectedConversation.course?.title || 'كورس' }}
                </p>
              </div>
              <button
                @click="archiveConversation"
                class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
              >
                أرشفة
              </button>
            </div>
          </div>

          <!-- Messages -->
          <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="messagesContainer">
            <div
              v-for="message in messages"
              :key="message.id"
              class="flex"
              :class="message.sender_id === currentUserId ? 'justify-end' : 'justify-start'"
            >
              <div
                class="max-w-[70%] rounded-lg p-3"
                :class="message.sender_id === currentUserId
                  ? 'bg-primary text-white'
                  : 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white'"
              >
                <p class="text-sm whitespace-pre-wrap">{{ message.message }}</p>
                <p class="text-xs mt-1 opacity-70">
                  {{ formatTime(message.created_at) }}
                </p>
              </div>
            </div>
            <p v-if="loadingMessages" class="text-center text-sm text-slate-400">جاري التحميل...</p>
            <p v-else-if="!messages.length" class="text-center text-sm text-slate-400">لا توجد رسائل.</p>
          </div>

          <!-- Message Input -->
          <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            <form @submit.prevent="sendMessage" class="flex gap-2">
              <textarea
                v-model="newMessage"
                class="flex-1 input resize-none"
                rows="2"
                placeholder="اكتب رسالتك..."
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="newMessage += '\n'"
              ></textarea>
              <button
                type="submit"
                class="btn-primary self-end"
                :disabled="!newMessage.trim() || sending"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </form>
          </div>
        </div>
        <div v-else class="flex items-center justify-center h-full p-12">
          <p class="text-slate-400 dark:text-slate-500">اختر محادثة لعرض الرسائل</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue';
import { useApi } from '../../../composables/useApi';
import { useToast } from '../../../composables/useToast';
import { useAuthStore } from '../../../stores/auth';

const { get, post, put } = useApi();
const toast = useToast();
const authStore = useAuthStore();
const currentUserId = ref(authStore.user?.id);
const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const loading = ref(false);
const loadingMessages = ref(false);
const sending = ref(false);
const newMessage = ref('');
const messagesContainer = ref(null);

async function loadConversations() {
  loading.value = true;
  try {
    const data = await get('/messaging/conversations');
    conversations.value = Array.isArray(data) ? data : (data.data || []);
  } catch (err) {
    console.error('Error loading conversations:', err);
    toast.error('حدث خطأ أثناء تحميل المحادثات');
  } finally {
    loading.value = false;
  }
}

async function selectConversation(conversation) {
  selectedConversation.value = conversation;
  await loadMessages(conversation.id);
  if (conversation.unread_count > 0) {
    conversation.unread_count = 0;
  }
}

async function loadMessages(conversationId) {
  loadingMessages.value = true;
  try {
    const data = await get(`/messaging/conversations/${conversationId}/messages`);
    messages.value = Array.isArray(data) ? data : (data.data || []);
    await nextTick();
    scrollToBottom();
  } catch (err) {
    console.error('Error loading messages:', err);
    toast.error('حدث خطأ أثناء تحميل الرسائل');
  } finally {
    loadingMessages.value = false;
  }
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

async function sendMessage() {
  if (!newMessage.value.trim() || !selectedConversation.value) return;

  sending.value = true;
  try {
    await post('/messaging/messages', {
      conversation_id: selectedConversation.value.id,
      message: newMessage.value.trim(),
    });
    newMessage.value = '';
    await loadMessages(selectedConversation.value.id);
    await loadConversations();
    await nextTick();
    scrollToBottom();
  } catch (err) {
    toast.error('حدث خطأ أثناء إرسال الرسالة');
  } finally {
    sending.value = false;
  }
}

async function archiveConversation() {
  if (!selectedConversation.value) return;
  if (!confirm('هل أنت متأكد من أرشفة هذه المحادثة؟')) return;

  try {
    await put(`/messaging/conversations/${selectedConversation.value.id}/archive`);
    toast.success('تم أرشفة المحادثة');
    selectedConversation.value = null;
    messages.value = [];
    await loadConversations();
  } catch (err) {
    toast.error('حدث خطأ أثناء الأرشفة');
  }
}

function formatDate(date) {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  
  if (days === 0) return 'اليوم';
  if (days === 1) return 'أمس';
  if (days < 7) return `منذ ${days} أيام`;
  return d.toLocaleDateString('ar-EG');
}

function formatTime(date) {
  if (!date) return '';
  return new Date(date).toLocaleTimeString('ar-EG', {
    hour: '2-digit',
    minute: '2-digit',
  });
}

watch(selectedConversation, () => {
  if (selectedConversation.value) {
    loadMessages(selectedConversation.value.id);
  }
});

onMounted(async () => {
  await loadConversations();
});
</script>

<style scoped>
.input {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 0.65rem 0.9rem;
  font-size: 0.9rem;
}
</style>

