<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vue/no-use-v-if-with-v-for -->
<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card v-for="question in questions" :key="question.question_id">
          <v-card-title class="headline">
            {{ question.question }}
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item v-if="question.type_id === 2">
                <v-list-item-content>
                  <v-checkbox-group v-model="selectedAnswers[question.question_id]">
                    <v-checkbox v-if="answerIsNotNull(answer)" v-for="(answer, index) in question.answers" :key="index" :label="answer.answer" :value="answer.answer"></v-checkbox>
                  </v-checkbox-group>
                </v-list-item-content>
              </v-list-item>
              <v-list-item v-else-if="question.type_id === 1">
                <v-list-item-content>
                  <v-text-field v-model="selectedAnswers[question.question_id]" label="Your Answer"></v-text-field>
                </v-list-item-content>
              </v-list-item>
            </v-list>
            <v-btn color="primary" @click="submitAnswers">Submit</v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      questions: [],
      selectedAnswers: {}
    };
  },
  mounted() {
    this.fetchQuestions();
  },
  methods: {
    fetchQuestions() {
      axios.get('https://node19.webte.fei.stuba.sk/nemecko/api/question/user/5')
        .then(response => {
          this.questions = response.data.reduce((acc, cur) => {
            const existingQuestion = acc.find(item => item.question_id === cur.question_id);
            if (existingQuestion) {
              if (cur.answer !== null) {
                existingQuestion.answers.push({ answer: cur.answer });
              }
            } else {
              if (cur.answer !== null) {
                acc.push({
                  question_id: cur.question_id,
                  question: cur.question,
                  type_id: cur.type_id,
                  answers: [{ answer: cur.answer }]
                });
              }
            }
            return acc;
          }, []);
          console.log(this.questions);
          console.log(response.data); // Zobraziť dáta v konzole
        })
        .catch(error => {
          console.error('Error fetching questions:', error);
        });
    },
    answerIsNotNull(answer) {
      return answer !== null;
    },
    submitAnswers() {
      // Tu môžete pridať ďalšiu logiku na spracovanie odpovedí
      console.log('Submitted answers:', this.selectedAnswers);
    }
  }
};
</script>

<style scoped>
/* Add your component styles here */
</style>