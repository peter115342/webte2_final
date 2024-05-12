<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="10" md="10" lg="8">
        <v-card class="wider-card">
          <v-card-title class="headline">{{ formMode === 'login' ? $t('login') : $t('register') }}</v-card-title>
          <v-card-text>
            <template v-if="formMode === 'login'">
              <v-form @submit.prevent="login"> 
                <v-text-field
                  v-model="username"
                  :label="$t('username')"
                  outlined
                  required
                ></v-text-field>
                <v-text-field
                  v-model="password"
                  :label="$t('password')"
                  type="password"
                  outlined
                  required
                ></v-text-field>
                <v-btn-group>
                  <v-btn type="submit" color="primary">{{ $t('login') }}</v-btn>
                  <v-btn @click="toggleFormMode" color="background">{{ $t('register') }}</v-btn>
                </v-btn-group>
              </v-form>
            </template>
            <template v-else>
              <v-form @submit.prevent="registerUser">
                <v-text-field
                  v-model="newUsername"
                  :label="$t('newUsername')"
                  outlined
                  required
                ></v-text-field>
                <v-text-field
                  v-model="newPassword"
                  :label="$t('newPassword')"
                  type="password"
                  outlined
                  required
                ></v-text-field>
                <v-text-field
                  v-model="confirmPassword"
                  :label="$t('confirmPassword')"
                  type="password"
                  outlined
                  required
                ></v-text-field>
                <v-btn-group>
                  <v-btn type="submit" color="primary">{{ $t('register') }}</v-btn>
                  <v-btn @click="toggleFormMode" color="background">{{ $t('backToLogin') }}</v-btn>
                </v-btn-group>
              </v-form>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="snackbar.timeout" :top="snackbar.top">
      {{ snackbar.text }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { defineEmits, ref } from 'vue';

const formMode = ref('login');
const username = ref('');
const password = ref('');
const newUsername = ref('');
const newPassword = ref('');
const confirmPassword = ref('');

const emit = defineEmits(['loginSuccess', 'loginError', 'accessToken']);

const login = async () => {
  // Check if username or password is empty
  if (!username.value || !password.value) {
    showSnackbar('Please enter username and password.', 'error');
    return;
  }
  
  try {
    const response = await axios.post('https://node79.webte.fei.stuba.sk/final/api/user/login', {
      username: username.value,
      password: password.value
    });
    if (response.data && response.data.access_token && isValidAccessToken(response.data.access_token)) {
      emit('accessToken', response.data.access_token);
      emit('username', username.value);
      emit('loginSuccess');
    } else {
      console.error('Login failed. Please check your credentials.');
      emit('loginError');
      showSnackbar('Login failed. Please check your credentials.', 'error');
    }
  } catch (error) {
    console.error('An error occurred during login:', error);
    emit('loginError');
    showSnackbar('Incorrect credentials.', 'error');
  }
};

const registerUser = async () => {
  // Check if new username, new password, or confirm password is empty
  if (!newUsername.value || !newPassword.value || !confirmPassword.value) {
    showSnackbar('Please fill in all fields.', 'error');
    return;
  }
  
  if (newPassword.value !== confirmPassword.value) {
    console.error("Passwords don't match.");
    showSnackbar("Passwords don't match.", 'error');
    return;
  }
  
  try {
    const response = await axios.post('https://node79.webte.fei.stuba.sk/final/api/user', {
      username: newUsername.value,
      password: newPassword.value,
      administrator: false
    });
    if (response.data) {
      showSnackbar('User created successfully, proceed with login', 'success');
      emit('registerSuccess');
    } else {
      console.error('Failed to register user.');
      showSnackbar('Failed to register user.', 'error');
    }
  } catch (error) {
    console.error('Failed to register user:', error);
    showSnackbar('Failed to register user.', 'error');
  }
};

const toggleFormMode = () => {
  formMode.value = formMode.value === 'login' ? 'register' : 'login';
};

const snackbar = ref({
  show: false,
  text: '',
  color: '',
  timeout: 6000,
  top: true
});

const showSnackbar = (text, color) => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const isValidAccessToken = (accessToken) => {
  return typeof accessToken === 'string' && accessToken.length === 32 && /^[a-zA-Z0-9]+$/.test(accessToken);
};

</script>

<style scoped>
.wider-card {
  width: 100%;
}
</style>
