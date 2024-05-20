<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title class="headline">{{ $t('answer') }}</v-card-title>
          <v-card-text>
            <div class="button-container">
              <v-btn @click="showWordCloud = true" v-text="$t('show_word_cloud')"></v-btn>
              <v-btn @click="showWordCloud = false" v-text="$t('show_answers')"></v-btn>
            </div>
            
            <div v-if="showWordCloud">
              <div class="word-cloud">
                <span v-for="(answer, index) in answers" :key="index" :style="{ fontSize: getFontSize(answer.count) + 'px' }">
                  {{ answer.answer }}
                </span>
              </div>
            </div>
            
            <div v-else>
              <v-list>
                <v-list-item v-for="answer in answers" :key="answer.answer_id">
                  <v-list-item-content>
                    <v-list-item-title>{{ answer.answer }}</v-list-item-title>
                    <v-list-item-subtitle>{{ $t('count') }}: {{ answer.count }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </div>
            
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
      answers: [],
      showWordCloud: false
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
    },
    getFontSize(count) {
      // Adjust the scaling factor as needed
      const scalingFactor = 20;
      return count * scalingFactor;
    }
  }
};
</script>

<style scoped>
.button-container {
  margin-bottom: 20px;
}

.word-cloud {
  display: flex;
  flex-wrap: wrap;
}

.word-cloud span {
  margin-right: 20px;
}
</style>
