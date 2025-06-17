<script setup lang="ts">
import { useSnackbarStore } from '@/stores/snackbar';
const snackbar = useSnackbarStore();
</script>

<template>
  <v-snackbar
    v-model="snackbar.status"
    :color="snackbar.color"
    :timeout="snackbar.timeout"
    :location="snackbar.location"
  >
    {{ snackbar.message }}
  </v-snackbar>
</template>
