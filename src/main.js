// Plugins
import { registerPlugins } from '@/plugins'

// Components
import App from './App.vue'

// Composables
import { createApp } from 'vue'
import vuetify from './plugins/vuetify'
import { createI18n } from 'vue-i18n'

const i18n = createI18n({
  legacy: false,
  locale: 'en', 
  messages: {
    en: () => import('./locales/en.json'),
    sk: () => import('./locales/sk.json')
  }
})

const app = createApp(App)

registerPlugins(app)

app.use(vuetify)
app.use(i18n)

app.mount('#app')
