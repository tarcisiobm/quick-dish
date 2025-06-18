<script setup lang="ts">
import { RouterView } from 'vue-router';
import snackbar from '@/components/snackbar.vue';
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import appbar from './components/appbar.vue';

const auth = useAuthStore();
const { t } = useI18n()

onMounted(() => {
  auth.initializeAuth()
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

<style lang="scss" scoped>
</style>
