<template>
  <div class="flex min-h-screen bg-black text-white">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Sidebar from './components/Sidebar.vue';
</script>

<style>
/* Any global styles if needed */
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
}
</style>
