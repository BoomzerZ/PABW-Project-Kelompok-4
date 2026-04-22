<template>
  <aside class="fixed inset-y-0 left-0 w-64 bg-zinc-900 border-r border-zinc-800 flex flex-col">
    <div class="p-6">
      <h1 class="text-2xl font-bold text-red-600 flex items-center gap-2">
        <Gamepad2 class="w-8 h-8" />
        GAMING GEAR
      </h1>
    </div>

    <nav class="flex-1 px-4 space-y-2 mt-4">
      <router-link 
        v-for="item in filteredMenuItems" 
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
  ShieldCheck
} from 'lucide-vue-next';
import { authState, logout, isAdmin } from '../utils/auth';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const router = useRouter();

const menuItems = [
  { name: 'Beranda', path: '/', icon: HomeIcon },
  { name: 'Chat AI', path: '/chat', icon: MessageSquare },
  { name: 'Keranjang', path: '/cart', icon: ShoppingCart },
  { name: 'Riwayat Pesanan', path: '/orders', icon: ClipboardList },
  { name: 'Admin Panel', path: '/admin', icon: ShieldCheck, adminOnly: true },
  { name: 'Profil', path: '/profile', icon: User },
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
