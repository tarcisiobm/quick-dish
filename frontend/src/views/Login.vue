<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'
import { useSnackbarStore } from '@/stores/snackbar'
const { t } = useI18n()
import createRules from '@/utils/rules'
const rules = createRules(t)
const snackbar = useSnackbarStore()
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()

const email = ref<string>('')
const password = ref<string>('')
const rememberMe = ref<boolean>(true)
const showPassword = ref<boolean>(false)
const form = ref<boolean>(false)

const login = async (): Promise<void> => {
  if (!form.value || !email.value.trim() || !password.value.trim()) {
    snackbar.error(t('pleaseFillOutAllRequiredFields'))
    return
  }
  await auth.login(
    {
      email: email.value,
      password: password.value,
    }, rememberMe.value
  )
}
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form
      v-model="form"
      style="width: 430px"
      class="d-flex flex-column justify-center align-start"
    >
      <h3>{{ t('login') }}</h3>
      <h4>{{ t('welcomeBack') }}</h4>

      <v-text-field
        v-model="email"
        :label="t('email')"
        :rules="[rules.required, rules.email]"
        required
        class="w-100"
      />

      <v-text-field
        v-model="password"
        :label="t('password')"
        :rules="[rules.required]"
        required
        :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword"
        class="w-100"
      />

      <div class="w-100 d-flex justify-space-between align-center">
        <v-checkbox v-model="rememberMe" :label="t('rememberMe')" />
        <!-- <RouterLink to="/forgot-password">{{ t('forgotPassword') }}</RouterLink> -->
      </div>

      <v-btn
        :disabled="!form"
        @click="login"
        class="w-100">
        {{ t('login') }}
      </v-btn>
      <p>{{ t('orLoginWith') }}</p>
    </v-form>
  </div>
</template>

<style lang="scss"></style>
