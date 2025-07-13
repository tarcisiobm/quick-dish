import { defineStore } from 'pinia';
import { useI18n } from 'vue-i18n';
import { useSnackbarStore } from './snackbar';

interface ErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

export const useErrorStore = defineStore('error', () => {
  const snackbarStore = useSnackbarStore();
  const { t } = useI18n();

  async function handle(error: any): Promise<void> {
    const errorData: ErrorResponse = error?.response?.data;

    if (errorData?.errors) {
      const messages = Object.values(errorData.errors).flat();
      for (const message of messages) {
        snackbarStore.error(message);
        await new Promise((resolve) => setTimeout(resolve, 100));
      }
      return;
    }

    if (errorData?.message) {
      snackbarStore.error(errorData.message);
      return;
    }

    snackbarStore.error(t('snackbar.unexpectedError'));
    console.error('Unhandled API Error:', error);
  }

  return {
    handle
  };
});
