<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

onMounted(async () => {
  await auth.initFromStorage();
});

function handleLogout() {
  auth.logout().then(() => router.push('/login'));
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-primary">Quiz App</h1>
        <nav class="flex items-center space-x-4">
          <div v-if="!auth.isAuthenticated">
            <router-link to="/login" class="text-sm text-secondary hover:underline">Entrar</router-link>
            <router-link to="/register" class="ml-3 text-sm text-gray-600 hover:underline">Registrar</router-link>
          </div>
          <div v-else class="flex items-center space-x-3">
            <span class="text-sm text-gray-700">Olá, <strong>{{ auth.user?.name }}</strong></span>
            <button @click="handleLogout" class="text-sm text-red-500 hover:underline">Sair</button>
          </div>
        </nav>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
/* layout styles if needed */
</style>
