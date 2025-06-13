import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import vuetify from './plugins/vuetify'

console.log('Iniciando aplicação...')

const app = createApp(App)

console.log('App criado:', app)
console.log('Router:', router)
console.log('Vuetify:', vuetify)

app.use(createPinia())
app.use(router)
app.use(vuetify)

console.log('Plugins registrados, montando app...')

app.mount('#app')

console.log('App montado!')
