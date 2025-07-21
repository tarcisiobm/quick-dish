<script setup lang="ts">
import { ref } from 'vue';
import { useSnackbarStore } from '@/stores/snackbar';

const snackbarStore = useSnackbarStore();

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
});

function submitForm() {
  snackbarStore.info('Funcionalidade de envio em desenvolvimento.');
}
</script>

<template>
  <v-container class="py-12">
    <v-row justify="center" class="text-center mb-10">
      <v-col cols="12" md="8">
        <h1 class="title text-h3 mb-2">Entre em Contato</h1>
        <p class="text text-body-1 color-subtitle">Adoraríamos ouvir de você! Seja para tirar dúvidas, dar sugestões ou fazer reservas, nossa equipe está pronta para ajudar.</p>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="5">
        <v-card variant="tonal" class="pa-4 mb-6">
          <v-list-item prepend-icon="mdi-map-marker-outline" title="Nosso Endereço" subtitle="Av. Afonso Pena, 1234 - Centro, Belo Horizonte - MG" class="mb-2" />
          <v-divider />
          <v-list-item prepend-icon="mdi-phone-outline" title="Telefone" subtitle="(31) 99999-8888" class="mb-2" />
          <v-divider />
          <v-list-item prepend-icon="mdi-email-outline" title="E-mail" subtitle="contato@quickdish.com.br" />
        </v-card>

        <v-card variant="outlined">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3751.213121545465!2d-43.93988358508587!3d-19.9155099866107!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa6998e35c8a20f%3A0x8632c0d50de5312!2sAv.%20Afonso%20Pena%20-%20Centro%2C%20Belo%20Horizonte%20-%20MG!5e0!3m2!1spt-BR!2sbr!4v1677609812345!5m2!1spt-BR!2sbr" width="100%" height="350" style="border: 0; border-radius: 4px" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </v-card>
      </v-col>

      <v-col cols="12" md="7">
        <v-card variant="outlined">
          <v-card-title class="subtitle text-h6 pa-4">Envie uma Mensagem</v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-form @submit.prevent="submitForm">
              <v-row>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="form.name" label="Seu Nome" variant="outlined"></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field v-model="form.email" label="Seu E-mail" variant="outlined" type="email"></v-text-field>
                </v-col>
              </v-row>
              <v-text-field v-model="form.subject" label="Assunto" variant="outlined" class="mb-4"></v-text-field>
              <v-textarea v-model="form.message" label="Sua Mensagem" variant="outlined" rows="6"></v-textarea>
              <v-btn type="submit" block size="large" class="mt-4"> Enviar Mensagem </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
