import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useSnackbarStore } from './snackbar'
import createExceptions from '@/utils/exception'
import api from '@/plugins/axios'
import router from '@/router'
import createWindow from '@/utils/window'
const newWindow = createWindow()

export interface AuthData {
  id: number
  name: string
  email: string
  phone: string
  status: number
  avatar: string | null
  deleted_at: string | null
  updated_at: string
  created_at: string
}

export interface LoginData {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  phone: string
  password: string
  password_confirmation: string
}

export const useAuthStore = defineStore('auth', () => {
  const { t } = useI18n()
  const user = ref<AuthData | null>(null)
  const token = ref<string | null>(null)
  const recoverToken = ref<string | null>(null)
  const recoverEmail = ref<string | null>(null)
  const snackbar = useSnackbarStore()
  const authWindow = ref<Window | null>(null)
  const exception = createExceptions(snackbar, t)

  const isAuthenticated = computed((): boolean => Boolean(token.value && user.value))

  const authenticate = (
    userData: AuthData,userToken: string,rememberUser: boolean = true): void => {
    if (!userData && !userToken) return
    console.log(userData);
    user.value = userData
    token.value = userToken
    api.defaults.headers.common['Authorization'] = `Bearer ${userToken}`
    if (rememberUser) {
      localStorage.setItem('auth_user', JSON.stringify(userData))
      localStorage.setItem('auth_token', userToken)
    }
  }

  const clearAuth = (): void => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    delete api.defaults.headers.common['Authorization']
  }

  const getUserSession = async (): Promise<void> => {
    try {
      const response = await api.get('/auth/user')
      user.value = response.data
      localStorage.setItem('auth_user', JSON.stringify(response.data))
    } catch (err) {
      snackbar.show(t('snackbar.sessionExpired'))
      console.error(err)
      clearAuth()
    }
  }

  const initializeAuth = async (): Promise<void> => {
    const savedToken = localStorage.getItem('auth_token')
    const savedUser = localStorage.getItem('auth_user')
    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
      api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
      await getUserSession()
    }
  }

  const register = async (data: RegisterData): Promise<void> => {
    try {
      const response = await api.post('/auth/sign-up', data)
      authenticate(response.data.user, response.data.token)
      router.push('/')
      snackbar.success(t('snackbar.accountCreatedSuccessfully'))
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }

  const login = async (data: LoginData, rememberUser: boolean): Promise<void> => {
    try {
      const response = await api.post('/auth/login', data)
      authenticate(response.data.user, response.data.token, rememberUser)
      router.push('/')
      snackbar.success(t('snackbar.loginSuccessful'))
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }

  const logout = async (): Promise<void> => {
    try {
      if (token.value) await api.post('/auth/logout')
    } catch (err) {
      exception.show(err)
      console.error(err)
    } finally {
      clearAuth()
      router.push('/login')
      snackbar.success(t('snackbar.logoutSuccessful'))
    }
  }

  const authenticateProvider = async (provider: string): Promise<void> => {
    try {
      const response = await api.get(`/auth/${provider}/redirect`)
      const redirectUrl = response.data.redirect_url
      authWindow.value = newWindow.open(redirectUrl, 'Authentication')
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }

  const handleAuthMessage = (event: any) => {
    if (event.origin !== process.env.VUE_APP_BACKEND_URL) return
    const { status, token, user } = event.data

    if (status === 'success') {
      authenticate(user, token, true);
      router.push('/');
      return
    }
    if (authWindow.value) authWindow.value.close()
    window.removeEventListener('message', handleAuthMessage)
  }

  const recoverPassword = async (email: string): Promise<void> => {
    if (!email || !email.trim()) return
    try {
      await api.post('/auth/recover-password', { email })
      recoverEmail.value = email
      router.push('/recover-password/verification')
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }
  const validateToken = async (token: string): Promise<void> => {
    if (!token || !token.trim()) return
    try {
      await api.post('/auth/validate-token', {
        token,
        email: recoverEmail.value,
      })
      recoverToken.value = token
      snackbar.success(t('snackbar.tokenValidated'))
      router.push('/recover-password/new')
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }

  const setNewPassword = async (password: string, password_confirmation: string): Promise<void> => {
    if (!password || !password_confirmation || !password.trim() || !password_confirmation.trim())
      return
    try {
      await api.post('/auth/reset-password', {
        email: recoverEmail.value,
        token: recoverToken.value,
        password,
        password_confirmation,
      })
      snackbar.success(t('snackbar.passwordChanged'))
      router.push('/login')
    } catch (err) {
      exception.show(err)
      console.error(err)
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    authenticate,
    clearAuth,
    initializeAuth,
    getUserSession,
    register,
    login,
    logout,
    authenticateProvider,
    handleAuthMessage,
    recoverPassword,
    validateToken,
    setNewPassword
  }
})
