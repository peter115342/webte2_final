// router/index.js
import { createRouter, createWebHistory } from 'vue-router'


import AddQuestion from '@/components/AddQuestion.vue'
import EditCopyDelete from '@/components/EditCopyDelete.vue'
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
    },
  
    {
      path: '/',
      name: 'AddQuestion',
      component: AddQuestion
    },
    {
      path: '/',
      name: 'EditCopyDelete',
      component: EditCopyDelete
    },
  ]
})


export default router
