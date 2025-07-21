<script setup lang="ts">
import { PhEnvelope } from '@phosphor-icons/vue';
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<template>
  <v-container class="fill-height">
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="text-center pa-8" variant="outlined">
          <v-avatar color="success" size="80">
            <component :is="PhEnvelope" color="white" weight="bold" size="40" />
          </v-avatar>

          <h1 class="title text-h5 mt-6 mb-2">Confirme seu E-mail</h1>

          <p class="text color-subtitle">Cadastro realizado com sucesso! Enviamos um link de confirmação para o seu e-mail. Por favor, verifique sua caixa de entrada (e a pasta de spam) para ativar sua conta.</p>

          <v-btn @click="router.push('/login')" variant="text" color="primary" class="mt-6"> Ir para o Login </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
