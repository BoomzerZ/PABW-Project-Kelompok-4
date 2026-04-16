<template>
  <div class="space-y-8">
    <h1 class="text-3xl font-bold">Kelola Pesanan</h1>

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-zinc-800/50 text-zinc-400 text-xs uppercase">
            <tr>
              <th class="px-6 py-4">Order ID</th>
              <th class="px-6 py-4">Pembeli</th>
              <th class="px-6 py-4">Total Harga</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-zinc-800/30 transition-colors text-sm">
              <td class="px-6 py-4 font-bold">#{{ order.id }}</td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-white font-medium">{{ order.user?.name }}</span>
                  <span class="text-zinc-500 text-xs">{{ order.user?.email }}</span>
                </div>
              </td>
              <td class="px-6 py-4 font-bold text-red-500">Rp {{ formatPrice(order.total_price) }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <select 
                  @change="updateStatus(order.id, $event.target.value)" 
                  :value="order.status"
                  class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-red-600 transition-colors"
                >
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="completed">Completed</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const getStatusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-500/10 text-green-500 border border-green-500/20';
    case 'processing': return 'bg-blue-500/10 text-blue-500 border border-blue-500/20';
    case 'pending': return 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20';
    default: return 'bg-zinc-800 text-zinc-400';
  }
};

const fetchOrders = async () => {
  try {
    const res = await axios.get('/api/admin/orders');
    orders.value = res.data;
  } catch (e) {
    console.error(e);
  }
};

const updateStatus = async (id, status) => {
  try {
    await axios.put(`/api/admin/orders/${id}/status`, { status });
    fetchOrders();
    alert('Status pesanan diperbarui');
  } catch (e) {
    alert('Gagal memperbarui status');
  }
};

onMounted(fetchOrders);
</script>
