import { defineStore } from 'pinia';

export type SnackbarLocation = 'top' | 'bottom' | 'left' | 'right' | 'center' | 'top left' | 'top right' | 'bottom left' | 'bottom right';

const defaultLocation: SnackbarLocation = 'bottom right';

export const useSnackbarStore = defineStore('snackbar', {
  state: () => ({
    message: '',
    color: 'success',
    timeout: 2000,
    status: false,
    location: defaultLocation as SnackbarLocation,
  }),

  getters: {
    isActive: (state) => state.status,
  },

  actions: {
    show(message: string, color: string = 'black', timeout: number = 2000, location: SnackbarLocation = defaultLocation) {
      this.message = message;
      this.color = color;
      this.timeout = timeout;
      this.location = location;
      this.status = true;
    },

    success(message: string, timeout: number = 2000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'success', timeout, location);
    },

    error(message: string, timeout: number = 2000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'error', timeout, location);
    },

    warning(message: string, timeout: number = 2000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'warning', timeout, location);
    },

    info(message: string, timeout: number = 2000, location: SnackbarLocation = defaultLocation) {
      this.show(message, 'info', timeout, location);
    },

    hide() {
      this.status = false;
    },

    clear() {
      this.message = '';
      this.status = false;
    }
  },
});
