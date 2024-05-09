<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title">{{ $t('appTitle') }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="!isLoggedIn">
        <v-btn @click="showLoginForm = true" class="login-button elevation-2">{{ $t('login') }}</v-btn>
        <v-dialog v-model="showLoginForm" max-width="500">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-text>
              <Login @loginSuccess="handleLoginSuccess" @loginError="handleLoginError" />
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
      <template v-else>
        <v-btn icon @click="showUserModal = !showUserModal">
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
        <v-dialog v-model="showUserModal" max-width="300" persistent @click:outside="closeUserModal">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-title>{{ $t('loggedInAs') }} {{ loggedInUser }}</v-card-title>
            <v-card-actions>
              <v-btn class="logout-button" @click="logout">{{ $t('logout') }}</v-btn>            </v-card-actions>
          </v-card>
        </v-dialog>
      </template>
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
import { ref, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import Login from './components/Login.vue';
import { useAppStore } from './store/app';


const { locale } = useI18n(); 
const store = useAppStore();

const isLoggedIn = ref(document.cookie.includes('isLoggedIn=true'));
const showLoginForm = ref(false);
const showUserModal = ref(false);

const loggedInUser = ref('');

const handleLoginSuccess = () => {
  isLoggedIn.value = true;
  showLoginForm.value = false;
  document.cookie = 'isLoggedIn=true; expires=Thu, 01 Jan 2026 00:00:00 UTC; path=/;';
};

const handleLoginError = () => {
  // Handle login error, show message or take appropriate action
};

// Function to toggle between languages
const toggleLanguage = async () => {
  locale.value = locale.value === 'en' ? 'sk' : 'en';
};

// Import translations for language flags
import skFlag from './assets/slovakia.png';
import enFlag from './assets/united-kingdom.png';

let currentFlag = ref('');
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? enFlag : skFlag;
// Function to handle logout
const logout = () => {
  isLoggedIn.value = false;
  // Clear cookie to indicate user is logged out
  document.cookie = 'isLoggedIn=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
};

// Close the user modal when clicked outside of it
const closeUserModal = () => {
  showUserModal.value = false;
};

// Watch for changes in login status and access token
watch(() => [isLoggedIn.value, store.accessToken], ([newValue, accessToken]) => {
  if (newValue && accessToken) {
    // Check if access token is valid (replace with your validation logic)
    if (!isValidAccessToken(accessToken)) {
      logout();
    }
  }
});

// Function to validate access token (Replace this with your actual validation logic)
const isValidAccessToken = (accessToken) => {
  // Example validation logic
  return accessToken.length === 32 && /^[a-z0-9]+$/i.test(accessToken);
};

// Add click event listener to close modal when clicked outside of it
onMounted(() => {
  document.body.addEventListener('click', (event) => {
    const modal = document.querySelector('.v-dialog__content--active');
    if (modal && !modal.contains(event.target)) {
      closeUserModal();
    }
  });
});

// Load initial language flag URLs
toggleLanguage();
</script>

<style scoped>
.title {
  color: var(--v-theme-on-surface-variant);
}
.logout-button {
  background-color: #f9de82;
}
.login-button {
  background-color: #ffd166;
}
</style>
