<template>
  <div class="space-y-8">
    <h1 class="text-3xl font-bold">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2 shadow-xl">
        <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest text-white/70">Total Penjualan</h3>
        <p class="text-4xl font-black text-red-600">Rp {{ formatPrice(stats.totalSales) }}</p>
      </div>
      <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2 shadow-xl">
        <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest text-white/70">Total Pesanan</h3>
        <p class="text-4xl font-black text-white">{{ stats.totalOrders }}</p>
      </div>
      <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2 shadow-xl">
        <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest text-white/70">Total Produk</h3>
        <p class="text-4xl font-black text-white">{{ stats.totalProducts }}</p>
      </div>
      <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2 shadow-xl">
        <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest text-white/70">Total Kupon</h3>
        <p class="text-4xl font-black text-white">{{ stats.totalCoupons }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4">Pesanan Terbaru</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="text-zinc-500 text-xs uppercase">
              <tr>
                <th class="pb-4">ID</th>
                <th class="pb-4">Pembeli</th>
                <th class="pb-4">Total</th>
                <th class="pb-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
              <tr v-for="order in recentOrders" :key="order.id" class="text-sm">
                <td class="py-4 font-medium">#{{ order.id }}</td>
                <td class="py-4">{{ order.user?.name }}</td>
                <td class="py-4">Rp {{ formatPrice(order.total_price) }}</td>
                <td class="py-4">
                  <span :class="getStatusClass(order.status)" class="px-2 py-1 rounded text-xs font-bold uppercase">
                    {{ order.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-4">Produk Terlaris</h2>
        <p class="text-zinc-400 text-sm italic">Fitur ini dalam pengembangan.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
  totalSales: 0,
  totalOrders: 0,
  totalProducts: 0,
  totalCoupons: 0
});
const recentOrders = ref([]);

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const getStatusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-500/10 text-green-500';
    case 'processing': return 'bg-blue-500/10 text-blue-500';
    case 'pending': return 'bg-yellow-500/10 text-yellow-500';
    default: return 'bg-zinc-800 text-zinc-400';
  }
};

const fetchData = async () => {
  try {
    const [ordersRes, productsRes, couponsRes] = await Promise.all([
      axios.get('/api/admin/orders'),
      axios.get('/api/products'),
      axios.get('/api/admin/coupons')
    ]);

    const orders = ordersRes.data;
    recentOrders.value = orders.slice(0, 5);
    
    stats.value.totalOrders = orders.length;
    stats.value.totalSales = orders
      .filter(o => o.status === 'completed')
      .reduce((sum, o) => sum + parseFloat(o.total_price), 0);
    
    // Check if productsRes.data is paginated
    stats.value.totalProducts = productsRes.data.total || productsRes.data.length || (productsRes.data.data ? productsRes.data.data.length : 0);
    
    stats.value.totalCoupons = couponsRes.data.length;
  } catch (error) {
    console.error('Failed to fetch dashboard data', error);
  }
};

onMounted(fetchData);
</script>
