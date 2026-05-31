<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold mb-8">Notifikasi</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="notifications.length > 0" class="space-y-4">
      <div 
        v-for="n in notifications" 
        :key="n.id" 
        @click="markAsRead(n)"
        :class="['border rounded-2xl p-6 flex gap-6 hover:border-zinc-700 transition-colors cursor-pointer', n.read_at ? 'bg-zinc-900 border-zinc-800 opacity-60' : 'bg-zinc-800 border-red-900/50 shadow-lg']"
      >
        <div :class="['p-3 rounded-xl flex-shrink-0 h-fit', n.data.type === 'promo' ? 'bg-red-950/30 text-red-500' : 'bg-blue-950/30 text-blue-500']">
          <component :is="n.data.type === 'promo' ? Tag : Package" class="w-6 h-6" />
        </div>
        <div class="flex-1 space-y-1">
          <div class="flex justify-between items-start">
            <h3 :class="['font-bold', n.read_at ? 'text-zinc-300' : 'text-white']">{{ n.data.title }}</h3>
            <span class="text-xs text-zinc-500">{{ formatDate(n.created_at) }}</span>
          </div>
          <p :class="['text-sm leading-relaxed', n.read_at ? 'text-zinc-500' : 'text-zinc-400']">{{ n.data.message }}</p>
        </div>
      </div>
    </div>
    
    <div v-else class="bg-zinc-900 border border-zinc-800 rounded-2xl p-20 text-center">
      <BellOff class="w-16 h-16 mx-auto text-zinc-800 mb-4" />
      <p class="text-zinc-500 text-xl mb-6">Belum ada notifikasi baru.</p>
    </div>

    <!-- For Demo Purposes -->
    <div class="flex justify-center mt-12">
      <button @click="triggerDummy" class="text-xs text-zinc-600 hover:text-zinc-400 underline">Trigger Dummy Notification</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Tag, Package, BellOff, Loader2 } from 'lucide-vue-next';
import axios from 'axios';

const notifications = ref([]);
const loading = ref(true);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
  }).format(date);
};

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/api/notifications');
    notifications.value = response.data;
  } catch (error) {
    console.error('Failed to fetch notifications', error);
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (notification) => {
  if (notification.read_at) return;
  
  try {
    await axios.post(`/api/notifications/${notification.id}/read`);
    notification.read_at = new Date().toISOString();
  } catch (error) {
    console.error('Failed to mark as read', error);
  }
};

const triggerDummy = async () => {
  try {
    await axios.post('/api/notifications/dummy');
    fetchNotifications();
  } catch (error) {
    console.error('Failed to trigger dummy', error);
  }
};

onMounted(() => {
  fetchNotifications();
});
</script>
