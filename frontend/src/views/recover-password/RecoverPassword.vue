<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import createRules from '@/utils/rules';
import { useAuthStore } from '@/stores/auth';
const auth = useAuthStore();
const { t } = useI18n();
const rules = createRules(t);

const email = ref<string>('');
const form = ref<boolean>(false);
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center main-window-container">
    <v-form v-model="form" style="width: 430px" class="d-flex flex-column justify-center align-start">
      <h3 class="title font-32 bold">{{ t('recover.recoverPassword') }}</h3>
      <h4 class="subtitle font-20 semibold">{{ t('recover.enterEmail') }}</h4>
      <v-text-field v-model="email" :label="t('fields.email')" :rules="[rules.required]" class="w-100"></v-text-field>
      <v-btn height="55" class="w-100" :disabled="!form" @click="auth.recoverPassword(email)">{{ t('recover.sendEmail') }}</v-btn>
    </v-form>
  </div>
</template>
