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
            <v-btn color="primary" @click="submitAnswers">{{ $t('submit') }}</v-btn>
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
  const code = "VlCPs"; //"code" otázky

  axios.get(`https://node79.webte.fei.stuba.sk/final/api/${code}`)
    .then(response => {
      const responseData = response.data;
      if (typeof responseData === 'object' && responseData !== null) {
        this.questions = [responseData].map(questionData => ({
          question_id: questionData.id,
          question: questionData.question,
          type_id: questionData.type_id,
          answers: [{ answer: questionData.answer }]
        }));
        this.questions.forEach(question => {
          axios.get(`https://node79.webte.fei.stuba.sk/final/api/question/${question.question_id}/answers`)
            .then(response => {
              // Pridanie odpovedí k danej otázke
              question.answers = response.data.map(answerData => ({
                answer: answerData.answer
              }));
            })
            .catch(error => {
              console.error('Error fetching answers for question:', question.question_id, error);
            });
        });
      } else {
        console.error("Invalid response data format:", responseData);
      }
    })
    .catch(error => {
      console.error('Error fetching questions:', error);
    });
},


    answerIsNotNull(answer) {
      return answer !== null;
    },
    submitAnswers() {
      console.log('Submitted answers:', this.selectedAnswers);
    }
  }
};
</script>

<style scoped>
/* Add your component styles here */
</style>