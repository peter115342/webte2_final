<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="10" md="10" lg="8">
          <v-card>
            <v-card-title class="headline">{{ formMode === 'login' ? 'Login' : 'Register' }}</v-card-title>
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
                    <v-btn type="submit" color="primary">Login</v-btn>
                    <v-btn @click="toggleFormMode">Register</v-btn>
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
                    <v-btn type="submit" color="primary">Register</v-btn>
                    <v-btn @click="toggleFormMode">Back to Login</v-btn>
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
import { defineEmits, ref } from 'vue';
  
  const formMode = ref('login');
  const username = ref('');
  const password = ref('');
  const newUsername = ref('');
  const newPassword = ref('');
  const confirmPassword = ref('');
  const { emit } = defineEmits(['loginSuccess', 'loginError']);
  
  const login = async () => {
    try {
      const response = await axios.post('/user/login', {
        username: username.value,
        password: password.value
      });
      if (response.data) {
        emit('loginSuccess');
      } else {
        emit('loginError');
      }
    } catch (error) {
      emit('loginError');
    }
  };
  
  const register = async () => {
    if (newPassword.value !== confirmPassword.value) {
      // Passwords don't match, handle error
      return;
    }
    try {
      const response = await axios.post('/user/register', {
        username: newUsername.value,
        password: newPassword.value
      });
      if (response.data) {
        // Optionally, you can automatically log in the user after registration
        // or show a success message and let them manually log in
      } else {
        // Handle registration error
      }
    } catch (error) {
      // Handle registration error
    }
  };
  
  const toggleFormMode = () => {
    formMode.value = formMode.value === 'login' ? 'register' : 'login';
  };
  </script>
  