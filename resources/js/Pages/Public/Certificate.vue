<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Public Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <img src="/images/idg_logo.jpg" alt="IDG Laboratory" class="w-12 h-12 rounded-full" />
            <div>
              <h1 class="text-xl font-bold text-green-700">IDG Laboratory</h1>
              <p class="text-sm text-gray-600">{{ __('Certificate Verification') }}</p>
            </div>
          </div>
          <div class="text-sm text-gray-500">
            {{ __('Verified Certificate') }} ✅
          </div>
        </div>
      </div>
    </div>

    <!-- Certificate Content -->
    <div class="max-w-4xl mx-auto py-8 px-4">
      <!-- Draft Warning -->
      <div v-if="certificate.is_draft" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
              {{ __('Draft Certificate') }}
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>{{ __('This is a draft certificate and has not been officially issued yet.') }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        
        <!-- Green Header Bar -->
        <div class="bg-green-700 px-8 py-4">
          <div class="flex justify-between items-center">
            <!-- Left: IDG Logo and Lab Info -->
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center p-1">
                <img src="/images/idg_logo.jpg" alt="IDG" class="w-14 h-14 rounded-full object-cover" />
              </div>
              <div class="text-white">
                <h1 class="text-xl font-bold">IDG Laboratory</h1>
                <h2 class="text-lg">Gemstone Report</h2>
              </div>
            </div>
            
            <!-- Right: Dates -->
            <div class="text-white text-sm space-y-1">
              <div><strong>{{ __('Received Date') }}:</strong> {{ formatDate(certificate.received_date) }}</div>
              <div><strong>{{ __('Report Date') }}:</strong> {{ formatDate(certificate.report_date) }}</div>
              <div><strong>{{ __('Test Date') }}:</strong> {{ formatDate(certificate.test_date) }}</div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="px-8 py-6 bg-white">
          <!-- Report Number and Test Method -->
          <div class="flex justify-between items-center mb-6">
            <div class="bg-gray-100 px-4 py-2 rounded">
              <div class="text-sm"><strong>{{ __('Report No') }}.</strong> {{ certificate.certificate_number }}</div>
              <div class="text-sm"><strong>{{ __('Test Method') }}:</strong> {{ certificate.test_method || 'SOPOI' }}</div>
            </div>
            
            <!-- IDENTIFICATION Box -->
            <div class="text-center">
              <div class="border-2 border-gray-400 px-8 py-4 bg-gray-50">
                <div class="text-xs text-gray-600 mb-1 font-semibold">{{ __('IDENTIFICATION') }}</div>
                <div class="text-2xl font-bold text-gray-800">{{ certificate.identification }}</div>
                <div class="w-16 h-px bg-gray-400 mx-auto mt-2"></div>
              </div>
            </div>
          </div>

          <!-- Gemstone Properties Table -->
          <div class="mb-6">
            <table class="w-full border-collapse border border-gray-300 text-sm">
              <tbody>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Shape/Cut') }}:</td>
                  <td class="px-3 py-2">{{ certificate.shape_cut || 'Oval/Mix' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Species') }}:</td>
                  <td class="px-3 py-2">{{ certificate.species || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Weight') }}:</td>
                  <td class="px-3 py-2 font-bold">{{ certificate.weight ? `${certificate.weight} Ct` : '0.00 Ct' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Variety') }}:</td>
                  <td class="px-3 py-2">{{ certificate.variety || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Color') }}:</td>
                  <td class="px-3 py-2">{{ certificate.color || 'Blue' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Group') }}:</td>
                  <td class="px-3 py-2">{{ certificate.group || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Dimensions') }}:</td>
                  <td class="px-3 py-2">{{ certificate.dimensions || '0.00 x 0.00 x 0.00 mm' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Origin Opinion') }}:</td>
                  <td class="px-3 py-2">{{ certificate.origin_opinion || 'Not requested' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Transparency') }}:</td>
                  <td class="px-3 py-2">{{ certificate.transparency || 'Transparent' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Conclusion') }}:</td>
                  <td class="px-3 py-2 font-bold text-green-700">{{ certificate.conclusion || 'Natural' }}</td>
                </tr>
                <tr>
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Phenomena') }}:</td>
                  <td class="px-3 py-2">{{ certificate.phenomena || '-' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2"></td>
                  <td class="px-3 py-2"></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Comments Section -->
          <div class="mb-6">
            <div class="text-sm">
              <strong>{{ __('Comments') }}:</strong> {{ certificate.comments || '----------' }}
            </div>
          </div>
        </div>

        <!-- Green Footer Bar -->
        <div class="bg-green-700 px-8 py-4">
          <div class="flex justify-between items-center">
            <!-- Left: Contact Info -->
            <div class="text-white text-xs space-y-1">
              <div><strong>{{ __('Contact') }}</strong></div>
              <div>IDG Laboratory, Saudi Arabia</div>
              <div>www.idg-lab.com.sa</div>
            </div>
            
            <!-- Center: QR Code and Signature -->
            <div class="flex items-center space-x-8">
              <!-- QR Code -->
              <div class="w-16 h-16 bg-white flex items-center justify-center">
                <div class="text-xs font-bold text-center">
                  <div>✓</div>
                  <div>VERIFIED</div>
                </div>
              </div>
              
              <!-- Signature -->
              <div class="text-center text-white">
                <div class="w-24 h-12 border-b border-white mb-2 flex items-end justify-center">
                  <span class="text-lg font-script">{{ __('Signature') }}</span>
                </div>
                <div class="text-xs">{{ __('Signature') }}</div>
              </div>
            </div>
            
            <!-- Right: Empty space for balance -->
            <div class="w-24"></div>
          </div>
          
          <!-- Bottom Text -->
          <div class="text-center text-white text-xs mt-4 border-t border-green-600 pt-2">
            <div>{{ __('Visit website for Terms & Conditions') }}</div>
            <div class="font-semibold">www.idg-lab.com.sa</div>
          </div>
        </div>
      </div>

      <!-- Verification Notice -->
      <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <div class="text-green-600 mr-3">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div>
            <h3 class="text-green-800 font-semibold">{{ __('Certificate Verified') }}</h3>
            <p class="text-green-700 text-sm">
              {{ __('This certificate has been verified as authentic and issued by IDG Laboratory.') }}
              {{ __('Generated on') }}: {{ formatDateTime(certificate.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Print Button -->
      <div class="mt-6 text-center">
        <button 
          @click="printCertificate"
          class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold"
        >
          {{ __('Print Certificate') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    certificate: Object,
    isPublic: Boolean
  },

  methods: {
    __(key) {
      // For public page, always display in English
      return key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },

    formatDateTime(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleString('en-US', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    printCertificate() {
      window.print()
    }
  }
}
</script>

<style scoped>
@media print {
  .bg-gray-100 {
    background: white !important;
  }
  
  .shadow-lg,
  .shadow-sm {
    box-shadow: none !important;
  }
  
  button {
    display: none !important;
  }
}
</style> 