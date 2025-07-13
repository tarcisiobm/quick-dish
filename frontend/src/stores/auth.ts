import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useSnackbarStore } from './snackbar';
import { useErrorStore } from './error';
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
  remember?: boolean;
}

export interface RegisterData {
  name: string;
  email: string;
  phone: string;
  password: string;
  password_confirmation: string;
}

export const useAuthStore = defineStore('auth', () => {
  const { t } = useI18n();
  const snackbar = useSnackbarStore();
  const errorStore = useErrorStore();

  const user = ref<AuthData | null>(null);
  const recoverEmail = ref('');
  const authWindow = ref<Window | null>(null);

  const isAuthenticated = computed(() => Boolean(user.value));

  const resetState = (): void => {
    user.value = null;
    recoverEmail.value = '';
  };

  const setUser = (userData: AuthData): void => {
    user.value = userData;
  };

  const getUserSession = async (): Promise<void> => {
    try {
      const response = await api.get('/auth/user');
      setUser(response.data);
    } catch (error) {
      resetState();
    }
  };

  const initializeAuth = async (): Promise<void> => {
    try {
      await getUserSession();
    } catch (error) {
      resetState();
    }
  };

  const register = async (data: RegisterData): Promise<void> => {
    try {
      const response = await api.post('/auth/sign-up', data);
      snackbar.success(response.data.message);
      router.push('/verify-email');
    } catch (error) {
      errorStore.handle(error);
    }
  };

  const login = async (credentials: LoginData): Promise<void> => {
    try {
      const response = await api.post('/auth/login', credentials);
      setUser(response.data.user);
      snackbar.success(response.data.message);
      router.push('/');
    } catch (error) {
      errorStore.handle(error);
    }
  };

  const logout = async (): Promise<void> => {
    try {
      const response = await api.post('/auth/logout');
      snackbar.success(response.data.message);
    } catch (error) {
      errorStore.handle(error);
    } finally {
      resetState();
      router.push('/');
    }
  };

  const closeAuthWindow = (): void => {
    if (!authWindow.value) return;
    authWindow.value.close();
    authWindow.value = null;
    window.removeEventListener('message', handleAuthMessage);
  };

  const handleAuthMessage = (event: MessageEvent): void => {
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

  const authenticateProvider = async (provider: string): Promise<void> => {
    try {
      const response = await api.get(`/auth/${provider}/redirect`);
      authWindow.value = createWindow().open(response.data.redirect_url, 'Authentication');
      window.addEventListener('message', handleAuthMessage);
    } catch (error) {
      errorStore.handle(error);
    }
  };

  const recoverPassword = async (email: string): Promise<void> => {
    if (!email?.trim()) return;

    try {
      const response = await api.post('/auth/recover-password', { email });
      recoverEmail.value = email;
      snackbar.success(response.data.message);
      router.push('/recover-password/verification');
    } catch (error) {
      errorStore.handle(error);
    }
  };

  const validateToken = async (token: string): Promise<void> => {
    if (!token?.trim()) return;

    try {
      const response = await api.post('/auth/validate-token', {
        token,
        email: recoverEmail.value
      });
      snackbar.success(response.data.message);
      router.push('/recover-password/new');
    } catch (error) {
      errorStore.handle(error);
    }
  };

  const setNewPassword = async (password: string, passwordConfirmation: string): Promise<void> => {
    if (!password?.trim() || !passwordConfirmation?.trim()) return;

    try {
      const response = await api.post('/auth/reset-password', {
        email: recoverEmail.value,
        password,
        password_confirmation: passwordConfirmation
      });
      snackbar.success(response.data.message);
      router.push('/login');
    } catch (error) {
      errorStore.handle(error);
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
