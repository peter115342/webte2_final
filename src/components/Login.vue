<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="10" md="10" lg="8">
          <v-card>
            <v-card-title class="headline">{{ formMode === 'login' ?  $t('login')  :  $t('register')  }}</v-card-title>
            <v-card-text>
              <template v-if="formMode === 'login'">
                <v-form @submit.prevent="login"> 
                  <v-text-field
                    v-model="username"
                    label="Username"
                    outlined
                    required
                  ></v-text-field>
                  <v-text-field
                    v-model="password"
                    label="Password"
                    type="password"
                    outlined
                    required
                  ></v-text-field>
                  <v-btn-group>
                    <v-btn type="submit" color="primary">{{ $t('login') }}</v-btn>
                    <v-btn @click="toggleFormMode">{{ $t('register') }}</v-btn>
                  </v-btn-group>
                </v-form>
              </template>
              <template v-else>
                <v-form @submit.prevent="register">
                  <v-text-field
                    v-model="newUsername"
                    label="New Username"
                    outlined
                    required
                  ></v-text-field>
                  <v-text-field
                    v-model="newPassword"
                    label="New Password"
                    type="password"
                    outlined
                    required
                  ></v-text-field>
                  <v-text-field
                    v-model="confirmPassword"
                    label="Confirm Password"
                    type="password"
                    outlined
                    required
                  ></v-text-field>
                  <v-btn-group>
                    <v-btn type="submit" color="primary">{{ $t('register') }}</v-btn>
                    <v-btn @click="toggleFormMode">{{ $t('backToLogin') }}</v-btn>
                  </v-btn-group>
                </v-form>
              </template>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>

<script setup>
import axios from 'axios';
import { ref, defineEmits } from 'vue';

const formMode = ref('login');
const username = ref('');
const password = ref('');
const newUsername = ref('');
const newPassword = ref('');
const confirmPassword = ref('');

const emit = defineEmits(['loginSuccess', 'loginError']);

const login = async () => {
  try {
    const response = await axios.post('https://node19.webte.fei.stuba.sk/nemecko/api/user/login', {
      username: username.value,
      password: password.value
    });
    if (response.data && response.data.access_token) {
      document.cookie = `access_token=${response.data.access_token}; path=/`;
      emit('loginSuccess');
    } else {
      console.error('Login failed. Please check your credentials.');
      emit('loginError');
    }
  } catch (error) {
    console.error('An error occurred during login:', error);
    emit('loginError');
  }
};

const register = async () => {
  if (newPassword.value !== confirmPassword.value) {
    console.error("Passwords don't match.");
    return;
  }
  try {
    const response = await axios.post('https://node19.webte.fei.stuba.sk/nemecko/api/user', {
      username: newUsername.value,
      password: newPassword.value
    });
    if (response.data) {
      // Handle successful registration
    } else {
      console.error('Failed to register user.');
    }
  } catch (error) {
    console.error('Failed to register user.');
  }
};

const toggleFormMode = () => {
  formMode.value = formMode.value === 'login' ? 'register' : 'login';
};
</script>
