<script setup lang="ts">
import { useRouter } from 'vue-router';
import { PhCompass } from '@phosphor-icons/vue';

const router = useRouter();
</script>

<template>
  <v-container class="fill-height text-center">
    <v-row align="center" justify="center">
      <v-col>
        <div class="d-flex justify-center align-center mb-4">
          <span class="title text-h1 mr-4">4</span>
          <component :is="PhCompass" :size="96" class="color-primary" />
          <span class="title text-h1 ml-4">4</span>
        </div>

        <h1 class="title text-h4 mb-2">Página Não Encontrada</h1>
        <p class="text color-subtitle mb-8">Oops! Parece que você se perdeu. A página que você procura não existe.</p>

        <v-btn size="large" prepend-icon="mdi-home" @click="router.push('/')"> Voltar para a Home </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>
