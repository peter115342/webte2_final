<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title class="headline">{{ $t('answer') }}</v-card-title>
          <v-card-text>
            <v-list>
              <v-list-item v-for="answer in answers" :key="answer.answer_id">
                <v-list-item-content>
                  <v-list-item-title>{{ answer.answer }}</v-list-item-title>
                  <v-list-item-subtitle>{{ $t('count') }}: {{ answer.count }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    questionId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      question: {},
      answers: []
    };
  },
  async mounted() {
    await this.fetchQuestionAndAnswers();
  },
  methods: {
    async fetchQuestionAndAnswers() {
      const questionId = this.questionId;

      try {
        // Fetch answers
        const answersResponse = await axios.get(`https://node79.webte.fei.stuba.sk/final/api/question/${questionId}/answers`);
        this.answers = answersResponse.data
          .filter(answer => answer.answer && answer.answer.trim() !== '') // Filter out empty or whitespace-only answers
          .map(answer => ({
            answer_id: answer.id,
            answer: answer.answer,
            count: answer.count
          }));
      } catch (error) {
        console.error('Error fetching question and answers:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
