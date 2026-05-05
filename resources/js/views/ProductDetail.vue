<template>
  <div class="max-w-7xl mx-auto pb-20">
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="product" class="space-y-12">
      <!-- Product Main Info -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Gallery -->
        <div class="space-y-4">
          <div class="aspect-square bg-zinc-900 border border-zinc-800 rounded-3xl overflow-hidden group">
            <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
          </div>
          <!-- Thumbnails placeholder -->
          <div class="grid grid-cols-4 gap-4">
            <div class="aspect-square bg-zinc-900 border border-red-600 rounded-xl overflow-hidden cursor-pointer">
              <img :src="product.image_url" class="w-full h-full object-cover" />
            </div>
            <div v-for="i in 3" :key="i" class="aspect-square bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden opacity-50 hover:opacity-100 transition-opacity cursor-pointer">
              <img :src="product.image_url" class="w-full h-full object-cover grayscale" />
            </div>
          </div>
        </div>

        <!-- Info & Actions -->
        <div class="flex flex-col">
          <div class="flex-1 space-y-6">
            <div>
              <span class="text-xs font-bold text-red-600 uppercase tracking-widest">{{ product.category?.name }}</span>
              <h1 class="text-4xl font-black text-white mt-2">{{ product.name }}</h1>
            </div>
            
            <div class="flex items-center gap-4">
              <div class="flex items-center text-yellow-500">
                <Star v-for="i in 5" :key="i" :class="['w-5 h-5 fill-current', i > 4 ? 'text-zinc-700' : '']" />
              </div>
              <span class="text-zinc-500 text-sm">(4.8 • 120 Review)</span>
            </div>

            <div class="text-4xl font-black text-red-500">
              Rp {{ formatPrice(product.price) }}
            </div>

            <p class="text-zinc-400 leading-relaxed text-lg">
              {{ product.description }}
            </p>

            <!-- Specs Table -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-4">
              <h3 class="font-bold text-white uppercase tracking-wider text-sm border-b border-zinc-800 pb-3">Spesifikasi Teknis</h3>
              <div class="grid grid-cols-2 gap-y-3 text-sm">
                <template v-if="product.switch_type">
                  <span class="text-zinc-500">Switch Type</span>
                  <span class="text-white font-medium">{{ product.switch_type }}</span>
                </template>
                <template v-if="product.dpi">
                  <span class="text-zinc-500">Max DPI</span>
                  <span class="text-white font-medium">{{ product.dpi }} DPI</span>
                </template>
                <template v-if="product.sensor">
                  <span class="text-zinc-500">Sensor</span>
                  <span class="text-white font-medium">{{ product.sensor }}</span>
                </template>
                <template v-if="product.connectivity">
                  <span class="text-zinc-500">Konektivitas</span>
                  <span class="text-white font-medium">{{ product.connectivity }}</span>
                </template>
                <template v-if="product.weight">
                  <span class="text-zinc-500">Berat</span>
                  <span class="text-white font-medium">{{ product.weight }}</span>
                </template>
                <span class="text-zinc-500">Garansi</span>
                <span class="text-white font-medium">1 Tahun Resmi</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-12 p-6 bg-zinc-900 border border-zinc-800 rounded-3xl space-y-6">
            <div class="flex items-center justify-between">
              <span class="text-zinc-400 font-medium">Informasi Stok</span>
              <span v-if="product.stock > 0" class="text-green-500 font-bold flex items-center gap-2">
                <CheckCircle2 class="w-5 h-5" /> Ready ({{ product.stock }} unit)
              </span>
              <span v-else class="text-red-500 font-bold flex items-center gap-2">
                <XCircle class="w-5 h-5" /> Stok Habis
              </span>
            </div>
            
            <div class="flex gap-4">
              <div class="flex items-center bg-black border border-zinc-700 rounded-xl px-2">
                <button @click="quantity > 1 && quantity--" class="p-3 text-zinc-400 hover:text-white transition-colors">-</button>
                <input v-model.number="quantity" type="number" class="w-12 bg-transparent text-center font-bold focus:outline-none" />
                <button @click="quantity < product.stock && quantity++" class="p-3 text-zinc-400 hover:text-white transition-colors">+</button>
              </div>
              <button 
                @click="addToCart" 
                :disabled="product.stock <= 0 || cartLoading"
                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl transition-all shadow-lg shadow-red-600/20 disabled:opacity-50 flex items-center justify-center gap-3 uppercase tracking-widest"
              >
                <ShoppingCart class="w-5 h-5" />
                {{ cartLoading ? 'Menambahkan...' : 'Tambah ke Keranjang' }}
              </button>
              <button class="p-4 bg-zinc-800 hover:bg-zinc-700 rounded-xl text-zinc-400 hover:text-white transition-colors">
                <Heart class="w-6 h-6" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews Section -->
      <section class="space-y-8">
        <h2 class="text-2xl font-black border-l-4 border-red-600 pl-4 uppercase tracking-wider">Ulasan Pembeli</h2>
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 space-y-8">
           <div v-for="i in 2" :key="i" class="border-b border-zinc-800 last:border-0 pb-8 last:pb-0 space-y-4">
             <div class="flex justify-between items-start">
               <div class="flex items-center gap-3">
                 <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center font-bold text-white uppercase text-xs">U{{ i }}</div>
                 <div>
                   <p class="font-bold text-white">User Gamer #{{ i }}</p>
                   <div class="flex text-yellow-500 scale-75 -ml-4">
                     <Star v-for="j in 5" :key="j" class="w-4 h-4 fill-current" />
                   </div>
                 </div>
               </div>
               <span class="text-xs text-zinc-500">2 hari yang lalu</span>
             </div>
             <p class="text-zinc-400 italic">"Produk sangat berkualitas, pengiriman cepat dan packing aman. Recommended seller!"</p>
           </div>
           
           <button @click="showReviewModal = true" class="w-full py-4 border-2 border-dashed border-zinc-800 rounded-2xl text-zinc-500 font-bold hover:border-zinc-700 hover:text-zinc-400 transition-all uppercase tracking-widest text-sm">
             Tulis Ulasan Kamu
           </button>
        </div>
      </section>

      <!-- Review Modal -->
      <div v-if="showReviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
        <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
          <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
            <h2 class="text-xl font-bold">Tulis Ulasan</h2>
            <button @click="showReviewModal = false" class="text-zinc-500 hover:text-white"><X class="w-6 h-6" /></button>
          </div>
          <form @submit.prevent="submitReview" class="p-6 space-y-6">
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Rating</label>
              <div class="flex gap-2">
                <Star 
                  v-for="i in 5" 
                  :key="i" 
                  @click="reviewForm.rating = i"
                  :class="['w-8 h-8 cursor-pointer transition-all', i <= reviewForm.rating ? 'text-yellow-500 fill-current scale-110' : 'text-zinc-700']" 
                />
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Ulasan Anda</label>
              <textarea 
                v-model="reviewForm.comment" 
                rows="4" 
                placeholder="Bagikan pengalamanmu menggunakan produk ini..."
                class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors"
              ></textarea>
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl transition-all shadow-lg shadow-red-600/20 uppercase tracking-widest">
              Kirim Ulasan
            </button>
          </form>
        </div>
      </div>

      <!-- Similar Products -->
      <section class="space-y-8 mt-12">
        <h2 class="text-2xl font-black border-l-4 border-red-600 pl-4 uppercase tracking-wider">Produk Serupa</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
           <div v-for="p in similarProducts" :key="p.id" @click="$router.push('/product/'+p.id)" class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-red-600/50 transition-all group cursor-pointer shadow-xl">
             <div class="aspect-square overflow-hidden">
               <img :src="p.image_url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
             </div>
             <div class="p-5">
               <h4 class="font-bold text-white line-clamp-1">{{ p.name }}</h4>
               <p class="text-red-500 font-black mt-2">Rp {{ formatPrice(p.price) }}</p>
             </div>
           </div>
        </div>
      </section>
    </div>

    <div v-else class="text-center py-20">
      <p class="text-zinc-500 text-xl">Produk tidak ditemukan.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { 
  Loader2, 
  Star, 
  ShoppingCart, 
  Heart, 
  CheckCircle2, 
  XCircle,
  Plus,
  Minus,
  X
} from 'lucide-vue-next';
import axios from 'axios';
import { authState } from '../utils/auth';

const route = useRoute();
const router = useRouter();
const product = ref(null);
const similarProducts = ref([]);
const loading = ref(true);
const cartLoading = ref(false);
const quantity = ref(1);
const showReviewModal = ref(false);
const reviewForm = ref({
  rating: 5,
  comment: ''
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const fetchProduct = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/products/${route.params.id}`);
    product.value = response.data;
    
    // Fetch similar products (simplified for now)
    const similarRes = await axios.get('/api/products', { 
      params: { category_id: product.value.category_id } 
    });
    similarProducts.value = (similarRes.data.data || similarRes.data)
      .filter(p => p.id !== product.value.id)
      .slice(0, 4);
  } catch (error) {
    console.error('Failed to fetch product', error);
  } finally {
    loading.value = false;
  }
};

const addToCart = async () => {
  if (!authState.isAuthenticated) {
    router.push('/login');
    return;
  }

  cartLoading.value = true;
  try {
    await axios.post('/api/cart', { 
      product_id: product.value.id,
      quantity: quantity.value
    });
    alert('Produk ditambahkan ke keranjang!');
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menambahkan ke keranjang');
  } finally {
    cartLoading.value = false;
  }
};

const submitReview = () => {
  alert('Ulasan kamu berhasil dikirim! Terima kasih atas feedback-nya.');
  showReviewModal.value = false;
  reviewForm.value = { rating: 5, comment: '' };
};

onMounted(fetchProduct);

// Re-fetch if ID changes
watch(() => route.params.id, fetchProduct);
</script>
