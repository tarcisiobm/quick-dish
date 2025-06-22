<script setup lang="ts">
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
import { ref } from 'vue'
import { useSnackbarStore } from '@/stores/snackbar'
const snackbar = useSnackbarStore()
import createRules from '@/utils/rules'
import { useAuthStore } from '@/stores/auth'
const rules = createRules(t)
const auth = useAuthStore()

const firstName = ref<string>('')
const lastName = ref<string>('')
const email = ref<string>('')
const phone = ref<string>('')
const password1 = ref<string>('')
const password2 = ref<string>('')
const showPassword1 = ref<boolean>(false)
const showPassword2 = ref<boolean>(false)
const form = ref<boolean>(false)

const signUp = async (): Promise<void> => {
  if (!form.value) {
    snackbar.error(t('snackbar.pleaseFillOutAllRequiredFields'))
    return
  }
  await auth.register(
    {
      name: `${firstName.value.trim()} ${lastName.value.trim()}`,
      email: email.value,
      phone: phone.value,
      password: password1.value,
      password_confirmation: password2.value,
    }
  )
}

const loginProvider = async (provider: string): Promise<void> => {
  await auth.authenticateProvider(provider);
}
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form
      v-model="form"
      style="width: 430px"
      class="d-flex flex-column justify-center align-start"
    >
      <h3>{{ t('signUp.signUp') }}</h3>
      <h4>{{ t('signUp.createAnAccount') }}</h4>
      <div class="w-100 d-flex justify-center align-center ga-4">
        <v-text-field
          v-model="firstName"
          :label="t('fields.firstName')"
          :rules="[rules.required]"
          class="w-100"
        ></v-text-field>
        <v-text-field
          v-model="lastName"
          :label="t('fields.lastName')"
          :rules="[rules.required]"
          class="w-100"
        ></v-text-field>
      </div>
      <v-text-field
        v-model="email"
        :label="t('fields.email')"
        :rules="[rules.required, rules.email]"
        required
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="phone"
        :label="t('fields.phone')"
        :rules="[rules.required]"
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="password1"
        :label="t('fields.password')"
        :rules="[rules.required]"
        :type="showPassword1 ? 'text' : 'password'"
        :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword1 = !showPassword1"
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="password2"
        :label="t('fields.reEnterPassword')"
        :rules="[rules.required, rules.different(password1, password2)]"
        :type="showPassword2 ? 'text' : 'password'"
        :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword2 = !showPassword2"
        class="w-100"
      ></v-text-field>
      <v-btn :disabled="!form" @click="signUp" class="w-100">{{ t('signUp.signUp') }}</v-btn>
      <p>{{ t('signUp.orRegisterWith') }}</p>
      <v-btn @click="loginProvider('google')">Google</v-btn>
      <v-btn @click="loginProvider('facebook')">Facebook</v-btn>

    </v-form>
  </div>
</template>

<style lang="scss"></style>
