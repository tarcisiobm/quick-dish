<script setup lang="ts">
import { RouterView } from 'vue-router';
import snackbar from '@/components/SnackBar.vue';
import { onBeforeUnmount, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from './stores/theme';
const auth = useAuthStore();
const theme = useThemeStore();

onMounted(() => {
  auth.initializeAuth();
  window.addEventListener('message', auth.handleAuthMessage);
});

onBeforeUnmount(() => {
  window.removeEventListener('message', auth.handleAuthMessage);
});
</script>

<template>
  <v-app :theme="theme.isDark ? 'dark' : 'light'">
    <RouterView />
    <snackbar></snackbar>
  </v-app>
</template>

<style lang="scss" scoped></style>
