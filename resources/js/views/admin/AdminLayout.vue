<template>
  <div class="flex min-h-screen bg-black text-white">
    <!-- Admin Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-zinc-900 border-r border-zinc-800 flex flex-col">
      <div class="p-6">
        <h1 class="text-xl font-bold text-red-600 flex items-center gap-2">
          <ShieldCheck class="w-6 h-6" />
          ADMIN PANEL
        </h1>
      </div>

      <nav class="flex-1 px-4 space-y-2 mt-4">
        <router-link 
          v-for="item in adminMenu" 
          :key="item.path" 
          :to="item.path"
          class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors group"
          :class="[
            $route.path === item.path 
              ? 'bg-red-600 text-white' 
              : 'text-zinc-400 hover:bg-zinc-800 hover:text-white'
          ]"
        >
          <component :is="item.icon" class="w-5 h-5" />
          <span class="font-medium">{{ item.name }}</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-zinc-800">
        <router-link to="/" class="flex items-center gap-3 px-4 py-3 text-zinc-400 hover:text-white cursor-pointer transition-colors">
          <ArrowLeft class="w-5 h-5" />
          <span class="font-medium">Kembali ke Toko</span>
        </router-link>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
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
