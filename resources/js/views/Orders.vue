<template>
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Riwayat Pesanan</h1>
    
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-12 text-center">
      <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
        <ClipboardList class="w-10 h-10 text-zinc-500" />
      </div>
      <h3 class="text-xl font-bold text-white mb-2">Belum ada pesanan</h3>
      <p class="text-zinc-400 mb-8">Anda belum memiliki riwayat pesanan di Gaming Gear Marketplace.</p>
      <router-link to="/" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-colors">
        Mulai Belanja
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ClipboardList } from 'lucide-vue-next';
</script>
