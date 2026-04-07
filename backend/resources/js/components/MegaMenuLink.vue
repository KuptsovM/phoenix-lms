<template>
  <router-link :to="to" class="mega-link" @click="$emit('click')">
    <div class="mega-link-content">
      <div class="icon-wrapper" :style="{ '--tint': tint }">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" v-html="path" />
      </div>
      <div class="text-wrapper">
        <span class="label">{{ label }}</span>
        <span class="desc">{{ desc }}</span>
      </div>
      <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  to: { type: [String, Object], required: true },
  label: { type: String, required: true },
  desc: { type: String, default: '' },
  icon: { type: String, default: '' },
  tint: { type: String, default: '#34d399' }
})

defineEmits(['click'])

const path = computed(() => props.icon || 'M4 6h16M4 12h16M4 18h16')
</script>

<style scoped>
.mega-link {
  display: block;
  border-radius: 16px;
  background: transparent;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.mega-link::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--tint, #34d399) 0%, transparent 100%);
  opacity: 0;
  transition: opacity 0.2s ease;
}

.mega-link:hover {
  background: rgba(255, 255, 255, 0.05);
}

.mega-link:hover::before {
  opacity: 0.08;
}

.mega-link-content {
  display: flex;
  align-items: center;
  gap: 0.875rem;
  padding: 0.875rem 1rem;
  position: relative;
  z-index: 1;
}

.icon-wrapper {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--tint, #34d399) 12%, transparent);
  color: var(--tint, #34d399);
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.icon {
  width: 22px;
  height: 22px;
}

.mega-link:hover .icon-wrapper {
  transform: scale(1.05);
  box-shadow: 0 8px 20px color-mix(in srgb, var(--tint, #34d399) 25%, transparent);
}

.text-wrapper {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.label {
  font-weight: 600;
  font-size: 0.95rem;
  color: #fff;
}

.desc {
  font-size: 0.8rem;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.arrow {
  width: 18px;
  height: 18px;
  color: #475569;
  opacity: 0;
  transform: translateX(-4px);
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.mega-link:hover .arrow {
  opacity: 1;
  transform: translateX(0);
  color: var(--tint, #34d399);
}
</style>
