<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-50">
    <!-- Header with Logo -->
    <header class="bg-white shadow-lg border-b-4 border-green-600">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <div class="relative">
              <img src="/images/idg_logo.jpg" alt="IDG Laboratory" class="h-16 w-16 rounded-full border-4 border-green-600 shadow-lg">
              <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-green-800">IDG Laboratory</h1>
              <p class="text-green-600 font-medium">International Diamond Group</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-600">Riyadh, Saudi Arabia</p>
            <p class="text-xs text-gray-500">www.idg-lab.com.sa</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Welcome Section -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
          <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Certificate Verification</h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Verify the authenticity of your IDG Laboratory certificate by entering the item code below
        </p>
      </div>

      <!-- Search Form -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 mb-8">
        <form @submit.prevent="searchCertificate" class="space-y-6">
          <div>
            <label for="certificate_number" class="block text-lg font-semibold text-gray-700 mb-3">
              Item Code
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <input
                id="certificate_number"
                v-model="form.certificate_number"
                type="text"
                placeholder="Enter item code (e.g., DR7405603037)"
                class="w-full pl-12 pr-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200"
                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-100': errors.certificate_number }"
                required
              >
            </div>
            <div v-if="errors.certificate_number" class="mt-2 text-red-600 text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ errors.certificate_number }}
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Verifying Certificate...
            </span>
            <span v-else class="flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Verify Certificate
            </span>
          </button>
        </form>
      </div>

      <!-- Success and Info Messages -->
      <div v-if="$page.props.success" class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-green-800">{{ $page.props.success }}</p>
          </div>
        </div>
      </div>

      <div v-if="$page.props.info" class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-blue-800">{{ $page.props.info }}</p>
          </div>
        </div>
      </div>

      <!-- Certificate Display -->
      <div v-if="$page.props.certificate_data" class="mb-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
          <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Certificate Found</h3>
            <p class="text-gray-600">Your IDG Laboratory certificate details</p>
          </div>

          <!-- Certificate Details -->
          <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Item Code</label>
                <p class="text-lg font-semibold text-gray-900">{{ $page.props.certificate_data.artifact_code }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                <p class="text-lg font-semibold text-gray-900">{{ $page.props.certificate_data.type }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Weight</label>
                <p class="text-lg font-semibold text-gray-900">{{ $page.props.certificate_data.weight }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Client</label>
                <p class="text-lg font-semibold text-gray-900">{{ $page.props.certificate_data.client_name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Issue Date</label>
                <p class="text-lg font-semibold text-gray-900">{{ $page.props.certificate_data.issue_date }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span v-if="$page.props.certificate_data.is_uploaded" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Uploaded Certificate
                </span>
                <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Generated Certificate
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
              @click="openPdfInNewWindow" 
              class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
              Open Certificate PDF
            </button>
            <button 
              @click="downloadPdf" 
              class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Download PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Information Cards -->
      <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
          <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Authentic</h3>
          </div>
          <p class="text-gray-600">All certificates are verified and authenticated by IDG Laboratory experts</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
          <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Secure</h3>
          </div>
          <p class="text-gray-600">Advanced security measures ensure certificate integrity and authenticity</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
          <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">24/7 Access</h3>
          </div>
          <p class="text-gray-600">Verify your certificates anytime, anywhere with our online verification system</p>
        </div>
      </div>

      <!-- How It Works -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">How It Works</h3>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl font-bold text-green-600">1</span>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Enter Item Code</h4>
            <p class="text-gray-600">Input your unique item code from your IDG Laboratory certificate</p>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl font-bold text-green-600">2</span>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">Instant Verification</h4>
            <p class="text-gray-600">Our system instantly verifies the authenticity of your certificate</p>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-2xl font-bold text-green-600">3</span>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 mb-2">View Results</h4>
            <p class="text-gray-600">Access detailed certificate information and download if needed</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="flex items-center justify-center mb-4">
            <img src="/images/idg_logo.jpg" alt="IDG" class="h-12 w-12 rounded-full mr-3">
            <span class="text-xl font-bold">IDG Laboratory</span>
          </div>
          <p class="text-gray-400 mb-4">International Diamond Group - Your Trusted Partner in Gemstone Authentication</p>
          <div class="flex justify-center space-x-6 text-sm text-gray-400">
            <span>Riyadh, Saudi Arabia</span>
            <span>•</span>
            <span>www.idg-lab.com.sa</span>
            <span>•</span>
            <span>+966 11 123 4567</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

export default {
  setup() {
    const form = ref({
      certificate_number: ''
    })

    const loading = ref(false)
    const page = usePage()

    const searchCertificate = () => {
      loading.value = true
      
      router.post('/search-certificate', form.value, {
        onFinish: () => {
          loading.value = false
        },
        onError: (errors) => {
          loading.value = false
          console.error('Search errors:', errors)
        }
      })
    }

    const openPdfInNewWindow = () => {
      if (page.props.certificate_data && page.props.certificate_data.pdf_url) {
        window.open(page.props.certificate_data.pdf_url, '_blank');
      }
    };

    const downloadPdf = () => {
      if (page.props.certificate_data && page.props.certificate_data.pdf_url) {
        // Create a temporary link to download the PDF
        const link = document.createElement('a');
        link.href = page.props.certificate_data.pdf_url;
        link.download = `certificate-${page.props.certificate_data.artifact_code}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    };

    return {
      form,
      loading,
      searchCertificate,
      openPdfInNewWindow,
      downloadPdf
    }
  },

  computed: {
    errors() {
      return this.$page.props.errors || {}
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Gradient text effect */
.gradient-text {
  background: linear-gradient(135deg, #059669, #10b981);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Hover effects */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Responsive design */
@media (max-width: 640px) {
  .text-4xl {
    font-size: 2rem;
  }
  
  .text-3xl {
    font-size: 1.875rem;
  }
}
</style> 