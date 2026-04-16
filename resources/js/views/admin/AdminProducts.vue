<template>
  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Kelola Produk</h1>
      <button @click="openCreateModal" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all">
        <Plus class="w-5 h-5" /> Tambah Produk
      </button>
    </div>

    <!-- Product Table -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-zinc-800/50 text-zinc-400 text-xs uppercase">
            <tr>
              <th class="px-6 py-4">Produk</th>
              <th class="px-6 py-4">Kategori</th>
              <th class="px-6 py-4">Harga</th>
              <th class="px-6 py-4">Stok</th>
              <th class="px-6 py-4">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-800">
            <tr v-for="product in products" :key="product.id" class="hover:bg-zinc-800/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                  <img :src="product.image_url" class="w-12 h-12 rounded object-cover" />
                  <span class="font-bold text-white">{{ product.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-zinc-400">{{ product.category?.name }}</td>
              <td class="px-6 py-4 font-bold text-red-500">Rp {{ formatPrice(product.price) }}</td>
              <td class="px-6 py-4">{{ product.stock }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <button @click="openEditModal(product)" class="text-zinc-400 hover:text-white transition-colors">
                    <Edit3 class="w-5 h-5" />
                  </button>
                  <button @click="deleteProduct(product.id)" class="text-zinc-400 hover:text-red-500 transition-colors">
                    <Trash2 class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Create/Edit (Simplistic implementation) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-zinc-900 border border-zinc-800 rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl">
        <div class="p-8 border-b border-zinc-800 flex justify-between items-center">
          <h2 class="text-2xl font-bold">{{ editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
          <button @click="showModal = false" class="text-zinc-500 hover:text-white"><X class="w-6 h-6" /></button>
        </div>
        <form @submit.prevent="saveProduct" class="p-8 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Nama Produk</label>
              <input v-model="form.name" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Harga (Rp)</label>
              <input v-model.number="form.price" type="number" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Kategori ID</label>
              <input v-model.number="form.category_id" type="number" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Stok</label>
              <input v-model.number="form.stock" type="number" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            
            <!-- Gaming Specs -->
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Switch Type</label>
              <input v-model="form.switch_type" placeholder="e.g. Cherry MX Red" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">DPI</label>
              <input v-model.number="form.dpi" type="number" placeholder="e.g. 16000" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Connectivity</label>
              <input v-model="form.connectivity" placeholder="e.g. Wired, Wireless" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Sensor</label>
              <input v-model="form.sensor" placeholder="e.g. Hero 25K" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Weight</label>
              <input v-model="form.weight" placeholder="e.g. 63g" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>

            <div class="md:col-span-2 space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Image URL</label>
              <input v-model="form.image_url" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors" />
            </div>
            <div class="md:col-span-2 space-y-2">
              <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Deskripsi</label>
              <textarea v-model="form.description" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-red-600 transition-colors"></textarea>
            </div>
          </div>
          <div class="flex justify-end pt-4">
            <button type="submit" :disabled="saveLoading" class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-xl font-bold shadow-lg transition-all disabled:opacity-50">
              {{ saveLoading ? 'Menyimpan...' : 'Simpan Produk' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { Plus, Edit3, Trash2, X } from 'lucide-vue-next';
import axios from 'axios';

const products = ref([]);
const showModal = ref(false);
const editingProduct = ref(null);
const saveLoading = ref(false);

const form = reactive({
  name: '',
  price: 0,
  stock: 0,
  category_id: 1,
  image_url: '',
  description: '',
  switch_type: '',
  dpi: null,
  connectivity: '',
  sensor: '',
  weight: ''
});

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

const fetchProducts = async () => {
  try {
    const res = await axios.get('/api/products');
    products.value = res.data.data || res.data;
  } catch (e) {
    console.error(e);
  }
};

const openCreateModal = () => {
  editingProduct.value = null;
  Object.assign(form, { 
    name: '', 
    price: 0, 
    stock: 0, 
    category_id: 1, 
    image_url: '', 
    description: '',
    switch_type: '',
    dpi: null,
    connectivity: '',
    sensor: '',
    weight: ''
  });
  showModal.value = true;
};

const openEditModal = (product) => {
  editingProduct.value = product;
  Object.assign(form, product);
  showModal.value = true;
};

const saveProduct = async () => {
  saveLoading.value = true;
  try {
    if (editingProduct.value) {
      await axios.put(`/api/admin/products/${editingProduct.value.id}`, form);
    } else {
      await axios.post('/api/admin/products', form);
    }
    showModal.value = false;
    fetchProducts();
    alert('Produk berhasil disimpan!');
  } catch (e) {
    alert('Gagal menyimpan produk');
  } finally {
    saveLoading.value = false;
  }
};

const deleteProduct = async (id) => {
  if (!confirm('Yakin ingin menghapus produk ini?')) return;
  try {
    await axios.delete(`/api/admin/products/${id}`);
    fetchProducts();
    alert('Produk berhasil dihapus');
  } catch (e) {
    alert('Gagal menghapus produk');
  }
};

onMounted(fetchProducts);
</script>
