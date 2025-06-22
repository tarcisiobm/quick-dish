import type { LocaleType } from '@/locales/i18n';
import type { LocaleNamespace } from '@/locales/i18n';

// Api Exceptions

const api: LocaleType = {
  theEmailHasAlreadyBeenTaken : 'The email has already been taken.',
  invalidLoginCredentials : 'Invalid login credentials.',
  emailNotVerified : 'Email not verified.',
  errorGeneratingRedirectLink: 'Error generationg redirect link.',
  emailAlreadyVerified: 'Email already verified.',
  validationError: 'Validation error.'
}

// Snackbar

const snackbar: LocaleType = {
  pleaseFillOutAllRequiredFields: "Please fill out all required fields.",
  accountCreatedSuccessfully: "Account created successfully.",
  loginSuccessful: "Logged in successfully.",
  logoutSuccessful: "Logged out successfully.",
  sessionExpired: "Session expired. Please log in again.",
  tokenValidated: "Token validated successfully.",
  passwordChanged: "Password changed successfully."
}

// Rules

const rules: LocaleType = {
  requiredField: "Required field.",
  invalidEmail: "Invalid email.",
  differentPasswords: "Different passwords."
}

// Fields

const fields: LocaleType = {
  firstName: 'First Name',
  lastName: 'Last Name',
  phone: 'Phone number',
  email: 'Email',
  password: 'Password',
  reEnterPassword: 'Re-enter password',
  submit: 'Submit'
}

// AppBar

const appBar: LocaleType = {
  home: 'Home',
  about: 'About',
  contact: 'Contact',
  reviews: 'Reviews',
  editProfile: 'Edit profile',
  logout: 'Logout'
}

// Sign-up

const signUp: LocaleType = {
  signUp: 'Sign-up',
  createAnAccount: 'Create an account',
  agree: 'I agree to the',
  terms: 'terms & conditions',
  orRegisterWith: 'or register with',
}

// Log-in

const login: LocaleType = {
  login: 'Login',
  welcomeBack: 'Welcome back',
  rememberMe: 'Remember me',
  forgotPassword: 'Forgot your password?',
  orLoginWith: 'Or login with',
}

const recover: LocaleType = {
  recoverPassword: 'Recover Password',
  enterEmail: 'Enter your email to reset your password',
  backLogin: 'Back to login',
  sendEmail: 'Send Email',
  enterVerificationCode:  'Enter verification code',
  sentCodeEmail: 'We’ve sent a 6-digit code to your email',
  verifyCode: 'Verify code',
  createNewPassword: 'Create a new password',
  resetAndContinue: 'Reset and continue',
}

const en:LocaleNamespace = {
  api: api,
  snackbar: snackbar,
  rules: rules,
  signUp: signUp,
  login: login,
  recover: recover,
  fields: fields,
  appBar: appBar,
};

export default en;
