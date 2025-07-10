import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useSnackbarStore } from './snackbar';
import createExceptions from '@/utils/exception';
import api from '@/plugins/axios';
import router from '@/router';
import createWindow from '@/utils/window';

export interface AuthData {
  id: number;
  name: string;
  email: string;
  phone: string;
  status: number;
  avatar: string | null;
  deleted_at: string | null;
  updated_at: string;
  created_at: string;
}

export interface LoginData {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  phone: string;
  password: string;
  password_confirmation: string;
}

const AUTH_KEY = 'wasLoggedIn';
const getStoredAuth = (): boolean => localStorage.getItem(AUTH_KEY) === 'true';
const setStoredAuth = (value: boolean): void => localStorage.setItem(AUTH_KEY, String(value));
const clearStoredAuth = (): void => localStorage.removeItem(AUTH_KEY);

export const useAuthStore = defineStore('auth', () => {
  const { t } = useI18n();
  const snackbar = useSnackbarStore();
  const exception = createExceptions(snackbar, t);

  const user = ref<AuthData | null>(null);
  const recoverEmail = ref('');
  const authWindow = ref<Window | null>(null);

  const isAuthenticated = computed(() => Boolean(user.value));

  const resetState = () => {
    user.value = null;
    recoverEmail.value = '';
    clearStoredAuth();
  };

  const setUser = (userData: AuthData) => {
    user.value = userData;
    setStoredAuth(true);
  };

  const handleError = (err: any, showSessionExpired = false) => {
    if (showSessionExpired && getStoredAuth() && err.response?.status === 401) {
      snackbar.show(t('snackbar.sessionExpired'));
    }
    exception.show(err);
  };

  const getUserSession = async () => {
    const response = await api.get('/auth/user');
    setUser(response.data);
  };

  const initializeAuth = async () => {
    try {
      await getUserSession();
    } catch (error) {
      handleError(error, true);
      resetState();
    }
  };

  const register = async (data: RegisterData) => {
    try {
      await api.post('/auth/sign-up', data);
      snackbar.success(t('snackbar.accountCreatedSuccessfully'));
      router.push('/verify-email');
    } catch (err) {
      handleError(err);
    }
  };

  const login = async (credentials: LoginData) => {
    try {
      const response = await api.post('/auth/login', credentials);
      setUser(response.data.user);
      snackbar.success(t('snackbar.loginSuccessful'));
      router.push('/');
    } catch (err) {
      handleError(err);
    }
  };

  const logout = async () => {
    try {
      await api.post('/auth/logout');
    } catch (err) {
      handleError(err);
    } finally {
      resetState();
      snackbar.success(t('snackbar.logoutSuccessful'));
      router.push('/');
    }
  };

  const closeAuthWindow = () => {
    if (!authWindow.value) return;
    authWindow.value.close();
    authWindow.value = null;
    window.removeEventListener('message', handleAuthMessage);
  };

  const handleAuthMessage = (event: MessageEvent) => {
    if (event.origin !== process.env.VUE_APP_BACKEND_URL) return;

    const { status, user: userData, error } = event.data;
    closeAuthWindow();

    if (status === 'success' && userData) {
      setUser(userData);
      snackbar.success(t('snackbar.loginSuccessful'));
      router.push('/');
      return;
    }
    snackbar.error(error || t('snackbar.authenticationError'));
  };

  const authenticateProvider = async (provider: string) => {
    try {
      const response = await api.get(`/auth/${provider}/redirect`);
      authWindow.value = createWindow().open(response.data.redirect_url, 'Authentication');
      window.addEventListener('message', handleAuthMessage);
    } catch (err) {
      handleError(err);
    }
  };

  const recoverPassword = async (email: string) => {
    if (!email?.trim()) return;
    try {
      await api.post('/auth/recover-password', { email });
      recoverEmail.value = email;
      router.push('/recover-password/verification');
    } catch (err) {
      handleError(err);
    }
  };

  const validateToken = async (token: string) => {
    if (!token?.trim()) return;
    try {
      await api.post('/auth/validate-token', { token, email: recoverEmail.value });
      snackbar.success(t('snackbar.tokenValidated'));
      router.push('/recover-password/new');
    } catch (err) {
      handleError(err);
    }
  };

  const setNewPassword = async (password: string, passwordConfirmation: string) => {
    if (!password?.trim() || !passwordConfirmation?.trim()) return;
    try {
      await api.post('/auth/reset-password', {
        email: recoverEmail.value,
        password,
        password_confirmation: passwordConfirmation
      });
      snackbar.success(t('snackbar.passwordChanged'));
      router.push('/login');
    } catch (err) {
      handleError(err);
    }
  };

  return {
    user,
    isAuthenticated,
    resetState,
    initializeAuth,
    register,
    login,
    logout,
    authenticateProvider,
    recoverPassword,
    validateToken,
    setNewPassword,
    handleAuthMessage
  };
});
