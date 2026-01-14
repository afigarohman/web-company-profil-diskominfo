<template>
  <div class="struktur-detail-page">
    <div class="struktur-detail-container">
      <div class="struktur-detail-header">
        <h1 class="struktur-detail-title">Struktur Organisasi Lengkap</h1>
        <h2 class="struktur-detail-subtitle">Dinas Komunikasi dan Informatika Kota Madiun</h2>
        <div class="struktur-detail-divider"></div>
      </div>

      <div class="struktur-detail-content">
        <div
          class="struktur-image-wrapper"
          @pointerdown="startDrag"
          @pointermove="onDrag"
          @pointerup="endDrag"
          @pointerleave="endDrag"
          @touchstart.prevent="startDrag"
          @touchmove.prevent="onDrag"
          @touchend="endDrag"
        >
          <div class="control-bar">
            <button type="button" @click="zoomOut" class="ctrl-btn" aria-label="Zoom out">－</button>
            <button type="button" @click="zoomIn" class="ctrl-btn" aria-label="Zoom in">＋</button>
            <a :href="imageUrl" download class="ctrl-btn" aria-label="Unduh gambar">⭳</a>
          </div>
          <div
            class="struktur-image-pan"
            :class="{ dragging: isDragging }"
            :style="panStyle"
          >
            <img
              :src="imageUrl"
              alt="Struktur Organisasi Dinas Komunikasi dan Informatika Kota Madiun"
              class="struktur-image"
            />
          </div>
        </div>
      </div>

      <div class="struktur-detail-actions">
        <button @click="goBack" class="struktur-back-button">
          ← Kembali
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const zoom = ref(1)
const imageUrl = new URL('@/assets/img/strukturorg.png', import.meta.url).href
const offset = ref({ x: 0, y: 0 })
const isDragging = ref(false)
const lastPoint = ref({ x: 0, y: 0 })

const goBack = () => {
  router.go(-1) // Go back to previous page
}

const zoomIn = () => {
  zoom.value = Math.min(2, +(zoom.value + 0.1).toFixed(2))
}

const zoomOut = () => {
  zoom.value = Math.max(0.5, +(zoom.value - 0.1).toFixed(2))
}

const panStyle = computed(() => ({
  transform: `translate(${offset.value.x}px, ${offset.value.y}px) scale(${zoom.value})`,
}))

const getPoint = (event) => {
  if (event.touches && event.touches.length) {
    const t = event.touches[0]
    return { x: t.clientX, y: t.clientY }
  }
  return { x: event.clientX, y: event.clientY }
}

const startDrag = (event) => {
  isDragging.value = true
  lastPoint.value = getPoint(event)
}

const onDrag = (event) => {
  if (!isDragging.value) return
  const point = getPoint(event)
  const dx = point.x - lastPoint.value.x
  const dy = point.y - lastPoint.value.y
  offset.value = {
    x: offset.value.x + dx,
    y: offset.value.y + dy,
  }
  lastPoint.value = point
}

const endDrag = () => {
  isDragging.value = false
}
</script>

<style scoped>
.struktur-detail-page {
  margin-top: 6%;
  min-height: 100vh;
  background-color: #f8fafc;
  padding: 2rem 1rem;
}

.struktur-detail-container {
  max-width: 1200px;
  margin: 0 auto;
  background-color: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.struktur-detail-header {
  text-align: center;
  padding: 3rem 2rem 2rem;
  background: linear-gradient(135deg, #2F7B52 0%, #0292D8 100%);
  color: white;
}

.struktur-detail-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@media (max-width: 768px) {
  .struktur-detail-title {
    font-size: 2rem;
  }
}

.struktur-detail-subtitle {
  font-size: 1.25rem;
  font-weight: 500;
  opacity: 0.9;
  margin-bottom: 1.5rem;
}

.struktur-detail-divider {
  width: 6rem;
  height: 0.25rem;
  background-color: rgba(255, 255, 255, 0.8);
  margin: 0 auto;
  border-radius: 0.125rem;
}

.struktur-detail-content {
  padding: 3rem 2rem;
}

.struktur-image-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  position: relative;
  overflow: hidden;
  background: #f5f7fb;
  border-radius: 0.5rem;
  min-height: 360px;
}

.struktur-image-pan {
  cursor: grab;
  transition: transform 0.1s ease;
}

.struktur-image-pan.dragging {
  cursor: grabbing;
}

.struktur-image {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  display: block;
  pointer-events: none;
}

.control-bar {
  position: absolute;
  top: 12px;
  right: 12px;
  display: flex;
  gap: 6px;
  z-index: 5;
}

.ctrl-btn {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  font-size: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
  text-decoration: none;
}

.ctrl-btn:hover {
  background: rgba(0, 0, 0, 0.65);
  transform: translateY(-1px);
}

.struktur-detail-actions {
  padding: 2rem;
  text-align: center;
  background-color: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.struktur-back-button {
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, #2F7B52 0%, #0292D8 100%);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.struktur-back-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.struktur-back-button:active {
  transform: translateY(0);
}

@media (max-width: 768px) {
  .struktur-detail-page {
    padding: 1rem 0.5rem;
  }

  .struktur-detail-header {
    padding: 2rem 1rem 1.5rem;
  }

  .struktur-detail-content {
    padding: 2rem 1rem;
  }

  .struktur-detail-actions {
    padding: 1.5rem 1rem;
  }
}
</style>