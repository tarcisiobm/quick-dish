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

export interface LoginCredentials {
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

  const isAuthenticated = computed((): boolean => Boolean(token.value && user.value))

  const authenticate = (userData: AuthData, userToken: string, rememberUser: boolean=true): void => {
    if (userData && userToken) {
      user.value = userData;
      token.value = userToken;
      if(rememberUser){
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
      const response: any = await api.get('/user')
      user.value = response.data;
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
    console.log('inicializando')
    if (savedToken && savedUser) {
      try {
        console.log(savedToken, savedUser);
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
      const response = await api.post('/sign-up', data)
      authenticate(response.data.user, response.data.token)
      router.push('/');
      snackbar.success(t('accountCreatedSuccessfully'));
    } catch (err) {
      snackbar.error(t('errorCreatingAccount'))
      console.error(err)
    }
  }

  const login = async (credentials: LoginCredentials, rememberUser: boolean): Promise<void> => {
    try {
      const response = await api.post('/login', credentials)
      authenticate(response.data.user, response.data.token, rememberUser)
      router.push('/');
      snackbar.success(t('loginSuccessful'))
    } catch (err) {
      snackbar.error(t('loginFailed'))
      console.error(err)
    }
  }

  const logout = async (): Promise<void> => {
    try {
      if (token.value) {
        await api.post('/logout')
      }
    } catch (err) {
      console.error(err)
    } finally {
      clearAuth()
      router.push('/login')
      snackbar.success(t('logoutSuccessful'))
    }
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
  }
})
