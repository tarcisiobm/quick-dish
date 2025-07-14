import { defineStore } from 'pinia';

export type SnackbarLocation = 'top' | 'bottom' | 'left' | 'right' | 'center' | 'top left' | 'top right' | 'bottom left' | 'bottom right';

const defaultLocation: SnackbarLocation = 'bottom right';

interface Snackbar {
  id: number;
  message: string;
  color: string;
  timeout: number;
  location: SnackbarLocation;
  status: boolean;
}

export const useSnackbarStore = defineStore('snackbar', {
  state: () => ({
    snackbars: [] as Snackbar[]
  }),

  actions: {
    show(message: string, color: string = 'black', timeout: number = 4000, location: SnackbarLocation = defaultLocation) {
      const id = Date.now() + Math.random();
      const newSnackbar: Snackbar = {
        id,
        message,
        color,
        timeout,
        location,
        status: true
      };

      this.snackbars.push(newSnackbar);

      setTimeout(() => this.hide(id), timeout);
    },

    hide(id: number) {
      const index = this.snackbars.findIndex((s) => s.id === id);
      if (index > -1) {
        this.snackbars.splice(index, 1);
      }
    },

    success(message: string, timeout: number = 4000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'success', timeout, location);
    },

    error(message: string, timeout: number = 4000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'error', timeout, location);
    },

    warning(message: string, timeout: number = 4000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'warning', timeout, location);
    },

    info(message: string, timeout: number = 4000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'info', timeout, location);
    }
  }
});
