<template>
  <div class="max-w-7xl mx-auto space-y-8 pb-20">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <h1 class="text-3xl font-bold flex items-center gap-2">
        <Gamepad2 class="text-red-600 w-8 h-8" />
        Katalog Produk
      </h1>
      <div class="relative w-full md:max-w-md">
        <input 
          v-model="filters.search"
          @input="debounceFetch"
          type="text" 
          placeholder="Cari produk..." 
          class="w-full bg-zinc-900 border border-zinc-800 rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-red-600 transition-colors shadow-lg"
        />
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 w-5 h-5" />
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Sidebar Filters -->
      <div class="w-full lg:w-1/4 space-y-6">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-6">
          <div>
            <h3 class="font-bold text-white mb-4 uppercase tracking-widest text-sm border-b border-zinc-800 pb-2">Urutkan</h3>
            <select v-model="filters.sort" @change="fetchProducts" class="w-full bg-black border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600">
              <option value="latest">Terbaru</option>
              <option value="price_asc">Harga Terendah</option>
              <option value="price_desc">Harga Tertinggi</option>
            </select>
          </div>
          
          <div>
            <h3 class="font-bold text-white mb-4 uppercase tracking-widest text-sm border-b border-zinc-800 pb-2">Harga Maksimal</h3>
            <input 
              type="range" 
              v-model="filters.max_price" 
              @change="fetchProducts"
              min="0" 
              max="5000000" 
              step="100000"
              class="w-full accent-red-600"
            />
            <div class="mt-2 text-red-500 font-bold">Rp {{ formatPrice(filters.max_price) }}</div>
          </div>
          
          <button @click="resetFilters" class="w-full py-3 border border-zinc-700 hover:border-red-500 hover:text-red-500 rounded-xl transition-colors font-medium text-sm">
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Product Grid -->
      <div class="w-full lg:w-3/4">
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <SkeletonCard v-for="i in 6" :key="i" type="product-card" />
        </div>

        <div v-else-if="products.length > 0">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="product in products" 
              :key="product.id"
              class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-red-600/50 transition-all group shadow-xl hover:shadow-red-600/5 flex flex-col"
            >
              <div class="relative aspect-square overflow-hidden cursor-pointer" @click="$router.push('/product/' + product.id)">
                <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              </div>
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <span class="text-xs font-bold text-red-500 uppercase tracking-widest">{{ product.category?.name || 'Gaming' }}</span>
                  <router-link :to="'/product/' + product.id">
                    <h4 class="text-lg font-bold text-white line-clamp-1 group-hover:text-red-500 transition-colors mt-1">{{ product.name }}</h4>
                  </router-link>
                  <div class="flex items-center text-yellow-500 mt-2 scale-75 origin-left">
                    <Star v-for="i in 5" :key="i" :class="['w-4 h-4', i <= Math.round(product.average_rating || 0) ? 'fill-current' : 'text-zinc-700']" />
                    <span class="text-zinc-500 ml-2 text-sm">({{ product.average_rating ? Number(product.average_rating).toFixed(1) : '0' }})</span>
                  </div>
                </div>
                <div class="pt-4 mt-auto border-t border-zinc-800/50 flex flex-col gap-3">
                  <div class="flex items-center justify-between">
                    <span class="text-xl font-black text-white">Rp {{ formatPrice(product.price) }}</span>
                  </div>
                  <button 
                    @click="addToCart(product)" 
                    :disabled="product.stock <= 0"
                    class="w-full bg-zinc-800 hover:bg-red-600 text-white p-3 rounded-xl transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm font-bold uppercase tracking-widest"
                  >
                    <ShoppingCart class="w-4 h-4" />
                    {{ product.stock > 0 ? 'Beli' : 'Habis' }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-12">
            <button 
              @click="changePage(currentPage - 1)" 
              :disabled="currentPage === 1"
              class="p-3 bg-zinc-900 rounded-xl hover:bg-zinc-800 disabled:opacity-50"
            ><ChevronLeft class="w-5 h-5" /></button>
            <span class="p-3 px-6 bg-zinc-900 rounded-xl font-bold">Halaman {{ currentPage }} dari {{ totalPages }}</span>
            <button 
              @click="changePage(currentPage + 1)" 
              :disabled="currentPage === totalPages"
              class="p-3 bg-zinc-900 rounded-xl hover:bg-zinc-800 disabled:opacity-50"
            ><ChevronRight class="w-5 h-5" /></button>
          </div>
        </div>

        <div v-else class="text-center py-20 border border-dashed border-zinc-800 rounded-3xl">
          <p class="text-zinc-500 text-xl">Tidak ada produk yang ditemukan.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Search, Gamepad2, ShoppingCart, Loader2, Star, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { authState } from '../utils/auth';
import SkeletonCard from '../components/SkeletonCard.vue';

const router = useRouter();
const products = ref([]);
const loading = ref(true);

const currentPage = ref(1);
const totalPages = ref(1);

const filters = ref({
  search: '',
  sort: 'latest',
  max_price: 5000000,
  category_id: ''
});

let searchTimeout = null;

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const debounceFetch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchProducts();
  }, 500);
};

const resetFilters = () => {
  filters.value = {
    search: '',
    sort: 'latest',
    max_price: 5000000,
    category_id: ''
  };
  currentPage.value = 1;
  fetchProducts();
};

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
  fetchProducts();
};

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/products', {
      params: {
        ...filters.value,
        page: currentPage.value,
        per_page: 9
      }
    });
    
    // Handle paginated response
    const data = response.data;
    if (data.data) {
      products.value = data.data;
      currentPage.value = data.current_page;
      totalPages.value = data.last_page;
    } else {
      products.value = data;
      totalPages.value = 1;
    }
  } catch (error) {
    console.error('Failed to fetch products', error);
  } finally {
    loading.value = false;
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
