<template>
  <div class="flex flex-col h-[calc(100vh-4rem)]">
    <div class="flex-1 overflow-y-auto space-y-6 pb-4 pr-4 custom-scrollbar">
      <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']">
        <div :class="['max-w-[80%] rounded-2xl p-4', msg.role === 'user' ? 'bg-red-600 text-white' : 'bg-zinc-900 text-zinc-100 border border-zinc-800']">
          <p class="whitespace-pre-wrap">{{ msg.content }}</p>
          
          <!-- Product Widget if any -->
          <div v-if="msg.products && msg.products.length > 0" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <ProductWidget v-for="p in msg.products" :key="p.id" :product="p" />
          </div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="mt-4 pt-4 border-t border-zinc-800">
      <div class="relative max-w-4xl mx-auto">
        <input 
          v-model="input" 
          @keyup.enter="sendMessage"
          type="text" 
          placeholder="Tanyakan rekomendasi gaming gear..."
          class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-4 pl-6 pr-14 text-white focus:outline-none focus:border-red-600 transition-colors"
        />
        <button 
          @click="sendMessage"
          class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
        >
          <Send class="w-5 h-5 text-white" />
        </button>
      </div>
      <p class="text-center text-zinc-500 text-xs mt-3">AI dapat memberikan rekomendasi berdasarkan stock yang ada.</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Send } from 'lucide-vue-next';
import ProductWidget from '../components/ProductWidget.vue';

const input = ref('');
const messages = ref([
  { 
    role: 'ai', 
    content: 'Halo! Ada yang bisa saya bantu untuk melengkapi setup gaming kamu hari ini? Saya bisa memberikan rekomendasi keyboard, mouse, atau headset sesuai budgetmu.',
  },
  {
    role: 'user',
    content: 'Rekomendasi keyboard mechanical budget dibawah 1 juta dong'
  },
  {
    role: 'ai',
    content: 'Tentu! Berikut adalah beberapa pilihan keyboard mechanical berkualitas dengan harga di bawah 1 juta rupiah:',
    products: [
      {
        id: 1,
        name: 'VortexSeries GT-8',
        description: 'Gasket Mount Mechanical Keyboard with South Facing RGB.',
        price: 850000,
        image: 'https://placehold.co/600x400/18181b/ffffff?text=VortexSeries+GT-8'
      },
      {
        id: 2,
        name: 'Digital Alliance Meca Warrior X',
        description: 'TKL Layout with RGB and Removable Switch.',
        price: 450000,
        image: 'https://placehold.co/600x400/18181b/ffffff?text=DA+Meca+Warrior'
      }
    ]
  }
]);

const sendMessage = () => {
  if (!input.value.trim()) return;
  
  messages.value.push({
    role: 'user',
    content: input.value
  });
  
  const userText = input.value;
  input.value = '';

  // Mock AI response
  setTimeout(() => {
    messages.value.push({
      role: 'ai',
      content: `Kamu bertanya tentang "${userText}". Saat ini saya sedang dalam mode pengembangan, namun nantinya saya akan mencari produk tersebut langsung dari database kami.`
    });
  }, 1000);
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #27272a;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #3f3f46;
}
</style>
