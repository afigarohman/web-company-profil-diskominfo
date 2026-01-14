<template>
  <section class="tugas-pokok-fungsi py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <!-- Title -->
      <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">
          Tugas Pokok dan Fungsi Dinas Komunikasi dan Informatika Kota Madiun
        </h2>
        <div class="w-32 h-1 bg-blue-600 mx-auto"></div>
      </div>

      <!-- Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        <!-- Left Column - Text Content -->
        <div class="bg-white rounded-lg shadow-lg p-8">
          <!-- Jika ada konten dari admin (tabel profil), tampilkan langsung -->
          <div v-if="tugasData?.konten" v-html="tugasData.konten"></div>

          <!-- Jika belum ada konten di admin, gunakan teks default -->
          <div v-else>
            <h3 class="text-xl font-bold text-blue-900 mb-6">Dasar Hukum</h3>
            <p class="text-gray-700 leading-relaxed mb-6">
              Tugas pokok dan fungsi Dinas Komunikasi dan Informatika Kota Madiun ditetapkan berdasarkan 
              Peraturan Wali Kota Madiun Nomor 72 Tahun 2021 tentang Kedudukan, Susunan Organisasi, 
              Tugas, Fungsi dan Tata Kerja Dinas Komunikasi dan Informatika.
            </p>
            
            <h4 class="text-lg font-semibold text-blue-800 mb-4">Tugas Pokok:</h4>
            <ul class="text-gray-700 space-y-3 mb-6">
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Melaksanakan urusan pemerintahan di bidang komunikasi dan informatika</span>
              </li>
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Melaksanakan urusan pemerintahan di bidang persandian</span>
              </li>
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Melaksanakan urusan pemerintahan di bidang statistik</span>
              </li>
            </ul>

            <h4 class="text-lg font-semibold text-blue-800 mb-4">Fungsi:</h4>
            <ul class="text-gray-700 space-y-3">
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Perumusan kebijakan teknis di bidang komunikasi dan informatika</span>
              </li>
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Pelaksanaan kebijakan di bidang komunikasi dan informatika</span>
              </li>
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Pelaksanaan evaluasi dan pelaporan di bidang komunikasi dan informatika</span>
              </li>
              <li class="flex items-start">
                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                <span>Pelaksanaan administrasi dinas</span>
              </li>
            </ul>
          </div>
        </div>

        
        <div class="flex justify-center">
          <div
            class="w-full max-w-lg bg-gray-200 rounded-xl overflow-hidden flex items-center justify-center shadow-md"
            style="aspect-ratio: 3 / 3;"
          >
            <iframe
              v-if="tugasData && tugasData.pdf_url"
              :src="tugasData.pdf_url"
              class="w-full h-full border-0"
            ></iframe>
            <div v-else class="text-center px-4 text-gray-500 text-sm">
              Belum ada dokumen PDF yang diunggah untuk Tugas Pokok dan Fungsi.
              Silakan unggah PDF pada menu Profil (kategori <strong>Tugas Pokok &amp; Fungsi</strong>) di halaman admin.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { profilService } from '@/service/api'

export default {
  name: 'TugasPokokFungsi',
  data() {
    return {
      tugasData: null,
      loading: false
    }
  },
  async mounted() {
    await this.fetchTugasData()
  },
  methods: {
    async fetchTugasData() {
      try {
        this.loading = true
        // Ambil konten Tugas Pokok & Fungsi dari tabel profil (kategori: tugas-pokok-fungsi)
        const response = await profilService.getProfilByCategory('tugas-pokok-fungsi')
        this.tugasData = response.data
      } catch (error) {
        console.error('Error fetching tugas pokok fungsi data:', error)
        this.tugasData = null
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.tugas-pokok-fungsi {
  background-color: #f9fafb;
}
</style> 