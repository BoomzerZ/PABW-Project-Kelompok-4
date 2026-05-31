<template>
  <div class="max-w-7xl mx-auto py-12">
    <div class="text-center mb-12">
      <Heart class="w-16 h-16 mx-auto text-red-600 mb-6 fill-current" />
      <h1 class="text-3xl font-bold text-white">Wishlist Saya</h1>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="wishlists.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <div 
        v-for="item in wishlists" 
        :key="item.id"
        class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-red-600/50 transition-all group shadow-xl"
      >
        <div class="relative aspect-square overflow-hidden cursor-pointer" @click="$router.push('/product/' + item.product.id)">
          <img :src="item.product.image_url" :alt="item.product.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
        </div>
        <div class="p-5 space-y-2">
          <router-link :to="'/product/' + item.product.id">
            <h4 class="text-lg font-bold text-white line-clamp-1 group-hover:text-red-500 transition-colors cursor-pointer">{{ item.product.name }}</h4>
          </router-link>
          <div class="pt-2 flex items-center justify-between">
            <span class="text-xl font-black text-red-500">Rp {{ formatPrice(item.product.price) }}</span>
          </div>
          <button @click="removeFromWishlist(item.product_id)" class="w-full mt-4 flex items-center justify-center gap-2 py-2 border border-zinc-700 hover:border-red-500 hover:text-red-500 rounded-xl transition-colors text-sm font-medium">
            <Trash2 class="w-4 h-4" /> Hapus
          </button>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 border border-dashed border-zinc-800 rounded-3xl">
      <p class="text-zinc-500 text-lg mb-8">Belum ada produk di wishlist kamu.</p>
      <router-link to="/" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-colors">
        Mulai Belanja
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Heart, Loader2, Trash2 } from 'lucide-vue-next';
import axios from 'axios';

const wishlists = ref([]);
const loading = ref(true);

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const fetchWishlists = async () => {
  try {
    const response = await axios.get('/api/wishlist');
    wishlists.value = response.data;
  } catch (error) {
    console.error('Failed to fetch wishlist', error);
  } finally {
    loading.value = false;
  }
};

const removeFromWishlist = async (productId) => {
  // Optimistic: simpan backup lalu hapus dari UI seketika
  const backup = [...wishlists.value];
  wishlists.value = wishlists.value.filter(w => w.product_id !== productId);

  try {
    await axios.post('/api/wishlist/toggle', { product_id: productId });
  } catch (error) {
    // Rollback: kembalikan UI ke kondisi semula
    wishlists.value = backup;
    console.error('Failed to remove from wishlist', error);
  }
};

onMounted(() => {
  fetchWishlists();
});
</script>
