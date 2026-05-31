<template>
  <div class="max-w-7xl mx-auto space-y-12 pb-20">
    <!-- Search Bar & Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="h-10 md:h-14">
        <img :src="'/img/logo.png'" alt="AXELOT Logo" class="h-full w-auto object-contain drop-shadow-xl" />
      </div>
      <div class="relative w-full md:max-w-md">
        <form @submit.prevent="$router.push({ path: '/products', query: { search: searchQuery } })">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari keyboard, mouse, headset..." 
            class="w-full bg-zinc-900 border border-zinc-800 rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-red-600 transition-colors shadow-lg"
          />
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 w-5 h-5" />
        </form>
      </div>
    </div>

    <!-- Hero Banner Carousel -->
    <div @click="openPromoModal" class="cursor-pointer relative rounded-3xl overflow-hidden aspect-[16/10] md:aspect-[3/1] group shadow-2xl">
      <!-- Slides -->
      <div 
        v-for="(img, index) in bannerImages" 
        :key="index"
        class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
        :class="index === currentBanner ? 'opacity-100 z-0' : 'opacity-0 -z-10'"
      >
        <img :src="img" alt="Promo Banner" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
      </div>

      <!-- Content Overlay -->
      <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent flex flex-col justify-center px-6 md:px-12 space-y-4 md:space-y-6 z-10 pointer-events-none">
        <span class="inline-block bg-red-600 text-white px-3 py-1 rounded-full text-[10px] md:text-xs font-bold tracking-widest uppercase self-start">Promo Spesial</span>
        <h2 class="text-3xl md:text-6xl font-black text-white leading-tight uppercase drop-shadow-lg">
          WELCOME TO<br/><span class="text-red-600 italic">AXELOT</span>
        </h2>
        <p class="text-zinc-300 text-sm md:text-lg max-w-lg line-clamp-2 md:line-clamp-none drop-shadow-md">
          Dapatkan diskon eksklusif dan penawaran terbaik untuk produk pilihan dari AXELOT.
        </p>
        <div class="pointer-events-auto">
          <router-link to="/products" @click.stop class="btn-cyber mt-4 w-fit">
            Belanja Sekarang
          </router-link>
        </div>
      </div>

      <!-- Carousel Indicators -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20 pointer-events-auto">
        <button 
          v-for="(_, index) in bannerImages" 
          :key="index"
          @click.stop="setBanner(index)"
          class="h-2.5 rounded-full transition-all duration-300"
          :class="index === currentBanner ? 'bg-red-600 w-8' : 'bg-white/50 w-2.5 hover:bg-white'"
        ></button>
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-if="loading" class="space-y-12">
      <section class="space-y-6">
        <div class="h-8 bg-zinc-800 rounded w-1/4 animate-pulse"></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <SkeletonCard v-for="i in 4" :key="i" type="product-card" />
        </div>
      </section>
      <section class="space-y-6">
        <div class="h-8 bg-zinc-800 rounded w-1/3 animate-pulse"></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <SkeletonCard v-for="i in 3" :key="i" type="product-horizontal" />
        </div>
      </section>
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
            class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-red-600/80 transition-all duration-300 group shadow-lg hover:shadow-[0_0_20px_rgba(220,38,38,0.1)] hover:-translate-y-1 relative"
          >
            <div class="relative aspect-square overflow-hidden">
              <div v-if="isNewProduct(product.created_at)" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded uppercase tracking-tighter z-10 animate-pulse">
                New Arrival
              </div>
              <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                <button 
                  @click="addToCart(product)" 
                  :disabled="product.stock <= 0"
                  class="bg-red-600 p-3 rounded-full text-white hover:bg-red-700 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <ShoppingCart class="w-6 h-6" />
                </button>
              </div>
            </div>
            <div class="p-5 space-y-2">
              <span class="text-xs font-bold text-red-500 uppercase tracking-widest">{{ product.category?.name || 'Gaming' }}</span>
              <router-link :to="'/product/' + product.id">
                <h4 class="text-lg font-bold text-white line-clamp-1 group-hover:text-red-500 transition-colors cursor-pointer">{{ product.name }}</h4>
              </router-link>
              <p class="text-zinc-500 text-sm line-clamp-2">{{ product.description }}</p>
              <div class="pt-4 flex items-center justify-between">
                <span class="text-xl font-black text-white drop-shadow-[0_0_8px_rgba(220,38,38,0.5)]">Rp {{ formatPrice(product.price) }}</span>
                <span v-if="product.stock > 0" class="text-xs text-green-500 font-bold drop-shadow-[0_0_5px_rgba(34,197,94,0.4)]">Stok Ready</span>
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
            class="bg-zinc-900/80 backdrop-blur-sm border-2 border-red-900/20 rounded-2xl p-6 flex gap-6 hover:border-red-600 transition-all duration-300 group shadow-lg hover:-translate-y-2 hover:shadow-[0_0_30px_rgba(220,38,38,0.3)] relative"
          >
            <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-zinc-950 p-2 border border-zinc-800">
              <img :src="product.image_url" :alt="product.name" class="w-full h-full object-contain" />
            </div>
            <div class="flex-1 flex flex-col justify-between py-1">
              <div>
                <router-link :to="'/product/' + product.id">
                  <h4 class="font-bold text-white line-clamp-1 hover:text-red-500 transition-colors cursor-pointer">{{ product.name }}</h4>
                </router-link>
                <p class="text-red-500 font-bold">Rp {{ formatPrice(product.price) }}</p>
              </div>
              <button 
                @click="addToCart(product)" 
                :disabled="product.stock <= 0"
                class="text-zinc-400 hover:text-white flex items-center gap-2 text-sm transition-colors disabled:opacity-50"
              >
                <Plus class="w-4 h-4" /> {{ product.stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- AI Chat CTA -->
    <div class="bg-gradient-to-r from-zinc-900 to-red-950/20 border border-zinc-800 rounded-3xl p-6 md:p-12 flex flex-col lg:flex-row items-center justify-between gap-8 md:gap-12 shadow-2xl">
      <div class="space-y-4 text-center lg:text-left">
        <h2 class="text-3xl md:text-4xl font-black text-white leading-tight uppercase">Bingung Pilih Gear?<br/><span class="text-red-600 italic">Tanyakan AI Kami!</span></h2>
        <p class="text-zinc-400 text-base md:text-lg max-w-md mx-auto lg:mx-0">Dapatkan rekomendasi gaming gear yang dipersonalisasi sesuai budget dan kebutuhanmu dalam hitungan detik.</p>
        <router-link to="/chat" class="inline-flex items-center gap-3 bg-white text-black px-6 py-3 md:px-8 md:py-4 rounded-xl font-bold text-base md:text-lg hover:bg-zinc-200 transition-colors shadow-xl">
          <MessageSquare class="w-6 h-6" /> Chat Sekarang
        </router-link>
      </div>
      <div class="w-full max-w-[280px] md:max-w-sm aspect-square bg-zinc-800/50 rounded-2xl flex items-center justify-center p-6 md:p-8 border border-zinc-700/50 relative overflow-hidden">
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

    <!-- Full Promo Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isPromoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm" @click="isPromoModalOpen = false">
        <button class="absolute top-6 right-6 p-3 bg-zinc-900 rounded-full text-zinc-400 hover:text-white hover:bg-red-600 transition-colors shadow-2xl z-50">
          <X class="w-6 h-6" />
        </button>
        <img :src="bannerImages[currentBanner]" alt="Promo Full" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-[0_0_50px_rgba(220,38,38,0.3)] transition-transform transform scale-100" @click.stop />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { 
  Search, 
  Gamepad2, 
  ShoppingCart, 
  Loader2, 
  ChevronLeft, 
  ChevronRight,
  MessageSquare,
  Plus,
  ArrowRight,
  X
} from 'lucide-vue-next';
import axios from 'axios';
import { authState } from '../utils/auth';
import { useRouter } from 'vue-router';
import SkeletonCard from '../components/SkeletonCard.vue';

const router = useRouter();
const searchQuery = ref('');
const products = ref([]);
const loading = ref(true);

// Carousel State
const bannerImages = [
  '/img/iklan1.png',
  '/img/iklan2.png',
  '/img/iklan3.png'
];
const currentBanner = ref(0);
let bannerInterval;
const isPromoModalOpen = ref(false);

const openPromoModal = () => {
  isPromoModalOpen.value = true;
};

const nextBanner = () => {
  currentBanner.value = (currentBanner.value + 1) % bannerImages.length;
};

const setBanner = (index) => {
  currentBanner.value = index;
  resetInterval();
};

const resetInterval = () => {
  clearInterval(bannerInterval);
  bannerInterval = setInterval(nextBanner, 4000); // ganti tiap 4 detik
};

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
  resetInterval();
});

onUnmounted(() => {
  if (bannerInterval) clearInterval(bannerInterval);
});
</script>
