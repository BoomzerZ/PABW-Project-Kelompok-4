<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Keranjang Belanja</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="cartItems.length > 0" class="space-y-6">
      <div v-for="item in cartItems" :key="item.id" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex items-center gap-6">
        <img :src="item.product.image_url" :alt="item.product.name" class="w-24 h-24 object-cover rounded-lg" />
        <div class="flex-1">
          <h3 class="text-xl font-bold">{{ item.product.name }}</h3>
          <p class="text-zinc-400 text-sm">Jumlah: {{ item.quantity }}</p>
          <p class="text-red-500 font-bold mt-1">Rp {{ formatPrice(item.product.price * item.quantity) }}</p>
        </div>
        <button @click="removeItem(item.id)" class="text-zinc-500 hover:text-red-500 transition-colors">
          <Trash2 class="w-6 h-6" />
        </button>
      </div>

      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 mt-10">
        <div class="flex justify-between items-center mb-6">
          <span class="text-xl text-zinc-400">Total Pembayaran</span>
          <span class="text-3xl font-bold text-red-500">Rp {{ formatPrice(totalPrice) }}</span>
        </div>
        <button 
          @click="handleCheckout"
          :disabled="checkoutLoading"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl font-bold text-lg transition-colors disabled:opacity-50"
        >
          {{ checkoutLoading ? 'Memproses...' : 'Checkout Sekarang' }}
        </button>
      </div>
    </div>

    <div v-else class="bg-zinc-900 border border-zinc-800 rounded-2xl p-20 text-center">
      <ShoppingCart class="w-16 h-16 mx-auto text-zinc-700 mb-4" />
      <p class="text-zinc-400 text-xl">Keranjang kamu masih kosong.</p>
      <router-link to="/" class="inline-block mt-6 text-red-500 font-bold hover:underline">
        Kembali ke Chat AI
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { ShoppingCart, Trash2, Loader2 } from 'lucide-vue-next';
import axios from 'axios';

const cartItems = ref([]);
const loading = ref(true);
const checkoutLoading = ref(false);

const totalPrice = computed(() => {
  return cartItems.value.reduce((total, item) => total + (item.product.price * item.quantity), 0);
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const fetchCart = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/cart');
    cartItems.value = response.data;
  } catch (error) {
    console.error('Failed to fetch cart', error);
  } finally {
    loading.value = false;
  }
};

const removeItem = async (id) => {
  try {
    await axios.delete(`/api/cart/${id}`);
    cartItems.value = cartItems.value.filter(item => item.id !== id);
  } catch (error) {
    alert('Gagal menghapus item');
  }
};

const handleCheckout = async () => {
  if (cartItems.value.length === 0) return;
  
  checkoutLoading.value = true;
  try {
    const response = await axios.post('/api/orders');
    alert('Checkout berhasil! ID Pesanan: ' + response.data.id);
    cartItems.value = [];
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal melakukan checkout');
  } finally {
    checkoutLoading.value = false;
  }
};

onMounted(() => {
  fetchCart();
});
</script>
