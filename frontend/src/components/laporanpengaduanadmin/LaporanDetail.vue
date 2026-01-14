<template>
  <div class="laporan-detail">
    <!-- Header Section -->
    <div class="detail-header">
      <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
           <div class="flex items-start mb-2">
             <h1 class="text-3xl font-bold text-gray-800">
               {{ laporan.judul || 'Laporan Pengaduan Pelayanan Publik' }}
             </h1>
           </div>
        </div>
      </div>
    </div>

    <!-- Content Section -->
    <div class="detail-content">
      <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
              <!-- Informasi laporan dihapus karena sudah ada di hero section -->

              <!-- Description -->
              <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                  Deskripsi Laporan
                </h2>
                <div class="prose max-w-none">
                  <p class="text-gray-700 leading-relaxed">
                    {{ laporan.deskripsi || 'Berikut ini terlampir Laporan Pengaduan Pelayanan Publik Dinas Komunikasi dan Informatika Kota Madiun Bulan Juni Tahun 2025' }}
                  </p>
                </div>
              </div>

              <!-- Summary -->
              <div class="mb-8" v-if="laporan.ringkasan">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                  Ringkasan
                </h2>
                <div class="bg-gray-50 rounded-lg p-6">
                  <p class="text-gray-700 leading-relaxed">
                    {{ laporan.ringkasan }}
                  </p>
                </div>
              </div>

              <!-- Admin Notes -->
              <div class="mb-8" v-if="laporan.catatan_admin">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                  Catatan Admin
                </h2>
                <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
                  <p class="text-gray-700 leading-relaxed">
                    {{ laporan.catatan_admin }}
                  </p>
                </div>
              </div>

              <!-- File Attachment -->
              <div v-if="laporan.file_lampiran">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                  Lampiran Berita
                </h2>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                  <!-- PDF Preview Header -->
                  <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-lg mr-4">
                          <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                        </div>
                        <div>
                          <h3 class="font-semibold text-gray-800 text-lg">
                            {{ getFileName(laporan.file_lampiran) }}
                          </h3>
                          <p class="text-sm text-gray-600 mt-1">
                            Laporan Pengaduan Pelayanan Publik Diskominfo Kota Madiun
                          </p>
                          <div class="flex items-center mt-2 text-xs text-gray-500">
                            <span class="mr-4">
                              <i class="fas fa-calendar-alt mr-1"></i>
                              {{ formatDate(laporan.tanggal_publikasi) }}
                            </span>
                            <span>
                              <i class="fas fa-file-alt mr-1"></i>
                              PDF Document
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="flex space-x-2">
                        <button @click="previewPDF" class="btn-preview">
                          <i class="fas fa-eye mr-2"></i>
                          Preview
                        </button>
                        <button @click="downloadLaporan" :disabled="isDownloading" class="btn-download-primary">
                          <div v-if="isDownloading" class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                            <span>{{ Math.round(downloadProgress) }}%</span>
                          </div>
                          <div v-else class="flex items-center">
                            <i class="fas fa-download mr-2"></i>
                            Download File
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <!-- PDF Preview Area -->
                  <div v-if="showPreview" class="p-4">
                    <div class="bg-gray-100 rounded-lg p-4">
                      <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                          <i class="fas fa-file-pdf text-red-500 text-2xl mr-2"></i>
                          <span class="text-gray-700 font-medium">Preview PDF</span>
                        </div>
                        <button @click="showPreview = false" class="text-gray-500 hover:text-gray-700">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                      
                      <!-- PDF Thumbnail -->
                      <div class="mb-4">
                        <div class="bg-white border border-gray-300 rounded-lg p-4 flex items-center justify-center">
                          <div class="text-center">
                            <div class="bg-red-100 p-4 rounded-lg inline-block mb-3">
                              <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">{{ getFileName(laporan.file_lampiran) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ getFileSize(laporan.file_lampiran) }}</p>
                          </div>
                        </div>
                      </div>
                      
                      <!-- PDF Viewer -->
                      <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-300 flex items-center justify-between">
                          <span class="text-sm text-gray-600">PDF Viewer</span>
                          <div class="flex space-x-2">
                            <button class="text-gray-500 hover:text-gray-700 text-sm">
                              <i class="fas fa-search-plus"></i>
                            </button>
                            <button class="text-gray-500 hover:text-gray-700 text-sm">
                              <i class="fas fa-search-minus"></i>
                            </button>
                            <button class="text-gray-500 hover:text-gray-700 text-sm">
                              <i class="fas fa-expand"></i>
                            </button>
                          </div>
                        </div>
                        <iframe 
                          v-if="laporan.file_lampiran"
                          :src="getFileUrl(laporan.file_lampiran) + '#toolbar=0&navpanes=0&scrollbar=0'"
                          class="w-full h-96"
                          frameborder="0">
                        </iframe>
                        <div v-else class="h-96 flex items-center justify-center bg-gray-50">
                          <div class="text-center">
                            <i class="fas fa-file-pdf text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">PDF tidak tersedia untuk preview</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- File Info -->
                  <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                      <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-gray-600">Format: {{ getFileType(laporan.file_lampiran) }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-weight-hanging text-orange-500 mr-2"></i>
                        <span class="text-gray-600">Ukuran: {{ getFileSize(laporan.file_lampiran) }}</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-download text-green-500 mr-2"></i>
                        <span class="text-gray-600">Tersedia untuk download</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-eye text-purple-500 mr-2"></i>
                        <span class="text-gray-600">Dapat di-preview</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Tags -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <span class="tag">Informasi Secara Berkala</span>
                <span class="tag">Laporan Pengaduan</span>
                <span class="tag">Pelayanan Publik</span>
                <span class="tag">Diskominfo Kota Madiun</span>
              </div>
            </div>

            <!-- Related Reports -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Laporan Terkait</h3>
              <div class="space-y-3">
                <div v-for="report in relatedReports" :key="report.id" class="related-item">
                  <router-link
                    :to="{ name: report.routeName, params: { id: report.id } }"
                    class="block hover:bg-gray-50 p-3 rounded-lg transition-colors"
                  >
                    <h4 class="font-medium text-gray-800 text-sm mb-1">
                      {{ report.title }}
                    </h4>
                    <p class="text-xs text-gray-600">
                      {{ report.date }}
                    </p>
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Kominfo Kota Madiun</h3>
              <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start">
                  <i class="fas fa-map-marker-alt text-blue-500 mt-1 mr-3"></i>
                  <div>
                    <p>Jl. Perintis Kemerdekaan No. 32</p>
                    <p>Kel. Kartoharjo, Kecamatan Kartoharjo,</p>
                    <p>Kota Madiun, Jawa Timur.</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <i class="fas fa-clock text-blue-500 mt-1 mr-3"></i>
                  <div>
                    <p class="font-medium">Jam Pelayanan :</p>
                    <p>Senin - Kamis (07.00 - 15.30)</p>
                    <p>Jumat (06.30 - 14.30)</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import apiClient, { publikasiService } from '@/service/api.js';

export default {
  name: 'LaporanDetail',
  data() {
    return {
      laporan: {
        judul: '',
        bulan: '',
        tahun: '',
        deskripsi: '',
        is_published: false,
        tanggal_publikasi: null,
        total_pengaduan: 0,
        pengaduan_diproses: 0,
        pengaduan_selesai: 0,
        pengaduan_ditolak: 0,
        file_lampiran: null,
        admin: { name: 'Admin Diskominfo' },
        kategori: ''
      },
      showPreview: false,
      isDownloading: false,
      downloadProgress: 0,
      relatedReports: [],
      loading: false,
      error: null
    }
  },
  async created() {
    const id = this.$route.params.id;
    if (!id) return;

    try {
      this.loading = true;
      const response = await publikasiService.getPublikasiById(id);
      const data = response.data.data || response.data;

      const meta = data.meta || {};

      this.laporan = {
        judul: data.judul,
        bulan: meta.bulan || meta.period || '',
        tahun: meta.tahun || (data.published_at ? new Date(data.published_at).getFullYear().toString() : ''),
        deskripsi: data.isi || data.ringkasan,
        is_published: data.is_published,
        tanggal_publikasi: data.published_at || data.created_at,
        total_pengaduan: meta.total_pengaduan || meta.total_laporan || 0,
        pengaduan_diproses: meta.pengaduan_diproses || 0,
        pengaduan_selesai: meta.pengaduan_selesai || 0,
        pengaduan_ditolak: meta.pengaduan_ditolak || 0,
        file_lampiran: data.file_path,
        admin: { name: 'Admin Diskominfo' },
        kategori: data.kategori
      };
      // Setelah mendapatkan laporan utama, ambil laporan terkait
      await this.fetchRelatedReports(data.kategori, data.id);
    } catch (err) {
      console.error('Error fetching laporan detail:', err);
      this.error = 'Gagal memuat detail laporan.';
    } finally {
      this.loading = false;
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return 'N/A';
      const d = new Date(date);
      return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    },
    getStatusClass(isPublished) {
      return isPublished 
        ? 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium'
        : 'px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium';
    },
    getFileUrl(path) {
      if (!path) return '';
      if (path.startsWith('http')) return path;
      const base = apiClient.defaults.baseURL
        ? apiClient.defaults.baseURL.replace(/\/api\/?$/, '')
        : '';
      return base + path;
    },
    getFileName(filePath) {
      if (!filePath) return 'N/A';
      return filePath.split('/').pop();
    },
    getFileSize(filePath) {
      // Simulasi file size untuk demo
      if (!filePath) return 'N/A';
      const sizes = ['2.5 MB', '1.8 MB', '3.2 MB', '2.1 MB', '2.8 MB'];
      const randomIndex = Math.floor(Math.random() * sizes.length);
      return sizes[randomIndex];
    },
    getFileType(filePath) {
      if (!filePath) return 'N/A';
      const extension = filePath.split('.').pop().toLowerCase();
      const typeMap = {
        'pdf': 'PDF Document',
        'doc': 'Word Document',
        'docx': 'Word Document',
        'xls': 'Excel Spreadsheet',
        'xlsx': 'Excel Spreadsheet'
      };
      return typeMap[extension] || 'Document';
    },
    shareLaporan() {
      if (navigator.share) {
        navigator.share({
          title: this.laporan.judul,
          text: this.laporan.deskripsi,
          url: window.location.href
        });
      } else {
        // Fallback untuk browser yang tidak mendukung Web Share API
        navigator.clipboard.writeText(window.location.href);
        alert('Link telah disalin ke clipboard!');
      }
    },
    previewPDF() {
      this.showPreview = !this.showPreview;
    },
    async downloadLaporan() {
      if (this.laporan.file_lampiran) {
        try {
          this.isDownloading = true;
          this.downloadProgress = 0;
          
          // Simulasi progress download
          const progressInterval = setInterval(() => {
            if (this.downloadProgress < 90) {
              this.downloadProgress += Math.random() * 20;
            }
          }, 200);
          
          // Download file
          const link = document.createElement('a');
          link.href = this.getFileUrl(this.laporan.file_lampiran);
          link.download = this.getFileName(this.laporan.file_lampiran);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          
          // Complete progress
          this.downloadProgress = 100;
          clearInterval(progressInterval);
          
          // Reset after 2 seconds
          setTimeout(() => {
            this.isDownloading = false;
            this.downloadProgress = 0;
          }, 2000);
          
        } catch (error) {
          console.error('Download error:', error);
          this.isDownloading = false;
          this.downloadProgress = 0;
        }
      }
    },
    async fetchRelatedReports(kategori, excludeId) {
      try {
        const response = await publikasiService.getAllPublikasi({ kategori });
        const list = response.data.data || response.data || [];
        const routeName =
          kategori === 'penerima' ? 'penerima.detail' : 'pengaduan.detail';

        this.relatedReports = list
          .filter(item => item.id !== excludeId)
          .slice(0, 3)
          .map(item => ({
            id: item.id,
            title: item.judul,
            date: this.formatDate(item.published_at || item.created_at),
            routeName
          }));
      } catch (error) {
        console.error('Error fetching related reports:', error);
        this.relatedReports = [];
      }
    }
  }
}
</script>

<style scoped>
.laporan-detail {
  background-color: #f8f9fa;
  min-height: 100vh;
  margin-top:5.7%;
}

.detail-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-share {
  @apply bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2;
}

.btn-download {
  @apply bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2;
}

.btn-preview {
  @apply bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2;
}

.btn-download-primary {
  @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center;
}

.btn-download-primary:disabled {
  @apply bg-gray-400 cursor-not-allowed;
}

.info-item {
  @apply p-4 bg-gray-50 rounded-lg;
}

.stat-card {
  @apply rounded-lg transition-transform duration-200 hover:scale-105;
}

.tag {
  @apply bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium;
}

.related-item {
  @apply border-b border-gray-200 last:border-b-0;
}

.prose {
  @apply text-gray-700;
}

.prose p {
  @apply mb-4;
}

.prose p:last-child {
  @apply mb-0;
}
</style>
