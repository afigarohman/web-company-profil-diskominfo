<template>
  <div>
    <HeroSection />

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Memuat data laporan pengaduan...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-message">
        <h3>Terjadi Kesalahan</h3>
        <p>{{ error }}</p>
        <button @click="fetchLaporanPengaduan" class="retry-button">Coba Lagi</button>
      </div>
    </div>

    <!-- Hanya daftar kartu laporan -->
    <div v-else>
      <CardList :cards="laporanList" />
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import HeroSection from '@/components/laporanpengaduanadmin/HeroSection.vue';
import CardList from '@/components/laporanpengaduanadmin/CardList.vue';
import { publikasiService } from '@/service/api.js';

export default {
  name: 'LaporanPengaduan',
  components: {
    HeroSection,
    CardList
  },
  setup() {
    const loading = ref(true);
    const error = ref(null);
    const laporanList = ref([]);

    const buildFileUrl = (path) => {
      if (!path) return null;
      if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
      }
      const base = import.meta?.env?.VITE_API_BASE_URL?.replace(/\/api$/, '') || 'http://localhost:8000';
      return `${base}/${path.replace(/^\/+/, '')}`;
    };

    const isImagePath = (path) => {
      if (!path) return false;
      const clean = path.split('?')[0].toLowerCase();
      return clean.endsWith('.jpg') || clean.endsWith('.jpeg') || clean.endsWith('.png') || clean.endsWith('.webp') || clean.endsWith('.gif');
    };

    const fetchLaporanPengaduan = async () => {
      try {
        loading.value = true;
        error.value = null;
        
        console.log('Fetching publikasi pengaduan...');
        
        const params = {
          kategori: 'pengaduan'
        };
        
        console.log('Request params:', params);
        
        const response = await publikasiService.getAllPublikasi(params);
        
        console.log('Response received:', response);
        console.log('Response data:', response.data);
        
        if (response.data && response.data.data) {
          laporanList.value = response.data.data.map(item => {
            const rawPath = item.file_path;
            const fileUrl = rawPath ? buildFileUrl(rawPath) : null;
            const isImage = isImagePath(rawPath);

            return {
              id: item.id,
              // thumbnail: pakai gambar file jika image, kalau bukan pakai gambar default
              foto: isImage && fileUrl ? fileUrl : '/src/assets/img/berita/laporan1.jpeg',
              judul: item.judul,
              tanggal: new Date(item.published_at || item.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
              }),
              penjelasan: item.ringkasan || item.isi,
              status: item.meta?.status || 'diproses',
              prioritas: item.meta?.total_pengaduan ? `${item.meta.total_pengaduan} Total` : 'Normal',
              kategori: item.kategori,
              link: `#laporan-${item.id}`,
              period: item.meta?.period,
              total_pengaduan: item.meta?.total_pengaduan,
              pengaduan_selesai: item.meta?.pengaduan_selesai,
              pengaduan_diproses: item.meta?.pengaduan_diproses,
              pengaduan_ditolak: item.meta?.pengaduan_ditolak,
              // file lampiran selalu pakai URL penuh untuk download
              file_lampiran: fileUrl
            };
          });

          console.log('Data processed successfully. Total items:', laporanList.value.length);
        } else {
          console.error('Invalid response structure:', response);
          error.value = 'Format data tidak valid. Silakan coba lagi.';
        }
      } catch (err) {
        console.error('Error fetching publikasi pengaduan:', err);
        console.error('Error details:', {
          message: err.message,
          status: err.response?.status,
          data: err.response?.data
        });
        
        if (err.code === 'ECONNREFUSED') {
          error.value = 'Tidak dapat terhubung ke server. Pastikan backend berjalan di port 8000.';
        } else if (err.code === 'ECONNABORTED') {
          error.value = 'Request timeout. Server mungkin lambat atau tidak merespons.';
        } else if (err.response?.status === 500) {
          error.value = 'Server error. Ada masalah di backend.';
        } else {
          error.value = 'Gagal memuat data laporan pengaduan. Silakan coba lagi.';
        }
      } finally {
        loading.value = false;
      }
    };

    onMounted(async () => {
      try {
        await fetchLaporanPengaduan();
      } catch (err) {
        console.error('Error in onMounted:', err);
        error.value = 'Gagal memuat data. Silakan coba lagi.';
      }
    });

    return {
        loading,
        error,
        laporanList,
        fetchLaporanPengaduan,
      };
  }
};
</script>

<style scoped>
/* Loading Styles */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  margin-top:6%;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error Styles */
.error-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
}

.error-message {
  text-align: center;
  max-width: 500px;
}

.error-message h3 {
  color: #e74c3c;
  margin-bottom: 1rem;
}



/* Info Box Styles */
.info-box {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background-color: #e3f2fd;
  color: #1976d2;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
}

.info-box i {
  font-size: 16px;
}

.retry-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  margin-top: 1rem;
  transition: background-color 0.2s;
}

.retry-button:hover {
  background-color: #2980b9;
}

/* Statistics Styles */
.statistics-section {
  background-color: #f8f9fa;
}

.stat-card {
  padding: 1.5rem;
  border-radius: 0.5rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  opacity: 0.9;
}

/* Filter Styles */
.filter-select, .filter-input {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  min-width: 150px;
}

.filter-input {
  min-width: 200px;
}

.filter-select:focus, .filter-input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

/* Pagination Styles */
.pagination-section {
  background-color: #f8f9fa;
}

.pagination-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.pagination-button:hover:not(.disabled) {
  background-color: #2980b9;
}

.pagination-button.disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.pagination-info {
  padding: 0.5rem 1rem;
  background-color: white;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
}

/* Action Button */
.action-button {
  background-color: #27ae60;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-button:hover {
  background-color: #229954;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 8px;
  max-width: 90vw;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
}

.modal-close:hover {
  background-color: #f3f4f6;
}

.modal-body {
  padding: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .filter-section .flex {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-select, .filter-input {
    min-width: auto;
    width: 100%;
  }
  
  .modal-content {
    max-width: 95vw;
    max-height: 95vh;
  }
  
  .modal-header,
  .modal-body {
    padding: 1rem;
  }
}
</style>