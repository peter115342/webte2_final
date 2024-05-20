<template>
  <div>
    <h2 class="mb-4">Questions</h2>
    <div class="subject-filter">
  <label for="subjectFilter">Filter by Subject:</label>
  <select id="subjectFilter" v-model="selectedSubject" class="subject-dropdown">
    <option value="">All Subjects</option>
    <option v-for="subject in uniqueSubjects" :value="subject">{{ subject }}</option>
  </select>
</div>


    <v-data-table
      :headers="headers"
      :items="uniqueQuestions"
      item-key="question_id"
      class="elevation-1"
    >
      <template v-slot:item="{ item }">
        <tr>
          <td>{{ item.question_id }}</td>
          <td>{{ item.question }}</td>
          <td>{{ item.subject }}</td>
          <td>
            <v-icon small class="red--text" @click="deleteQuestion(item)">mdi-delete</v-icon>
          </td>
          <td>
            <v-icon small @click="copyQuestion(item)">mdi-content-copy</v-icon>
          </td>
          <td>
            <v-icon small @click="showEditForm(item)">mdi-pencil</v-icon>
          </td>
        </tr>
      </template>
    </v-data-table>

    <v-dialog v-model="showEdit" max-width="500px">
      <v-card>
        <v-card-title class="headline">Edit Question</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="submitEditedQuestion">
            <v-text-field v-model="editedQuestion.question" label="Question" outlined required></v-text-field>
            <v-text-field v-model="editedQuestion.subject" label="Subject" outlined required></v-text-field>
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
      selectedSubject: '',
      showEdit: false,
      editedQuestion: {
        question_id: null,
        question: '',
        subject: '',
        type_id: null
      },
      headers: [
        { text: 'Question ID', value: 'question_id' },
        { text: 'Question', value: 'question' },
        { text: 'Subject', value: 'subject' },
        { text: 'Delete', value: 'actions', sortable: false },
        { text: 'Copy', value: 'actions', sortable: false },
        { text: 'Edit', value: 'actions', sortable: false }
      ],
      allUserIds: [], // New array to store all user IDs
      allUserQuestions: [], // New array to store questions of all users
    };
  },
  computed: {
    uniqueQuestions() {
    const filteredQuestions = this.questions.filter(question => {
      return this.selectedSubject === '' || question.subject === this.selectedSubject;
    });

    // Deduplicate questions based on 'question_id'
    const uniqueQuestions = [];
    const questionIds = new Set();
    filteredQuestions.forEach(question => {
      if (!questionIds.has(question.question_id)) {
        uniqueQuestions.push(question);
        questionIds.add(question.question_id);
      }
    });
    return uniqueQuestions;
  },
    uniqueSubjects() {
    const subjects = new Set();
    this.questions.forEach(question => subjects.add(question.subject));
    return Array.from(subjects);
  }
  },
  mounted() {
    console.log("administrator", localStorage.getItem('admin'));
    if (localStorage.getItem('admin') === '1') {
      this.getAllUserIds();
    }
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
      const text = await response.text();
      console.log('Response Text:', text);
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
    async copyQuestion(question) {
  try {
    const accessToken = this.getAccessToken();

    let questionType;
    if (question.type_id === 1) {
      questionType = 'open_ended';
    } else if (question.type_id === 2) {
      questionType = 'multiple_choice';
    } else {
      throw new Error('Invalid type_id');
    }

    // Make a POST request to create a new question
    const requestOptions = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        access_token: accessToken,
        subject: question.subject,
        question: question.question,
        type: questionType,
        user_id: question.user_id
      })
    };

    const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/question', requestOptions);
    const data = await response.json();

    if (response.ok) {
      this.questions.push(data);

      // Fetch updated list of questions after adding new question
      this.fetchQuestionsByUserId(localStorage.getItem('userId'));

      // Fetch the question with the highest ID for the current user
      const lastQuestionRequestOptions = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          access_token: accessToken
        })
      };

      const lastQuestionResponse = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/user/${question.user_id}`, lastQuestionRequestOptions);
      const lastQuestionData = await lastQuestionResponse.json();
      
      // Find the question with the highest ID
      const lastQuestion = lastQuestionData.reduce((maxQuestion, currentQuestion) => {
        return currentQuestion.question_id > maxQuestion.question_id ? currentQuestion : maxQuestion;
      }, { question_id: -Infinity });

      // Fetch answers for the original question
      const originalQuestionAnswersResponse = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/${question.question_id}/answers`);
      const originalQuestionAnswersData = await originalQuestionAnswersResponse.json();

      // Add all answers from the original question to the new question
      for (const originalAnswer of originalQuestionAnswersData) {
        const addAnswerRequestOptions = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            correct: originalAnswer.correct,
            answer: originalAnswer.answer,
            question_id: lastQuestion.question_id
          })
        };

        const addAnswerResponse = await fetch('https://node79.webte.fei.stuba.sk/final/api/answer', addAnswerRequestOptions);
        const addAnswerData = await addAnswerResponse.json();

        if (!addAnswerResponse.ok) {
          console.error('Failed to add answer:', addAnswerResponse.statusText);
        }
      }

      console.log('Answers for the original question:', originalQuestionAnswersData);
    } else {
      console.error('Failed to copy question:', response.statusText);
    }
  } catch (error) {
    console.error('Error copying question:', error);
  }
},

    showEditForm(question) {
      this.showEdit = true;
      // Pre-fill the edit form with existing question details
      this.editedQuestion.question_id = question.question_id;
      this.editedQuestion.question = question.question;
      this.editedQuestion.subject = question.subject;
      this.editedQuestion.type_id = question.type_id;  // Ensure type_id is copied
    },

    cancelEdit() {
      this.showEdit = false;
      // Clear the editedQuestion object
      this.editedQuestion = {
        question_id: null,
        question: '',
        subject: '',
        type_id: null
      };
    },

    getAccessToken() {
      const cookieValue = document.cookie;
      return cookieValue.split('=')[1];
    },

    async submitEditedQuestion() {
      try {
        const accessToken = this.getAccessToken();
        const questionId = this.editedQuestion.question_id;

        let questionType;
        if (this.editedQuestion.type_id === 1) {
          questionType = 'open_ended';
        } else if (this.editedQuestion.type_id === 2) {
          questionType = 'multiple_choice';
        } else {
          throw new Error('Invalid type_id');
        }

        const requestOptions = {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            access_token: accessToken,
            question: this.editedQuestion.question,
            subject: this.editedQuestion.subject,
            type: questionType
          })
        };

        const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/${questionId}`, requestOptions);

        if (response.ok) {
          // Update the local questions array
          const index = this.questions.findIndex(q => q.question_id === questionId);
          if (index !== -1) {
            this.questions[index] = {
              ...this.questions[index],
              question: this.editedQuestion.question,
              subject: this.editedQuestion.subject
            };
          }
          // Hide the edit form and clear the editedQuestion object
          this.showEdit = false;
          this.editedQuestion = {
            question_id: null,
            question: '',
            subject: '',
            type_id: null
          };
          // Fetch updated list of questions after editing
          this.fetchQuestionsByUserId(localStorage.getItem('userId'));
        } else {
          console.error('Failed to update question:', response.statusText);
        }
      } catch (error) {
        console.error('Error updating question:', error);
      }
    },
    async getAllUserIds() {
      try {
        const accessToken = this.getAccessToken();
        const requestOptions = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ access_token: accessToken })
        };

        const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/user/list', requestOptions);
        if (response.ok) {
          const data = await response.json();
          const allUserIds = data.map(user => user.id);
          console.log('All User IDs:', allUserIds);
          this.getAllUserQuestions(allUserIds); // After getting user IDs, get their questions
        } else {
          console.error('Failed to fetch user IDs:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching user IDs:', error);
      }
    },
 async getAllUserQuestions(allUserIds) {
  try {
    const accessToken = this.getAccessToken();
    for (const userId of allUserIds) {
      const requestOptions = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ access_token: accessToken })
      };

      const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/question/user/${userId}`, requestOptions);
      if (response.ok) {
        const data = await response.json();
        if (data.length > 0) {
          // Add questions for each user to the 'questions' array
          this.questions.push(...data);
        }
      } else {
        console.error(`Failed to fetch questions for user ${userId}:`, response.statusText);
      }
    }
    console.log('All User Questions:', this.questions);
  } catch (error) {
    console.error('Error fetching user questions:', error);
  }
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
.red--text {
    color: red;
  }
  .subject-filter {
  margin-bottom: 20px;
}

.subject-dropdown {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #f9f9f9;
  font-size: 14px;
  width: 200px;
}

.subject-dropdown:focus {
  outline: none;
  border-color: #4caf50;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

</style>
