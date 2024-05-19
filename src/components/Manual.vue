<template>
    <div class="user-manual">
      <h1>Užívateľská príručka</h1>
      <div v-html="manualContent"></div>
      <button @click="downloadPDF">Stiahnuť PDF</button>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        manualContent: `
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
        `
      };
    },
    methods: {
      downloadPDF() {
        // URL upravená podľa vašej požiadavky
        window.location.href = 'https://node22.webte.fei.stuba.sk/Manual/getPDF.php';
      }
    }
  };
  </script>
  
  <style scoped>
  .user-manual {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto; /* Posunutie do stredu a odstup od okraja */
    padding: 20px;
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
  