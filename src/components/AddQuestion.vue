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
                <v-text-field
                  v-model="subject"
                  label="Subject"
                  outlined
                  required
                ></v-text-field>
                <v-textarea
                  v-model="question"
                  label="Question"
                  outlined
                  required
                ></v-textarea>
                <v-select
                  v-model="selectedType"
                  :items="['Multiple Choice', 'Short Answer']"
                  label="Type"
                  outlined
                  required
                ></v-select>
                <template v-if="selectedType === 'Multiple Choice'">
                  <template v-for="(option, index) in options" :key="index">
                    <v-text-field
                      v-model="options[index].text"
                      :label="'Option ' + (index + 1)"
                      outlined
                      required
                    ></v-text-field>
                    <v-checkbox
                      v-model="options[index].correct"
                      :label="'Correct ' + (index + 1)"
                      value="true"
                    ></v-checkbox>
                  </template>
                  <v-btn @click="addOption" color="primary" class="mr-4" small>{{ $t('addOption') }}</v-btn>
                </template>
                <v-btn-group>
                  <v-btn type="submit" color="primary" small>{{ $t('submit') }}</v-btn>
                  <v-btn @click="resetForm" color="background" small>{{ $t('reset') }}</v-btn>
                </v-btn-group>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
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
        options: [
          { text: '', correct: false }
        ], 
        snackbar: {
          show: false,
          color: '', // Set color based on success or error
          timeout: 3000,
          top: false,
          text: ''
        }
      };
    },
    methods: {
      async submitQuestion() {
        // Prepare payload based on selected type
        let payload = {
          subject: this.subject,
          question: this.question,
          type: this.selectedType === 'Multiple Choice' ? 'multiple_choice' : 'short_answer',
          user_id: 123 // Replace with actual user id
        };
        
        if (this.selectedType === 'Multiple Choice') {
          
          payload.options = this.options.filter(option => option.text.trim() !== '');
        }
  
        
        try {
          const response = await fetch('/question', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          });
  
          if (response.ok) {
            this.showSnackbar('Question added successfully!', 'success');
            this.resetForm();
          } else {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to add question');
          }
        } catch (error) {
          console.error('Error:', error);
          this.showSnackbar('Failed to add question. Please try again.', 'error');
        }
      },
      resetForm() {
        this.subject = '';
        this.question = '';
        this.selectedType = 'Multiple Choice';
        this.options = [
          { text: '', correct: false }
        ];
      },
      showSnackbar(text, color) {
        this.snackbar.text = text;
        this.snackbar.color = color;
        this.snackbar.show = true;
      },
      addOption() {
        this.options.push({ text: '', correct: false });
      }
    }
  };
  </script>
  