<script setup lang="ts">
import { PhCheckCircle } from '@phosphor-icons/vue';
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<template>
  <v-container class="fill-height">
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="text-center pa-8" variant="outlined">
          <v-avatar color="success" size="80">
            <component :is="PhCheckCircle" color="white" weight="bold" size="40" />
          </v-avatar>

          <h1 class="title text-h5 mt-6 mb-2">E-mail Verificado com Sucesso!</h1>

          <p class="text color-subtitle">
            Sua conta foi ativada. Agora você já pode fazer login e aproveitar tudo que nosso sistema tem a oferecer.
          </p>

          <v-btn
            size="large"
            @click="router.push('/login')"
            class="mt-8"
          >
            Fazer Login
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
