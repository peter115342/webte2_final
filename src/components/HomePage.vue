<template>
  <v-container class="fill-height">
    <v-responsive class="align-center text-center fill-height logo-container">
      <v-img height="300" src="@/assets/app_logo.png" style="margin-bottom: 75px;"  />
     <!-- <div class="py-14" />-->

      <v-form @submit.prevent="submitCode">
        <v-text-field
          v-model="code"
          :label="$t('enterCode')"
          outlined
          required
        ></v-text-field>
        <v-btn type="submit" color="primary">{{ $t('submit') }}</v-btn>
      </v-form>
    </v-responsive>
  </v-container>
</template>

<script setup>
  import { ref } from 'vue';
import { useRouter } from 'vue-router';

  const router = useRouter();
  const code = ref('');

  const submitCode = () => {
    // Odoslanie kódu cez router
    router.push({ name: 'Question', params: { code: code.value }});
  };
</script>

<style scoped>
  .fill-height {
    height: 100%;
  }
  
  .logo-container {
    margin-bottom: 50px;
  }
</style>
