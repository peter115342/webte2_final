<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" sm="10" md="10" lg="8">
        <v-card class="wider-card">
          <v-card-title class="headline">
            <v-icon class="mr-2" color="green">mdi-plus</v-icon>
            {{ $t('addQuestion') }}
          </v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitQuestion">
              <!-- Message for admin to select user -->
              <template v-if="isAdmin">
                <v-select
                  v-model="selectedUser"
                  :items="userOptions"
                  :label="$t('selectUser')"
                  outlined
                  required
                ></v-select>
              </template>
              <v-text-field
                v-model="subject"
                :label="$t('subject')"
                outlined
                required
              ></v-text-field>
              <v-textarea
                v-model="question"
                :label="$t('question')"
                outlined
                required
              ></v-textarea>
              <v-select
                v-model="selectedType"
                :items="['Multiple Choice', 'Short Answer']"
                :label="$t('type')"
                outlined
                required
              ></v-select>
              <!-- Option fields for Multiple Choice -->
              <template v-if="selectedType === 'Multiple Choice'">
                <template v-for="(option, index) in options" :key="index">
                  <v-icon v-if="options.length > 1 && index === options.length - 1" @click="removeOption(index)" color="red" size="24">mdi-minus</v-icon>
                  <v-icon v-if="index === options.length - 1" @click="addOption" color="green" size="24">mdi-plus</v-icon>
                  <v-text-field
                    v-model="option.text"
                    :label="$t('option ') + (index + 1)"
                    outlined
                    required
                  ></v-text-field>
                  <v-checkbox
                    v-model="option.correct"
                    :label="$t('correct ') + (index + 1)"
                    :value="true"
                  ></v-checkbox>
                </template>
              </template>
              <!-- Submit and reset buttons -->
              <v-btn-group>
                <v-btn type="submit" color="primary" small>{{ $t('submit') }}</v-btn>
                <v-btn @click="resetForm" color="background" small>{{ $t('reset') }}</v-btn>
              </v-btn-group>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <!-- Snackbar for notifications -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="snackbar.timeout" :top="snackbar.top">
      {{ snackbar.text }}
    </v-snackbar>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      subject: '',
      question: '',
      selectedType: 'Multiple Choice',
      options: [{ text: '', correct: false }],
      snackbar: { show: false, color: '', timeout: 3000, top: false, text: '' },
      isAdmin: false,
      users: [],
      selectedUser: null,
    };
  },
  computed: {
    userOptions() {
      return this.users.map(user => user.id);
    }
  },
  async mounted() {
    const isAdmin = localStorage.getItem('admin') === '1';
    if (isAdmin) {
      this.isAdmin = true;
      await this.getAllUsers();
    }
  },
  methods: {
    async getAllUsers() {
      try {
        const accessToken = this.getAccessToken();
        const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/user/list', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ access_token: accessToken })
        });

        if (response.ok) {
          this.users = await response.json();
          console.log('Users:', this.users);
        } else {
          console.error('Failed to fetch users:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    },
    async submitQuestion() {
      let payload = {
        access_token: this.getAccessToken(),
        subject: this.subject,
        question: this.question,
        type: this.selectedType === 'Multiple Choice' ? 'multiple_choice' : 'open_ended',
        user_id: this.isAdmin ? this.selectedUser : localStorage.getItem('userId')
      };
      
      if (this.selectedType === 'Multiple Choice') {
        payload.options = this.options.filter(option => option.text.trim() !== '');
      }

      try {
        const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/question', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        if (response.ok) {
          const questionId = await this.getQuestionsByUserId(this.isAdmin ? this.selectedUser : localStorage.getItem('userId'));
          
          if (questionId) {
            for (const option of this.options) {
              await this.createAnswer(questionId, option.text, option.correct);
            }
          }

          this.showSnackbar(this.$t('questionAddedSuccess'), 'success');
          this.resetForm();
        } else {
          const errorData = await response.json();
          throw new Error(errorData.message || this.$t('failedAddQuestion'));
        }
      } catch (error) {
        console.error('Error:', error);
        this.showSnackbar(this.$t('failedAddQuestionRetry'), 'error');
      }
    },
    async getQuestionsByUserId(userId) {
      const accessToken = this.getAccessToken();
      try {
        const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/user/${userId}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ access_token: accessToken })
        });

        if (response.ok) {
          const questions = await response.json();
          const highestQuestion = questions.reduce((maxQuestion, question) => {
            return question.question_id > maxQuestion.question_id ? question : maxQuestion;
          }, questions[0]);
          return highestQuestion.question_id;
        } else {
          throw new Error('Failed to fetch user questions');
        }
      } catch (error) {
        console.error('Error:', error);
        return null;
      }
    },
    async createAnswer(questionId, answerText, correct) {
      const accessToken = this.getAccessToken();
      try {
        const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/answer', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ access_token: accessToken, correct, question_id: questionId, answer: answerText })
        });

        if (!response.ok) throw new Error('Failed to add answer');
      } catch (error) {
        console.error('Error adding answer:', error);
      }
    },
    getAccessToken() {
      const cookieValue = document.cookie;
      return cookieValue.split('=')[1];
    },
    resetForm() {
      this.subject = '';
      this.question = '';
      this.selectedType = 'Multiple Choice';
      this.options = [{ text: '', correct: false }];
      if (this.isAdmin) this.selectedUser = null;
    },
    showSnackbar(text, color) {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    },
    addOption() {
      this.options.push({ text: '', correct: false });
    },
    removeOption(index) {
      this.options.splice(index, 1);
    }
  }
};
</script>
