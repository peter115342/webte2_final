<template>
    <v-container>
      <v-row justify="center">
        <v-col cols="12">
          <v-card>
            <v-card-title>
              <v-icon class="mr-2" color="green">mdi-account-multiple</v-icon>
              {{ $t('userList') }}
            </v-card-title>
            <v-card-text>
              <v-data-table
                :headers="headers"
                :items="users"
                :loading="loading"
                loading-text="Loading... Please wait"
              >
                <template v-slot:item.delete="{ item }">
                  <v-icon @click="deleteUser(item.id)" style="color: red;">mdi-delete</v-icon>
                </template>
              </v-data-table>
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
        users: [],
        loading: false,
        snackbar: { show: false, color: '', timeout: 3000, top: false, text: '' },
        headers: [
          { text: 'ID', value: 'id' },
          { text: 'Username', value: 'username' },
          { text: 'Email', value: 'email' },
          { text: 'Role', value: 'role' },
          { text: 'Actions', value: 'delete', sortable: false } // New column for delete action
        ]
      };
    },
    async mounted() {
      await this.fetchUsers();
    },
    methods: {
      async fetchUsers() {
        this.loading = true;
        try {
          const accessToken = this.getAccessToken();
          const response = await fetch('https://node79.webte.fei.stuba.sk/final/api/user/list', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ access_token: accessToken })
          });
  
          if (response.ok) {
            this.users = await response.json();
          } else {
            const errorData = await response.json();
            throw new Error(errorData.message || this.$t('failedFetchUsers'));
          }
        } catch (error) {
          console.error('Error fetching users:', error);
          this.showSnackbar(this.$t('failedFetchUsersRetry'), 'error');
        } finally {
          this.loading = false;
        }
      },
      async deleteUser(userId) {
        try {
          const accessToken = this.getAccessToken();
          const response = await fetch(`https://node79.webte.fei.stuba.sk/final/api/user/delete/${userId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ access_token: accessToken })
          });
  
          if (response.ok) {
            // Remove the deleted user from the list
            this.users = this.users.filter(user => user.id !== userId);
            this.showSnackbar(this.$t('deleteUserSuccess'), 'success');
          } else {
            const errorData = await response.json();
            throw new Error(errorData.message || this.$t('failedDeleteUser'));
          }
        } catch (error) {
          console.error('Error deleting user:', error);
          this.showSnackbar(this.$t('failedDeleteUser'), 'error');
        }
      },
      getAccessToken() {
        const cookieValue = document.cookie;
        return cookieValue.split('=')[1];
      },
      showSnackbar(text, color) {
        this.snackbar.text = text;
        this.snackbar.color = color;
        this.snackbar.show = true;
      }
    }
  };
  </script>
  
  <style scoped>
  /* Add any necessary styles here */
  </style>
  