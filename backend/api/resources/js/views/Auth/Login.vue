<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const error = ref(null);

async function submit() {
  error.value = null;
  const res = await auth.login({ email: email.value, password: password.value });
  if (res.ok) {
    router.push('/dashboard');
  } else {
    error.value = res.error || auth.error;
  }
}
</script>

<template>
  <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold text-primary mb-4">Entrar</h2>

    <div v-if="error" class="mb-3 text-sm text-red-600">{{ error }}</div>

    <label class="block mb-2 text-sm">Email</label>
    <input v-model="email" type="email" class="w-full border rounded px-3 py-2 mb-3" />

    <label class="block mb-2 text-sm">Senha</label>
    <input v-model="password" type="password" class="w-full border rounded px-3 py-2 mb-4" />

    <div class="flex items-center justify-between">
      <button @click="submit" :disabled="auth.loading" class="bg-primary text-white px-4 py-2 rounded hover:opacity-90">
        <span v-if="!auth.loading">Entrar</span>
        <span v-else>Entrando...</span>
      </button>

      <router-link to="/register" class="text-sm text-secondary">Criar conta</router-link>
    </div>
  </div>
</template>
