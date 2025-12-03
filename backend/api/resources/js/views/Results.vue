<script setup>
import { computed, onMounted } from 'vue';
import { useQuizStore } from '@/stores/quiz';
import { useRouter } from 'vue-router';

const quiz = useQuizStore();
const router = useRouter();

onMounted(() => {
  if (!quiz.results) {
    router.push('/dashboard');
  }
});

const correct = computed(() => quiz.results?.correct_answers ?? 0);
const wrong = computed(() => quiz.results?.wrong_answers ?? 0);
const score = computed(() => quiz.results?.score ?? 0);
const time = computed(() => quiz.results?.time_spent ?? quiz.timerSeconds);

function backToDashboard() {
  quiz.resetQuizState();
  router.push('/dashboard');
}
</script>

<template>
  <div class="bg-white p-6 rounded shadow max-w-xl mx-auto">
    <h2 class="text-2xl font-semibold text-primary mb-4">Resultados</h2>

    <div class="grid grid-cols-2 gap-4">
      <div class="bg-gray-50 p-4 rounded">
        <div class="text-sm text-gray-500">Acertos</div>
        <div class="text-xl font-bold text-green-600">{{ correct }}</div>
      </div>
      <div class="bg-gray-50 p-4 rounded">
        <div class="text-sm text-gray-500">Erros</div>
        <div class="text-xl font-bold text-red-600">{{ wrong }}</div>
      </div>
      <div class="bg-gray-50 p-4 rounded">
        <div class="text-sm text-gray-500">Score</div>
        <div class="text-xl font-bold text-primary">{{ score }}</div>
      </div>
      <div class="bg-gray-50 p-4 rounded">
        <div class="text-sm text-gray-500">Tempo (s)</div>
        <div class="text-xl font-bold text-gray-700">{{ time }}</div>
      </div>
    </div>

    <div class="mt-6 flex justify-end">
      <button @click="backToDashboard" class="bg-secondary text-white px-4 py-2 rounded">Voltar ao Dashboard</button>
    </div>
  </div>
</template>
