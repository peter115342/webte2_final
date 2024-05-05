<template>
  <v-container class="fill-height">
    <v-responsive class="align-center text-center fill-height logo-container">
      <v-img height="300" src="@/assets/app_logo.png" style="margin-bottom: 75px;"  />
     <!-- <div class="py-14" />-->

      <v-form @submit.prevent="submitCode">
        <v-text-field
          v-model="code"
          label="Enter Code"
          outlined
          required
        ></v-text-field>
        <v-btn type="submit" color="primary">Submit</v-btn>
      </v-form>
    </v-responsive>
  </v-container>
</template>

<script setup>
  import { useRouter } from 'vue-router';
  import { ref } from 'vue';

  const router = useRouter();
  const code = ref('');

  const submitCode = () => {
    //TODO - Add logic to check if code is valid and navigate to question page
    router.push('/question');
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
