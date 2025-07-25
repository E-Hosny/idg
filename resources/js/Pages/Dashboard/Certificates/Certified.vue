<template>
  <DashboardLayout :pageTitle="__('Certified Artifacts')">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Certified Artifacts') }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('certified artifacts') }}
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

      <!-- Certified Artifacts Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold text-gray-800">{{ __('Certified Artifacts List') }}</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded shadow border">
            <thead>
              <tr class="bg-gray-100 border-b">
                <th class="px-4 py-2 text-left font-bold">#</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Artifact Code') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Certificate No') }}</th>
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
                <td class="px-4 py-2">
                  <span class="font-medium text-green-600">{{ artifact.latest_certificate?.certificate_number || '-' }}</span>
                </td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ artifact.type }}</span>
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
                    <!-- View Certificate Button -->
                    <button 
                      v-if="artifact.latest_certificate"
                      @click="viewCertificate(artifact.latest_certificate)" 
                      class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                      :title="__('View Certificate')"
                    >
                      📜 {{ __('View') }}
                    </button>
                    
                    <!-- Print Certificate Button -->
                    <button 
                      v-if="artifact.latest_certificate"
                      @click="printCertificate(artifact.latest_certificate)" 
                      class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                      :title="__('Print Certificate')"
                    >
                      🖨️ {{ __('Print') }}
                    </button>
                    
                    <!-- Download PDF Button -->
                    <button 
                      v-if="artifact.latest_certificate"
                      @click="downloadPDF(artifact.latest_certificate)" 
                      class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-semibold"
                      :title="__('Download PDF')"
                    >
                      📄 {{ __('PDF') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!artifacts.data?.length">
                <td colspan="8" class="text-center text-gray-400 py-8">{{ __('No certified artifacts found.') }}</td>
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
    __(key) {
      const translations = {
        'Certified Artifacts': 'القطع المعتمدة',
        'Total': 'المجموع',
        'certified artifacts': 'قطعة معتمدة',
        'Total Certified': 'إجمالي المعتمد',
        'This Month': 'هذا الشهر',
        'Diamond Certificates': 'شهادات الألماس',
        'Certified Artifacts List': 'قائمة القطع المعتمدة',
        'Artifact Code': 'كود القطعة',
        'Certificate No': 'رقم الشهادة',
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
        'No certified artifacts found.': 'لا توجد قطع معتمدة.',
        'Page': 'صفحة',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
        
        // Weight units
        'ct': 'قيراط',
        'gm': 'جرام',
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