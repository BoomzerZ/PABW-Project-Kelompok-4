import { createRouter, createWebHistory } from 'vue-router';
import Chat from '../views/Chat.vue';
import Cart from '../views/Cart.vue';
import Orders from '../views/Orders.vue';
import Profile from '../views/Profile.vue';
import Settings from '../views/Settings.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import { authState } from '../utils/auth';

const routes = [
  { path: '/', name: 'Chat', component: Chat },
  { path: '/cart', name: 'Cart', component: Cart, meta: { requiresAuth: true } },
  { path: '/orders', name: 'Orders', component: Orders, meta: { requiresAuth: true } },
  { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/settings', name: 'Settings', component: Settings },
  { path: '/login', name: 'Login', component: Login, meta: { guestOnly: true, hideSidebar: true } },
  { path: '/register', name: 'Register', component: Register, meta: { guestOnly: true, hideSidebar: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !authState.isAuthenticated) {
    next('/login');
  } else if (to.meta.guestOnly && authState.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;
