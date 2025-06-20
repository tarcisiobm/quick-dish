import type { LocaleType } from '@/locales/i18n';

// Api Exceptions

const apiExceptions: LocaleType = {
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
  errorCreatingAccount: "Error creating account.",
  loginSuccessful: "Logged in successfully.",
  loginFailed: "Login failed.",
  logoutSuccessful: "Logged out successfully.",
  invalidCredentials: "Invalid email or password.",
  sessionExpired: "Session expired. Please log in again.",
}

// Rules

const rules: LocaleType = {
  requiredField: "Required field.",
  invalidEmail: "Invalid email.",
  minimumLenghtNotMet: "Minimum length not met.",
  differentPasswords: "Different passwords."
}

// Sign-up

const signUp: LocaleType = {
  signUp: 'Sign-up',
  createAnAccount: 'Create an account',
  firstName: 'First Name',
  lastName: 'Last Name',
  phoneNumber: 'Phone number',
  email: 'Email',
  password: 'Password',
  reEnterPassword: 'Re-enter password',
  agree: 'I agree to the',
  terms: 'terms & conditions',
  register: 'or register with',
}

const profilePicture: LocaleType ={
  profilePicture: 'Profile picture',
  AddPhoto: 'Add a photo so people recognize you',
  DragAndDrop: 'Drag and drop files here',
  clickUpload: 'Click here to upload',
  continueWithoutPhoto: 'Continue without a photo',
}

// Log-in

const login: LocaleType = {
  login: 'Login',
  welcomeBack: 'Welcome back',
  rememberMe: 'Remember me',
  forgotPassword: 'Forgot your password?',
  orLoginWith: 'Or login with',
}

const recoverPassword: LocaleType = {
  recoverPassword: 'Recover Password',
  enterEmail: 'Enter your email to reset your password',
  sendEmail: 'Enter your email to reset your password',
  backLogin: 'Back to login',
}

const createNewPassword: LocaleType = {
  createNewPassword: 'Create a new password',
  resetAndContinue: 'Reset and continue',
  submit: 'Submit'
}

const en: LocaleType = {
...apiExceptions,
...snackbar,
...rules,
...signUp,
...profilePicture,
...login,
...recoverPassword,
...createNewPassword,
}

export default en;
