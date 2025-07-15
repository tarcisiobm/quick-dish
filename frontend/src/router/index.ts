import { createRouter, createWebHistory } from 'vue-router';

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
          path: 'email-verified',
          name: 'emailverified',
          component: () => import('@/views/email-verification/EmailVerified.vue')
        },
        {
          path: 'reservations',
          name: 'reservations',
          component: () => import('@/views/Reservation.vue')
        },
        {
          path: 'admin',
          name: 'admin',
          children: [
            {
              path: 'reservations',
              name: 'admin-reservations',
              component: () => import('@/views/AdminReservations.vue')
            }
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

export default router;
