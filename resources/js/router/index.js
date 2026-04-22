import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Chat from '../views/Chat.vue';
import Cart from '../views/Cart.vue';
import Orders from '../views/Orders.vue';
import OrderDetails from '../views/OrderDetails.vue';
import Profile from '../views/Profile.vue';
import Settings from '../views/Settings.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';

// Admin Views
import AdminLayout from '../views/admin/AdminLayout.vue';
import AdminDashboard from '../views/admin/AdminDashboard.vue';
import AdminProducts from '../views/admin/AdminProducts.vue';
import AdminOrders from '../views/admin/AdminOrders.vue';

import { authState } from '../utils/auth';

import { isAdmin } from '../utils/auth';

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/chat', name: 'Chat', component: Chat },
  { path: '/cart', name: 'Cart', component: Cart, meta: { requiresAuth: true } },
  { path: '/orders', name: 'Orders', component: Orders, meta: { requiresAuth: true } },
  { path: '/orders/:id', name: 'OrderDetails', component: OrderDetails, meta: { requiresAuth: true } },
  { path: '/profile', name: 'Profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/settings', name: 'Settings', component: Settings },
  { path: '/login', name: 'Login', component: Login, meta: { guestOnly: true, hideSidebar: true } },
  { path: '/register', name: 'Register', component: Register, meta: { guestOnly: true, hideSidebar: true } },
  
  // Admin Routes
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'products', name: 'AdminProducts', component: AdminProducts },
      { path: 'orders', name: 'AdminOrders', component: AdminOrders },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !authState.isAuthenticated) {
    next('/login');
  } else if (to.meta.requiresAdmin && !isAdmin.value) {
    next('/');
  } else if (to.meta.guestOnly && authState.isAuthenticated) {
    next('/');
  } else {
    next();
  }
});

export default router;
