<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import createRules from '@/utils/rules';
import { useAuthStore } from '@/stores/auth';
const { t } = useI18n();
const rules = createRules(t);
const auth = useAuthStore();

const token = ref<string>('');
const form = ref<boolean>(false);
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form v-model="form" style="width: 430px" class="d-flex flex-column justify-center align-start">
      <h1 class="title">{{ t('recover.enterVerificationCode') }}</h1>
      <h2 class="subtitle">{{ t('recover.sentCodeEmail') }}</h2>
      <v-text-field v-model="token" :rules="[rules.required]" class="w-100"></v-text-field>
      <v-btn :disabled="!form" @click="auth.validateToken(token)">{{ t('recover.verifyCode') }}</v-btn>
    </v-form>
  </div>
</template>
