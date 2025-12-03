import { defineStore } from 'pinia';
import api from '@/services/api';

export const useQuizStore = defineStore('quiz', {
  state: () => ({
    quizAttemptId: null,
    questions: [],
    currentIndex: 0,
    answers: [],
    timerSeconds: 0,
    timerRunning: false,
    intervalId: null,
    loading: false,
    error: null,
    results: null,
  }),
  getters: {
    currentQuestion(state) {
      return state.questions[state.currentIndex] || null;
    },
    isLastQuestion(state) {
      return state.currentIndex >= state.questions.length - 1;
    },
  },
  actions: {
    startTimer() {
      if (this.intervalId) return;
      this.timerRunning = true;
      this.intervalId = setInterval(() => {
        this.timerSeconds += 1;
      }, 1000);
    },
    stopTimer() {
      this.timerRunning = false;
      if (this.intervalId) {
        clearInterval(this.intervalId);
        this.intervalId = null;
      }
    },

    formatTimer() {
      const s = this.timerSeconds;
      const mins = Math.floor(s / 60).toString().padStart(2, '0');
      const secs = (s % 60).toString().padStart(2, '0');
      return `${mins}:${secs}`;
    },

    async startQuiz() {
      this.loading = true;
      this.error = null;
      this.questions = [];
      this.answers = [];
      this.currentIndex = 0;
      this.quizAttemptId = null;
      this.timerSeconds = 0;
      try {
        const res = await api.post('/quiz/start');
        const { quiz_attempt_id, questions } = res.data;
        this.quizAttemptId = quiz_attempt_id;
        // store questions as returned by API
        this.questions = questions || [];
        this.loading = false;
        // start timer when quiz begins
        this.startTimer();
        return { ok: true };
      } catch (err) {
        this.loading = false;
        this.error = err.response?.data?.message || 'Erro ao iniciar quiz';
        return { ok: false, error: this.error };
      }
    },

    async submitAnswer(optionId) {
      if (!this.quizAttemptId) {
        this.error = 'Quiz não iniciado';
        return { ok: false, error: this.error };
      }
      const q = this.currentQuestion;
      if (!q) {
        this.error = 'Pergunta inválida';
        return { ok: false, error: this.error };
      }
      this.loading = true;
      try {
        const payload = {
          quiz_attempt_id: this.quizAttemptId,
          question_id: q.id,
          option_id: optionId,
        };
        const res = await api.post('/quiz/answer', payload);
        const { is_correct } = res.data;
        this.answers.push({
          question_id: q.id,
          option_id: optionId,
          is_correct,
        });
        this.loading = false;
        if (!this.isLastQuestion) {
          this.currentIndex += 1;
        }
        return { ok: true, is_correct };
      } catch (err) {
        this.loading = false;
        this.error = err.response?.data?.message || 'Erro ao salvar resposta';
        return { ok: false, error: this.error };
      }
    },

    async completeQuiz() {
      if (!this.quizAttemptId) {
        this.error = 'Quiz não iniciado';
        return { ok: false, error: this.error };
      }
      this.loading = true;
      try {
        this.stopTimer();
        const res = await api.post('/quiz/complete', {
          quiz_attempt_id: this.quizAttemptId,
          time_spent: this.timerSeconds,
        });
        // API returns a `summary` with the computed results (correct_answers, wrong_answers, score, time_seconds)
        this.results = res.data.summary ?? res.data;
        this.loading = false;
        return { ok: true, results: this.results };
      } catch (err) {
        this.loading = false;
        this.error = err.response?.data?.message || 'Erro ao finalizar quiz';
        return { ok: false, error: this.error };
      }
    },

    resetQuizState() {
      this.quizAttemptId = null;
      this.questions = [];
      this.currentIndex = 0;
      this.answers = [];
      this.timerSeconds = 0;
      this.stopTimer();
      this.loading = false;
      this.error = null;
      this.results = null;
    },
  },
});
