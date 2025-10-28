<template>
  <DashboardLayout :pageTitle="__('Certified Items')">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Certified Items') }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('certified items') }}
        </div>
      </div>

      <!-- Statistics Card -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Total Certified') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ artifacts.total || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('This Month') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ thisMonthCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Diamond Certificates') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ diamondCount }}</p>
        </div>
      </div>

      <!-- Certified Items Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
                      <h3 class="text-lg font-semibold text-gray-800">{{ __('Certified Items List') }}</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded shadow border">
            <thead>
              <tr class="bg-gray-100 border-b">
                <th class="px-4 py-2 text-left font-bold">#</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Item Code') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Receiving Request No') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Weight') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Client') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Issue Date') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(artifact, idx) in artifacts.data" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-2">{{ getRowNumber(idx) }}</td>
                <td class="px-4 py-2">
                  <span class="font-medium text-blue-600">{{ artifact.artifact_code }}</span>
                </td>
                <td class="px-4 py-2">{{ artifact.test_request?.receiving_record_no || '-' }}</td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ getFullType(artifact) }}</span>
                    <span v-if="artifact.type === 'Colorless Diamonds'" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                      💎
                    </span>
                  </div>
                </td>
                <td class="px-4 py-2">
                  <span v-if="artifact.weight">
                    {{ artifact.weight }} 
                    <span v-if="artifact.weight_unit" class="text-gray-600 text-sm">
                      {{ __(artifact.weight_unit) }}
                    </span>
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-2">{{ artifact.client ? artifact.client.full_name : '-' }}</td>
                <td class="px-4 py-2">{{ formatDate(artifact.latest_certificate?.created_at) }}</td>
                <td class="px-4 py-2">
                  <div class="flex space-x-2">
                    <!-- For issued certificates (system generated) -->
                    <template v-if="artifact.latest_certificate && artifact.latest_certificate.status === 'issued'">
                      <!-- View Certificate Button -->
                      <button 
                        @click="viewCertificate(artifact.latest_certificate)" 
                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                        :title="__('View Certificate')"
                      >
                        📜 {{ __('View') }}
                      </button>
                      
                      <!-- Print Certificate Button -->
                      <button 
                        @click="printCertificate(artifact.latest_certificate)" 
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                        :title="__('Print Certificate')"
                      >
                        🖨️ {{ __('Print') }}
                      </button>
                      
                      <!-- Download PDF Button -->
                      <button 
                        @click="downloadPDF(artifact.latest_certificate)" 
                        class="px-3 py-1 bg-red-600 text-white rounded hover:red-700 text-xs font-semibold"
                        :title="__('Download PDF')"
                      >
                        📄 {{ __('PDF') }}
                      </button>
                    </template>

                    <!-- For uploaded certificates (user uploaded) -->
                    <template v-if="artifact.latest_certificate && artifact.latest_certificate.status === 'uploaded'">
                      <!-- View Uploaded Certificate -->
                      <button 
                        @click="viewUploadedCertificate(artifact.latest_certificate)" 
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                        :title="__('View Uploaded Certificate')"
                      >
                        📋 {{ __('View') }}
                      </button>
                      
                      <!-- Download Uploaded PDF -->
                      <button 
                        @click="downloadUploadedPDF(artifact.latest_certificate)" 
                        class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs font-semibold"
                        :title="__('Download Uploaded PDF')"
                      >
                        📄 {{ __('Download') }}
                      </button>

                      <!-- Status Badge -->
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">
                        {{ __('Uploaded') }}
                      </span>
                    </template>
                  </div>
                </td>
              </tr>
              <tr v-if="!artifacts.data?.length">
                <td colspan="8" class="text-center text-gray-400 py-8">{{ __('No certified items found.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="artifacts.last_page > 1" class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-600">
            {{ __('Page') }} {{ artifacts.current_page }} {{ __('of') }} {{ artifacts.last_page }}
          </div>
          <div class="flex space-x-2">
            <Link 
              v-if="artifacts.prev_page_url"
              :href="artifacts.prev_page_url"
              class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              {{ __('Previous') }}
            </Link>
            <Link 
              v-if="artifacts.next_page_url"
              :href="artifacts.next_page_url"
              class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              {{ __('Next') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    artifacts: Object
  },
  
  setup(props) {
    const thisMonthCount = computed(() => {
      const thisMonth = new Date().getMonth()
      const thisYear = new Date().getFullYear()
      
      return props.artifacts?.data?.filter(a => {
        const certDate = new Date(a.latest_certificate?.created_at)
        return certDate.getMonth() === thisMonth && certDate.getFullYear() === thisYear
      }).length || 0
    })

    const diamondCount = computed(() => {
      return props.artifacts?.data?.filter(a => a.type === 'Colorless Diamonds').length || 0
    })

    return {
      thisMonthCount,
      diamondCount
    }
  },

  methods: {
    getFullType(artifact) {
      if (!artifact.type) return '-';
      return artifact.subtype ? `${artifact.type} - ${artifact.subtype}` : artifact.type;
    },
    
    __(key) {
      const translations = {
        'Certified Items': 'العناصر المعتمدة',
        'Total': 'المجموع',
        'certified items': 'عنصر معتمد',
        'Total Certified': 'إجمالي المعتمد',
        'This Month': 'هذا الشهر',
        'Diamond Certificates': 'شهادات الألماس',
        'Certified Items List': 'قائمة العناصر المعتمدة',
        'Item Code': 'كود العنصر',
        'Type': 'النوع',
        'Weight': 'الوزن',
        'Client': 'العميل',
        'Issue Date': 'تاريخ الإصدار',
        'Actions': 'الإجراءات',
        'View Certificate': 'عرض الشهادة',
        'Print Certificate': 'طباعة الشهادة',
        'Download PDF': 'تحميل PDF',
        'View': 'عرض',
        'Print': 'طباعة',
        'PDF': 'PDF',
        'No certified items found.': 'لا توجد عناصر معتمدة.',
        'Page': 'صفحة',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
        
        // Weight units
        'ct': 'قيراط',
        'gm': 'جرام',
        
        // Upload-related translations
        'View Uploaded Certificate': 'عرض الشهادة المرفوعة',
        'Download Uploaded PDF': 'تحميل الشهادة المرفوعة',
        'Uploaded': 'مرفوعة',
        'No uploaded certificate found': 'لم يتم العثور على شهادة مرفوعة',
        
        // Receiving Request No
        'Receiving Request No': 'رقم طلب الاستلام',
      }
      
      return this.$page.props.locale === 'ar' ? translations[key] || key : key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },

    getRowNumber(idx) {
      const currentPage = this.artifacts.current_page || 1
      const perPage = this.artifacts.per_page || 15
      return (currentPage - 1) * perPage + idx + 1
    },

    viewCertificate(certificate) {
      // Navigate to certificate view page
      this.$inertia.visit(`/certificates/${certificate.id}`)
    },

    viewUploadedCertificate(certificate) {
      // Open uploaded certificate in new window
      if (certificate.uploaded_certificate_path) {
        const url = `/storage/${certificate.uploaded_certificate_path}`
        window.open(url, '_blank')
      } else {
        alert(this.__('No uploaded certificate found'))
      }
    },

    downloadUploadedPDF(certificate) {
      // Download the uploaded PDF
      if (certificate.uploaded_certificate_path) {
        const url = `/storage/${certificate.uploaded_certificate_path}`
        const link = document.createElement('a')
        link.href = url
        link.download = `uploaded-certificate-${certificate.certificate_number}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
      } else {
        alert(this.__('No uploaded certificate found'))
      }
    },

    printCertificate(certificate) {
      // Open certificate in new window for printing
      const url = `/certificates/${certificate.id}`
      const printWindow = window.open(url, '_blank')
      
      // Wait for the page to load then trigger print
      if (printWindow) {
        printWindow.addEventListener('load', () => {
          setTimeout(() => {
            printWindow.print()
          }, 500)
        })
      }
    },

    downloadPDF(certificate) {
      // Download certificate as PDF
      window.open(`/certificates/${certificate.id}/pdf`, '_blank')
    }
  }
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style> 