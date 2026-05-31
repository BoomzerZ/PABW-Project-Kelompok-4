<template>
  <div class="flex min-h-screen">
    <!-- Admin Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-zinc-950/90 backdrop-blur-xl border-r border-red-900/30 flex flex-col shadow-[10px_0_30px_rgba(220,38,38,0.05)] z-40">
      <div class="h-20 flex items-center justify-center border-b border-red-900/30 p-3 bg-red-950/10">
        <img :src="'/img/logo.png'" alt="AXELOT Admin" class="w-full h-full object-contain drop-shadow-xl" />
      </div>

      <nav class="flex-1 px-4 space-y-2 mt-4">
        <router-link 
          v-for="item in adminMenu" 
          :key="item.path" 
          :to="item.path"
          class="flex items-center gap-3 px-4 py-3 rounded-r-xl transition-all duration-300 group"
          :class="[
            $route.path === item.path 
              ? 'bg-gradient-to-r from-red-600/20 to-transparent text-white border-l-4 border-red-600 shadow-[inset_4px_0_10px_rgba(220,38,38,0.1)]' 
              : 'text-zinc-400 hover:bg-zinc-800/50 hover:text-white border-l-4 border-transparent hover:border-zinc-700 hover:translate-x-1'
          ]"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span class="font-medium">{{ item.name }}</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-red-900/30">
        <router-link to="/" class="flex items-center gap-3 px-4 py-3 text-zinc-400 hover:text-white cursor-pointer transition-colors group hover:translate-x-1">
          <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
          <span class="font-medium">Kembali ke Toko</span>
        </router-link>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8 relative z-10">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { 
  LayoutDashboard, 
  Package, 
  ClipboardList, 
  Ticket,
  ShieldCheck,
  ArrowLeft
} from 'lucide-vue-next';

const adminMenu = [
  { name: 'Dashboard', path: '/admin', icon: LayoutDashboard },
  { name: 'Kelola Produk', path: '/admin/products', icon: Package },
  { name: 'Kelola Pesanan', path: '/admin/orders', icon: ClipboardList },
  { name: 'Kelola Kupon', path: '/admin/coupons', icon: Ticket },
];
</script>
