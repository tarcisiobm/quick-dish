<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
const router = useRouter();
const auth = useAuthStore();

</script>

<template>
   <v-app-bar app>
      <div class="wrapper">
        <nav class="d-flex align-baseline">
          <div v-if="auth.isAuthenticated" class="d-flex ga-4">
            <h1>USUARIO LOGADO</h1>
            <p>{{ auth.user?.name }}</p>
            <p>{{ auth.user?.email }}</p>
            <v-btn @click="auth.logout">LOGOUT</v-btn>
          </div>
          <div v-else>
            <v-btn variant="text" @click="router.push('/')" class="mr-2">Home</v-btn>
            <v-btn variant="text" @click="router.push('/login')">Login</v-btn>
            <v-btn variant="text" @click="router.push('/signup')">Sign-Up</v-btn>
          </div>
        </nav>
      </div>
    </v-app-bar>
</template>
