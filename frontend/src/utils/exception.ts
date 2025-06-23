import { AxiosError } from 'axios';

const createExceptions = (snackbar: any, t: Function) => ({
  show: (error: unknown) => {
    if (error instanceof AxiosError) {
      const i18nKey = error.response?.data?.i18n;
      if (i18nKey) snackbar.error(t(i18nKey));
    }
  }
});

export default createExceptions;
