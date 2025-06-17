<script setup lang="ts">
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
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
      <v-btn class="w-100">{{ t('login') }}</v-btn>
    </v-form>
  </div>
</template>

<style lang="scss">
</style>
