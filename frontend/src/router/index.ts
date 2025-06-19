import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/Home.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Login.vue'),
    },
     {
      path: '/signup',
      name: 'signup',
      component: () => import('../views/SignUp.vue'),
    },
    {
      path: '/email-verified',
      name: 'emailverified',
      component: () => import('../views/Email-verified.vue'),
    },
    {
      path: '/404',
      name: '404',
      component: () => import('../views/404.vue'),
    }
  ],
})

export default router
