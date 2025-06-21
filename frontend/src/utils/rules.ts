const createRules = (t: Function) => ({
  required: (value: string): boolean | string => {
    if (!value.trim()) return t('rules.requiredField');
    return true;
  },
  email: (value: string): boolean | string => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(value)) return t('rules.invalidEmail');
    return true;
  },
  different: (password1: string, password2: string):boolean | string => {
    if (password1 != password2) return t('rules.differentPasswords');
    return true;
  }
});

export default createRules;
