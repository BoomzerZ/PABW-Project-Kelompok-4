<template>
  <div class="flex flex-col h-[calc(100vh-4rem)]">
    <div ref="scrollContainer" class="flex-1 overflow-y-auto space-y-6 pb-4 pr-4 custom-scrollbar">
      <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']">
        <div :class="[
          'max-w-[85%] rounded-2xl p-4 shadow-lg transition-all duration-300', 
          msg.role === 'user' 
            ? 'bg-red-600 text-white rounded-br-none' 
            : (msg.isError 
                ? 'bg-red-950/20 border border-red-900/50 text-red-200 rounded-bl-none' 
                : 'bg-zinc-900 text-zinc-100 border border-zinc-800 rounded-bl-none')
        ]">
          <div v-if="msg.isError" class="flex items-center gap-2 mb-2 text-red-500">
            <AlertCircle class="w-4 h-4" />
            <span class="text-[10px] font-black uppercase tracking-widest">Connection Error</span>
          </div>
          
          <div class="chat-content max-w-none">
            <div v-if="msg.role === 'ai'" v-html="renderMarkdown(msg.content)" class="markdown-body"></div>
            <p v-else class="whitespace-pre-wrap leading-relaxed">{{ msg.content }}</p>
          </div>
          
          <!-- Product Widget if any -->
          <div v-if="msg.products && msg.products.length > 0" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <ProductWidget v-for="p in msg.products" :key="p.id" :product="p" />
          </div>

          <button 
            v-if="msg.isError" 
            @click="resendLastMessage" 
            class="mt-3 flex items-center gap-1.5 text-xs font-bold text-red-500 hover:text-red-400 transition-colors uppercase tracking-wider"
          >
            <RefreshCw class="w-3 h-3" /> Coba Kirim Ulang
          </button>
        </div>
      </div>
      
      <!-- Typing Indicator -->
      <div v-if="loading" class="flex justify-start">
        <div class="bg-zinc-900 text-zinc-100 border border-zinc-800 rounded-2xl rounded-bl-none p-4 shadow-lg flex items-center gap-4">
          <div class="flex gap-1.5">
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce"></span>
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
          </div>
          <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">AI sedang mengetik...</span>
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
import { Send, Loader2, AlertCircle, RefreshCw } from 'lucide-vue-next';
import axios from 'axios';
import { marked } from 'marked';
import ProductWidget from '../components/ProductWidget.vue';
import { authState } from '../utils/auth';

const input = ref('');
const lastUserMessage = ref('');
const loading = ref(false);
const scrollContainer = ref(null);
const allProducts = ref([]);
const messages = ref([
  { 
    role: 'ai', 
    content: 'Halo! Ada yang bisa saya bantu untuk melengkapi setup gaming kamu hari ini? Saya bisa memberikan rekomendasi keyboard, mouse, atau headset sesuai budgetmu.',
  }
]);

const renderMarkdown = (content) => {
  return marked.parse(content);
};

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
      [...response.data].reverse().forEach(m => {
        history.push({ role: 'user', content: m.message });
        
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
      messages.value = [messages.value[0], ...history];
      scrollToBottom();
    }
  } catch (error) {
    console.error('Failed to load chat history', error);
  }
};

const sendMessage = async () => {
  if (!input.value.trim() || loading.value) return;
  
  const userText = input.value;
  lastUserMessage.value = userText;
  messages.value.push({
    role: 'user',
    content: userText
  });
  
  input.value = '';
  processChat(userText);
};

const resendLastMessage = () => {
  // Remove the last error message
  if (messages.value[messages.value.length - 1].isError) {
    messages.value.pop();
  }
  processChat(lastUserMessage.value);
};

const processChat = async (userText) => {
  loading.value = true;
  scrollToBottom();

  try {
    const response = await axios.post('/api/chat', { message: userText });
    const aiText = response.data.response;
    
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
      products: recommendedProducts.slice(0, 4)
    });
  } catch (error) {
    messages.value.push({
      role: 'ai',
      isError: true,
      content: 'Maaf, sepertinya asisten AI kami sedang offline atau mengalami kendala koneksi. Mohon pastikan Ollama berjalan dan coba lagi.'
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
.markdown-body :deep(p) {
  margin-bottom: 1rem;
}
.markdown-body :deep(p:last-child) {
  margin-bottom: 0;
}
.markdown-body :deep(ul) {
  list-style-type: disc;
  margin-left: 1.25rem;
  margin-bottom: 1rem;
}
.markdown-body :deep(ol) {
  list-style-type: decimal;
  margin-left: 1.25rem;
  margin-bottom: 1rem;
}
.markdown-body :deep(li) {
  margin-bottom: 0.25rem;
}
.markdown-body :deep(strong) {
  font-weight: 700;
  color: #fff;
}
.markdown-body :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}
.markdown-body :deep(th), .markdown-body :deep(td) {
  border: 1px solid #3f3f46;
  padding: 0.5rem;
  text-align: left;
}
.markdown-body :deep(th) {
  background-color: #27272a;
}
</style>
