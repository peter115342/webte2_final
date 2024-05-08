<template>
    <div>
      <form @submit.prevent="login">
        <label for="username">Username:</label>
        <input type="text" v-model="username" id="username" required>
        <label for="password">Password:</label>
        <input type="password" v-model="password" id="password" required>
        <button type="submit">Login</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import axios from 'axios';
import { defineEmits, ref } from 'vue';
  
  const username = ref('');
  const password = ref('');
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
  </script>
  