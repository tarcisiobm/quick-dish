const createRules = (t: Function) => ({
  required: (value: string): boolean | string => {
    if (!value.trim()) return t('requiredField');
    return true;
  },
  email: (value: string): boolean | string => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(value)) return t('invalidEmail');
    return true;
  },
  different: (password1: string, password2: string):boolean | string => {
    if (password1 != password2) return t('differentPasswords');
    return true;
  }
});

export default createRules;
