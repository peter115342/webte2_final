<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vue/no-use-v-if-with-v-for -->
<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title class="headline">
            {{ $t('questionsTitle') }}
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item v-for="question in questions" :key="question.question_id">
                <v-list-item-content>
                  <v-list-item-title>{{ question.question }}</v-list-item-title>
                  <v-checkbox-group v-model="selectedAnswers[question.question_id]">
                    <v-checkbox v-if="answerIsNotNull(answer)" v-for="(answer, index) in question.answers" :key="index" :label="answer.answer" :value="answer.answer"></v-checkbox>
                  </v-checkbox-group>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-btn color="primary" @click="submitAnswers">Submit</v-btn>
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
      axios.get('https://node19.webte.fei.stuba.sk/nemecko/api/question/user/3)
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
                  answers: [{ answer: cur.answer }]
                });
              }
            }
            return acc;
          }, []);
          console.log(this.questions);
          console.log(response.data); // Display data in the console
        })
        .catch(error => {
          console.error('Error fetching questions:', error);
        });
    },
    answerIsNotNull(answer) {
      return answer !== null;
    },
    submitAnswers() {
      //TODO submitting logic
    }
  }
};
</script>

<style scoped>
/* Add your component styles here */
</style>
