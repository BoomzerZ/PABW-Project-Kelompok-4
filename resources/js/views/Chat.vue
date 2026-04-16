<template>
  <div class="flex flex-col h-[calc(100vh-4rem)]">
    <div ref="scrollContainer" class="flex-1 overflow-y-auto space-y-6 pb-4 pr-4 custom-scrollbar">
      <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']">
        <div :class="['max-w-[80%] rounded-2xl p-4', msg.role === 'user' ? 'bg-red-600 text-white' : 'bg-zinc-900 text-zinc-100 border border-zinc-800']">
          <p class="whitespace-pre-wrap">{{ msg.content }}</p>
          
          <!-- Product Widget if any -->
          <div v-if="msg.products && msg.products.length > 0" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <ProductWidget v-for="p in msg.products" :key="p.id" :product="p" />
          </div>
        </div>
      </div>
      <div v-if="loading" class="flex justify-start">
        <div class="bg-zinc-900 text-zinc-100 border border-zinc-800 rounded-2xl p-4 flex items-center gap-2">
          <Loader2 class="w-4 h-4 animate-spin text-red-500" />
          <span>AI sedang berpikir...</span>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="mt-4 pt-4 border-t border-zinc-800">
      <div class="relative max-w-4xl mx-auto">
        <input 
          v-model="input" 
          @keyup.enter="sendMessage"
          :disabled="loading"
          type="text" 
          placeholder="Tanyakan rekomendasi gaming gear..."
          class="w-full bg-zinc-900 border border-zinc-700 rounded-xl py-4 pl-6 pr-14 text-white focus:outline-none focus:border-red-600 transition-colors disabled:opacity-50"
        />
        <button 
          @click="sendMessage"
          :disabled="loading"
          class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50"
        >
          <Send class="w-5 h-5 text-white" />
        </button>
      </div>
      <p class="text-center text-zinc-500 text-xs mt-3">AI dapat memberikan rekomendasi berdasarkan stock yang ada.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Send, Loader2 } from 'lucide-vue-next';
import axios from 'axios';
import ProductWidget from '../components/ProductWidget.vue';
import { authState } from '../utils/auth';

const input = ref('');
const loading = ref(false);
const scrollContainer = ref(null);
const allProducts = ref([]);
const messages = ref([
  { 
    role: 'ai', 
    content: 'Halo! Ada yang bisa saya bantu untuk melengkapi setup gaming kamu hari ini? Saya bisa memberikan rekomendasi keyboard, mouse, atau headset sesuai budgetmu.',
  }
]);

const scrollToBottom = async () => {
  await nextTick();
  if (scrollContainer.value) {
    scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
  }
};

const fetchProducts = async () => {
  try {
    const response = await axios.get('/api/products/context');
    allProducts.value = response.data;
  } catch (error) {
    console.error('Failed to fetch products for context', error);
  }
};

const loadHistory = async () => {
  if (!authState.isAuthenticated) return;
  try {
    const response = await axios.get('/api/chat/history');
    if (response.data.length > 0) {
      const history = [];
      // Data is descending (newest first), we want ascending for chat bubbles
      [...response.data].reverse().forEach(m => {
        history.push({ role: 'user', content: m.message });
        
        // Match products for history too
        const recommendedProducts = [];
        if (allProducts.value.length > 0) {
          allProducts.value.forEach(product => {
            if (m.response.toLowerCase().includes(product.name.toLowerCase())) {
              recommendedProducts.push(product);
            }
          });
        }
        
        history.push({ 
          role: 'ai', 
          content: m.response,
          products: recommendedProducts.slice(0, 4)
        });
      });
      messages.value = [messages.value[0], ...history]; // Keep greeting
      scrollToBottom();
    }
  } catch (error) {
    console.error('Failed to load chat history', error);
  }
};

const sendMessage = async () => {
  if (!input.value.trim() || loading.value) return;
  
  const userText = input.value;
  messages.value.push({
    role: 'user',
    content: userText
  });
  
  input.value = '';
  loading.value = true;
  scrollToBottom();

  try {
    const response = await axios.post('/api/chat', { message: userText });
    const aiText = response.data.response;
    
    // Simple heuristic: search for product names in the AI response
    const recommendedProducts = [];
    if (allProducts.value.length > 0) {
      allProducts.value.forEach(product => {
        if (aiText.toLowerCase().includes(product.name.toLowerCase())) {
          recommendedProducts.push(product);
        }
      });
    }

    messages.value.push({
      role: 'ai',
      content: aiText,
      products: recommendedProducts.slice(0, 4) // Limit to 4 widgets
    });
  } catch (error) {
    messages.value.push({
      role: 'ai',
      content: 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi nanti.'
    });
  } finally {
    loading.value = false;
    scrollToBottom();
  }
};

onMounted(async () => {
  await fetchProducts();
  loadHistory();
});
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
