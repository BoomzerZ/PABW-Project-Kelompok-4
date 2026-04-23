<template>
  <div class="max-w-7xl mx-auto space-y-12 pb-20">
    <!-- Search Bar & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <h1 class="text-3xl font-bold flex items-center gap-2">
        <Gamepad2 class="text-red-600 w-8 h-8" />
        Gaming Gear Shop
      </h1>
      <div class="relative w-full md:max-w-md">
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text" 
          placeholder="Cari keyboard, mouse, headset..." 
          class="w-full bg-zinc-900 border border-zinc-800 rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-red-600 transition-colors shadow-lg"
        />
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 w-5 h-5" />
      </div>
    </div>

    <!-- Hero Banner -->
    <div class="relative rounded-3xl overflow-hidden aspect-[21/9] md:aspect-[3/1] group shadow-2xl">
      <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=2000" alt="Hero Banner" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
      <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent flex flex-col justify-center px-12 space-y-6">
        <span class="inline-block bg-red-600 text-white px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase">Promo Ramadhan</span>
        <h2 class="text-5xl md:text-6xl font-black text-white leading-tight">
          LEVEL UP YOUR<br/><span class="text-red-600 italic">GAMING SETUP</span>
        </h2>
        <p class="text-zinc-300 text-lg max-w-lg">
          Dapatkan diskon hingga 50% untuk produk pilihan Logitech, Razer, dan SteelSeries.
        </p>
        <div>
          <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all transform hover:translate-y-[-2px] shadow-lg shadow-red-600/20">
            Belanja Sekarang
          </button>
        </div>
      </div>
    </div>

    <!-- Product Showcase -->
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else class="space-y-12">
      <!-- Category: All Products / Featured -->
      <section class="space-y-6">
        <div class="flex items-center justify-between">
          <h3 class="text-2xl font-bold text-white border-l-4 border-red-600 pl-4">Rekomendasi Produk</h3>
          <div class="flex gap-2">
            <button class="p-2 bg-zinc-900 rounded-lg hover:bg-zinc-800 transition-colors"><ChevronLeft class="w-5 h-5" /></button>
            <button class="p-2 bg-zinc-900 rounded-lg hover:bg-zinc-800 transition-colors"><ChevronRight class="w-5 h-5" /></button>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div 
            v-for="product in products" 
            :key="product.id"
            class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-red-600/50 transition-all group shadow-xl hover:shadow-red-600/5"
          >
            <div class="relative aspect-square overflow-hidden">
              <div v-if="isNewProduct(product.created_at)" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded uppercase tracking-tighter z-10 animate-pulse">
                New Arrival
              </div>
              <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                <button @click="addToCart(product)" class="bg-red-600 p-3 rounded-full text-white hover:bg-red-700 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-xl">
                  <ShoppingCart class="w-6 h-6" />
                </button>
              </div>
            </div>
            <div class="p-5 space-y-2">
              <span class="text-xs font-bold text-red-500 uppercase tracking-widest">{{ product.category?.name || 'Gaming' }}</span>
              <h4 class="text-lg font-bold text-white line-clamp-1 group-hover:text-red-500 transition-colors">{{ product.name }}</h4>
              <p class="text-zinc-500 text-sm line-clamp-2">{{ product.description }}</p>
              <div class="pt-4 flex items-center justify-between">
                <span class="text-xl font-black text-white">Rp {{ formatPrice(product.price) }}</span>
                <span v-if="product.stock > 0" class="text-xs text-green-500 font-medium">Stok Ready</span>
                <span v-else class="text-xs text-red-500 font-medium">Habis</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Category Focus: Keyboards -->
      <section v-if="keyboards.length > 0" class="space-y-6">
        <h3 class="text-2xl font-bold text-white border-l-4 border-red-600 pl-4">Keyboard Mechanical Terfavorit</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
           <div 
            v-for="product in keyboards.slice(0, 3)" 
            :key="product.id"
            class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex gap-6 hover:border-red-600/50 transition-all group shadow-lg"
          >
            <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-zinc-800">
              <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1 flex flex-col justify-between py-1">
              <div>
                <h4 class="font-bold text-white line-clamp-1">{{ product.name }}</h4>
                <p class="text-red-500 font-bold">Rp {{ formatPrice(product.price) }}</p>
              </div>
              <button @click="addToCart(product)" class="text-zinc-400 hover:text-white flex items-center gap-2 text-sm transition-colors">
                <Plus class="w-4 h-4" /> Tambah ke Keranjang
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- AI Chat CTA -->
    <div class="bg-gradient-to-r from-zinc-900 to-red-950/20 border border-zinc-800 rounded-3xl p-12 flex flex-col md:flex-row items-center justify-between gap-12 shadow-2xl">
      <div class="space-y-4">
        <h2 class="text-4xl font-black text-white leading-tight">Bingung Pilih Gear?<br/><span class="text-red-600 italic">Tanyakan AI Kami!</span></h2>
        <p class="text-zinc-400 text-lg max-w-md">Dapatkan rekomendasi gaming gear yang dipersonalisasi sesuai budget dan kebutuhanmu dalam hitungan detik.</p>
        <router-link to="/chat" class="inline-flex items-center gap-3 bg-white text-black px-8 py-4 rounded-xl font-bold text-lg hover:bg-zinc-200 transition-colors shadow-xl">
          <MessageSquare class="w-6 h-6" /> Chat Sekarang
        </router-link>
      </div>
      <div class="w-full md:max-w-sm aspect-square bg-zinc-800/50 rounded-2xl flex items-center justify-center p-8 border border-zinc-700/50 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-red-600/10 blur-3xl rounded-full"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-blue-600/5 blur-3xl rounded-full"></div>
        <div class="relative z-10 space-y-4">
          <div class="bg-zinc-900 p-4 rounded-2xl rounded-bl-none border border-zinc-700 text-sm text-zinc-300">
            "Rekomendasi keyboard mechanical budget di bawah 1 juta dong?"
          </div>
          <div class="bg-red-600 p-4 rounded-2xl rounded-br-none border border-red-500 text-sm text-white self-end ml-12">
            "Tentu! Kamu bisa lirik VortexSeries GT-8 atau DA Meca Warrior X..."
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { 
  Search, 
  Gamepad2, 
  ShoppingCart, 
  Loader2, 
  ChevronLeft, 
  ChevronRight,
  MessageSquare,
  Plus,
  ArrowRight
} from 'lucide-vue-next';
import axios from 'axios';
import { authState } from '../utils/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const searchQuery = ref('');
const products = ref([]);
const loading = ref(true);

const keyboards = computed(() => {
  return products.value.filter(p => p.category?.name.toLowerCase().includes('keyboard'));
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const isNewProduct = (date) => {
  if (!date) return false;
  const createdDate = new Date(date);
  const now = new Date();
  const diffDays = Math.ceil((now - createdDate) / (1000 * 60 * 60 * 24));
  return diffDays <= 7;
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/products');
    // Handle paginated response
    products.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch products', error);
  } finally {
    loading.value = false;
  }
};

const handleSearch = async () => {
  try {
    const response = await axios.get('/api/products', {
      params: { search: searchQuery.value }
    });
    // Handle paginated response
    products.value = response.data.data || response.data;
  } catch (error) {
    console.error('Search failed', error);
  }
};

const addToCart = async (product) => {
  if (!authState.isAuthenticated) {
    router.push('/login');
    return;
  }

  try {
    await axios.post('/api/cart', { 
      product_id: product.id,
      quantity: 1
    });
    alert('Produk ditambahkan ke keranjang!');
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menambahkan ke keranjang');
  }
};

onMounted(() => {
  fetchProducts();
});
</script>
