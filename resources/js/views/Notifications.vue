<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold mb-8">Notifikasi</h1>
    
    <div v-if="notifications.length > 0" class="space-y-4">
      <div v-for="n in notifications" :key="n.id" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex gap-6 hover:border-zinc-700 transition-colors cursor-pointer">
        <div :class="['p-3 rounded-xl flex-shrink-0 h-fit', n.type === 'promo' ? 'bg-red-950/30 text-red-500' : 'bg-blue-950/30 text-blue-500']">
          <component :is="n.type === 'promo' ? Tag : Package" class="w-6 h-6" />
        </div>
        <div class="flex-1 space-y-1">
          <div class="flex justify-between items-start">
            <h3 class="font-bold text-white">{{ n.title }}</h3>
            <span class="text-xs text-zinc-500">{{ n.time }}</span>
          </div>
          <p class="text-zinc-400 text-sm leading-relaxed">{{ n.message }}</p>
        </div>
      </div>
    </div>
    
    <div v-else class="bg-zinc-900 border border-zinc-800 rounded-2xl p-20 text-center">
      <BellOff class="w-16 h-16 mx-auto text-zinc-800 mb-4" />
      <p class="text-zinc-500 text-xl">Belum ada notifikasi baru.</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Tag, Package, BellOff } from 'lucide-vue-next';

const notifications = ref([
  {
    id: 1,
    type: 'promo',
    title: 'Promo Flash Sale Ramadan!',
    message: 'Diskon hingga 70% untuk aksesoris gaming pilihan. Cek sekarang sebelum kehabisan!',
    time: '2 jam yang lalu'
  },
  {
    id: 2,
    type: 'order',
    title: 'Pesanan Diproses',
    message: 'Pesanan #ORD-9928 sedang disiapkan oleh penjual. Mohon tunggu update selanjutnya.',
    time: '5 jam yang lalu'
  },
  {
    id: 3,
    type: 'promo',
    title: 'Voucher Baru Tersedia',
    message: 'Gunakan kode GAMERSETIA untuk mendapatkan cashback Rp 50.000 minimal belanja Rp 500.000.',
    time: '1 hari yang lalu'
  }
]);
</script>
