<template>
  <div class="flex min-h-screen bg-black text-white">
    <!-- Sidebar -->
    <Sidebar v-if="!$route.meta.hideSidebar" class="hidden md:flex" />

    <!-- Main Content -->
    <main :class="['flex-1 p-4 md:p-8 transition-all', !$route.meta.hideSidebar ? 'md:ml-64 pb-24 md:pb-8' : '']">
      <router-view />
    </main>

    <!-- Bottom Navbar (Mobile) -->
    <BottomNavbar v-if="!$route.meta.hideSidebar" class="md:hidden" />
  </div>
</template>

<script setup>
import Sidebar from './components/Sidebar.vue';
import BottomNavbar from './components/BottomNavbar.vue';
</script>

<style>
/* Any global styles if needed */
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
}
</style>
