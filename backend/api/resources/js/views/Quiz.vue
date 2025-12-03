<script setup>
import { ref, computed, onMounted } from 'vue';
import { useQuizStore } from '@/stores/quiz';
import { useRouter } from 'vue-router';

const quiz = useQuizStore();
const router = useRouter();

const selectedOption = ref(null);
const feedback = ref(null);
const selectedState = ref(null); // 'correct' | 'wrong' | null
const disabledOptions = ref(false);
const lastAnswered = ref(false);

const question = computed(() => quiz.currentQuestion);
const index = computed(() => quiz.currentIndex);
const total = computed(() => quiz.questions.length);

onMounted(() => {
  // try to restore in-progress quiz from localStorage; if none, go back
  quiz.loadState();
  if (!quiz.quizAttemptId) {
    router.push('/dashboard');
  }
});

function selectOption(optId) {
  if (disabledOptions.value) return;
  selectedOption.value = optId;
  feedback.value = null;
  selectedState.value = null;
}

async function confirmAnswer() {
  if (!selectedOption.value) return;
  disabledOptions.value = true;
  // Capture isLastQuestion at THIS moment, not 700ms later
  const isLastQuestionNow = quiz.isLastQuestion;
  const result = await quiz.submitAnswer(selectedOption.value);
  if (!result.ok) {
    alert(result.error || 'Erro ao enviar resposta');
    disabledOptions.value = false;
    return;
  }
  // keep selectedOption so we can color it
  selectedState.value = result.is_correct ? 'correct' : 'wrong';
  // short delay to show color transition then advance or allow finalization
  setTimeout(async () => {
    if (isLastQuestionNow) {
      // mark that the last question was answered - present Finalizar button
      lastAnswered.value = true;
      // keep disabledOptions true to avoid changing selection
    } else {
      // next question already advanced in store; reset selection state
      selectedOption.value = null;
      selectedState.value = null;
      disabledOptions.value = false;
    }
  }, 700);
}

async function finishQuiz() {
  if (!quiz.quizAttemptId) return;
  disabledOptions.value = true;
  const comp = await quiz.completeQuiz();
  if (comp.ok) {
    router.push('/results');
  } else {
    alert(comp.error || 'Erro ao finalizar quiz');
    disabledOptions.value = false;
  }
}
</script>

<template>
  <div class="bg-white p-6 rounded shadow">
    <div class="flex items-center justify-between mb-4">
      <div>
        <div class="text-sm text-gray-500">Pergunta {{ index + 1 }} / {{ total }}</div>
        <div class="font-semibold text-lg">{{ question?.question_text }}</div>
      </div>
      <div class="text-right">
        <div class="timer-badge">{{ quiz.formatTimer() }}</div>
        <div class="text-xs text-gray-500 mt-1">Tempo gasto</div>
      </div>
    </div>

    <div class="grid gap-3">
      <button
        v-for="opt in question?.options || []"
        :key="opt.id"
        @click="selectOption(opt.id)"
        :class="[
          'text-left p-3 rounded border transition-colors duration-300 ease-in-out',
          // when option is selected, color based on selectedState
          (selectedOption === opt.id && selectedState === 'correct') ? 'bg-green-500 text-white border-green-600' : '',
          (selectedOption === opt.id && selectedState === 'wrong') ? 'bg-red-500 text-white border-red-600' : '',
          // default selected (before confirming)
          (selectedOption === opt.id && !selectedState) ? 'bg-primary text-white border-primary' : '',
          // not selected
          (selectedOption !== opt.id) ? 'bg-white text-gray-800' : ''
        ]"
        :disabled="disabledOptions"
      >
        {{ opt.option_text }}
      </button>
    </div>

    <div class="flex items-center justify-between mt-6">
      <div>
        <div v-if="selectedState === 'correct'" class="text-green-600">Correto!</div>
        <div v-else-if="selectedState === 'wrong'" class="text-red-600">Errado!</div>
      </div>
      <div class="ml-auto">
        <button v-if="!lastAnswered" @click="confirmAnswer" :disabled="!selectedOption" class="bg-secondary text-white px-4 py-2 rounded">
          Confirmar
        </button>
        <button v-else @click="finishQuiz" class="bg-primary text-white px-4 py-2 rounded">Finalizar Quiz</button>
      </div>
    </div>
  </div>
</template>
