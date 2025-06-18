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
    snackbar.error(t('pleaseFillOutAllRequiredFields'))
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
</script>

<template>
  <div class="w-100 h-100 d-flex justify-center align-center">
    <v-form
      v-model="form"
      style="width: 430px"
      class="d-flex flex-column justify-center align-start"
    >
      <h3>{{ t('signUp') }}</h3>
      <h4>{{ t('createAnAccount') }}</h4>
      <div class="w-100 d-flex justify-center align-center ga-4">
        <v-text-field
          v-model="firstName"
          :label="t('firstName')"
          :rules="[rules.required]"
          class="w-100"
        ></v-text-field>
        <v-text-field
          v-model="lastName"
          :label="t('lastName')"
          :rules="[rules.required]"
          class="w-100"
        ></v-text-field>
      </div>
      <v-text-field
        v-model="email"
        :label="t('email')"
        :rules="[rules.required, rules.email]"
        required
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="phone"
        :label="t('phone')"
        :rules="[rules.required]"
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="password1"
        :label="t('password')"
        :rules="[rules.required]"
        :type="showPassword1 ? 'text' : 'password'"
        :append-icon="showPassword1 ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword1 = !showPassword1"
        class="w-100"
      ></v-text-field>
      <v-text-field
        v-model="password2"
        :label="t('reEnterPassword')"
        :rules="[rules.required, rules.different(password1, password2)]"
        :type="showPassword2 ? 'text' : 'password'"
        :append-icon="showPassword2 ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword2 = !showPassword2"
        class="w-100"
      ></v-text-field>
      <RouterLink to="/forgot-password">{{ t('forgotPassword') }}</RouterLink>
      <v-btn :disabled="!form" @click="signUp" class="w-100">{{ t('signUp') }}</v-btn>
    </v-form>
  </div>
</template>

<style lang="scss"></style>
