<template>
  <div class="mega-panel text-white" @click="$emit('close')">
    <div class="mega-content" @click.stop>
      <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        <!-- Учёба -->
        <div class="mega-section">
          <div class="mega-section-header">
            <div class="mega-section-icon mega-section-icon--study">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <span>Учёба</span>
          </div>
          <div class="mega-links">
            <MegaLink
              v-for="item in studyItems"
              :key="item.label"
              v-bind="item"
              @click="$emit('close')"
            />
          </div>
        </div>

        <!-- Преподаватель -->
        <div class="mega-section" v-if="isTeacher">
          <div class="mega-section-header">
            <div class="mega-section-icon mega-section-icon--teacher">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <span>Преподаватель</span>
          </div>
          <div class="mega-links">
            <MegaLink
              v-for="item in teacherItems"
              :key="item.label"
              v-bind="item"
              @click="$emit('close')"
            />
          </div>
        </div>

        <!-- Ресурсы -->
        <div class="mega-section">
          <div class="mega-section-header">
            <div class="mega-section-icon mega-section-icon--resources">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <span>Ресурсы</span>
          </div>
          <div class="mega-links">
            <MegaLink
              v-for="item in resourceItems"
              :key="item.label"
              v-bind="item"
              @click="$emit('close')"
            />
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="mega-stats">
        <div class="mega-stat">
          <span class="mega-stat-value">50+</span>
          <span class="mega-stat-label">Курсов</span>
        </div>
        <div class="mega-stat-divider"></div>
        <div class="mega-stat">
          <span class="mega-stat-value">200+</span>
          <span class="mega-stat-label">Лекций</span>
        </div>
        <div class="mega-stat-divider"></div>
        <div class="mega-stat">
          <span class="mega-stat-value">1000+</span>
          <span class="mega-stat-label">Студентов</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MegaLink from './MegaMenuLink.vue'

defineProps({
  isTeacher: {
    type: Boolean,
    default: false
  },
  studyItems: {
    type: Array,
    default: () => []
  },
  teacherItems: {
    type: Array,
    default: () => []
  },
  resourceItems: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close'])
</script>

<style scoped>
.mega-panel {
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
  border: 1px solid rgba(148, 163, 184, 0.15);
  border-radius: 24px;
  background: 
    radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.15) 0%, transparent 40%),
    radial-gradient(circle at 80% 10%, rgba(167, 139, 250, 0.12) 0%, transparent 35%),
    radial-gradient(circle at 0% 80%, rgba(52, 211, 153, 0.1) 0%, transparent 30%),
    linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
  padding: 0.25rem;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
}

.mega-content {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
  padding: 1.5rem;
}

.mega-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.mega-section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #94a3b8;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.mega-section-icon {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mega-section-icon--study {
  background: rgba(56, 189, 248, 0.15);
  color: #38bdf8;
}

.mega-section-icon--teacher {
  background: rgba(251, 191, 36, 0.15);
  color: #fbbf24;
}

.mega-section-icon--resources {
  background: rgba(167, 139, 250, 0.15);
  color: #a78bfa;
}

.mega-links {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.mega-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(148, 163, 184, 0.1);
}

.mega-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.mega-stat-value {
  font-size: 1.25rem;
  font-weight: 800;
  color: #fff;
}

.mega-stat-label {
  font-size: 0.7rem;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.mega-stat-divider {
  width: 1px;
  height: 30px;
  background: rgba(148, 163, 184, 0.2);
}
</style>
