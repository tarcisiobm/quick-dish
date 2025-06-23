<script setup lang="ts">
import api from '@/plugins/axios';
import { ref } from 'vue';
const message = ref('');
const pingServer = async () => {
  try {
    const response = await api.get('/ping');
    message.value = response.data.message;
  } catch (error) {
    console.error('erro na requisição:', error);
  }
};
</script>

<template>
  <main>
    <h1 class="title">Hello Frontend</h1>
    <v-btn @click="pingServer">ping</v-btn>
    <div v-if="message">{{ message }}</div>
  </main>
</template>
