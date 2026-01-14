<template>
  <section class="galeri py-16">
    <div class="container mx-auto px-4">
      <!-- Title -->
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">
          Galeri Foto Dinas Komunikasi dan Informatika Kota Madiun
        </h2>
        <div class="w-32 h-1 bg-blue-600 mx-auto"></div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Dynamic Cards -->
        <div
          v-for="item in galeriList.slice(0, 3)"
          :key="item.id"
          class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 cursor-pointer"
          @click="goToGallery"
        >
          <!-- Image Section with Date Overlay -->
          <div v-if="item.gambar_url" class="relative">
            <img
              :src="item.gambar_url"
              :alt="item.judul"
              class="w-full object-cover bg-gray-50 h-48"
            >
            <!-- Date overlay at bottom left -->
            <div class="absolute bottom-2 left-2 bg-black/60 text-white px-2 py-1 rounded text-xs font-medium">
              {{ item.displayDate }}
            </div>
          </div>

          <!-- No Image Fallback -->
          <div v-else class="p-6">
            <h4 class="text-lg font-bold text-gray-800 mb-2">{{ item.judul }}</h4>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">{{ item.displayDate }}</span>
            </div>
          </div>

          <!-- Card Content -->
          <div class="p-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
              {{ item.judul }}
            </h4>
            <p v-if="item.deskripsi" class="text-sm text-gray-600 line-clamp-2">
              {{ item.deskripsi }}
            </p>
          </div>
        </div>

        <!-- Fallback static cards if no data -->
        <div v-if="galeriList.length === 0" class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
          <div class="bg-blue-600 p-4">
            <div class="flex items-center justify-between">
              <h3 class="text-white font-semibold text-sm">Galeri Foto</h3>
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Foto Kegiatan</h4>
            <p class="text-gray-600 text-sm mb-4">Dokumentasi kegiatan Dinas Komunikasi dan Informatika</p>
          </div>
        </div>
      </div>

      <!-- Button -->
      <div class="text-center">
        <router-link
          to="/galeri"
          class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105"
        >
          Selengkapnya Galeri
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
import { galeriService } from '@/service/api'

export default {
  name: 'Galeri',
  data() {
    return {
      galeriList: []
    }
  },
  async mounted() {
    await this.fetchGaleri()
  },
  methods: {
    async fetchGaleri() {
      try {
        const response = await galeriService.getAll({ limit: 3 })
        const raw = response.data
        // Handle both response formats: {success: true, data: [...]} or direct array
        const items = Array.isArray(raw.data) ? raw.data : (Array.isArray(raw) ? raw : [])
        this.galeriList = items.map(item => ({
          ...item,
          displayDate: this.formatDate(item.tanggal || item.created_at),
          gambar_url: item.gambar_url || (item.gambar ? this.buildStorageUrl(item.gambar) : null)
        }))
      } catch (error) {
        console.error('Error fetching galeri:', error)
        this.galeriList = []
      }
    },
    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    buildStorageUrl(path) {
      if (!path) return ''
      const base = import.meta?.env?.VITE_API_BASE_URL?.replace(/\/api$/, '') || 'http://localhost:8000'
      return `${base}/storage/${path}`
    },
    goToGallery() {
      this.$router.push({ path: '/galeri' })
    }
  }
}
</script>

<style scoped>
.galeri {
  background-color: #ffffff;
}
</style>

