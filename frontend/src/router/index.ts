import { createRouter, createWebHistory } from 'vue-router'

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
          component: () => import('@/views/Home.vue'),
        },
        {
          path: '/login',
          name: 'login',
          component: () => import('@/views/authentication/Login.vue'),
        },
        {
          path: 'signup',
          name: 'signup',
          component: () => import('@/views/authentication/SignUp.vue'),
        },
        {
          path: 'email-verified',
          name: 'emailverified',
          component: () => import('@/views/email-verification/EmailVerified.vue'),
        },
      ],
    },
    {
      path: '/404',
      name: '404',
      component: () => import('../views/404.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/404',
    },
  ],
})

export default router
