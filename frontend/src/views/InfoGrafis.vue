<template>
  <div class="bg-gray-100 py-12 px-6 lg:px-12 min-h-screen">
    <div class="mx-auto max-w-7xl">
      <h2 class="text-3xl font-bold text-blue-900 mb-6 text-center">Infografis</h2>
      
      <div v-if="loading" class="text-center py-8">
        Memuat infografis...
      </div>
      
      <div v-else-if="error" class="text-center text-red-600 py-8">
        {{ error }}
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="item in infografis"
          :key="item.id"
          class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col"
        >
          <button
            type="button"
            class="text-left flex flex-col flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            @click="goToDetail(item.id)"
          >
            <img
              v-if="item.image"
              :src="item.image"
              :alt="item.title"
              class="w-full h-auto object-contain bg-black/5"
            />
            <div class="p-4 flex flex-col flex-1">
              <p class="text-xs text-gray-500 mb-1">{{ item.date }}</p>
              <h3 class="font-semibold text-blue-900 mb-2 line-clamp-2">
                {{ item.title }}
              </h3>
              <p class="text-sm text-gray-700 flex-1" v-html="item.description"></p>
            </div>
          </button>
          <div class="px-4 pb-4">
            <router-link
              :to="`/infografis/${item.id}`"
              class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 transition-colors"
            >
              Detail
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { infografisService } from '../service/api';

const infografis = ref([]);
const loading = ref(true);
const error = ref(null);
const router = useRouter();

const goToDetail = (id) => {
  if (!id) return;
  router.push({ path: `/infografis/${id}` });
};

const fetchInfografis = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await infografisService.getAll();
    const data = response.data.data || response.data;

    infografis.value = data.map(item => ({
      id: item.id,
      title: item.judul,
      description: item.deskripsi,
      image: item.gambar_url,
      date: item.tanggal
        ? new Date(item.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          })
        : '',
    }));
  } catch (err) {
    console.error('Error fetching infografis:', err);
    error.value = 'Gagal memuat data infografis';
    infografis.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(fetchInfografis);
</script>

<style scoped>
/* Tambahkan gaya khusus jika diperlukan */
</style>