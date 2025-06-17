<script setup lang="ts">
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import api from '@/plugins/axios';

interface UserData {
  email: string,
  password: string,
}

const login = (data: UserData) => {
  api.post('/login', data);
}
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form style="width: 430px;" class="d-flex flex-column justify-center align-start">
      <h3>{{ t('login') }}</h3>
      <h4>{{ t('welcomeBack') }}</h4>
      <v-text-field :label="t('email')" class="w-100"></v-text-field>
      <v-text-field :label="t('password')" class="w-100"></v-text-field>
      <div class="w-100 d-flex justify-space-between align-center">
        <v-checkbox :label="t('rememberMe')"></v-checkbox>
        <RouterLink to="/forgot-password">{{ t('forgotPassword') }}</RouterLink>
      </div>
      <p>{{ t('orLoginWith') }}</p>
      <v-btn class="w-100">{{ t('login') }}</v-btn>
    </v-form>
  </div>
</template>

<style lang="scss">
</style>
