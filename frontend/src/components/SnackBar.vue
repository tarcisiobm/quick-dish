<script setup lang="ts">
import { useSnackbarStore } from '@/stores/snackbar';
const snackbarStore = useSnackbarStore();

function getStackingStyle(index: number) {
  const offset = 60;
  return {
    transform: `translateY(-${index * offset}px)`
  };
}
</script>

<template>
  <div class="snackbar-container">
    <v-snackbar v-for="(snackbar, index) in snackbarStore.snackbars" :key="snackbar.id" v-model="snackbar.status" :color="snackbar.color" :timeout="-1" :location="snackbar.location" :style="getStackingStyle(index)" class="stacked-snackbar">
      <p style="color: #ffffff !important">{{ snackbar.message }}</p>
      <template v-slot:actions>
        <v-btn variant="text" @click="snackbarStore.hide(snackbar.id)" density="compact" color="#FFFFFF" icon>
          <v-icon color="#FFFFFF" size="x-small">mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<style scoped>
.stacked-snackbar {
  transition: transform 0.3s ease-in-out;
}
</style>
