import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import vuetify from './plugins/vuetify';
import { createI18n } from 'vue-i18n';
import locales from '@/locales/i18n';
import './styles/index.scss'

const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages: locales
});

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.use(i18n);
app.use(vuetify);
app.mount('#app');
