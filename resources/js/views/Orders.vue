<template>
  <div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-3xl font-bold">Riwayat Pesanan</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="orders.length > 0" class="space-y-6">
      <div v-for="order in orders" :key="order.id" class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 hover:border-zinc-700 transition-all group shadow-lg">
        <div class="flex flex-col md:flex-row justify-between gap-6">
          <div class="flex-1 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-zinc-800 rounded-xl">
                  <ClipboardList class="w-6 h-6 text-red-500" />
                </div>
                <div>
                  <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Order ID</p>
                  <p class="text-white font-black">#{{ order.id }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Status</p>
                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border">
                  {{ order.status }}
                </span>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <div>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Tanggal</p>
                <p class="text-zinc-300 text-sm">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Total Harga</p>
                <p class="text-red-500 font-black text-lg">Rp {{ formatPrice(order.total_price) }}</p>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-center md:border-l md:border-zinc-800 md:pl-6">
            <router-link 
              :to="`/orders/${order.id}`" 
              class="bg-zinc-800 group-hover:bg-red-600 text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2 whitespace-nowrap"
            >
              Lihat Detail
              <ChevronRight class="w-4 h-4" />
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="bg-zinc-900 border border-zinc-800 rounded-3xl p-20 text-center shadow-2xl">
      <div class="w-24 h-24 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-8">
        <ClipboardList class="w-12 h-12 text-zinc-500" />
      </div>
      <h3 class="text-2xl font-bold text-white mb-4">Belum ada pesanan</h3>
      <p class="text-zinc-400 max-w-sm mx-auto mb-10">Anda belum memiliki riwayat pesanan di Gaming Gear Marketplace. Mulai belanja produk gaming impianmu sekarang!</p>
      <router-link to="/" class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all shadow-lg shadow-red-600/20">
        Mulai Belanja
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { ClipboardList, Loader2, ChevronRight } from 'lucide-vue-next';
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

const getStatusClass = (status) => {
  switch (status) {
    case 'completed': return 'text-green-500 border-green-500/20 bg-green-500/5';
    case 'pending': return 'text-yellow-500 border-yellow-500/20 bg-yellow-500/5';
    default: return 'text-zinc-500 border-zinc-800 bg-zinc-800/5';
  }
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

onMounted(fetchOrders);
</script>
