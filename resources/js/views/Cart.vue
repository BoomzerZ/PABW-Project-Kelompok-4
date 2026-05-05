<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Keranjang Belanja</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="cartItems.length > 0" class="space-y-4 md:space-y-6">
      <div v-for="item in cartItems" :key="item.id" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 md:p-6 flex items-center gap-4 md:gap-6">
        <img :src="item.product.image_url" :alt="item.product.name" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg" />
        <div class="flex-1 min-w-0">
          <h3 class="text-lg md:text-xl font-bold truncate">{{ item.product.name }}</h3>
          <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 mt-1">
            <p class="text-zinc-400 text-sm">Jumlah: {{ item.quantity }}</p>
            <p v-if="item.product.stock < item.quantity" class="text-[10px] font-black bg-red-600 text-white px-2 py-0.5 rounded uppercase tracking-tighter animate-pulse self-start">
              Stok Kurang (Tersedia: {{ item.product.stock }})
            </p>
            <p v-else class="text-xs text-zinc-500 font-medium italic">Tersedia: {{ item.product.stock }}</p>
          </div>
          <p class="text-red-500 font-bold mt-1 text-sm md:text-base">Rp {{ formatPrice(item.product.price * item.quantity) }}</p>
        </div>
        <button @click="removeItem(item.id)" class="text-zinc-500 hover:text-red-500 transition-colors flex-shrink-0">
          <Trash2 class="w-5 h-5 md:w-6 md:h-6" />
        </button>
      </div>

      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 md:p-8 mt-6 md:mt-10 space-y-6">
        <!-- Coupon Section -->
        <div>
          <label class="block text-sm font-medium text-zinc-400 mb-2 uppercase tracking-widest text-[10px]">Punya Kode Promo?</label>
          <div v-if="!appliedCoupon" class="flex gap-2">
            <div class="relative flex-1">
              <Ticket class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" />
              <input 
                v-model="couponCode"
                type="text" 
                placeholder="Kode kupon"
                class="w-full bg-black border border-zinc-800 rounded-xl py-3 pl-9 pr-4 text-sm text-white focus:outline-none focus:border-red-600 transition-colors uppercase"
                @keyup.enter="handleApplyCoupon"
              />
            </div>
            <button 
              @click="handleApplyCoupon"
              :disabled="couponLoading || !couponCode"
              class="bg-zinc-800 hover:bg-zinc-700 text-white px-4 md:px-6 rounded-xl font-bold text-sm transition-colors disabled:opacity-50"
            >
              {{ couponLoading ? '...' : 'Pakai' }}
            </button>
          </div>
          
          <div v-else class="flex items-center justify-between bg-red-900/10 border border-red-900/30 rounded-xl p-3 md:p-4">
            <div class="flex items-center gap-3">
              <Ticket class="w-5 h-5 text-red-500" />
              <div>
                <p class="font-bold text-red-500 text-sm md:text-base">{{ appliedCoupon.code }}</p>
                <p class="text-[10px] text-red-400/80 italic">Kupon berhasil digunakan</p>
              </div>
            </div>
            <button @click="removeCoupon" class="text-zinc-500 hover:text-white transition-colors">
              <X class="w-4 h-4" />
            </button>
          </div>
          
          <p v-if="couponError" class="text-red-500 text-xs mt-2 font-medium">{{ couponError }}</p>
        </div>

        <div class="border-t border-zinc-800 pt-6 space-y-4">
          <div class="flex justify-between items-center text-zinc-400 text-sm">
            <span>Subtotal</span>
            <span>Rp {{ formatPrice(subtotal) }}</span>
          </div>
          <div v-if="appliedCoupon" class="flex justify-between items-center text-green-500 text-sm">
            <span>Diskon</span>
            <span>- Rp {{ formatPrice(discount) }}</span>
          </div>
          <div class="flex justify-between items-center pt-2 border-t border-zinc-800/50">
            <span class="text-base md:text-xl font-bold text-white uppercase tracking-tighter">Total</span>
            <span class="text-2xl md:text-3xl font-black text-red-500">Rp {{ formatPrice(totalPrice) }}</span>
          </div>
        </div>

        <button 
          @click="handleCheckout"
          :disabled="checkoutLoading || !isStockValid"
          class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl font-black text-base md:text-lg transition-all shadow-lg shadow-red-600/20 disabled:opacity-50 uppercase tracking-widest"
        >
          {{ checkoutLoading ? 'Memproses...' : (isStockValid ? 'Checkout Sekarang' : 'Stok Tidak Mencukupi') }}
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
import { ShoppingCart, Trash2, Loader2, Ticket, X } from 'lucide-vue-next';
import axios from 'axios';

const cartItems = ref([]);
const loading = ref(true);
const checkoutLoading = ref(false);
const couponCode = ref('');
const couponLoading = ref(false);
const appliedCoupon = ref(null);
const couponError = ref('');

const subtotal = computed(() => {
  return cartItems.value.reduce((total, item) => total + (item.product.price * item.quantity), 0);
});

const discount = computed(() => {
  if (!appliedCoupon.value) return 0;
  return appliedCoupon.value.discount;
});

const totalPrice = computed(() => {
  return subtotal.value - discount.value;
});

const isStockValid = computed(() => {
  return cartItems.value.every(item => item.product.stock >= item.quantity);
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

const handleApplyCoupon = async () => {
  if (!couponCode.value) return;
  
  couponLoading.value = true;
  couponError.value = '';
  try {
    const response = await axios.post('/api/coupons/validate', {
      code: couponCode.value,
      total_price: subtotal.value
    });
    appliedCoupon.value = response.data;
  } catch (error) {
    couponError.value = error.response?.data?.message || 'Kupon tidak valid';
    appliedCoupon.value = null;
  } finally {
    couponLoading.value = false;
  }
};

const removeCoupon = () => {
  appliedCoupon.value = null;
  couponCode.value = '';
  couponError.value = '';
};

const removeItem = async (id) => {
  try {
    await axios.delete(`/api/cart/${id}`);
    cartItems.value = cartItems.value.filter(item => item.id !== id);
    // Refresh coupon if subtotal changes
    if (appliedCoupon.value) {
      handleApplyCoupon();
    }
  } catch (error) {
    alert('Gagal menghapus item');
  }
};

const handleCheckout = async () => {
  if (cartItems.value.length === 0 || !isStockValid.value) return;
  
  checkoutLoading.value = true;
  try {
    const response = await axios.post('/api/orders', {
      coupon_code: appliedCoupon.value?.code
    });
    alert('Checkout berhasil! ID Pesanan: ' + response.data.id);
    cartItems.value = [];
    appliedCoupon.value = null;
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
