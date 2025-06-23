<script setup lang="ts">
import { useSnackbarStore } from '@/stores/snackbar';
const snackbar = useSnackbarStore();
</script>

<template>
  <v-snackbar v-model="snackbar.status" :color="snackbar.color" :timeout="snackbar.timeout" :location="snackbar.location">
    <p style="color: #FFFFFF !important">{{ snackbar.message }}</p>
    <template v-slot:actions>
      <v-btn variant="text" @click="snackbar.hide" density="compact" color="#FFFFFF" height="undefined" icon>
        <v-icon color="#FFFFFF" size="x-small">
          mdi-close
        </v-icon>
      </v-btn>
    </template>
  </v-snackbar>
</template>
