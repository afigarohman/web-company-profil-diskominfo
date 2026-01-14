<template>
  <div>
    <HeroSection :title="heroTitle" />
    <CardList :cards="laporanPenerimaanList" detailRouteName="penerima.detail" />
  </div>
</template>

<script>
import HeroSection from '@/components/laporanpenerimaan/HeroSection.vue';
import CardList from '@/components/laporanpengaduanadmin/CardList.vue';
import { publikasiService } from '@/service/api.js';

export default {
  name: 'LaporanPenerimaan',
  components: {
    HeroSection,
    CardList
  },
  data() {
    return {
      heroTitle: 'LAPORAN PENERIMAAN DISKOMINFO',
      laporanPenerimaanList: [],
      loading: false,
      error: null
    };
  },
  created() {
    this.fetchLaporanPenerimaan();
  },
  methods: {
    buildFileUrl(path) {
      if (!path) return null;
      if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
      }
      const base = import.meta?.env?.VITE_API_BASE_URL?.replace(/\/api$/, '') || 'http://localhost:8000';
      return `${base}/storage/${path}`;
    },
    isImagePath(path) {
      if (!path) return false;
      const clean = path.split('?')[0].toLowerCase();
      return clean.endsWith('.jpg') || clean.endsWith('.jpeg') || clean.endsWith('.png') || clean.endsWith('.webp') || clean.endsWith('.gif');
    },
    async fetchLaporanPenerimaan() {
      try {
        this.loading = true;
        this.error = null;

        const params = { kategori: 'penerima' };
        const response = await publikasiService.getAllPublikasi(params);

        if (response.data && response.data.data) {
          this.laporanPenerimaanList = response.data.data.map(item => {
            const rawPath = item.file_path;
            const fileUrl = rawPath ? this.buildFileUrl(rawPath) : null;
            const isImage = this.isImagePath(rawPath);

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
              status: item.meta?.status || 'PENDING',
              period: item.meta?.period || '',
              total_pengaduan: item.meta?.total_pengaduan || '',
              pengaduan_selesai: item.meta?.pengaduan_selesai || '',
              pengaduan_diproses: item.meta?.pengaduan_diproses || '',
              pengaduan_ditolak: item.meta?.pengaduan_ditolak || '',
              file_lampiran: fileUrl || item.file_path
            };
          });
        } else {
          this.laporanPenerimaanList = [];
        }
      } catch (err) {
        console.error('Error fetching laporan penerimaan:', err);
        this.error = 'Gagal memuat data laporan penerimaan.';
        this.laporanPenerimaanList = [];
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>

</style>