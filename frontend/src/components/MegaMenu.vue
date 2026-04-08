<template>
  <div class="mega-backdrop" @click="$emit('close')">
    <div class="mega-panel" @click.stop>
      <div class="mega-grid" :class="isTeacher ? 'mega-grid--2col' : 'mega-grid--1col'">
        <!-- Учёба -->
        <div class="mega-section">
          <div class="mega-section-title">
            <span class="mega-section-dot mega-section-dot--blue"></span>
            Учёба
          </div>
          <div class="mega-links">
            <MegaLink v-for="item in studyItems" :key="item.label" v-bind="item" @click="$emit('close')" />
          </div>
        </div>

        <!-- Преподаватель -->
        <div v-if="isTeacher" class="mega-section">
          <div class="mega-section-title">
            <span class="mega-section-dot mega-section-dot--amber"></span>
            Преподаватель
          </div>
          <div class="mega-links">
            <MegaLink v-for="item in teacherItems" :key="item.label" v-bind="item" @click="$emit('close')" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MegaLink from './MegaMenuLink.vue'

defineProps({
  isTeacher: { type: Boolean, default: false },
  studyItems: { type: Array, default: () => [] },
  teacherItems: { type: Array, default: () => [] },
})
defineEmits(['close'])
</script>

<style scoped>
.mega-backdrop {
  position: absolute;
  left: 0;
  right: 0;
  top: 100%;
  z-index: 39;
  padding: 0.5rem 1rem 0.75rem;
  max-width: 1280px;
  margin: 0 auto;
}

.mega-panel {
  background:
    radial-gradient(circle at 15% 20%, rgba(56, 189, 248, 0.12) 0%, transparent 45%),
    radial-gradient(circle at 85% 10%, rgba(167, 139, 250, 0.1) 0%, transparent 40%),
    linear-gradient(180deg, rgba(15, 23, 42, 0.97) 0%, rgba(15, 23, 42, 0.99) 100%);
  border: 1px solid rgba(148, 163, 184, 0.12);
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
}

.mega-grid {
  display: grid;
  gap: 1.5rem;
}
.mega-grid--1col { grid-template-columns: 1fr; max-width: 320px; }
.mega-grid--2col { grid-template-columns: repeat(2, 1fr); }

@media (min-width: 768px) {
  .mega-panel { padding: 1.5rem; border-radius: 20px; }
  .mega-grid--2col { grid-template-columns: repeat(2, minmax(200px, 280px)); }
}

.mega-section { display: flex; flex-direction: column; gap: 0.5rem; }

.mega-section-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.mega-section-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}
.mega-section-dot--blue { background: #38bdf8; }
.mega-section-dot--amber { background: #fbbf24; }

.mega-links { display: flex; flex-direction: column; gap: 2px; }
</style>
