<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useSnackbarStore } from '@/stores/snackbar';
import { useErrorStore } from '@/stores/error';
import api from '@/plugins/axios';

const authStore = useAuthStore();
const snackbarStore = useSnackbarStore();
const errorStore = useErrorStore();

const profileForm = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || ''
});

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

async function updateProfile() {
  try {
    const response = await api.put(`/users/${authStore.user?.id}`, profileForm.value);
    await authStore.getUserSession();
    snackbarStore.success(response.data.message || 'Perfil atualizado com sucesso!');
  } catch (error) {
    errorStore.handle(error);
  }
}

async function changePassword() {
  try {
    const response = await api.post('/auth/change-password', passwordForm.value);
    snackbarStore.success(response.data.message || 'Senha alterada com sucesso!');
    passwordForm.value = { current_password: '', new_password: '', new_password_confirmation: '' };
  } catch (error) {
    errorStore.handle(error);
  }
}
</script>

<template>
  <v-row>
    <v-col cols="12" md="6">
      <v-card variant="outlined">
        <v-card-title class="subtitle">Informações do Perfil</v-card-title>
        <v-card-text>
          <v-text-field v-model="profileForm.name" label="Nome Completo"></v-text-field>
          <v-text-field v-model="profileForm.email" label="E-mail"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn @click="updateProfile">Salvar Alterações</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-col cols="12" md="6">
      <v-card variant="outlined">
        <v-card-title class="subtitle">Alterar Senha</v-card-title>
        <v-card-text>
          <v-text-field v-model="passwordForm.current_password" label="Senha Atual" type="password"></v-text-field>
          <v-text-field v-model="passwordForm.new_password" label="Nova Senha" type="password"></v-text-field>
          <v-text-field v-model="passwordForm.new_password_confirmation" label="Confirmar Nova Senha" type="password"></v-text-field>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn @click="changePassword">Alterar Senha</v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>
