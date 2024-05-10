<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title">{{ $t('appTitle') }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="!hasAccessToken">
        <v-btn @click="showLoginForm = true" class="login-button elevation-2">{{ $t('login') }}</v-btn>
        <v-dialog v-model="showLoginForm" max-width="500">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-text>
              <!-- Login component emits accessToken -->
              <Login @loginSuccess="handleLoginSuccess" @registerSuccess="handleRegisterSuccess" @loginError="handleLoginError" @accessToken="handleAccessToken" @username = "saveUsername" />
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
      <template v-else>
        <v-btn icon @click="showUserModal = !showUserModal">
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
        <v-dialog v-model="showUserModal" max-width="300"  persistent @click:outside="closeUserModal" content-class="user-modal">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-title>{{ $t('loggedInAs') }} {{ getUsernameFromLocalStorage()}}</v-card-title>
            <v-card-actions>
              <v-btn    class="logout-button" @click="logout">{{ $t('logout') }}</v-btn>
            </v-card-actions>
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
import { ref, watch, onMounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Login from './components/Login.vue';

const { locale } = useI18n();

// Define reactive variables
const showLoginForm = ref(false);
const showUserModal = ref(false);
const cookieValue = ref(document.cookie);

// Handle successful login
const handleLoginSuccess = () => {
  showLoginForm.value = false;
  handleAccessToken(cookieValue.value.split('=')[1]);
  window.location.reload();

};
const saveUsername = (username) => {
  localStorage.setItem('username', username);
};
const getUsernameFromLocalStorage = () => {
  return localStorage.getItem('username');
};
// Handle login error
const handleLoginError = () => {
  // Handle login error, show message or take appropriate action
};

// Handle successful user registration
const handleRegisterSuccess = () => {
  setTimeout(() => {
    showLoginForm.value = false;
  }, 2500);
};

// Handle received access token
const handleAccessToken = (accessToken) => {
  if (isValidAccessToken(accessToken)) {
    // Set the access token cookie
    document.cookie = `access_token=${accessToken}; path=/;`;
  }
};

// Function to toggle between languages
const toggleLanguage = async () => {
  locale.value = locale.value === 'en' ? 'sk' : 'en';
};

// Import translations for language flags
import skFlag from './assets/slovakia.png';
import enFlag from './assets/united-kingdom.png';

// Define current language flag
const currentFlag = ref('');
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? enFlag : skFlag;
});
toggleLanguage(); // Toggle language on startup

// Close the user modal when clicked outside
const closeUserModal = () => {
  showUserModal.value = false;
};

// Watch for changes in document.cookie
watch(() => document.cookie, (newValue) => {
  cookieValue.value = newValue;
});

// Check if the access token is valid
const isValidAccessToken = (access_token) => {
  return access_token && /^[a-zA-Z0-9]{32}$/.test(access_token);
};

// Logout function
const logout = () => {
  document.cookie = 'access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
  localStorage.removeItem('username');
  window.location.reload();
};

// Close user modal when mounted
onMounted(() => {
  document.body.addEventListener('click', (event) => {
    const modal = document.querySelector('.v-dialog__content--active');
    if (modal && !modal.contains(event.target)) {
      closeUserModal();
    }
  });
});

const hasAccessToken = computed(() => {
  return isValidAccessToken(cookieValue.value.split('=')[1]);
});
</script>

<style scoped>
.title {
  color: var(--v-theme-on-surface-variant);
}
.logout-button {
  background-color: #f9de82;
  margin: 0 auto;
}
.login-button {
  background-color: #ffd166;
}

.user-modal .v-dialog {
  position: absolute !important ;
  top: 0 !important;
  right: 0;
  z-index: 1000;
}
</style>
