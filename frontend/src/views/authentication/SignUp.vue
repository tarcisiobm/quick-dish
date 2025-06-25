<script setup lang="ts">
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
import { ref } from 'vue';
import { useSnackbarStore } from '@/stores/snackbar';
const snackbar = useSnackbarStore();
import createRules from '@/utils/rules';
import { useAuthStore } from '@/stores/auth';
const rules = createRules(t);
const auth = useAuthStore();

const firstName = ref<string>('');
const lastName = ref<string>('');
const email = ref<string>('');
const phone = ref<string>('');
const password1 = ref<string>('');
const password2 = ref<string>('');
const showPassword1 = ref<boolean>(false);
const showPassword2 = ref<boolean>(false);
const form = ref<boolean>(false);

const signUp = async (): Promise<void> => {
  if (!form.value) {
    snackbar.error(t('snackbar.pleaseFillOutAllRequiredFields'));
    return;
  }
  await auth.register({
    name: `${firstName.value.trim()} ${lastName.value.trim()}`,
    email: email.value,
    phone: phone.value,
    password: password1.value,
    password_confirmation: password2.value
  });
};

const signUpProvider = async (provider: string): Promise<void> => {
  await auth.authenticateProvider(provider);
};
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center main-window-container">
    <v-form v-model="form" style="width: 430px" class="d-flex flex-column justify-center align-start">
      <h3 class="title font-32 bold">{{ t('signUp.signUp') }}</h3>
      <h4 class="subtitle font-20 semibold">{{ t('signUp.createAnAccount') }}</h4>
      <div class="w-100 d-flex justify-center align-center ga-4">
        <v-text-field v-model="firstName" :label="t('fields.firstName')" :rules="[rules.required]" class="w-100"></v-text-field>
        <v-text-field v-model="lastName" :label="t('fields.lastName')" :rules="[rules.required]" class="w-100"></v-text-field>
      </div>
      <v-text-field v-model="email" :label="t('fields.email')" :rules="[rules.required, rules.email]" required class="w-100"></v-text-field>
      <v-text-field v-model="phone" :label="t('fields.phone')" :rules="[rules.required]" class="w-100"></v-text-field>
      <v-text-field v-model="password1" :label="t('fields.password')" :rules="[rules.required]" :type="showPassword1 ? 'text' : 'password'" :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword1 = !showPassword1" class="w-100"></v-text-field>
      <v-text-field v-model="password2" :label="t('fields.reEnterPassword')" :rules="[rules.required, rules.different(password1, password2)]" :type="showPassword2 ? 'text' : 'password'" :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword2 = !showPassword2" class="w-100"></v-text-field>
      <div class="d-flex flex-column ga-4 w-100">
        <v-btn :disabled="!form" @click="signUp" class="w-100 btn-xl">{{ t('signUp.signUp') }}</v-btn>
        <div class="d-flex align-center w-100">
          <v-divider color="title" style="opacity: 1 !important" class="flex-grow-1"></v-divider>
          <p class="mx-4 flex-shrink-0">{{ t('signUp.orRegisterWith') }}</p>
          <v-divider color="title" style="opacity: 1 !important" class="flex-grow-1"></v-divider>
        </div>
        <div class="d-flex w-100 ga-8 justify-center align-center">
          <v-btn variant="outlined" color="social_btn_background" class="border-color-border pa-0 social-button" height="55" @click="signUpProvider('google')">
            <v-img width="25" :src="require('@/assets/google-logo.svg')"></v-img>
          </v-btn>
          <v-btn variant="outlined" color="social_btn_background" class="border-color-border pa-0 social-button" height="55" @click="signUpProvider('google')">
            <v-img width="25" :src="require('@/assets/facebook-logo.svg')"></v-img>
          </v-btn>
        </div>
      </div>
    </v-form>
  </div>
</template>

<style lang="scss"></style>
