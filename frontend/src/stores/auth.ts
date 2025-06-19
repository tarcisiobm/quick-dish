import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n' // Assumindo que você usa vue-i18n
import api from '@/plugins/axios'
import router from '@/router'
import { useSnackbarStore } from './snackbar'

export interface AuthData {
  id: number
  name: string
  email: string
  phone: string
  status: number
  image_path: string | null
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
  const snackbar = useSnackbarStore()
  const authWindow = ref<Window | null>(null)

  const isAuthenticated = computed((): boolean => Boolean(token.value && user.value))

  const authenticate = (
    userData: AuthData,
    userToken: string,
    rememberUser: boolean = true,
  ): void => {
    if (userData && userToken) {
      user.value = userData
      token.value = userToken
      if (rememberUser) {
        localStorage.setItem('auth_user', JSON.stringify(userData))
        localStorage.setItem('auth_token', userToken)
      }
    }
    api.defaults.headers.common['Authorization'] = `Bearer ${userToken}`
  }

  const clearAuth = (): void => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    delete api.defaults.headers.common['Authorization']
  }

  const getCurrentUser = async (): Promise<void> => {
    try {
      const response: any = await api.get('/auth/user')
      user.value = response.data
      localStorage.setItem('auth_user', JSON.stringify(response.data))
    } catch (err) {
      console.error(err)
      snackbar.show(t('sessionExpired'))
      clearAuth()
    }
  }

  const initializeAuth = (): void => {
    const savedToken = localStorage.getItem('auth_token')
    const savedUser = localStorage.getItem('auth_user')
    if (savedToken && savedUser) {
      try {
        token.value = savedToken
        user.value = JSON.parse(savedUser)
        api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
        getCurrentUser().catch(() => clearAuth())
      } catch (err) {
        console.error(err)
        clearAuth()
      }
    }
  }

  const register = async (data: RegisterData): Promise<void> => {
    try {
      const response = await api.post('/auth/sign-up', data)
      authenticate(response.data.user, response.data.token)
      router.push('/')
      snackbar.success(t('accountCreatedSuccessfully'))
    } catch (err) {
      snackbar.error(t('errorCreatingAccount'))
      console.error(err)
    }
  }

  const login = async (data: LoginData, rememberUser: boolean): Promise<void> => {
    try {
      const response = await api.post('/auth/login', data)
      authenticate(response.data.user, response.data.token, rememberUser)
      router.push('/')
      snackbar.success(t('loginSuccessful'))
    } catch (err) {
      snackbar.error(t('loginFailed'))
      console.error(err)
    }
  }

  const logout = async (): Promise<void> => {
    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (err) {
      console.error(err)
    } finally {
      clearAuth()
      router.push('/login')
      snackbar.success(t('logoutSuccessful'))
    }
  }

  const authenticateProvider = async (provider: string): Promise<void> => {
    try {
      const w = 600
      const h = 600
      const response = await api.get(`/auth/${provider}/redirect`)
      const redirectUrl = response.data.redirect_url
      const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX
      const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY

      const width = window.innerWidth
        ? window.innerWidth
        : document.documentElement.clientWidth
          ? document.documentElement.clientWidth
          : screen.width
      const height = window.innerHeight
        ? window.innerHeight
        : document.documentElement.clientHeight
          ? document.documentElement.clientHeight
          : screen.height

      const systemZoom = width / window.screen.availWidth
      const left = (width - w) / 2 / systemZoom + dualScreenLeft
      const top = (height - h) / 2 / systemZoom + dualScreenTop
      authWindow.value = window.open(
        redirectUrl,
        'Authentication',
        `
      scrollbars=yes,
      width=${w / systemZoom},
      height=${h / systemZoom},
      top=${top},
      left=${left}
      `,
      )
    } catch (err) {
      snackbar.error(t('failedAuthentication'))
      console.error(err)
    }
  }

  const handleAuthMessage = (event: any) => {
    if (event.origin !== 'http://localhost:3100') return
    const { status, token, user } = event.data

    if (status === 'success') {
      authenticate(user, token, true)
      return
    }
    if (authWindow.value) authWindow.value.close()
    window.removeEventListener('message', handleAuthMessage)
  }

  return {
    user,
    token,
    isAuthenticated,
    authenticate,
    clearAuth,
    initializeAuth,
    getCurrentUser,
    register,
    login,
    logout,
    authenticateProvider,
    handleAuthMessage,
  }
})
