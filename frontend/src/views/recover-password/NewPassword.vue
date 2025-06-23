<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import createRules from '@/utils/rules';
import { useAuthStore } from '@/stores/auth';
const { t } = useI18n();
const rules = createRules(t);
const auth = useAuthStore();

const password = ref<string>('');
const password_confirmation = ref<string>('');
const showPassword1 = ref<boolean>(false);
const showPassword2 = ref<boolean>(false);
const form = ref<boolean>(false);
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form v-model="form" style="width: 430px" class="d-flex flex-column justify-center align-start">
      <h1 class="title">{{ t('recover.createNewPassword') }}</h1>
      <h2 class="subtitle">{{ t('recover.resetAndContinue') }}</h2>
      <v-text-field v-model="password" :label="t('fields.password')" :rules="[rules.required]" :type="showPassword1 ? 'text' : 'password'" :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword1 = !showPassword1" class="w-100"></v-text-field>
      <v-text-field v-model="password_confirmation" :label="t('fields.reEnterPassword')" :rules="[rules.required, rules.different(password, password_confirmation)]" :type="showPassword2 ? 'text' : 'password'" :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword2 = !showPassword2" class="w-100"></v-text-field>
      <v-btn :disabled="!form" @click="auth.setNewPassword(password, password_confirmation)">{{ t('fields.submit') }}</v-btn>
    </v-form>
  </div>
</template>
