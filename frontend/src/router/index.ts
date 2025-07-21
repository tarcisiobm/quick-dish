import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      component: () => import('@/views/Layout.vue'),
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/Home.vue')
        },
        {
          path: 'contact',
          name: 'contact',
          component: () => import('@/views/Contact.vue')
        },
        {
          path: 'account-settings',
          name: 'account-settings',
          component: () => import('@/views/AccountSettings.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: 'reviews',
          name: 'reviews',
          component: () => import('@/views/Reviews.vue')
        },
        {
          path: 'menu',
          name: 'menu-layout',
          component: () => import('@/views/Menu.vue')
        },
        {
          path: 'menu/category/:id',
          name: 'menu-category',
          component: () => import('@/views/Menu.vue'),
          props: true
        },
        {
          path: 'checkout',
          name: 'checkout',
          component: () => import('@/views/Checkout.vue')
        },
        {
          path: 'my-account',
          name: 'my-account',
          component: () => import('@/views/MyAccount.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: '/login',
          name: 'login',
          component: () => import('@/views/authentication/Login.vue')
        },
        {
          path: '/recover-password',
          name: 'recover password',
          component: () => import('@/views/recover-password/RecoverPassword.vue')
        },
        {
          path: '/recover-password/verification',
          name: 'verification code',
          component: () => import('@/views/recover-password/VerificationCode.vue')
        },
        {
          path: '/recover-password/new',
          name: 'new password',
          component: () => import('@/views/recover-password/NewPassword.vue')
        },
        {
          path: 'signup',
          name: 'signup',
          component: () => import('@/views/authentication/SignUp.vue')
        },
        {
          path: 'verify-email',
          name: 'verify-email',
          component: () => import('@/views/email-verification/VerifyEmail.vue')
        },
        {
          path: 'email-verified',
          name: 'emailverified',
          component: () => import('@/views/email-verification/EmailVerified.vue')
        },
        {
          path: 'reservations',
          name: 'reservations',
          component: () => import('@/views/Reservation.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: 'admin',
          component: () => import('@/views/admin/Layout.vue'),
          meta: { requiresAuth: true, requiresEmployee: true },
          children: [
            { path: '', name: 'dashboard', component: () => import('@/views/admin/Dashboard.vue') },
            { path: 'products', name: 'products', component: () => import('@/views/admin/Products.vue') },
            { path: 'ingredients', name: 'ingredients', component: () => import('@/views/admin/Ingredients.vue') },
            { path: 'users', name: 'users', component: () => import('@/views/admin/Users.vue') },
            { path: 'payment', name: 'payment', component: () => import('@/views/admin/Payment.vue') },
            { path: 'orders', name: 'admin-orders', component: () => import('@/views/admin/Orders.vue') },
            { path: 'reservations', name: 'admin-reservations', component: () => import('@/views/admin/Reservations.vue') }
          ]
        }
      ]
    },
    {
      path: '/404',
      name: '404',
      component: () => import('../views/404.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/404'
    }
  ]
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  if (auth.isLoading) {
    await auth.initializeAuth();
  }

  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  const requiresEmployee = to.matched.some((record) => record.meta.requiresEmployee);

  if (requiresAuth && !auth.isAuthenticated) {
    next({ name: 'login' });
  } else if (requiresEmployee && !auth.user?.is_employee) {
    next({ name: 'home' });
  } else {
    next();
  }
});

export default router;
