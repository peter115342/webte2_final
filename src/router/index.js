// router/index.js
import { createRouter, createWebHistory } from 'vue-router'


import AddQuestion from '@/components/AddQuestion.vue'
import EditCopyDelete from '@/components/EditCopyDelete.vue'
import Homepage from '@/components/HomePage.vue'
import Question from '@/components/Question.vue'
import Manual from '../components/Manual.vue';
import Results from '../components/Results.vue';
import Users from '../components/Users.vue';
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
      component: Question,
      props: true
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
    {
      path: '/manual',
      name: 'Manual',
      component: Manual
    },
    {
      path: '/result/:questionId',
      name: 'Results',
      component: Results,
      props: true
    },
    {
      path: '/',
      name: 'Users',
      component: Users,
    },
  ]
})


export default router
