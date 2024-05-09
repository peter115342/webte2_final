// app.js in store folder
// Utilities
import { defineStore } from 'pinia'

export const useAppStore = defineStore('app', {
  state: () => ({
    accessToken: ''
  }),
})
