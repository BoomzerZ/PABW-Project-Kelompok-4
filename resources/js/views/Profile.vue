<template>
  <div>
    <h1 class="text-3xl font-bold mb-6">Profil Pengguna</h1>
    <div v-if="authState.user" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8">
      <div class="flex items-center gap-6">
        <div class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center text-4xl font-bold text-white uppercase">
          {{ authState.user.name.charAt(0) }}
        </div>
        <div>
          <h2 class="text-2xl font-bold text-white">{{ authState.user.name }}</h2>
          <p class="text-zinc-400">{{ authState.user.email }}</p>
          <div class="mt-4 inline-block px-3 py-1 bg-zinc-800 border border-zinc-700 rounded-full text-xs text-zinc-300 font-medium">
            Member Gaming Gear
          </div>
        </div>
      </div>

      <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-800/50 p-6 rounded-xl border border-zinc-700">
          <h3 class="text-zinc-400 text-sm font-medium mb-1">Total Pesanan</h3>
          <p class="text-2xl font-bold text-white">0</p>
        </div>
        <div class="bg-zinc-800/50 p-6 rounded-xl border border-zinc-700">
          <h3 class="text-zinc-400 text-sm font-medium mb-1">Status Akun</h3>
          <p class="text-2xl font-bold text-green-500">Aktif</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { authState, fetchUser } from '../utils/auth';

onMounted(() => {
  fetchUser();
});
</script>
