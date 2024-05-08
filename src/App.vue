<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title">PresentFlow</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="!isLoggedIn">
        <v-btn @click="showLoginForm = true">{{ $t('login') }}</v-btn>
        <v-dialog v-model="showLoginForm" max-width="500">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-title>Login</v-card-title>
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

const { locale } = useI18n(); 

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

let englishFlagUrl = '';
let slovakFlagUrl = '';

toggleLanguage();

const currentFlag = ref('');
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? englishFlagUrl : slovakFlagUrl;
});
</script>

<style scoped>
.title {
  color: var(--v-theme-on-surface-variant);
}
</style>
