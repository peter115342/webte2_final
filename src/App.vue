<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title">{{ $t('appTitle') }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="!isLoggedIn">
        <v-btn @click="showLoginForm = true">{{ $t('login') }}</v-btn>
        <v-dialog v-model="showLoginForm" max-width="500">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-text>
              <Login @loginSuccess="handleLoginSuccess" @loginError="handleLoginError" />
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
      <v-icon v-if="isLoggedIn">mdi-account-circle</v-icon>
      <v-btn icon @click="toggleLanguage">
        <v-img :src="currentFlag" alt="Language Flag" width="24" height="24" />
      </v-btn>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import Login from './components/Login.vue';

const { locale, t } = useI18n();

const isLoggedIn = ref(false);
const showLoginForm = ref(false);

const handleLoginSuccess = () => {
  isLoggedIn.value = true;
  showLoginForm.value = false;
};

const handleLoginError = () => {
  // Handle login error, show message or take appropriate action
};

const toggleLanguage = async () => {
  locale.value = locale.value === 'en' ? 'sk' : 'en';
};

// Import translations for language flags
import skFlag from './assets/slovakia.png';
import enFlag from './assets/united-kingdom.png';

let currentFlag = ref('');
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? enFlag : skFlag;
});

// Load initial language flag URLs
toggleLanguage();
</script>

<style scoped>
.title {
  color: var(--v-theme-on-surface-variant);
}
</style>
