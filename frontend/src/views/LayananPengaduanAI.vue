<template>
  <section class="container mx-auto mt-12 px-4 py-8 max-w-4xl">
    <h1 class="text-2xl font-semibold mb-2">Layanan Pengaduan - Chat Asisten AI</h1>
    <p class="text-sm text-gray-600 mb-4">
      Asisten ini hanya menjawab topik: PPID, Aduan/Konsultasi Hukum, Kontribusi Berita, Laporan Kekerasan pada Perempuan & Anak,
      Lapor Bapak, Insiden Keamanan Informasi, SP4N LAPOR, Pengaduan, Aduan Konten Negatif, Aduan Nomor Telepon, Aduan Nomor Rekening, Aduan Hoaks.
      Untuk rincian lebih lengkap, kunjungi situs resmi Awak Sigap.
    </p>

    <div class="border rounded-lg p-4 bg-white shadow-sm">
      <div class="h-80 overflow-y-auto space-y-4 mb-4 pr-2" ref="messagesRef">
        <div v-for="(m, idx) in messages" :key="idx" class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
          <div :class="[
              'px-3 py-2 rounded-lg max-w-[85%] whitespace-pre-wrap text-sm',
              m.role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800'
            ]">
            {{ m.content }}
            <template v-if="m.role === 'assistant' && m.link">
              <div class="mt-2 text-xs">
                Ingin detail lebih lanjut? Lihat di
                <a class="text-blue-600 underline" href="https://awaksigap.madiunkota.go.id/" target="_blank" rel="noopener noreferrer">Awak Sigap</a>.
              </div>
            </template>
          </div>
        </div>
        <div v-if="isTyping" class="text-xs text-gray-500">Asisten sedang menulis…</div>
        
        <!-- Template Pertanyaan - Tampilkan jika belum ada banyak pesan -->
        <div v-if="messages.length <= 1" class="space-y-3 pt-2">
          <p class="text-xs font-medium text-gray-600 mb-2">Pertanyaan yang sering ditanyakan:</p>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="(question, idx) in suggestedQuestions"
              :key="idx"
              @click="selectQuestion(question.text)"
              class="px-3 py-2 text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-full border border-blue-200 transition-colors duration-200"
              :disabled="isTyping"
            >
              {{ question.text }}
            </button>
          </div>
        </div>
      </div>

      <!-- Template Pertanyaan di bawah chat -->
      <div v-if="messages.length > 1" class="mb-3 pb-3 border-b">
        <p class="text-xs font-medium text-gray-600 mb-2">Pertanyaan cepat:</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(question, idx) in suggestedQuestions"
            :key="idx"
            @click="selectQuestion(question.text)"
            class="px-3 py-1.5 text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-full border border-gray-200 transition-colors duration-200"
            :disabled="isTyping"
          >
            {{ question.text }}
          </button>
        </div>
      </div>

      <form @submit.prevent="onSend" class="flex gap-2">
        <input
          v-model="input"
          type="text"
          placeholder="Tulis pertanyaan Anda..."
          class="flex-1 border rounded-md px-3 py-2"
          :disabled="isTyping"
        />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors" :disabled="isTyping || !input.trim()">
          Kirim
        </button>
      </form>
      <p class="mt-2 text-xs text-gray-500">
        Catatan: Untuk jawaban rinci dan prosedur lengkap, kunjungi
        <a class="text-blue-600 underline" href="https://awaksigap.madiunkota.go.id/" target="_blank" rel="noopener noreferrer">Awak Sigap</a>.
      </p>
    </div>
  </section>
  
</template>

<script setup>
import { ref, nextTick } from 'vue'

// Template pertanyaan yang ditampilkan di UI
const suggestedQuestions = [
  { text: 'Saya bingung, mau lapor tapi tidak tahu harus kemana', keywords: ['bingung'] },
  { text: 'Bagaimana cara mengajukan permohonan PPID?', keywords: ['ppid'] },
  { text: 'Saya butuh konsultasi hukum', keywords: ['konsultasi hukum'] },
  { text: 'Cara melaporkan kekerasan pada perempuan dan anak?', keywords: ['kekerasan'] },
  { text: 'Bagaimana cara menggunakan SP4N LAPOR?', keywords: ['sp4n'] },
  { text: 'Saya ingin melaporkan berita hoaks', keywords: ['hoaks'] },
  { text: 'Cara mengadukan nomor telepon yang mengganggu?', keywords: ['nomor telepon'] },
  { text: 'Saya ingin mengadukan nomor rekening penipuan', keywords: ['nomor rekening'] }
]

// Template pertanyaan dan jawaban - Urutan: sapaan -> user bingung -> layanan spesifik -> fallback
const questionTemplates = [
  // 1. SAPAAN
  {
    keywords: ['halo', 'hai', 'hello', 'hi', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam', 'assalamualaikum', 'salam'],
    answer: 'Halo! Selamat datang di Layanan Pengaduan. Saya di sini untuk membantu Anda menemukan layanan yang tepat.\n\nLayanan yang tersedia:\n• PPID / Informasi Publik\n• Konsultasi atau Aduan Hukum\n• Kontribusi Berita\n• Kekerasan terhadap Perempuan & Anak\n• SP4N LAPOR / Lapor Bapak\n• Insiden Keamanan Informasi\n• Pengaduan Umum\n• Aduan Konten Negatif\n• Aduan Nomor Telepon\n• Aduan Nomor Rekening\n• Aduan Hoaks\n\nSilakan ceritakan apa yang bisa saya bantu, atau pilih salah satu kategori di atas.'
  },
  {
    keywords: ['terima kasih', 'thanks', 'makasih', 'makasih ya', 'terimakasih'],
    answer: 'Sama-sama! Senang bisa membantu Anda. Jika ada pertanyaan lain atau butuh informasi lebih lanjut, jangan ragu untuk bertanya. Kami siap membantu Anda kapan saja.'
  },
  
  // 2. USER BINGUNG / PERTANYAAN UMUM (dipindah ke bawah setelah layanan spesifik)
  
  // 3. LAYANAN SPESIFIK - PPID
  {
    keywords: ['ppid', 'informasi publik', 'permohonan informasi', 'keterbukaan informasi', 'minta informasi', 'butuh informasi', 'info publik', 'cara ppid', 'bagaimana ppid', 'prosedur ppid'],
    answer: 'Baik, saya jelaskan tentang layanan PPID (Pejabat Pengelola Informasi dan Dokumentasi).\n\nLayanan ini membantu Anda mendapatkan informasi publik dari instansi pemerintah sesuai dengan Undang-Undang Keterbukaan Informasi Publik.\n\nLangkah singkat:\n1. Kunjungi website resmi PPID atau Awak Sigap\n2. Isi formulir permohonan informasi\n3. Lampirkan dokumen pendukung (jika diperlukan)\n4. Tunggu konfirmasi dan proses verifikasi\n\nApakah Anda sudah tahu informasi apa yang ingin dimohonkan? Jika sudah, silakan lanjutkan ke website Awak Sigap untuk mengisi formulir.'
  },
  
  // 3. LAYANAN SPESIFIK - KONSULTASI HUKUM
  {
    keywords: ['konsultasi hukum', 'aduan hukum', 'bantuan hukum', 'masalah hukum', 'butuh pengacara', 'perlu hukum', 'masalah legal', 'saran hukum', 'cara konsultasi hukum', 'bagaimana konsultasi hukum', 'prosedur konsultasi hukum'],
    answer: 'Baik, saya jelaskan tentang layanan Konsultasi atau Aduan Hukum.\n\nLayanan ini membantu Anda mendapatkan bantuan hukum, konsultasi, atau mengajukan aduan terkait masalah hukum yang Anda hadapi.\n\nLangkah singkat:\n1. Siapkan dokumen terkait masalah hukum Anda\n2. Akses layanan melalui Awak Sigap\n3. Isi formulir konsultasi atau aduan\n4. Tim hukum akan meninjau dan memberikan respon\n\nApakah Anda sudah siap dengan dokumen pendukung? Jika ya, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - KONTRIBUSI BERITA
  {
    keywords: ['kontribusi berita', 'kirim berita', 'berita', 'artikel', 'kirim artikel', 'submit berita', 'mau kirim berita'],
    answer: 'Baik, saya jelaskan tentang layanan Kontribusi Berita.\n\nLayanan ini memungkinkan Anda mengirimkan berita atau artikel untuk dipublikasikan melalui media resmi pemerintah.\n\nLangkah singkat:\n1. Siapkan konten berita yang ingin Anda kirimkan\n2. Pastikan berita sesuai dengan pedoman konten\n3. Kirim melalui formulir kontribusi berita di Awak Sigap\n4. Tim redaksi akan meninjau dan memverifikasi\n\nApakah Anda sudah menyiapkan konten beritanya? Jika sudah, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - KEKERASAN PEREMPUAN & ANAK
  {
    keywords: ['kekerasan perempuan', 'kekerasan anak', 'perempuan dan anak', 'kekerasan', 'lapor kekerasan', 'korban kekerasan', 'kekerasan domestik', 'kekerasan rumah tangga'],
    answer: 'Baik, saya jelaskan tentang layanan Laporan Kekerasan pada Perempuan & Anak.\n\nLayanan ini khusus untuk melaporkan kasus kekerasan yang dialami perempuan atau anak. Identitas pelapor akan dirahasiakan dan laporan akan ditindaklanjuti dengan segera.\n\nLangkah penting:\n1. Jika dalam keadaan darurat, hubungi hotline darurat terlebih dahulu\n2. Laporkan melalui formulir di Awak Sigap\n3. Tim akan segera menindaklanjuti laporan Anda\n4. Identitas pelapor dijamin kerahasiaannya\n\nApakah Anda dalam keadaan darurat? Jika ya, silakan hubungi hotline darurat terlebih dahulu. Untuk laporan non-darurat, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - SP4N LAPOR / LAPOR BAPAK
  {
    keywords: ['sp4n', 'sp4n lapor', 'lapor sp4n', 'lapor bapak', 'lapor', 'aduan bapak', 'lapor ke bapak', 'cara sp4n', 'bagaimana sp4n', 'cara lapor bapak', 'bagaimana lapor bapak', 'cara lapor', 'bagaimana lapor'],
    answer: 'Baik, saya jelaskan tentang layanan SP4N LAPOR atau Lapor Bapak.\n\nLayanan ini adalah platform pengaduan resmi pemerintah untuk menampung berbagai jenis pengaduan masyarakat. Anda bisa melaporkan masalah pelayanan publik, infrastruktur, atau pengaduan lainnya.\n\nLangkah singkat:\n1. Akses platform SP4N LAPOR melalui Awak Sigap\n2. Daftar atau login ke akun Anda\n3. Isi formulir pengaduan sesuai kategori\n4. Pantau status pengaduan melalui dashboard\n\nApakah Anda sudah memiliki akun? Jika belum, silakan daftar terlebih dahulu di website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - INSIDEN KEAMANAN INFORMASI
  {
    keywords: ['insiden keamanan', 'keamanan informasi', 'cyber security', 'keamanan siber', 'hacker', 'serangan siber', 'data bocor', 'kebocoran data', 'phishing'],
    answer: 'Baik, saya jelaskan tentang layanan Laporan Insiden Keamanan Informasi.\n\nLayanan ini untuk melaporkan insiden keamanan siber seperti serangan hacker, kebocoran data, phishing, atau ancaman keamanan informasi lainnya.\n\nLangkah penting:\n1. Segera laporkan insiden yang terjadi\n2. Isi formulir laporan insiden keamanan\n3. Berikan detail insiden sebanyak mungkin\n4. Tim keamanan akan segera menindaklanjuti\n\nApakah insiden masih berlangsung? Jika ya, silakan laporkan segera melalui website Awak Sigap agar tim bisa segera menindaklanjuti.'
  },
  
  // 3. LAYANAN SPESIFIK - PENGADUAN UMUM
  {
    keywords: ['pengaduan', 'aduan umum', 'komplain', 'keluhan', 'mau komplain', 'ingin komplain', 'mau keluh'],
    answer: 'Baik, saya jelaskan tentang layanan Pengaduan Umum.\n\nLayanan ini untuk menampung berbagai jenis pengaduan atau keluhan masyarakat terkait pelayanan publik, kebijakan, atau hal lainnya.\n\nLangkah singkat:\n1. Identifikasi kategori pengaduan Anda\n2. Isi formulir pengaduan di Awak Sigap\n3. Lampirkan dokumen pendukung (jika ada)\n4. Tunggu proses penanganan dari tim terkait\n\nBisa ceritakan singkat kategori pengaduan Anda? Ini akan membantu saya mengarahkan ke layanan yang lebih spesifik.'
  },
  
  // 3. LAYANAN SPESIFIK - ADUAN KONTEN NEGATIF
  {
    keywords: ['konten negatif', 'aduan konten', 'konten tidak pantas', 'konten negatif', 'konten hoaks', 'konten menyesatkan', 'konten provokatif'],
    answer: 'Baik, saya jelaskan tentang layanan Aduan Konten Negatif.\n\nLayanan ini untuk melaporkan konten di media atau internet yang dianggap negatif, tidak pantas, atau melanggar norma.\n\nLangkah singkat:\n1. Catat URL atau link konten yang diadukan\n2. Isi formulir aduan konten negatif di Awak Sigap\n3. Berikan detail mengapa konten dianggap negatif\n4. Tim akan meninjau dan mengambil tindakan\n\nApakah Anda sudah memiliki link atau URL konten yang ingin diadukan? Jika sudah, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - ADUAN NOMOR TELEPON
  {
    keywords: ['nomor telepon', 'telepon', 'no telepon', 'nomor telp', 'telp', 'hp', 'wa', 'whatsapp', 'sms mengganggu', 'telepon mengganggu', 'spam call'],
    answer: 'Baik, saya jelaskan tentang layanan Aduan Nomor Telepon.\n\nLayanan ini untuk melaporkan nomor telepon yang digunakan untuk aktivitas mengganggu, penipuan, atau spam.\n\nLangkah singkat:\n1. Catat nomor telepon yang diadukan\n2. Isi formulir aduan nomor telepon di Awak Sigap\n3. Jelaskan masalah yang terjadi (spam, penipuan, dll)\n4. Lampirkan bukti jika ada (screenshot, dll)\n\nApakah Anda sudah mencatat nomor telepon yang ingin diadukan? Jika sudah, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - ADUAN NOMOR REKENING
  {
    keywords: ['nomor rekening', 'rekening', 'no rekening', 'bank', 'rekening penipuan', 'rekening penipu', 'nomor rek penipuan'],
    answer: 'Baik, saya jelaskan tentang layanan Aduan Nomor Rekening.\n\nLayanan ini untuk melaporkan nomor rekening yang digunakan untuk aktivitas penipuan atau transaksi mencurigakan.\n\nLangkah singkat:\n1. Catat nomor rekening yang diadukan\n2. Isi formulir aduan nomor rekening di Awak Sigap\n3. Jelaskan masalah yang terjadi\n4. Lampirkan bukti transaksi jika ada\n\nApakah Anda sudah mencatat nomor rekening yang ingin diadukan? Jika sudah, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 3. LAYANAN SPESIFIK - ADUAN HOAKS
  {
    keywords: ['hoaks', 'berita hoaks', 'informasi hoaks', 'fake news', 'berita palsu', 'informasi palsu', 'berita bohong', 'hoax', 'lapor hoaks', 'cara lapor hoaks', 'bagaimana lapor hoaks'],
    answer: 'Baik, saya jelaskan tentang layanan Aduan Hoaks.\n\nLayanan ini untuk melaporkan berita atau informasi palsu yang beredar di media atau internet.\n\nLangkah singkat:\n1. Catat sumber informasi hoaks yang ditemukan\n2. Isi formulir aduan hoaks di Awak Sigap\n3. Lampirkan bukti atau link konten hoaks\n4. Tim verifikasi akan meninjau dan mengambil tindakan\n\nApakah Anda sudah memiliki link atau bukti hoaks yang ingin dilaporkan? Jika sudah, silakan lanjutkan ke website Awak Sigap.'
  },
  
  // 2. USER BINGUNG / PERTANYAAN UMUM (ditempatkan setelah layanan spesifik)
  {
    keywords: ['bingung', 'tidak tahu', 'gak tahu', 'ga tahu', 'bantu saya', 'tolong bantu', 'mau lapor', 'ingin lapor', 'mau aduan', 'ingin aduan', 'ada masalah', 'punya masalah', 'butuh bantuan', 'perlu bantuan', 'mau konsultasi', 'ingin konsultasi'],
    answer: 'Baik, saya mengerti. Mari kita cari solusi bersama.\n\nUntuk membantu Anda lebih tepat, bisa ceritakan singkat masalah atau kebutuhan Anda? Atau Anda bisa memilih salah satu layanan berikut:\n\n• PPID - untuk permohonan informasi publik\n• Konsultasi Hukum - untuk bantuan hukum\n• Kontribusi Berita - untuk mengirim berita\n• Kekerasan Perempuan & Anak - untuk laporan darurat\n• SP4N LAPOR - untuk pengaduan umum\n• Insiden Keamanan - untuk laporan keamanan siber\n• Aduan Konten Negatif - untuk melaporkan konten tidak pantas\n• Aduan Nomor Telepon/Rekening - untuk melaporkan penipuan\n• Aduan Hoaks - untuk melaporkan berita palsu\n\nPilih salah satu, atau jelaskan singkat masalah Anda.'
  },
  {
    keywords: ['cara', 'bagaimana', 'prosedur', 'langkah', 'tata cara'],
    answer: 'Baik, saya akan bantu jelaskan. Untuk memberikan panduan yang tepat, bisa sebutkan layanan apa yang ingin Anda ketahui?\n\nContoh: "cara lapor hoaks", "bagaimana PPID", atau "prosedur konsultasi hukum".'
  }
]

const messages = ref([
  { role: 'assistant', content: 'Halo! Selamat datang di Layanan Pengaduan. Saya di sini untuk membantu Anda menemukan layanan yang tepat. Silakan ceritakan apa yang bisa saya bantu hari ini, atau pilih salah satu pertanyaan di bawah ini.', link: true }
])
const input = ref('')
const isTyping = ref(false)
const messagesRef = ref(null)

function scrollToBottom() {
  nextTick(() => {
    if (messagesRef.value) {
      messagesRef.value.scrollTop = messagesRef.value.scrollHeight
    }
  })
}

function selectQuestion(questionText) {
  if (isTyping.value) return
  input.value = questionText
  onSend()
}

function findMatchingTemplate(userMessage) {
  const lowerMessage = userMessage.toLowerCase().trim()
  
  // Cari semua template yang cocok dengan scoring
  const matches = []
  
  for (const template of questionTemplates) {
    const matchedKeywords = template.keywords.filter(keyword => 
      lowerMessage.includes(keyword.toLowerCase())
    )
    
    if (matchedKeywords.length > 0) {
      // Hitung score: keyword yang lebih panjang/spesifik mendapat score lebih tinggi
      const score = matchedKeywords.reduce((sum, keyword) => {
        return sum + keyword.length
      }, 0)
      
      matches.push({
        template,
        score,
        matchedKeywords
      })
    }
  }
  
  // Jika ada match, pilih yang memiliki score tertinggi (paling spesifik)
  if (matches.length > 0) {
    // Urutkan berdasarkan score (tertinggi dulu)
    matches.sort((a, b) => b.score - a.score)
    return matches[0].template.answer
  }
  
  // FALLBACK - Jika tidak ada yang cocok, gunakan bahasa ramah dan menenangkan
  return 'Maaf, saya belum sepenuhnya memahami pertanyaan Anda. Tidak apa-apa, mari kita coba lagi.\n\nBisa ceritakan singkat apa yang ingin Anda tanyakan atau laporkan? Atau Anda bisa memilih salah satu layanan berikut:\n\n• PPID / Informasi Publik\n• Konsultasi atau Aduan Hukum\n• Kontribusi Berita\n• Kekerasan terhadap Perempuan & Anak\n• SP4N LAPOR / Lapor Bapak\n• Insiden Keamanan Informasi\n• Pengaduan Umum\n• Aduan Konten Negatif\n• Aduan Nomor Telepon\n• Aduan Nomor Rekening\n• Aduan Hoaks\n\nSaya di sini untuk membantu Anda menemukan solusi yang tepat.'
}

function onSend() {
  const text = input.value.trim()
  if (!text) return

  messages.value.push({ role: 'user', content: text })
  input.value = ''
  isTyping.value = true
  scrollToBottom()

  // Simulasi delay untuk efek typing
  setTimeout(() => {
    const answer = findMatchingTemplate(text)
    messages.value.push({ role: 'assistant', content: answer, link: true })
    isTyping.value = false
    scrollToBottom()
  }, 500)
}
</script>

<style scoped>
.container {
  
  max-width: 900px; }
</style>


