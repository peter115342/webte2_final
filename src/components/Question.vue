<template>
  <v-container fluid>
    <!-- Question Section -->
    <v-row justify="center">
      <v-col cols="12" md="8">
        <!-- QR Code Section -->

        <v-card v-for="question in questions" :key="question.question_id">
          <v-card-title class="headline">
            {{ question.subject }}
          </v-card-title>
          
          <v-card-title class="headline">
            {{ question.question }}
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item v-if="question.type_id === 2">
                <v-list-item-content>
                  <v-checkbox-group
                    v-model="checkedAnswers[question.question_id]"
                    @change="updateCheckedAnswers(question.question_id, $event)"
                  >
                    <v-checkbox
                      v-for="(answer, index) in question.answers"
                      :key="index"
                      :label="answer.answer"
                      :value="index"
                    ></v-checkbox>
                  </v-checkbox-group>
                </v-list-item-content>
              </v-list-item>
              <v-list-item v-else-if="question.type_id === 1">
                <v-list-item-content>
                  <v-text-field
                    v-model="checkedAnswers[question.question_id]"
                    label="Your Answer"
                  ></v-text-field>
                </v-list-item-content>
              </v-list-item>
            </v-list>
            <v-btn color="primary" @click="submitAnswers(question.question_id)">{{ $t('submit') }}</v-btn>
          </v-card-text>
        </v-card>
        <img :src="qrCodeUrl" alt="QR Code" class="mb-4" style="margin-top: 55px; border-radius: 10px; border: 5px solid rgb(249, 222, 130); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);" />

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
      checkedAnswers: {},
      qrCodeUrl: ''
    };
  },
  mounted() {
    this.fetchQuestions();
    this.generateQRCode();
  },
  methods: {
    async fetchQuestions() {
      const code = this.$route.params.code;

      try {
        const response = await axios.get(`https://node79.webte.fei.stuba.sk/final/api/${code}`);
        const responseData = response.data;

        if (typeof responseData === 'object' && responseData !== null) {
          this.questions = [responseData].map(questionData => ({
            question_id: questionData.id,
            question: questionData.question,
            type_id: questionData.type_id,
            subject: questionData.subject,
            answers: [] // Initialize as an empty array
          }));

          for (const question of this.questions) {
            try {
              const answersResponse = await axios.get(`https://node79.webte.fei.stuba.sk/final/api/question/${question.question_id}/answers`);
              question.answers = answersResponse.data.map(answerData => ({
                answer_id: answerData.id,
                answer: answerData.answer
              }));
            } catch (error) {
              console.error('Error fetching answers for question:', question.question_id, error);
            }
            console.log('Fetched answers for question:', question.question_id, question.answers); // Debugging log
          }
        } else {
          console.error("Invalid response data format:", responseData);
        }
      } catch (error) {
        console.error('Error fetching questions:', error);
      }
    },
    updateCheckedAnswers(questionId, event) {
      if (!this.checkedAnswers) {
        this.checkedAnswers = {};
      }
      this.checkedAnswers[questionId] = event.target.value;
    },
    async submitAnswers(questionId) {
      console.log('submitAnswers method called with questionId:', questionId); // Debugging log

      try {
        const question = this.questions.find(q => q.question_id == questionId);
        if (question && question.type_id === 2) { // Handle multiple-choice answers
          const selectedIndexes = this.checkedAnswers[questionId];
          console.log('Selected indexes:', selectedIndexes); // Debugging log
          for (const index of selectedIndexes) {
            const answer = question.answers[index];
            await axios.post('https://node79.webte.fei.stuba.sk/final/api/answer', {
              question_id: questionId,
              answer: answer.answer,
              correct: false
            });
          }
        } else if (question && question.type_id === 1) { // Handle text answers
          const answer = this.checkedAnswers[questionId];
          await axios.post('https://node79.webte.fei.stuba.sk/final/api/answer', {
            question_id: questionId,
            answer: answer,
            correct: false
          });
        }
        console.log('Submitted answers for questionId:', questionId, this.checkedAnswers[questionId]);
        // Navigate to results page after submitting answers
        this.$router.push({ name: 'Results', params: { questionId: questionId.toString() } });
      } catch (error) {
        console.error('Error submitting answers for questionId:', questionId, error);
      }
    },
    async generateQRCode() {
      const currentPageUrl = window.location.href;
      const qrCodeApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(currentPageUrl)}`;

      try {
        const response = await axios.get(qrCodeApiUrl, { responseType: 'blob' });
        const qrCodeBlob = new Blob([response.data], { type: 'image/png' });
        const qrCodeUrl = URL.createObjectURL(qrCodeBlob);
        this.qrCodeUrl = qrCodeUrl;
      } catch (error) {
        console.error('Error generating QR code:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Add your component styles here */
</style>
