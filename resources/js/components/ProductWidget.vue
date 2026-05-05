<template>
  <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden max-w-sm relative group">
    <div v-if="isNewArrival" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded uppercase tracking-tighter z-10 animate-pulse">
      New Arrival
    </div>
    <img :src="product.image_url" :alt="product.name" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" />
    <div class="p-4">
      <h3 class="text-lg font-bold text-white">{{ product.name }}</h3>
      <p class="text-zinc-400 text-sm mt-1 line-clamp-2">{{ product.description }}</p>
      <div class="mt-4 flex items-center justify-between">
        <span class="text-xl font-bold text-red-500">Rp {{ formatPrice(product.price) }}</span>
        <button 
          @click="handleAddToCart"
          :disabled="loading || product.stock <= 0"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 disabled:opacity-50"
        >
          <ShoppingCart class="w-4 h-4" />
          {{ loading ? '...' : (product.stock > 0 ? 'Tambah' : 'Habis') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { ShoppingCart } from 'lucide-vue-next';
import { authState } from '../utils/auth';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const isNewArrival = computed(() => {
  if (!props.product.created_at) return false;
  const createdDate = new Date(props.product.created_at);
  const now = new Date();
  const diffDays = Math.ceil((now - createdDate) / (1000 * 60 * 60 * 24));
  return diffDays <= 7;
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const handleAddToCart = async () => {
  if (!authState.isAuthenticated) {
    router.push('/login');
    return;
  }

  loading.value = true;
  try {
    await axios.post('/api/cart', { 
      product_id: props.product.id,
      quantity: 1
    });
    alert('Produk ditambahkan ke keranjang!');
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menambahkan ke keranjang');
  } finally {
    loading.value = false;
  }
};
</script>
