<template>
  <AppLayout title="Manajemen Tiket Keluhan">
    <div class="h-[calc(100vh-120px)] flex gap-6">
      
      <!-- Left: Ticket List -->
      <div class="w-1/3 flex flex-col bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-[#157347]/5">
          <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Daftar Keluhan</h3>
          <div class="mt-4 flex gap-2 overflow-x-auto pb-2">
            <button 
              v-for="status in ['all', 'pending', 'ongoing', 'resolved']" 
              :key="status"
              @click="filterStatus = status"
              class="px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all whitespace-nowrap"
              :class="filterStatus === status 
                ? 'bg-[#157347] text-white shadow-lg shadow-[#157347]/30' 
                : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
            >
              {{ status }}
            </button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
          <div 
            v-for="ticket in filteredTickets" 
            :key="ticket.id"
            @click="selectTicket(ticket)"
            class="p-4 rounded-2xl border transition-all cursor-pointer group relative"
            :class="selectedTicket?.id === ticket.id 
              ? 'bg-[#157347]/5 border-[#157347] shadow-sm' 
              : 'bg-gray-50/50 dark:bg-gray-800/30 border-transparent hover:border-gray-200 dark:hover:border-gray-700'"
          >
            <div class="flex justify-between items-start mb-2">
              <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full"
                :class="{
                  'bg-amber-100 text-amber-700 dark:bg-amber-900/30': ticket.status === 'pending',
                  'bg-blue-100 text-blue-700 dark:bg-blue-900/30': ticket.status === 'ongoing',
                  'bg-green-100 text-green-700 dark:bg-green-900/30': ticket.status === 'resolved',
                }"
              >
                {{ ticket.status }}
              </span>
              <span class="text-[10px] text-gray-400 font-bold">{{ formatDate(ticket.last_message_at) }}</span>
            </div>
            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 line-clamp-1 group-hover:text-[#157347] transition-colors">{{ ticket.title }}</h4>
            <div class="mt-2 flex items-center gap-2">
              <div class="w-6 h-6 rounded-lg bg-[#157347]/10 flex items-center justify-center text-[10px] font-bold text-[#157347]">
                {{ ticket.user.name.charAt(0) }}
              </div>
              <p class="text-xs text-gray-500 font-medium truncate">{{ ticket.user.name }}</p>
            </div>
          </div>
          
          <div v-if="filteredTickets.length === 0" class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <p class="text-sm text-gray-500 font-bold">Tidak ada tiket</p>
          </div>
        </div>
      </div>

      <!-- Right: Chat Area -->
      <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <template v-if="selectedTicket">
          <!-- Chat Header -->
          <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 shadow-sm z-10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#157347] text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-[#157347]/20">
                  {{ selectedTicket.user.name.charAt(0) }}
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ selectedTicket.title }}</h3>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ selectedTicket.user.name }} ({{ selectedTicket.user.nik }})</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <select 
                  v-model="selectedTicket.status" 
                  @change="updateTicketStatus"
                  class="bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-xs font-bold text-[#157347] focus:ring-2 focus:ring-[#157347] transition-all"
                >
                  <option value="pending">Pending</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="resolved">Resolved</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Messages Area -->
          <div ref="messageContainer" class="flex-1 overflow-y-auto p-8 space-y-6 bg-gray-50/50 dark:bg-gray-800/30 custom-scrollbar">
            <div 
              v-for="msg in messages" 
              :key="msg.id"
              class="flex flex-col"
              :class="msg.sender_id === $page.props.auth.user.id ? 'items-end' : 'items-start'"
            >
              <div class="max-w-[75%] space-y-2">
                <div 
                  class="px-5 py-3 rounded-3xl text-sm leading-relaxed shadow-sm"
                  :class="msg.sender_id === $page.props.auth.user.id 
                    ? 'bg-[#157347] text-white rounded-tr-none' 
                    : 'bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-tl-none border border-gray-100 dark:border-gray-800'"
                >
                  {{ msg.message }}
                </div>
                
                <div v-if="msg.attachment_path" class="mt-2">
                  <a :href="'/storage/' + msg.attachment_path" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-lg border-2 border-white dark:border-gray-800">
                    <img :src="'/storage/' + msg.attachment_path" class="max-w-xs group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </div>
                  </a>
                </div>
                
                <div class="flex items-center gap-2" :class="msg.sender_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'">
                  <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ msg.sender.name }}</p>
                  <p class="text-[10px] text-gray-400 font-bold">{{ formatTime(msg.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Input Area -->
          <div class="p-6 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
            <div class="relative flex items-center gap-4 bg-gray-50 dark:bg-gray-800 p-2 rounded-2xl border-2 border-transparent focus-within:border-[#157347]/30 transition-all">
              <button 
                @click="$refs.attachmentInput.click()"
                class="p-3 text-gray-500 hover:text-[#157347] hover:bg-[#157347]/10 rounded-xl transition-all"
                :class="{ 'text-[#157347] bg-[#157347]/10': pendingAttachment }"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
              </button>
              <input type="file" ref="attachmentInput" class="hidden" @change="handleAttachment">
              
              <input 
                v-model="newMessage"
                @keyup.enter="sendReply"
                class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2 dark:text-gray-100"
                placeholder="Tulis balasan untuk user..."
              />

              <button 
                @click="sendReply"
                :disabled="!newMessage.trim() && !pendingAttachment"
                class="p-3 bg-[#157347] text-white rounded-xl shadow-lg shadow-[#157347]/30 hover:scale-110 active:scale-95 transition-all disabled:opacity-50 disabled:grayscale disabled:scale-100"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </div>
            <div v-if="pendingAttachment" class="mt-3 flex items-center gap-2 px-2 text-xs font-bold text-[#157347]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span>{{ pendingAttachment.name }}</span>
              <button @click="pendingAttachment = null" class="text-red-500 hover:text-red-700 ml-1 underline">Hapus</button>
            </div>
          </div>
        </template>
        
        <div v-else class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-gray-50/50 dark:bg-gray-800/30">
          <div class="w-32 h-32 bg-white dark:bg-gray-900 rounded-[40px] shadow-2xl flex items-center justify-center mb-8 border border-gray-100 dark:border-gray-800">
            <svg class="w-16 h-16 text-[#157347]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-4 px-20">Pilih keluhan untuk memulai penyelesaian masalah</h2>
          <p class="text-gray-500 dark:text-gray-400 font-medium max-w-sm">Informasi akan muncul di sini setelah Anda memilih tiket dari daftar di sebelah kiri.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, nextTick, onMounted } from 'vue';
import { useSettings } from '@/Composables/useSettings';
import axios from 'axios';

const props = defineProps({
  tickets: Array
});

const { formatDateGlobal } = useSettings();
const formatDate = formatDateGlobal;

const filterStatus = ref('all');
const selectedTicket = ref(null);
const messages = ref([]);
const newMessage = ref('');
const pendingAttachment = ref(null);
const messageContainer = ref(null);
const pollingInterval = ref(null);
const listPollingInterval = ref(null);

const filteredTickets = computed(() => {
  if (filterStatus.value === 'all') return props.tickets;
  return props.tickets.filter(t => t.status === filterStatus.value);
});

const selectTicket = async (ticket) => {
  try {
    const res = await axios.get(route('complaints.show', ticket.id));
    selectedTicket.value = res.data;
    messages.value = res.data.messages;
    scrollToBottom();
    startMessagePolling();
  } catch (e) {
    console.error('Failed to load ticket', e);
  }
};

const startMessagePolling = () => {
  if (pollingInterval.value) clearInterval(pollingInterval.value);
  pollingInterval.value = setInterval(async () => {
    if (!selectedTicket.value) {
      clearInterval(pollingInterval.value);
      return;
    }
    try {
      const res = await axios.get(route('complaints.show', selectedTicket.value.id));
      if (res.data.messages.length !== messages.value.length) {
        messages.value = res.data.messages;
        scrollToBottom();
      }
      if (res.data.status !== selectedTicket.value.status) {
        selectedTicket.value.status = res.data.status;
      }
    } catch (e) {
      console.error('Message polling failed');
    }
  }, 5000);
};

const startListPolling = () => {
  listPollingInterval.value = setInterval(async () => {
    try {
      // We need a way to refresh only the tickets prop or fetch via API
      // Since it's passed as prop, we'll use a local tickets ref instead for better management
      const res = await axios.get(route('admin.complaints.index'), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      // Updating prop directly is not recommended, so we'll use a wrapper if it was an API
      // but for simplicity in this case where it's a small app, we'll just fetch manually
      // Let's assume the index route returns JSON if requested
    } catch (e) {
      console.error('List polling failed');
    }
  }, 10000); // Ticket list every 10 seconds
};

const handleAttachment = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  pendingAttachment.value = file;
};

const sendReply = async () => {
  if (!newMessage.value.trim() && !pendingAttachment.value) return;

  const formData = new FormData();
  formData.append('message', newMessage.value);
  if (pendingAttachment.value) {
    formData.append('attachment', pendingAttachment.value);
  }

  try {
    const res = await axios.post(route('complaints.send-message', selectedTicket.value.id), formData);
    messages.value.push(res.data);
    
    // Auto mark as ongoing in local list if it was pending
    const listTicket = props.tickets.find(t => t.id === selectedTicket.value.id);
    if (listTicket && listTicket.status === 'pending') {
      listTicket.status = 'ongoing';
      selectedTicket.value.status = 'ongoing';
    }
    listTicket.last_message_at = new Date().toISOString();

    newMessage.value = '';
    pendingAttachment.value = null;
    scrollToBottom();
  } catch (e) {
    alert('Gagal mengirim balasan');
  }
};

const updateTicketStatus = async () => {
  try {
    await axios.patch(route('admin.complaints.update-status', selectedTicket.value.id), {
      status: selectedTicket.value.status
    });
    
    // Update local list
    const listTicket = props.tickets.find(t => t.id === selectedTicket.value.id);
    if (listTicket) {
      listTicket.status = selectedTicket.value.status;
    }
  } catch (e) {
    alert('Gagal memperbarui status');
  }
};

const formatTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
  });
};

import { onUnmounted } from 'vue';

onMounted(() => {
  // If tickets exist, maybe select the first one?
  // if (props.tickets.length > 0) selectTicket(props.tickets[0]);
  
  // Start list polling (optional, for demo let's just focus on message polling)
});

onUnmounted(() => {
  if (pollingInterval.value) clearInterval(pollingInterval.value);
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #157347;
}
</style>
