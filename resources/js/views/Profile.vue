<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Profil Pengguna</h1>
    
    <div v-if="loading" class="flex justify-center py-20">
      <Loader2 class="w-10 h-10 animate-spin text-red-600" />
    </div>

    <div v-else-if="authState.user" class="space-y-8">
      <!-- Profile Info -->
      <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
          <div class="w-32 h-32 bg-red-600 rounded-full flex items-center justify-center text-5xl font-bold text-white uppercase shadow-2xl">
            {{ authState.user.name.charAt(0) }}
          </div>
          <div class="flex-1 text-center md:text-left space-y-2">
            <h2 class="text-3xl font-bold text-white">{{ authState.user.name }}</h2>
            <p class="text-zinc-400 text-lg">{{ authState.user.email }}</p>
            <div class="inline-block px-4 py-1 bg-red-600/10 border border-red-600/20 rounded-full text-xs text-red-500 font-bold tracking-widest uppercase">
              Pro Gamer Member
            </div>
          </div>
          <button 
            @click="isEditing = !isEditing"
            class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl font-bold transition-colors"
          >
            {{ isEditing ? 'Batal' : 'Edit Profil' }}
          </button>
        </div>
      </div>

      <!-- Edit Form -->
      <div v-if="isEditing" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8 animate-in fade-in slide-in-from-top-4 duration-300">
        <form @submit.prevent="updateProfile" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider">Nama Lengkap</label>
              <input 
                v-model="editForm.name"
                type="text" 
                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider">Alamat Email</label>
              <input 
                v-model="editForm.email"
                type="email" 
                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors"
              />
            </div>
          </div>
          <div class="flex justify-end">
            <button 
              type="submit"
              :disabled="saveLoading"
              class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-colors disabled:opacity-50"
            >
              {{ saveLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2">
          <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest">Total Transaksi</h3>
          <p class="text-4xl font-black text-white">{{ stats.total_orders || 0 }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2">
          <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest">Item di Keranjang</h3>
          <p class="text-4xl font-black text-red-600">{{ stats.cart_count || 0 }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl space-y-2">
          <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest">Loyalty Points</h3>
          <p class="text-4xl font-black text-white">2.500</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { authState, fetchUser } from '../utils/auth';
import { Loader2 } from 'lucide-vue-next';
import axios from 'axios';

const loading = ref(true);
const isEditing = ref(false);
const saveLoading = ref(false);
const stats = ref({});

const editForm = reactive({
  name: '',
  email: ''
});

const loadProfileData = async () => {
  loading.value = true;
  await fetchUser();
  if (authState.user) {
    editForm.name = authState.user.name;
    editForm.email = authState.user.email;
    
    // Fetch stats (mocked if not available or combine from other APIs)
    try {
      const ordersRes = await axios.get('/api/orders');
      const cartRes = await axios.get('/api/cart');
      stats.value = {
        total_orders: ordersRes.data.length,
        cart_count: cartRes.data.length
      };
    } catch (e) {
      console.error('Stats load failed', e);
    }
  }
  loading.value = false;
};

const updateProfile = async () => {
  saveLoading.value = true;
  try {
    const response = await axios.put('/api/user', editForm);
    authState.user = response.data.user;
    isEditing.value = false;
    alert('Profil berhasil diperbarui!');
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal memperbarui profil');
  } finally {
    saveLoading.value = false;
  }
};

onMounted(() => {
  loadProfileData();
});
</script>
