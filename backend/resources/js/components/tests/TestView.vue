<template>
  <div class="card p-4 sm:p-6">
    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-3 sm:mb-4">{{ test?.title }}</h2>
    <p class="text-slate-500 mb-4 sm:mb-6">{{ test?.description }}</p>
    <div class="text-center text-slate-400 py-8">
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
