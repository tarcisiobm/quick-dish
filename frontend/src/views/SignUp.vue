<script setup lang="ts">
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
import { ref } from 'vue';
import api from '@/plugins/axios';
import { useSnackbarStore } from '@/stores/snackbar';
const snackbar = useSnackbarStore();

const firstName = ref<string>('');
const lastName = ref<string>('');
const phoneNumber = ref<string>('');
const password = ref<string>('');

interface UserData {
  firstName: string,
  lastName: string,
  phoneNumber: string,
  password: string,
}

const submitData = (): void => {
  snackbar.success('Enviado');
}

const signUp = (data: UserData): void => {
  api.post('/sign-up', data);
}
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form style="width: 430px;" class="d-flex flex-column justify-center align-start">
      <h3>{{ t('signUp') }}</h3>
      <h4>{{ t('createAnAccount') }}</h4>
      <div class="w-100 d-flex justify-center align-center ga-4">
        <v-text-field :label="t('firstName')" class="w-100"></v-text-field>
        <v-text-field :label="t('lastName')" class="w-100"></v-text-field>
      </div>
      <v-text-field :label="t('phoneNumber')" class="w-100"></v-text-field>
      <v-text-field :label="t('email')" class="w-100"></v-text-field>
      <v-text-field :label="t('password')" class="w-100"></v-text-field>
      <v-text-field :label="t('reEnterPassword')" class="w-100"></v-text-field>
      <RouterLink to="/forgot-password">{{ t('forgotPassword') }}</RouterLink>
      <v-btn @click="submitData" class="w-100">{{ t('login') }}</v-btn>
    </v-form>
  </div>
</template>

<style lang="scss"></style>
