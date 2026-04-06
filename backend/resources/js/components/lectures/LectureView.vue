<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-semibold mb-4">{{ lecture?.title }}</h2>
    <div class="prose max-w-none" v-html="lecture?.content"></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const lecture = ref(null)

onMounted(async () => {
  // Загрузка лекции по ID
  lecture.value = {
    id: route.params.id,
    title: 'Пример лекции',
    content: '<p>Содержание лекции...</p>'
  }
})
</script>
