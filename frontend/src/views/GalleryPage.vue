<template>
  <div class="bg-gray-100 py-12 px-6 lg:px-12 min-h-screen mt-9">
    <div class="mx-auto max-w-7xl">
      <!-- Tombol Kembali -->
      <div class="mb-6">
        <button
          @click="goBack"
          class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 group"
        >
          <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Kembali
        </button>
      </div>
      
      <h2 class="text-3xl font-bold text-blue-900 mb-6 text-center">Galeri Foto</h2>
      
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Memuat galeri...</p>
      </div>
      
      <div v-else-if="error" class="text-center text-red-600 py-8">
        {{ error }}
      </div>
      
      <div v-else-if="allImages.length === 0" class="text-center py-8">
        <p class="text-gray-600">Belum ada foto di galeri.</p>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(image, index) in allImages"
          :key="`${image.galeriId}-${index}`"
          class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer transform hover:scale-105 transition-transform duration-300"
          @click="openLightbox(image, index)"
        >
          <div class="relative">
            <img
              :src="image.url"
              :alt="image.alt"
              class="w-full h-64 object-cover"
              loading="lazy"
            />
            <div v-if="image.title" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
              <h3 class="text-white font-semibold text-sm mb-1">{{ image.title }}</h3>
              <p v-if="image.date" class="text-white/80 text-xs">{{ image.date }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <div
      v-if="lightboxImage"
      class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
      @click="closeLightbox"
    >
      <div class="relative max-w-6xl w-full max-h-full">
        <button
          @click="closeLightbox"
          class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black/50 rounded-full p-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <button
          v-if="lightboxIndex > 0"
          @click.stop="previousLightboxImage"
          class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/50 rounded-full p-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        
        <button
          v-if="lightboxIndex < allImages.length - 1"
          @click.stop="nextLightboxImage"
          class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black/50 rounded-full p-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
        
        <img
          :src="lightboxImage.url"
          :alt="lightboxImage.alt"
          class="max-w-full max-h-[90vh] mx-auto object-contain"
          @click.stop
        />
        
        <div v-if="lightboxImage.title || lightboxImage.description" class="absolute bottom-0 left-0 right-0 bg-black/70 text-white p-4">
          <h3 v-if="lightboxImage.title" class="text-xl font-semibold mb-2">{{ lightboxImage.title }}</h3>
          <p v-if="lightboxImage.description" class="text-sm">{{ lightboxImage.description }}</p>
          <p v-if="lightboxImage.date" class="text-xs text-gray-300 mt-2">{{ lightboxImage.date }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { galeriService } from '../service/api';

const router = useRouter();

const goBack = () => {
  // Cek apakah ada history sebelumnya, jika tidak maka arahkan ke profil
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push('/profil');
  }
};

const galeri = ref([]);
const loading = ref(true);
const error = ref(null);
const lightboxImage = ref(null);
const lightboxIndex = ref(0);

const allImages = computed(() => {
  const images = [];
  galeri.value.forEach(item => {
    const imagesList = item.gambar_list || [];
    if (imagesList.length > 0) {
      imagesList.forEach((url, index) => {
        images.push({
          url,
          alt: item.judul || `Gambar ${index + 1}`,
          title: item.judul,
          description: item.deskripsi,
          date: item.tanggal
            ? new Date(item.tanggal).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
              })
            : '',
          galeriId: item.id,
        });
      });
    } else if (item.gambar_url) {
      images.push({
        url: item.gambar_url,
        alt: item.judul || 'Gambar',
        title: item.judul,
        description: item.deskripsi,
        date: item.tanggal
          ? new Date(item.tanggal).toLocaleDateString('id-ID', {
              day: 'numeric',
              month: 'long',
              year: 'numeric',
            })
          : '',
        galeriId: item.id,
      });
    }
  });
  return images;
});

const openLightbox = (image, index) => {
  lightboxImage.value = image;
  lightboxIndex.value = index;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  lightboxImage.value = null;
  document.body.style.overflow = '';
};

const previousLightboxImage = () => {
  if (lightboxIndex.value > 0) {
    lightboxIndex.value--;
    lightboxImage.value = allImages.value[lightboxIndex.value];
  }
};

const nextLightboxImage = () => {
  if (lightboxIndex.value < allImages.value.length - 1) {
    lightboxIndex.value++;
    lightboxImage.value = allImages.value[lightboxIndex.value];
  }
};

// Handle keyboard navigation
const handleKeydown = (e) => {
  if (!lightboxImage.value) return;
  
  if (e.key === 'Escape') {
    closeLightbox();
  } else if (e.key === 'ArrowLeft') {
    previousLightboxImage();
  } else if (e.key === 'ArrowRight') {
    nextLightboxImage();
  }
};

const fetchGaleri = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await galeriService.getAll();
    const data = response.data.data || response.data;
    
    galeri.value = Array.isArray(data) ? data : (data ? [data] : []);
  } catch (err) {
    console.error('Error fetching galeri:', err);
    error.value = 'Gagal memuat data galeri';
    galeri.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchGaleri();
  window.addEventListener('keydown', handleKeydown);
});

// Cleanup
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
});
</script>

<style scoped>
/* Additional styles if needed */
</style>
