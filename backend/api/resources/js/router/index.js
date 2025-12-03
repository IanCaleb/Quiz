import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const Login = () => import('@/views/Auth/Login.vue');
const Register = () => import('@/views/Auth/Register.vue');
const Dashboard = () => import('@/views/Dashboard.vue');
const Quiz = () => import('@/views/Quiz.vue');
const Results = () => import('@/views/Results.vue');

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login', component: Login, meta: { guest: true } },
  { path: '/register', component: Register, meta: { guest: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/quiz', component: Quiz, meta: { requiresAuth: true } },
  { path: '/results', component: Results, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();
  if (auth.token === null) {
    await auth.initFromStorage();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ path: '/login' });
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return next({ path: '/dashboard' });
  }

  return next();
});

export default router;
