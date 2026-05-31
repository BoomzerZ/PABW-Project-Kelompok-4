<template>
  <aside class="fixed inset-y-0 left-0 w-64 bg-zinc-950/90 backdrop-blur-xl border-r border-red-900/30 flex flex-col shadow-[10px_0_30px_rgba(220,38,38,0.05)] z-40">
    <div class="h-20 flex items-center justify-center border-b border-red-900/30 p-3 bg-red-950/10">
      <img :src="'/img/logo.png'" alt="AXELOT Logo" class="w-full h-full object-contain drop-shadow-xl" />
    </div>

    <nav class="flex-1 px-4 space-y-2 mt-4">
      <router-link 
        v-for="item in filteredMenuItems" 
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

    <div class="p-4 border-t border-zinc-800">
      <div v-if="authState.isAuthenticated" @click="handleLogout" class="flex items-center gap-3 px-4 py-3 text-zinc-400 hover:text-white cursor-pointer transition-colors">
        <LogOut class="w-5 h-5" />
        <span class="font-medium">Keluar</span>
      </div>
      <router-link v-else to="/login" class="flex items-center gap-3 px-4 py-3 text-zinc-400 hover:text-white cursor-pointer transition-colors">
        <LogIn class="w-5 h-5" />
        <span class="font-medium">Masuk</span>
      </router-link>
    </div>
  </aside>
</template>

<script setup>
import { 
  Home as HomeIcon,
  MessageSquare, 
  ShoppingCart, 
  Settings, 
  User, 
  ClipboardList, 
  LogOut,
  LogIn,
  Gamepad2,
  ShieldCheck,
  Heart,
  Bell
} from 'lucide-vue-next';
import { authState, logout, isAdmin } from '../utils/auth';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const router = useRouter();

const menuItems = [
  { name: 'Beranda', path: '/', icon: HomeIcon },
  { name: 'Chat AI', path: '/chat', icon: MessageSquare },
  { name: 'Wishlist', path: '/wishlist', icon: Heart },
  { name: 'Notifikasi', path: '/notifications', icon: Bell },
  { name: 'Keranjang', path: '/cart', icon: ShoppingCart },
  { name: 'Riwayat Pesanan', path: '/orders', icon: ClipboardList },
  { name: 'Admin Panel', path: '/admin', icon: ShieldCheck, adminOnly: true },
  { name: 'Pengaturan', path: '/settings', icon: Settings },
];

const filteredMenuItems = computed(() => {
  return menuItems.filter(item => !item.adminOnly || isAdmin.value);
});

const handleLogout = async () => {
  await logout();
  router.push('/login');
};
</script>
