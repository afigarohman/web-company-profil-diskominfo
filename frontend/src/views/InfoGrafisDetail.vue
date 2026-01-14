<template>
  <div class="bg-white py-6 px-0 min-h-screen">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-10">
      <button
        type="button"
        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-4"
        @click="goBack"
      >
        ← Kembali
      </button>

      <div v-if="loading" class="text-center py-12 text-gray-500">
        Memuat detail infografis...
      </div>

      <div v-else-if="error" class="text-center py-12 text-red-600">
        {{ error }}
      </div>

      <div v-else-if="!item" class="text-center py-12 text-gray-500">
        Data infografis tidak ditemukan.
      </div>

      <div v-else class="bg-white rounded-none lg:rounded-xl lg:shadow-md overflow-hidden">
        <!-- Header judul + tanggal -->
        <div class="px-2 sm:px-4 lg:px-6 pt-2 pb-4 lg:pt-4 lg:pb-6 bg-white">
          <p class="flex items-center text-[13px] sm:text-sm text-gray-500 mb-1">
            <span class="mr-1">🗓</span>
            <span>{{ item.date }}</span>
          </p>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">
            {{ item.title }}
          </h1>
        </div>

        <!-- Gambar utama (full, seperti poster) -->
        <div v-if="item.images && item.images.length" class="bg-black">
          <img
            :src="item.images[activeImageIndex]"
            :alt="item.title"
            class="w-full max-h-[90vh] object-contain bg-black"
          />
          <div
            v-if="item.images.length > 1"
            class="flex items-center gap-2 px-4 py-3 bg-gray-900/80 overflow-x-auto"
          >
            <button
              v-for="(img, idx) in item.images"
              :key="idx"
              type="button"
              class="flex-shrink-0 border-2 rounded-md overflow-hidden"
              :class="idx === activeImageIndex ? 'border-blue-400' : 'border-transparent opacity-70 hover:opacity-100'"
              @click="activeImageIndex = idx"
            >
              <img
                :src="img"
                :alt="`Thumbnail ${idx + 1}`"
                class="h-16 w-24 object-cover"
              />
            </button>
          </div>
        </div>

        <!-- Deskripsi / keterangan tambahan -->
        <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-3 bg-white">
          <h2 class="text-base sm:text-lg font-semibold text-gray-800">
            Keterangan Infografis
          </h2>
          <div
            class="prose prose-sm sm:prose max-w-none text-gray-800"
            v-html="item.description"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { infografisService } from '@/service/api';

const route = useRoute();
const router = useRouter();

const item = ref(null);
const loading = ref(true);
const error = ref(null);
const activeImageIndex = ref(0);

const fetchDetail = async (id) => {
  if (!id) return;
  try {
    loading.value = true;
    error.value = null;

    const response = await infografisService.getById(id);
    const data = response.data.data || response.data;

    item.value = {
      id: data.id,
      title: data.judul,
      description: data.deskripsi,
      date: data.tanggal
        ? new Date(data.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          })
        : '',
      // gunakan gambar_list dari backend bila tersedia, fallback ke gambar_url tunggal
      images: Array.isArray(data.gambar_list) && data.gambar_list.length
        ? data.gambar_list
        : (data.gambar_url ? [data.gambar_url] : []),
    };

    activeImageIndex.value = 0;
  } catch (err) {
    console.error('Error fetching infografis detail:', err);
    error.value = 'Gagal memuat detail infografis.';
    item.value = null;
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  if (window.history.length > 1) {
    router.back();
  } else {
    router.push({ path: '/infografis' });
  }
};

onMounted(() => {
  fetchDetail(route.params.id);
});

watch(
  () => route.params.id,
  (newId) => {
    fetchDetail(newId);
  }
);
</script>

<style scoped>
.prose :deep(img) {
  max-width: 100%;
  height: auto;
}
</style>


