import { defineStore } from 'pinia';
import api, { setAuthHeader } from '@/services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    loading: false,
    error: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async initFromStorage() {
      const token = localStorage.getItem('token');
      if (token) {
        this.token = token;
        setAuthHeader(token);
        try {
          await this.fetchMe();
        } catch (e) {
          this.logoutSilent();
        }
      }
    },

    async register(payload) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.post('/register', payload);
        const { token, user } = res.data;
        this.token = token;
        this.user = user;
        localStorage.setItem('token', token);
        setAuthHeader(token);
        this.loading = false;
        return { ok: true };
      } catch (err) {
        this.loading = false;
        this.error = err.response?.data?.message || 'Erro no registro';
        return { ok: false, error: this.error };
      }
    },

    async login(payload) {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.post('/login', payload);
        const { token, user } = res.data;
        this.token = token;
        this.user = user;
        localStorage.setItem('token', token);
        setAuthHeader(token);
        this.loading = false;
        return { ok: true };
      } catch (err) {
        this.loading = false;
        this.error = err.response?.data?.message || 'Erro no login';
        return { ok: false, error: this.error };
      }
    },

    async fetchMe() {
      this.loading = true;
      this.error = null;
      try {
        const res = await api.get('/me');
        this.user = res.data;
        this.loading = false;
      } catch (err) {
        this.loading = false;
        this.error = 'Falha ao buscar usuário';
        throw err;
      }
    },

    async logout() {
      try {
        await api.post('/logout');
      } catch (e) {
        // ignore
      } finally {
        this.logoutSilent();
      }
    },

    logoutSilent() {
      this.user = null;
      this.token = null;
      this.loading = false;
      this.error = null;
      localStorage.removeItem('token');
      setAuthHeader(null);
    },
  },
});
