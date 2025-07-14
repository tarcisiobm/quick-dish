<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { useSnackbarStore } from '@/stores/snackbar';
import createRules from '@/utils/rules';
import { useAuthStore } from '@/stores/auth';

const { t } = useI18n();
const rules = createRules(t);
const snackbar = useSnackbarStore();
const auth = useAuthStore();

const email = ref<string>('');
const password = ref<string>('');
const rememberMe = ref<boolean>(true);
const showPassword = ref<boolean>(false);
const form = ref<boolean>(false);

const login = async (): Promise<void> => {
  if (!form.value || !email.value.trim() || !password.value.trim()) {
    snackbar.error(t('snackbar.pleaseFillOutAllRequiredFields'));
    return;
  }

  await auth.login({
    email: email.value,
    password: password.value,
    remember: rememberMe.value
  });
};

const loginProvider = async (provider: string): Promise<void> => {
  await auth.authenticateProvider(provider);
};
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center main-window-container">
    <v-form v-model="form" style="width: 430px" class="d-flex flex-column justify-center align-start">
      <h3 class="title font-32 bold">{{ t('login.login') }}</h3>
      <h4 class="subtitle font-20 semibold">{{ t('login.welcomeBack') }}</h4>

      <v-text-field v-model="email" :label="t('fields.email')" :rules="[rules.required, rules.email]" required class="w-100" />

      <v-text-field v-model="password" :label="t('fields.password')" :rules="[rules.required]" required :type="showPassword ? 'text' : 'password'" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword = !showPassword" class="w-100" />

      <div class="w-100 d-flex justify-space-between align-center">
        <v-checkbox hide-details v-model="rememberMe" :label="t('login.rememberMe')" />
        <RouterLink to="/recover-password" class="color-title">{{ t('login.forgotPassword') }}</RouterLink>
      </div>

      <div class="d-flex flex-column ga-4 w-100">
        <v-btn :disabled="!form" @click="login" class="w-100 btn-xl">
          {{ t('login.login') }}
        </v-btn>
        <div class="d-flex align-center w-100">
          <v-divider color="title" style="opacity: 1 !important" class="flex-grow-1"></v-divider>
          <p class="mx-4 flex-shrink-0">{{ t('login.orLoginWith') }}</p>
          <v-divider color="title" style="opacity: 1 !important" class="flex-grow-1"></v-divider>
        </div>
        <div class="d-flex w-100 ga-8 justify-center align-center">
          <v-btn variant="outlined" color="social_btn_background" class="border-color-border pa-0 social-button" height="55" @click="loginProvider('google')">
            <v-img width="25" :src="require('@/assets/google-logo.svg')"></v-img>
          </v-btn>
          <v-btn variant="outlined" color="social_btn_background" class="border-color-border pa-0 social-button" height="55" @click="loginProvider('facebook')">
            <v-img width="25" :src="require('@/assets/facebook-logo.svg')"></v-img>
          </v-btn>
        </div>
      </div>
    </v-form>
  </div>
</template>

<style lang="scss"></style>
