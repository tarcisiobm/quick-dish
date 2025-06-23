<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { RouterLink, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
const router = useRouter();
const auth = useAuthStore();
const { t } = useI18n();
</script>

<template>
  <nav class="superior-nav-bar d-flex justify-space-between px-2 w-100">
    <div @click="router.push('/')" class="d-flex align-center ga-3 cursor-pointer">
      <img width="45" src="@/assets/logo.svg" />
      <h1 class="font-22 font-bold">Quick<span class="font-22 font-bold color-primary">Dish</span></h1>
    </div>
    <div class="d-flex justify-center align-center ga-4">
      <RouterLink to="/" class="nav-link color-text">Home</RouterLink>
      <RouterLink to="/about" class="nav-link color-text">About</RouterLink>
      <RouterLink to="/contact" class="nav-link color-text">Contact</RouterLink>
      <RouterLink to="/reviews" class="nav-link color-text">Reviews</RouterLink>
    </div>
    <div class="d-flex justify-center align-center ga-4">
      <v-btn height="undefined" icon variant="outlined">
        <v-icon height="48" color="text"> mdi-basket-outline </v-icon>
      </v-btn>
      <div v-if="!auth.isAuthenticated" class="d-flex justify-center align-center ga-4">
        <v-btn color="text" variant="text" prepend-icon="mdi-account-circle-outline" @click="router.push('/login')">Login</v-btn>
        <v-btn @click="router.push('/signup')">SignUp</v-btn>
      </div>
      <div v-else width="36" class="user-card d-flex justify-center align-center">
        <v-avatar :image="auth.user?.avatar ?? require('@/assets/user-default.png')"></v-avatar>
        <p class="font-14 color-text">{{ auth.user?.name }}</p>
        <v-btn color="text" icon variant="text" height="undefined" density="compact">
          <v-icon size="small"> mdi-menu-down </v-icon>
        </v-btn>
        <v-menu activator="parent">
          <v-list class="user-options-menu">
            <v-list-item to="/account-settings" prepend-icon="mdi-pencil" base-color="text">
              <v-list-item-title>{{ t('appBar.editProfile') }}</v-list-item-title>
            </v-list-item>
            <v-list-item @click="auth.logout" prepend-icon="mdi-logout-variant" base-color="error_dense">
              <v-list-item-title class="color-error_dense">{{ t('appBar.logout') }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </div>
  </nav>
</template>

<style lang="scss" scoped>
.superior-nav-bar {
  height: 56px;
}

.nav-link {
  text-decoration: none;
  text-transform: uppercase;
}

.user-card {
  width: 234px;
  height: 46px;
  gap: 10px;
  padding: 5px 15px;

  p {
    max-width: 124px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
.user-options-menu {
  width: 220px;
}
</style>
