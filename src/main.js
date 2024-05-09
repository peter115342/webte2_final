// Plugins
import { registerPlugins } from '@/plugins'

// Components
import App from './App.vue'

// Composables
import { createApp } from 'vue'
import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import sk from './locales/sk.json'
import vuetify from './plugins/vuetify'

const i18n = createI18n({
  legacy: false,
  locale: 'en', 
  messages: {
    en: en,
    sk: sk,
  }
})

const app = createApp(App)

registerPlugins(app)

app.use(vuetify)
app.use(i18n)

app.mount('#app')
