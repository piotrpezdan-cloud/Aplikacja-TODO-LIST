import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from './stores/authStore';

import Login from './components/Login.vue';
import Register from './components/Register.vue';
import TaskList from './components/TaskList.vue';
import TaskForm from './components/TaskForm.vue';
import Profile from './components/Profile.vue';

const routes = [
  {
    path: '/user/login',
    component: Login,
  },
  {
    path: '/user/register',
    component: Register,
  },
  {
    path: '/user/tasks',
    component: TaskList,
    meta: { requiresAuth: true },
  },
  {
    path: '/user/tasks/new',
    component: TaskForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/user/tasks/:id/edit',
    component: TaskForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/user/profile',
    component: Profile,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/user/login',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated()) {
    next('/user/login');
  } else if ((to.path === '/user/login' || to.path === '/user/register') && auth.isAuthenticated()) {
    next('/user/tasks');
  } else {
    next();
  }
});

export default router;
