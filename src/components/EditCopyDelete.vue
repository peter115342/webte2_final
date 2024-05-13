<template>
    <div>
      <h2>Questions</h2>
      <table>
        <thead>
          <tr>
            <th>Question ID</th>
            <th>Question</th>
            <th>Subject</th>
            <th>Delete</th>
            <th>Copy</th>
            <th>Edit</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="question in uniqueQuestions" :key="question.question_id">
            <td>{{ question.question_id }}</td>
            <td>{{ question.question }}</td>
            <td>{{ question.subject }}</td>
            <td><button @click="deleteQuestion(question)"><i class="mdi mdi-delete"></i></button></td>
            <td><button @click="copyQuestion(question)"><i class="mdi mdi-content-copy"></i></button></td>
            <td><button @click="showEditForm(question)"><i class="mdi mdi-pencil"></i></button></td>
          </tr>
        </tbody>
      </table>
      <div v-if="copySuccess" class="copy-success-message">
        <p>Question copied to clipboard!</p>
      </div>
      <v-dialog v-model="showEdit" max-width="500px">
        <v-card>
          <v-card-title>Edit Question</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitEditedQuestion">
              <v-text-field
                v-model="editedQuestion.question"
                label="Question"
                outlined
                required
              ></v-text-field>
              <v-text-field
                v-model="editedQuestion.subject"
                label="Subject"
                outlined
                required
              ></v-text-field>
              <v-btn type="submit" color="primary">Save</v-btn>
              <v-btn @click="cancelEdit">Cancel</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        questions: [],
        copySuccess: false,
        showEdit: false,
        editedQuestion: {
          question_id: null,
          question: '',
          subject: ''
        }
      };
    },
    computed: {
      uniqueQuestions() {
        // Deduplicate questions based on 'question_id'
        const uniqueQuestions = [];
        const questionIds = new Set();
        this.questions.forEach(question => {
          if (!questionIds.has(question.question_id)) {
            uniqueQuestions.push(question);
            questionIds.add(question.question_id);
          }
        });
        return uniqueQuestions;
      }
    },
    mounted() {
      this.fetchQuestionsByUserId(localStorage.getItem('userId'));
    },
    methods: {
      async fetchQuestionsByUserId(userId) {
  const accessToken = this.getAccessToken();
  const requestOptions = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ access_token: accessToken })
  };

  try {
    const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/user/${userId}`, requestOptions);

    if (response.ok) {
      const data = await response.json();
      this.questions = data;
    } else {
      console.error('Failed to fetch questions:', response.statusText);
    }
  } catch (error) {
    console.error('Error fetching questions:', error);
  }
},

async deleteAnswers(questionId) {
  try {
    const accessToken = this.getAccessToken();

    const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/${questionId}/answers`);
    const data = await response.json();

    if (data.length === 0) {
      return;
    }

    // Iterate over answers and delete each one
    const promises = data.map(async (answer) => {
      const requestOptions = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ access_token: accessToken })
      };

      await fetch(`https://node79.webte.fei.stuba.sk/final/api/answer/delete/${answer.id}`, requestOptions);
    });

    await Promise.all(promises);
  } catch (error) {
    console.error('Error deleting answers:', error);
  }
},


      async deleteQuestion(question) {
  try {
    const accessToken = this.getAccessToken();
    const questionId = question.question_id;

      await this.deleteAnswers(questionId);

      const requestOptions = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ access_token: accessToken })
      };

      const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/delete/${questionId}`, requestOptions);

      if (response.ok) {
        // Remove the question from the local array
        const index = this.questions.findIndex(q => q.question_id === questionId);
        if (index !== -1) {
          this.questions.splice(index, 1);
        }
        // Fetch updated list of questions after deletion
        this.fetchQuestionsByUserId(localStorage.getItem('userId'));
      } else {
        console.error('Failed to delete question:', response.statusText);
      }
  } catch (error) {
    console.error('Error deleting question:', error);
  }
},

      copyQuestion(question) {
        const questionText = question.question;
        navigator.clipboard.writeText(questionText)
          .then(() => {
            this.copySuccess = true;
            setTimeout(() => {
              this.copySuccess = false;
            }, 3000); // Hide the success message after 3 seconds
          })
          .catch(error => {
            console.error('Error copying question:', error);
            alert('Failed to copy question. Please try again.');
          });
      },
      showEditForm(question) {
        this.showEdit = true;
        // Pre-fill the edit form with existing question details
        this.editedQuestion.question_id = question.question_id;
        this.editedQuestion.question = question.question;
        this.editedQuestion.subject = question.subject;
      },
      cancelEdit() {
        this.showEdit = false;
        // Clear the editedQuestion object
        this.editedQuestion = {
          question_id: null,
          question: '',
          subject: ''
        };
      },
      getAccessToken() {
    const cookieValue = document.cookie;
    return cookieValue.split('=')[1];
  },
      submitEditedQuestion() {
        // Implement logic to submit the edited question to your backend
        // You can access the edited question details from this.editedQuestion object
        // After submitting, you may want to hide the edit form and fetch updated questions
        this.showEdit = false;
        this.editedQuestion = {
          question_id: null,
          question: '',
          subject: ''
        };
        // Fetch updated list of questions after editing
        this.fetchQuestionsByUserId(localStorage.getItem('userId'));
      }
    }
  };
  </script>
  
  <style scoped>
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  th {
    background-color: #f2f2f2;
  }
  
  button {
    background: none;
    border: none;
    cursor: pointer;
  }
  
  button i {
    color: red;
  }
  
  .copy-success-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #4caf50;
    color: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 999;
  }
  </style>
  