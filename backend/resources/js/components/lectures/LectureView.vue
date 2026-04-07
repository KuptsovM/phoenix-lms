<template>
  <div class="animate-fade-in">
    <div class="card p-6 md:p-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 mb-4 pb-4 border-b border-slate-100">
        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Лекция
      </div>
      <h2 class="text-2xl font-bold text-slate-800 mb-6">{{ lecture?.title }}</h2>
      <div class="prose max-w-none text-slate-600" v-html="lecture?.content"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const lecture = ref(null)

onMounted(async () => {
  lecture.value = {
    id: route.params.id,
    title: 'Пример лекции',
    content: '<p>Содержание лекции...</p>'
  }
})
</script>

<style scoped>
.prose { line-height: 1.8; }
.prose :deep(h1), .prose :deep(h2), .prose :deep(h3) {
  color: #0f172a;
  font-weight: 700;
  margin-top: 1.5em;
  margin-bottom: 0.5em;
}
.prose :deep(p) { margin-bottom: 1em; }
.prose :deep(ul), .prose :deep(ol) { padding-left: 1.5em; margin-bottom: 1em; }
.prose :deep(li) { margin-bottom: 0.5em; }
</style>
