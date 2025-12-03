<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useQuizStore } from '@/stores/quiz';
import { useRouter } from 'vue-router';

const ranking = ref([]);
const loading = ref(false);
const error = ref(null);
const quiz = useQuizStore();
const router = useRouter();

async function fetchRanking() {
  loading.value = true;
  error.value = null;
  try {
    const res = await api.get('/ranking');
    ranking.value = res.data;
  } catch (err) {
    error.value = 'Falha ao carregar ranking';
  } finally {
    loading.value = false;
  }
}

async function start() {
  const res = await quiz.startQuiz();
  if (res.ok) {
    router.push('/quiz');
  } else {
    alert(res.error || quiz.error || 'Não foi possível iniciar o quiz');
  }
}

onMounted(() => {
  fetchRanking();
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-semibold text-primary">Dashboard</h2>
      <button @click="start" class="bg-secondary text-white px-4 py-2 rounded">Iniciar Quiz</button>
    </div>

    <div class="bg-white p-4 rounded shadow">
      <h3 class="text-lg font-medium mb-3">Ranking</h3>

      <div v-if="loading" class="text-sm text-gray-500">Carregando...</div>
      <div v-if="error" class="text-sm text-red-600">{{ error }}</div>

      <ul v-if="!loading && ranking.length" class="space-y-2">
        <li v-for="(item, idx) in ranking" :key="idx" class="flex items-center justify-between border-b py-2">
          <div>
            <div class="font-medium">{{ item.user?.name || '—' }}</div>
            <div class="text-sm text-gray-500">Tempo: {{ item.time_seconds }}s</div>
          </div>
          <div class="text-lg font-semibold text-primary">{{ item.score }}</div>
        </li>
      </ul>

      <div v-if="!loading && !ranking.length" class="text-sm text-gray-500">Nenhum resultado ainda.</div>
    </div>
  </div>
</template>
