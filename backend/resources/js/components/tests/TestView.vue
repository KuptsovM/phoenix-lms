<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-semibold mb-4">{{ test?.title }}</h2>
    <p class="text-gray-600 mb-6">{{ test?.description }}</p>
    <div class="text-center text-gray-500">
      Компонент просмотра теста будет реализован позже
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const test = ref(null)

onMounted(async () => {
  // Загрузка теста по ID
  test.value = {
    id: route.params.id,
    title: 'Пример теста',
    description: 'Описание теста'
  }
})
</script>
