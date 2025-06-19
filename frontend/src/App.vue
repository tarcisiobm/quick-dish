<script setup lang="ts">
import { RouterView } from 'vue-router'
import snackbar from '@/components/snackbar.vue'
import { onBeforeUnmount, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import appbar from './components/appbar.vue'
const auth = useAuthStore()

onMounted(() => {
  auth.initializeAuth()
  window.addEventListener('message', auth.handleAuthMessage)
})

onBeforeUnmount(() => {
  window.removeEventListener('message', auth.handleAuthMessage)
})
</script>

<template>
  <v-app>
    <appbar></appbar>
    <v-main>
      <RouterView />
    </v-main>
    <snackbar></snackbar>
  </v-app>
</template>

<style lang="scss" scoped></style>
