<template>
  <section class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-blue-900">
          Infografis Pelayanan Publik
        </h2>
        <router-link
          to="/infografis"
          class="text-sm text-blue-600 hover:text-blue-800 font-medium"
        >
          Lihat semua
        </router-link>
      </div>

      <div v-if="loading" class="text-center py-8 text-gray-500 text-sm">
        Memuat infografis...
      </div>

      <div v-else-if="error" class="text-center py-8 text-red-600 text-sm">
        {{ error }}
      </div>

      <div v-else-if="items.length === 0" class="text-center py-8 text-gray-500 text-sm">
        Belum ada infografis yang dipublikasikan.
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in items"
          :key="item.id"
          class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col"
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
              <p class="text-xs text-gray-500 mb-1">
                {{ item.date }}
              </p>
              <h3 class="font-semibold text-blue-900 text-sm mb-2 line-clamp-2">
                {{ item.title }}
              </h3>
              <p class="text-xs text-gray-700 flex-1 line-clamp-3" v-html="item.description"></p>
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
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { infografisService } from '@/service/api';

const items = ref([]);
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

    const response = await infografisService.getAll({ limit: 6 });
    const data = response.data.data || response.data;

    items.value = data.map(item => ({
      id: item.id,
      title: item.judul,
      description: item.deskripsi,
      image: item.gambar_url,
      date: item.tanggal
        ? new Date(item.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
          })
        : '',
    }));
  } catch (err) {
    console.error('Error fetching infografis for home:', err);
    error.value = 'Gagal memuat infografis.';
    items.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(fetchInfografis);
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>


