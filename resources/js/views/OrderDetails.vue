<template>
  <div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center gap-4">
      <router-link to="/orders" class="p-2 hover:bg-zinc-900 rounded-lg transition-colors">
        <ArrowLeft class="w-6 h-6" />
      </router-link>
      <h1 class="text-3xl font-bold">Detail Pesanan</h1>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="order" class="space-y-8">
      <!-- Order Summary Card -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8">
          <span :class="getStatusClass(order.status)" class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest border">
            {{ order.status }}
          </span>
        </div>
        
        <div class="space-y-6">
          <div>
            <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">Nomor Pesanan</p>
            <p class="text-2xl font-black text-white">#{{ order.id }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">Tanggal Pesanan</p>
              <p class="text-white">{{ formatDate(order.created_at) }}</p>
            </div>
            <div>
              <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">Metode Pembayaran</p>
              <p class="text-white">Simulasi Transfer Bank</p>
            </div>
          </div>
        </div>

        <!-- Payment Action -->
        <div v-if="order.status === 'pending'" class="mt-10 p-6 bg-red-600/10 border border-red-600/20 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center">
              <CreditCard class="w-6 h-6 text-white" />
            </div>
            <div>
              <p class="font-bold text-white text-lg">Menunggu Pembayaran</p>
              <p class="text-zinc-400 text-sm">Silakan selesaikan pembayaran untuk memproses pesanan Anda.</p>
            </div>
          </div>
          <button 
            @click="simulatePayment"
            :disabled="paymentLoading"
            class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-red-600/20 disabled:opacity-50"
          >
            {{ paymentLoading ? 'Memproses...' : 'Simulasi Bayar Sekarang' }}
          </button>
        </div>
      </div>

      <!-- Items -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="p-6 border-b border-zinc-800">
          <h2 class="font-bold text-lg">Item Pesanan</h2>
        </div>
        <div class="divide-y divide-zinc-800">
          <div v-for="item in order.items" :key="item.id" class="p-6 flex items-center gap-6">
            <img :src="item.product.image_url" class="w-20 h-20 object-cover rounded-xl" />
            <div class="flex-1">
              <h3 class="font-bold text-white text-lg">{{ item.product.name }}</h3>
              <p class="text-zinc-500 text-sm">{{ item.quantity }} x Rp {{ formatPrice(item.price) }}</p>
            </div>
            <p class="font-bold text-white">Rp {{ formatPrice(item.price * item.quantity) }}</p>
          </div>
        </div>
        <div class="p-8 bg-zinc-800/30 flex justify-between items-center">
          <span class="text-zinc-400 font-bold uppercase tracking-widest">Total Bayar</span>
          <span class="text-3xl font-black text-red-600">Rp {{ formatPrice(order.total_price) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { ArrowLeft, Loader2, CreditCard } from 'lucide-vue-next';
import axios from 'axios';

const route = useRoute();
const order = ref(null);
const loading = ref(true);
const paymentLoading = ref(false);

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

const fetchOrderDetails = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`/api/orders/${route.params.id}`);
    order.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const simulatePayment = async () => {
  paymentLoading.value = true;
  try {
    const res = await axios.post(`/api/orders/${order.value.id}/pay`);
    order.value = res.data.order;
    alert('Pembayaran berhasil disimulasikan!');
  } catch (e) {
    alert('Gagal melakukan pembayaran');
  } finally {
    paymentLoading.value = false;
  }
};

onMounted(fetchOrderDetails);
</script>
