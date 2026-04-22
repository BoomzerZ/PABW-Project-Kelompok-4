<template>
  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Kelola Kupon</h1>
      <button @click="openCreateModal" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all">
        <Plus class="w-5 h-5" /> Tambah Kupon
      </button>
    </div>

    <!-- Coupon Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-zinc-800/50 text-zinc-400 text-xs uppercase">
            <tr>
              <th class="px-6 py-4">Kode</th>
              <th class="px-6 py-4">Diskon</th>
              <th class="px-6 py-4">Berlaku Hingga</th>
              <th class="px-6 py-4">Penggunaan</th>
              <th class="px-6 py-4">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800">
            <tr v-for="coupon in coupons" :key="coupon.id" class="hover:bg-zinc-800/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <Ticket class="w-4 h-4 text-red-500" />
                  <span class="font-bold text-white uppercase">{{ coupon.code }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <span v-if="coupon.discount_percentage" class="text-green-500 font-bold">
                  {{ coupon.discount_percentage }}%
                </span>
                <span v-else class="text-green-500 font-bold">
                  Rp {{ formatPrice(coupon.discount_amount) }}
                </span>
              </td>
              <td class="px-6 py-4 text-zinc-400">
                {{ coupon.valid_until ? formatDate(coupon.valid_until) : 'Tanpa Batas' }}
              </td>
              <td class="px-6 py-4 text-zinc-400">
                {{ coupon.used_count }} / {{ coupon.usage_limit || '∞' }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <button @click="openEditModal(coupon)" class="text-zinc-400 hover:text-white transition-colors">
                    <Edit3 class="w-5 h-5" />
                  </button>
                  <button @click="deleteCoupon(coupon.id)" class="text-zinc-400 hover:text-red-500 transition-colors">
                    <Trash2 class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="p-8 border-b border-zinc-800 flex justify-between items-center">
          <h2 class="text-2xl font-bold">{{ editingCoupon ? 'Edit Kupon' : 'Tambah Kupon Baru' }}</h2>
          <button @click="showModal = false" class="text-zinc-500 hover:text-white"><X class="w-6 h-6" /></button>
        </div>
        <form @submit.prevent="saveCoupon" class="p-8 space-y-6">
          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Kode Kupon</label>
              <input v-model="form.code" required placeholder="e.g. GAMER10" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors uppercase" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Persentase (%)</label>
                <input v-model.number="form.discount_percentage" type="number" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Atau Potongan (Rp)</label>
                <input v-model.number="form.discount_amount" type="number" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Berlaku Hingga</label>
              <input v-model="form.valid_until" type="date" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>

            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Limit Penggunaan</label>
              <input v-model.number="form.usage_limit" type="number" placeholder="Kosongkan jika tidak ada limit" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
          </div>
          
          <div class="flex justify-end pt-4">
            <button type="submit" :disabled="saveLoading" class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-xl font-bold shadow-lg transition-all disabled:opacity-50">
              {{ saveLoading ? 'Menyimpan...' : 'Simpan Kupon' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Plus, Edit3, Trash2, X, Ticket } from 'lucide-vue-next';
import axios from 'axios';

const coupons = ref([]);
const showModal = ref(false);
const editingCoupon = ref(null);
const saveLoading = ref(false);

const form = reactive({
  code: '',
  discount_percentage: null,
  discount_amount: null,
  valid_until: '',
  usage_limit: null
});

const formatPrice = (price) => {
  if (!price) return '0';
  return new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const fetchCoupons = async () => {
  try {
    const res = await axios.get('/api/admin/coupons');
    coupons.value = res.data;
  } catch (e) {
    console.error(e);
  }
};

const openCreateModal = () => {
  editingCoupon.value = null;
  Object.assign(form, { 
    code: '', 
    discount_percentage: null, 
    discount_amount: null, 
    valid_until: '', 
    usage_limit: null 
  });
  showModal.value = true;
};

const openEditModal = (coupon) => {
  editingCoupon.value = coupon;
  Object.assign(form, {
    ...coupon,
    valid_until: coupon.valid_until ? coupon.valid_until.split('T')[0] : ''
  });
  showModal.value = true;
};

const saveCoupon = async () => {
  if (!form.discount_percentage && !form.discount_amount) {
    alert('Harap masukkan persentase atau jumlah diskon');
    return;
  }

  saveLoading.value = true;
  try {
    if (editingCoupon.value) {
      await axios.put(`/api/admin/coupons/${editingCoupon.value.id}`, form);
    } else {
      await axios.post('/api/admin/coupons', form);
    }
    showModal.value = false;
    fetchCoupons();
    alert('Kupon berhasil disimpan!');
  } catch (e) {
    alert(e.response?.data?.message || 'Gagal menyimpan kupon');
  } finally {
    saveLoading.value = false;
  }
};

const deleteCoupon = async (id) => {
  if (!confirm('Yakin ingin menghapus kupon ini?')) return;
  try {
    await axios.delete(`/api/admin/coupons/${id}`);
    fetchCoupons();
    alert('Kupon berhasil dihapus');
  } catch (e) {
    alert('Gagal menghapus kupon');
  }
};

onMounted(fetchCoupons);
</script>
