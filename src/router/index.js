// router/index.js

import { createRouter, createWebHistory } from 'vue-router'

import Homepage from '@/components/HomePage.vue'
import Question from '@/components/Question.vue'


const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes: [
    
    {
      path: '/',
      name: 'Homepage',
      component: Homepage
    },
    
    {
      path: '/question/:code',
      name: 'Question',
      component: Question
    }
  ]
})


export default router
