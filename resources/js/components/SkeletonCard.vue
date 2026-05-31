<template>
  <div class="animate-pulse">
    <!-- Product Card Skeleton -->
    <div v-if="type === 'product-card'" class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
      <div class="aspect-square bg-zinc-800"></div>
      <div class="p-5 space-y-3">
        <div class="h-3 bg-zinc-800 rounded w-1/3"></div>
        <div class="h-5 bg-zinc-800 rounded w-3/4"></div>
        <div class="h-3 bg-zinc-800 rounded w-full"></div>
        <div class="flex items-center justify-between pt-2">
          <div class="h-6 bg-zinc-800 rounded w-1/3"></div>
          <div class="h-4 bg-zinc-800 rounded w-1/4"></div>
        </div>
      </div>
    </div>

    <!-- Product Detail Skeleton -->
    <div v-else-if="type === 'product-detail'" class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <div class="space-y-4">
        <div class="aspect-square bg-zinc-900 border border-zinc-800 rounded-3xl"></div>
        <div class="grid grid-cols-4 gap-4">
          <div v-for="i in 4" :key="i" class="aspect-square bg-zinc-900 border border-zinc-800 rounded-xl"></div>
        </div>
      </div>
      <div class="space-y-6 pt-4">
        <div class="h-3 bg-zinc-800 rounded w-1/4"></div>
        <div class="h-8 bg-zinc-800 rounded w-3/4"></div>
        <div class="h-4 bg-zinc-800 rounded w-1/3"></div>
        <div class="h-10 bg-zinc-800 rounded w-1/3"></div>
        <div class="space-y-2">
          <div class="h-3 bg-zinc-800 rounded w-full"></div>
          <div class="h-3 bg-zinc-800 rounded w-5/6"></div>
          <div class="h-3 bg-zinc-800 rounded w-2/3"></div>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-4">
          <div class="h-4 bg-zinc-800 rounded w-1/3"></div>
          <div class="grid grid-cols-2 gap-y-3">
            <div v-for="i in 6" :key="i" class="h-3 bg-zinc-800 rounded w-3/4"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Horizontal Product Skeleton -->
    <div v-else-if="type === 'product-horizontal'" class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex gap-6">
      <div class="w-24 h-24 flex-shrink-0 rounded-xl bg-zinc-800"></div>
      <div class="flex-1 space-y-3 py-1">
        <div class="h-4 bg-zinc-800 rounded w-2/3"></div>
        <div class="h-3 bg-zinc-800 rounded w-full"></div>
        <div class="h-5 bg-zinc-800 rounded w-1/3"></div>
      </div>
    </div>

    <!-- Generic Line Skeleton -->
    <div v-else class="space-y-3">
      <div class="h-4 bg-zinc-800 rounded w-full"></div>
      <div class="h-4 bg-zinc-800 rounded w-5/6"></div>
      <div class="h-4 bg-zinc-800 rounded w-2/3"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'product-card',
    validator: (v) => ['product-card', 'product-detail', 'product-horizontal', 'generic'].includes(v)
  }
});
</script>
