<template>
  <div class="user-manual">
    <div v-html="manualContent"></div>
    <button @click="downloadPDF">{{ t('downloadPDF') }}</button>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import { ref, watch } from 'vue';

export default {
  setup() {
    const { locale, t } = useI18n();

    const manualContentSk = `
      <h1>Návod na použitie</h1>
      <section>
        <h3>Úvod</h3>
        <p>Tento návod popisuje, ako používať našu aplikáciu.</p>
      </section>
      <section>
        <h3>Roly a oprávnenia</h3>
        <ul>
          <li>
            <strong>Pohľad neprihláseného používateľa:</strong> 
            <ol>
              <li>Na stránku s hlasovacou otázkou sa dá dostať načítaním zverejneného QR kódu, zadaním vstupného kódu na domovskej stránke aplikácie alebo zadaním adresy do prehľadávača v tvare https://nodeXX.webte.fei.stuba.sk/abcde, kde "abcde" predstavuje 5-znakový vstupný kód.</li>
              <li>Po vyplnení hlasovacej otázky je užívateľ presmerovaný na stránku s grafickým zobrazením výsledkov hlasovania na danú otázku.</li>
            </ol>
          </li>
          <li>
            <strong>Pohľad prihláseného používateľa:</strong> 
            <ol>
              <li>Ikona možnosti prihlásenia užívateľa sa nachádza v pravom hornom rohu. Po prihlásení sa zobrazí domovská stránka s pravým bočným menu pre užívateľa. V tomto užívateľskom menu sa nachádzajú tieto možnosti:
                <ol type="a">
                  <li>Pridanie novej otázky</li>
                  <li>Úprava, vymazanie a kopírovanie existujúcich otázok</li>
                  <li>Zmenu vlastného hesla</li>
                </ol>
              </li>
            </ol>
          </li>
          <li>
            <strong>Pohľad administrátora:</strong>
            <p>Administrátor má tú istú funkcionalitu ako prihlásený používateľ. Okrem toho má k dispozícii hlasovacie otázky všetkých prihlásených používateľov a má možnosť vytvárať otázky vo svojom mene alebo aj v mene iného používateľa. Taktiež spravuje všetkých používateľov.</p>
          </li>
        </ul>
      </section>
    `;

    const manualContentEn = `
      <h1>Usage Guide</h1>
      <section>
        <h3>Introduction</h3>
        <p>This guide describes how to use our application.</p>
      </section>
      <section>
        <h3>Roles and Permissions</h3>
        <ul>
          <li>
            <strong>View of Unauthenticated User:</strong> 
            <ol>
              <li>Access the voting question page by scanning a published QR code, entering the entry code on the application's homepage, or entering the address in the browser as https://nodeXX.webte.fei.stuba.sk/abcde, where "abcde" represents the 5-character entry code.</li>
              <li>After filling out the voting question, the user is redirected to a page with a graphical representation of the voting results for that question.</li>
            </ol>
          </li>
          <li>
            <strong>View of Authenticated User:</strong> 
            <ol>
              <li>The user login icon is located in the upper right corner. After logging in, the homepage with the right-side menu for the user is displayed. This user menu contains the following options:
                <ol type="a">
                  <li>Add a new question</li>
                  <li>Edit, delete, and copy existing questions</li>
                  <li>Change own password</li>
                </ol>
              </li>
            </ol>
          </li>
          <li>
            <strong>View of Administrator:</strong>
            <p>The administrator has the same functionality as the authenticated user. Additionally, they have access to the voting questions of all authenticated users and can create questions on their own behalf or on behalf of another user. They also manage all users.</p>
          </li>
        </ul>
      </section>
    `;

    const manualContent = ref('');

    watch(locale, (newLocale) => {
      manualContent.value = newLocale === 'sk' ? manualContentSk : manualContentEn;
    }, { immediate: true });

    const downloadPDF = () => {
      const url = locale.value === 'sk'
        ? 'https://node79.webte.fei.stuba.sk/final/api/pdf/SK'
        : 'https://node79.webte.fei.stuba.sk/final/api/pdf/EN';
      window.location.href = url;
    };

    return {
      manualContent,
      downloadPDF,
      t,
    };
  }
};
</script>

<style scoped>
.user-manual {
  font-family: Arial, sans-serif;
  max-width: 800px;
  margin: 50px auto;
  padding: 50px;
  padding-top: 15px;
  background-color: #f7f7f7;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-manual h1 {
  font-size: 32px;
  color: #333;
  margin-bottom: 20px;
}

.user-manual section {
  margin-bottom: 30px;
}

.user-manual h3 {
  font-size: 24px;
  color: #555;
  margin-bottom: 10px;
}

.user-manual p {
  color: #666;
  margin-bottom: 10px;
}

.user-manual ul {
  list-style-type: none;
  padding-left: 0;
}

.user-manual li {
  margin-bottom: 10px;
}

.user-manual strong {
  font-weight: bold;
}

.user-manual ol {
  padding-left: 20px;
}

.user-manual ol ol {
  list-style-type: lower-alpha;
}

button {
  margin-top: 20px;
  padding: 10px 20px;
  font-size: 16px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}

button:active {
  background-color: #3e8e41;
}
</style>
