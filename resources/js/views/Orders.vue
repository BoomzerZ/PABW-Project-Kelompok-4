<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Riwayat Pesanan</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="orders.length > 0" class="space-y-6">
      <div v-for="order in orders" :key="order.id" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-4">
        <div class="flex justify-between items-center pb-4 border-b border-zinc-800">
          <div>
            <p class="text-zinc-500 text-sm">Nomor Pesanan</p>
            <p class="font-bold text-white">#{{ order.id }}</p>
          </div>
          <div class="text-right">
            <p class="text-zinc-500 text-sm">Tanggal</p>
            <p class="text-white">{{ formatDate(order.created_at) }}</p>
          </div>
          <div class="px-4 py-1 bg-green-500/10 border border-green-500/20 rounded-full text-green-500 text-xs font-bold uppercase">
            {{ order.status }}
          </div>
        </div>

        <div class="space-y-3">
          <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4">
            <img :src="item.product.image_url" :alt="item.product.name" class="w-12 h-12 object-cover rounded-lg" />
            <div class="flex-1">
              <p class="text-white font-medium">{{ item.product.name }}</p>
              <p class="text-zinc-500 text-xs">{{ item.quantity }} x Rp {{ formatPrice(item.price) }}</p>
            </div>
          </div>
        </div>

        <div class="pt-4 flex justify-between items-center border-t border-zinc-800">
          <span class="text-zinc-400">Total Pembayaran</span>
          <span class="text-xl font-bold text-red-500">Rp {{ formatPrice(order.total_price) }}</span>
        </div>
      </div>
    </div>

    <div v-else class="bg-zinc-900 border border-zinc-800 rounded-2xl p-12 text-center">
      <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
        <ClipboardList class="w-10 h-10 text-zinc-500" />
      </div>
      <h3 class="text-xl font-bold text-white mb-2">Belum ada pesanan</h3>
      <p class="text-zinc-400 mb-8">Anda belum memiliki riwayat pesanan di Gaming Gear Marketplace.</p>
      <router-link to="/" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-colors">
        Mulai Belanja
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ClipboardList, Loader2 } from 'lucide-vue-next';
import axios from 'axios';

const orders = ref([]);
const loading = ref(true);

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const fetchOrders = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/orders');
    orders.value = response.data;
  } catch (error) {
    console.error('Failed to fetch orders', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOrders();
});
</script>
