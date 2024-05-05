<template>
  <v-app>
    <v-app-bar app color="primary">
      <v-toolbar-title class="title">PresentFlow</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn v-if="!isLoggedIn" @click="login">{{ $t('login') }}</v-btn>
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
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n(); // useI18n should be called within the setup function

const isLoggedIn = ref(false);
const login = () => {
  isLoggedIn.value = true;
};

let englishFlagUrl = '';
let slovakFlagUrl = '';

const toggleLanguage = async () => {
  locale.value = locale.value === 'en' ? 'sk' : 'en';
  // Load the flag images
  if (locale.value === 'en') {
    englishFlagUrl = (await import('@/assets/united-kingdom.png')).default;
  } else {
    slovakFlagUrl = (await import('@/assets/slovakia.png')).default;
  }
};

// Initial loading of flag images and setting the initial flag URL
toggleLanguage();

// Computed property to dynamically get the current flag URL
const currentFlag = ref('');
import { watch } from 'vue';
watch(locale, () => {
  currentFlag.value = locale.value === 'en' ? englishFlagUrl : slovakFlagUrl;
});

</script>

<style scoped>
.title {
  color: var(--v-theme-on-surface-variant);
}
</style>
