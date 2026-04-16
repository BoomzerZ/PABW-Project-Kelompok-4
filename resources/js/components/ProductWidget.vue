<template>
  <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden max-w-sm">
    <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover" />
    <div class="p-4">
      <h3 class="text-lg font-bold text-white">{{ product.name }}</h3>
      <p class="text-zinc-400 text-sm mt-1">{{ product.description }}</p>
      <div class="mt-4 flex items-center justify-between">
        <span class="text-xl font-bold text-red-500">Rp {{ formatPrice(product.price) }}</span>
        <button 
          @click="addToCart"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2"
        >
          <ShoppingCart class="w-4 h-4" />
          Tambah
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ShoppingCart } from 'lucide-vue-next';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const addToCart = () => {
  // Mock logic: check if user is guest, if so redirect to login
  // For now, just alert
  console.log('Adding to cart:', props.product.name);
  alert('Menambahkan ke keranjang: ' + props.product.name);
};
</script>
