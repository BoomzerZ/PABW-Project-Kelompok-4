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
              <td class="px-6 py-4 flex items-center gap-3">
                <select 
                  @change="updateStatus(order.id, $event.target.value)" 
                  :value="order.status"
                  class="bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-red-600 transition-colors"
                >
                  <option value="pending">Pending</option>
                  <option value="processing">Processing (Awaiting Verification)</option>
                  <option value="completed">Completed</option>
                </select>
                <button 
                  @click="openDetailModal(order)"
                  class="bg-zinc-800 hover:bg-zinc-700 text-white px-3 py-2 rounded-lg text-xs font-bold transition-colors"
                >
                  Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div v-if="selectedOrder" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
      <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-zinc-900/95 p-6 border-b border-zinc-800 flex justify-between items-center z-10">
          <h2 class="text-xl font-bold">Detail Pesanan #{{ selectedOrder.id }}</h2>
          <button @click="closeDetailModal" class="p-2 hover:bg-zinc-800 rounded-xl transition-colors">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <div class="p-6 space-y-8">
          <!-- User Info & Shipping -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <h3 class="font-bold text-sm text-zinc-400 uppercase tracking-widest">Informasi Pembeli</h3>
              <div class="bg-zinc-800/30 p-4 rounded-xl">
                <p class="font-bold">{{ selectedOrder.user?.name }}</p>
                <p class="text-zinc-400 text-sm">{{ selectedOrder.user?.email }}</p>
              </div>
            </div>
            
            <div class="space-y-4">
              <h3 class="font-bold text-sm text-zinc-400 uppercase tracking-widest">Alamat Pengiriman</h3>
              <div class="bg-zinc-800/30 p-4 rounded-xl text-sm">
                <p>{{ selectedOrder.shipping_address || '-' }}</p>
                <p>{{ selectedOrder.city }}, {{ selectedOrder.province }}</p>
                <p>Kode Pos: {{ selectedOrder.postal_code }}</p>
              </div>
            </div>
          </div>

          <!-- Items -->
          <div class="space-y-4">
            <h3 class="font-bold text-sm text-zinc-400 uppercase tracking-widest">Daftar Produk</h3>
            <div class="bg-zinc-800/30 rounded-xl divide-y divide-zinc-800">
              <div v-for="item in selectedOrder.items" :key="item.id" class="p-4 flex items-center gap-4">
                <img :src="item.product.image_url" class="w-12 h-12 object-cover rounded-lg" />
                <div class="flex-1">
                  <h4 class="font-bold text-sm">{{ item.product.name }}</h4>
                  <p class="text-xs text-zinc-500">{{ item.quantity }} x Rp {{ formatPrice(item.price) }}</p>
                </div>
                <p class="font-bold text-sm">Rp {{ formatPrice(item.price * item.quantity) }}</p>
              </div>
              <div class="p-4 bg-zinc-800/50 flex flex-col gap-2">
                <div class="flex justify-between text-sm text-zinc-400">
                  <span>Subtotal</span>
                  <span>Rp {{ formatPrice(selectedOrder.total_price - (selectedOrder.shipping_cost || 0)) }}</span>
                </div>
                <div class="flex justify-between text-sm text-zinc-400">
                  <span>Ongkos Kirim</span>
                  <span>Rp {{ formatPrice(selectedOrder.shipping_cost || 0) }}</span>
                </div>
                <div class="flex justify-between font-bold pt-2 border-t border-zinc-700">
                  <span>Total Harga</span>
                  <span class="text-red-500">Rp {{ formatPrice(selectedOrder.total_price) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Receipt -->
          <div class="space-y-4">
            <h3 class="font-bold text-sm text-zinc-400 uppercase tracking-widest">Bukti Pembayaran</h3>
            <div class="bg-zinc-800/30 p-4 rounded-xl">
              <div v-if="selectedOrder.payment_receipt" class="space-y-4">
                <a :href="selectedOrder.payment_receipt" target="_blank" class="block rounded-lg overflow-hidden border border-zinc-700 hover:border-red-500 transition-colors w-fit">
                  <img :src="selectedOrder.payment_receipt" alt="Bukti Transfer" class="max-w-xs max-h-64 object-contain" />
                </a>
                <p class="text-xs text-zinc-500">Klik gambar untuk memperbesar</p>
              </div>
              <p v-else class="text-sm text-zinc-500 italic">Belum ada bukti pembayaran yang diunggah.</p>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { X } from 'lucide-vue-next';

const orders = ref([]);
const selectedOrder = ref(null);

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
    // Update selected order if modal is open
    if (selectedOrder.value && selectedOrder.value.id === id) {
      selectedOrder.value.status = status;
    }
  } catch (e) {
    alert('Gagal memperbarui status');
  }
};

const openDetailModal = (order) => {
  selectedOrder.value = order;
  document.body.style.overflow = 'hidden'; // Prevent scrolling
};

const closeDetailModal = () => {
  selectedOrder.value = null;
  document.body.style.overflow = 'auto';
};

onMounted(fetchOrders);
</script>
