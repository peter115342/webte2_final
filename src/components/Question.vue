<template>
  <div class="question">
    <h2>{{ question.subject }}</h2>
    <p>{{ question.question }}</p>
    <ul>
      <li v-for="(answer, index) in question.answers" :key="index">
        {{ answer.answer }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    question: {
      type: Object,
      required: true
    }
  }
};
</script>

<style scoped>
.question {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

h2 {
  color: #333;
  margin-bottom: 10px;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  margin-bottom: 5px;
}
</style>
