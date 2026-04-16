<template>
  <div class="min-h-screen flex items-center justify-center bg-black px-4">
    <div class="max-w-md w-full space-y-8 bg-zinc-900 p-10 rounded-2xl border border-zinc-800">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
          Masuk ke Akun Anda
        </h2>
        <p class="mt-2 text-center text-sm text-zinc-400">
          Atau
          <router-link to="/register" class="font-medium text-red-600 hover:text-red-500">
            daftar akun baru
          </router-link>
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="email-address" class="sr-only">Alamat Email</label>
            <input 
              v-model="form.email" 
              id="email-address" 
              name="email" 
              type="email" 
              required 
              class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-zinc-700 bg-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" 
              placeholder="Alamat Email"
            >
          </div>
          <div>
            <label for="password" class="sr-only">Kata Sandi</label>
            <input 
              v-model="form.password" 
              id="password" 
              name="password" 
              type="password" 
              required 
              class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-zinc-700 bg-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm" 
              placeholder="Kata Sandi"
            >
          </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <div>
          <button 
            type="submit" 
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 transition-colors"
          >
            {{ loading ? 'Memproses...' : 'Masuk' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '../utils/auth';

const router = useRouter();
const loading = ref(false);
const error = ref('');

const form = reactive({
  email: '',
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  const result = await login(form);
  if (result.success) {
    router.push('/');
  } else {
    error.value = result.message;
  }
  loading.value = false;
};
</script>
