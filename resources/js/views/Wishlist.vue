<template>
  <div class="max-w-4xl mx-auto py-12 text-center">
    <Heart class="w-20 h-20 mx-auto text-zinc-800 mb-6" />
    <h1 class="text-3xl font-bold mb-4 text-white">Wishlist Saya</h1>
    <p class="text-zinc-500 text-lg mb-8">Fitur Wishlist sedang dalam pengembangan. Segera hadir!</p>
    <router-link to="/" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-bold transition-colors">
      Kembali Belanja
    </router-link>
  </div>
</template>

<script setup>
import { Heart } from 'lucide-vue-next';
</script>
