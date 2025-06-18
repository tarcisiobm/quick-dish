<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth';
const t = useI18n();
const auth = useAuthStore();

</script>

<template>
   <v-app-bar app>
      <div class="wrapper">
        <nav class="d-flex align-baseline">
          <v-btn  variant="text" to="/" class="mr-2">Home</v-btn>
          <div v-if="auth.isAuthenticated" class="d-flex ga-4">
            <h1>USUARIO LOGADO</h1>
            <p>{{ auth.user?.name }}</p>
            <p>{{ auth.user?.email }}</p>
            <v-btn @click="auth.logout">LOGOUT</v-btn>
          </div>
          <div v-else>
            <v-btn  variant="text" to="/login">Login</v-btn>
            <v-btn variant="text" to="/signup">Sign-Up</v-btn>
          </div>
        </nav>
      </div>
    </v-app-bar>
</template>
