<template>
  <nav class="fixed bottom-0 left-0 right-0 bg-zinc-900 border-t border-zinc-800 px-2 py-3 flex justify-around items-center z-50 shadow-2xl">
    <router-link 
      v-for="item in menuItems" 
      :key="item.path" 
      :to="item.path"
      class="flex flex-col items-center gap-1 min-w-[64px] transition-colors"
      :class="[
        $route.path === item.path 
          ? 'text-red-600' 
          : 'text-zinc-500 hover:text-zinc-300'
      ]"
    >
      <component :is="item.icon" class="w-6 h-6" />
      <span class="text-[10px] font-bold uppercase tracking-tighter">{{ item.name }}</span>
    </router-link>
  </nav>
</template>

<script setup>
import { 
  Home as HomeIcon, 
  MessageSquare, 
  Heart, 
  User 
} from 'lucide-vue-next';

const menuItems = [
  { name: 'Beranda', path: '/', icon: HomeIcon },
  { name: 'Chat AI', path: '/chat', icon: MessageSquare },
  { name: 'Wishlist', path: '/wishlist', icon: Heart },
  { name: 'Akun', path: '/profile', icon: User },
];
</script>

<style scoped>
.router-link-active {
  @apply text-red-600;
}
</style>
