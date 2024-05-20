<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title clickable" @click="$router.push({ name: 'Homepage' })">{{ $t('appTitle') }}</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="!hasAccessToken">
        <v-btn @click="showLoginForm = true" class="login-button elevation-2">{{ $t('login') }}</v-btn>
        <v-dialog v-model="showLoginForm" max-width="500">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-text>
              <Login @loginSuccess="handleLoginSuccess" @registerSuccess="handleRegisterSuccess" @loginError="handleLoginError" @accessToken="handleAccessToken" @username="saveUsername" @id="saveUserId" @admin="saveAdmin" />
            </v-card-text>
          </v-card>
        </v-dialog>
      </template>
      <template v-else>
        <v-btn icon @click="showUserModal = !showUserModal">
          <v-icon>mdi-account-circle</v-icon>
        </v-btn>
        <v-dialog v-model="showUserModal" max-width="300" persistent @click:outside="closeUserModal" content-class="user-modal">
          <template v-slot:activator="{ on }"></template>
          <v-card>
            <v-card-title>{{ $t('loggedInAs') }} {{ getUsernameFromLocalStorage() }}</v-card-title>
            <v-card-actions>
              <v-btn class="logout-button" @click="logout">{{ $t('logout') }}</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </template>
      <v-btn icon @click="toggleLanguage">
        <v-img :src="currentFlag" alt="Language Flag" width="24" height="24" />
      </v-btn>
      <v-btn icon @click="$router.push({ name: 'Manual' })">
        <v-icon>mdi-book-open</v-icon>
      </v-btn>
      <!-- Drawer icon button -->
      <v-btn v-if="hasAccessToken" icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-app-bar>

    <template v-if="hasAccessToken">
      <v-navigation-drawer v-model="drawer" app>
        <v-list dense nav>
          <v-list-item prepend-icon="mdi-plus" :title="$t('addQuestion')" @click="$router.push({ name: 'AddQuestion' })"></v-list-item>
          <v-list-item prepend-icon="mdi-pencil" :title="$t('editQuestion')" @click="$router.push({ name: 'EditCopyDelete' })"></v-list-item>
          <v-list-item prepend-icon="mdi-lock" :title="$t('changePassword')" value="changePassword" @click="showChangePasswordForm = true"></v-list-item>
        </v-list>
      </v-navigation-drawer>
      
      <v-dialog v-model="showChangePasswordForm" max-width="500">
        <v-card>
          <v-card-title>{{ $t('changePassword') }}</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="changePassword">
              <v-text-field v-model="newPassword" :label="$t('newPassword')" type="password"></v-text-field>
              <v-text-field v-model="confirmNewPassword" :label="$t('confirmNewPassword')" type="password"></v-text-field>
              <v-btn type="submit" color="primary">{{ $t('changePassword') }}</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
        <v-alert v-if="newPassword !== confirmNewPassword" type="error">{{ $t('Hesla sa nezhoduju') }}</v-alert>
      </v-dialog>
    </template>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import Login from './components/Login.vue';
import axios from 'axios';

const { locale } = useI18n();

const showLoginForm = ref(false);
const showUserModal = ref(false);
const drawer = ref(false);
const showChangePasswordForm = ref(false);
const cookieValue = ref(document.cookie);
const newPassword = ref('');
const confirmNewPassword = ref('');

const changePassword = async () => {
  try {
    const userId = localStorage.getItem('userId');
    const accessToken = cookieValue.value.split('=')[1];
    const url = `https://node79.webte.fei.stuba.sk/final/api/user/${userId}`;
    if (newPassword.value !== confirmNewPassword.value) {
      alert('New password and confirm password do not match');
      return;
    }
    const response = await axios.put(url, {
      username: localStorage.getItem('username'),
      access_token: accessToken,
      password: newPassword.value,
      administrator: localStorage.getItem('administrator')
    });
    if (response.status === 200) {
      console.log('Password changed successfully');
    } else {
      console.error('Failed to change password');
    }
  } catch (error) {
    console.error('An error occurred while changing password:', error);
  }
};

const saveUserId = (id) => {
  localStorage.setItem('userId', id);
};

const handleLoginSuccess = () => {
  showLoginForm.value = false;
  handleAccessToken(cookieValue.value.split('=')[1]);
  window.location.reload();
};

const saveUsername = (username) => {
  localStorage.setItem('username', username);
};

const saveAdmin = (admin) => {
  localStorage.setItem('admin', admin);
};

const getUsernameFromLocalStorage = () => {
  return localStorage.getItem('username');
};

const handleLoginError = () => {};

const handleRegisterSuccess = () => {
  setTimeout(() => {
    showLoginForm.value = false;
  }, 2500);
};

const handleAccessToken = (accessToken) => {
  if (isValidAccessToken(accessToken)) {
    document.cookie = `access_token=${accessToken}; path=/;`;
  }
};

const toggleLanguage = async () => {
  locale.value = locale.value === 'en' ? 'sk' : 'en';
};

import skFlag from './assets/slovakia.png';
import enFlag from './assets/united-kingdom.png';

const currentFlag = ref('');
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? enFlag : skFlag;
});
toggleLanguage();

const closeUserModal = () => {
  showUserModal.value = false;
};

watch(() => document.cookie, (newValue) => {
  cookieValue.value = newValue;
});

const isValidAccessToken = (access_token) => {
  return access_token && /^[a-zA-Z0-9]{32}$/.test(access_token);
};

const logout = () => {
  document.cookie = 'access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
  localStorage.removeItem('username');
  localStorage.removeItem('userId');
  localStorage.removeItem('admin');
  window.location.reload();
};

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
  position: absolute !important;
  top: 0 !important;
  right: 0;
  z-index: 1000;
}
.clickable {
  cursor: pointer;
}
</style>
