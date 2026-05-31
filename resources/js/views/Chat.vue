<template>
  <div class="flex flex-col h-[calc(100vh-8rem)] md:h-[calc(100vh-4rem)] max-w-5xl mx-auto w-full">
    
    <!-- Chat Header -->
    <div class="flex items-center gap-4 py-4 px-4 border-b border-zinc-800/50 mb-6 bg-zinc-900/30 rounded-2xl">
      <div class="w-12 h-12 bg-zinc-950 rounded-xl flex items-center justify-center border border-red-900/50 shadow-[inset_0_0_10px_rgba(220,38,38,0.15)]">
        <img :src="'/img/logo.png'" alt="AXELOT" class="w-8 h-8 object-contain drop-shadow-md" />
      </div>
      <div>
        <h1 class="text-xl font-black text-white uppercase tracking-widest">AXELOT AI</h1>
        <p class="text-xs text-zinc-400 font-medium tracking-wide">Asisten Belanja Personal Anda</p>
      </div>
    </div>

    <div ref="scrollContainer" class="flex-1 overflow-y-auto space-y-6 pb-4 pr-2 md:pr-4 custom-scrollbar">
      <div v-for="(msg, index) in messages" :key="index" :class="['flex gap-4', msg.role === 'user' ? 'flex-row-reverse' : 'flex-row']">
        
        <!-- Avatars -->
        <div class="flex-shrink-0 mt-1">
          <div v-if="msg.role === 'ai'" class="w-10 h-10 bg-zinc-950 border border-red-900/50 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(220,38,38,0.15)]">
            <img :src="'/img/logo.png'" alt="AI" class="w-6 h-6 object-contain" />
          </div>
          <div v-else class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center font-black text-white shadow-lg">
            U
          </div>
        </div>

        <div :class="[
          'max-w-[80%] p-5 shadow-lg transition-all duration-300', 
          msg.role === 'user' 
            ? 'bg-gradient-to-br from-red-600 to-red-800 text-white rounded-2xl rounded-tr-sm shadow-red-900/20' 
            : (msg.isError 
                ? 'bg-red-950/20 border border-red-900/50 text-red-200 rounded-2xl rounded-tl-sm' 
                : 'bg-zinc-900/80 backdrop-blur-md text-zinc-100 border border-zinc-800/50 border-l-4 border-l-red-600 rounded-2xl rounded-tl-sm shadow-[0_5px_20px_rgba(0,0,0,0.3)]')
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
      <div v-if="loading" class="flex gap-4">
        <div class="flex-shrink-0 mt-1">
          <div class="w-10 h-10 bg-zinc-950 border border-red-900/50 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(220,38,38,0.15)]">
            <img :src="'/img/logo.png'" alt="AI" class="w-6 h-6 object-contain animate-pulse" />
          </div>
        </div>
        <div class="bg-zinc-900/80 backdrop-blur-md text-zinc-100 border border-zinc-800/50 border-l-4 border-l-red-600 rounded-2xl rounded-tl-sm p-4 shadow-lg flex items-center gap-4">
          <div class="flex gap-1.5">
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce"></span>
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
            <span class="w-2 h-2 bg-red-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
          </div>
          <div>
            <span class="font-bold text-white tracking-widest text-sm">AXELOT AI</span>
            <div class="text-[10px] text-zinc-400 uppercase tracking-widest">Sedang mengetik...</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="mt-4 pt-4 pb-2">
      <div class="relative w-full shadow-2xl">
        <input 
          v-model="input" 
          @keyup.enter="sendMessage"
          :disabled="loading"
          type="text" 
          placeholder="Ketik pesan untuk AXELOT AI..."
          class="w-full bg-zinc-900/90 backdrop-blur-md border border-zinc-700/50 rounded-2xl py-4 pl-6 pr-16 text-white focus:outline-none focus:border-red-600 focus:shadow-[0_0_20px_rgba(220,38,38,0.2)] transition-all disabled:opacity-50 font-medium"
        />
        <button 
          @click="sendMessage"
          :disabled="loading"
          class="absolute right-2 top-1/2 -translate-y-1/2 p-3 bg-red-600 hover:bg-red-500 rounded-xl transition-all disabled:opacity-50 hover:shadow-[0_0_15px_rgba(220,38,38,0.4)]"
        >
          <Send class="w-5 h-5 text-white" />
        </button>
      </div>
      <div class="text-center text-zinc-500 text-xs mt-4 flex items-center justify-center gap-2 font-medium">
        <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_5px_#22c55e]"></div> 
        AI memberikan rekomendasi berdasarkan ketersediaan stok
      </div>
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
    content: 'Halo! Ada yang bisa saya bantu untuk melengkapi setup gaming kamu hari ini? Saya bisa memberikan rekomendasi terbaik dari AXELOT.',
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
  font-weight: 900;
  color: #fca5a5; /* red-300 */
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
