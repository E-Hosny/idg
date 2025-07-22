<template>
  <DashboardLayout :pageTitle="__('Evaluated Artifacts')">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Evaluated Artifacts') }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('artifacts') }}
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Total Evaluated') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ artifacts.total || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Certified') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ certifiedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Diamond Evaluations') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ diamondCount }}</p>
        </div>
      </div>

      <!-- Artifacts Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold text-gray-800">{{ __('Evaluated Artifacts List') }}</h3>
          <div class="flex space-x-4">
            <!-- Filter Options -->
            <select v-model="statusFilter" @change="filterArtifacts" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">{{ __('All Status') }}</option>
              <option value="evaluated">{{ __('Evaluated') }}</option>
              <option value="certified">{{ __('Certified') }}</option>
            </select>
            
            <select v-model="typeFilter" @change="filterArtifacts" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">{{ __('All Types') }}</option>
              <option value="Colorless Diamonds">{{ __('Colorless Diamonds') }}</option>
              <option value="Colored Gemstones">{{ __('Colored Gemstones') }}</option>
              <option value="Other Colored Gemstones">{{ __('Other Colored Gemstones') }}</option>
              <option value="Jewellery">{{ __('Jewellery') }}</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded shadow border">
            <thead>
              <tr class="bg-gray-100 border-b">
                <th class="px-4 py-2 text-left font-bold">#</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Code') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Service') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Weight') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Status') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Client') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Evaluated At') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(artifact, idx) in filteredArtifacts" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-2">{{ getRowNumber(idx) }}</td>
                <td class="px-4 py-2">
                  <span class="font-medium text-blue-600">{{ artifact.artifact_code }}</span>
                </td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ artifact.type }}</span>
                    <span v-if="artifact.type === 'Colorless Diamonds'" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                      💎
                    </span>
                  </div>
                </td>
                <td class="px-4 py-2">{{ artifact.service }}</td>
                <td class="px-4 py-2">
                  <span v-if="artifact.weight">
                    {{ artifact.weight }} 
                    <span v-if="artifact.weight_unit" class="text-gray-600 text-sm">
                      {{ __(artifact.weight_unit) }}
                    </span>
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-2">
                  <span :class="getStatusClass(artifact.status)" class="px-2 py-1 text-xs rounded-full font-semibold">
                    {{ __(artifact.status) }}
                  </span>
                </td>
                <td class="px-4 py-2">{{ artifact.client ? artifact.client.full_name : '-' }}</td>
                <td class="px-4 py-2">{{ formatDate(artifact.updated_at) }}</td>
                <td class="px-4 py-2">
                  <div class="flex space-x-2">
                    <!-- View Evaluation Button -->
                    <button 
                      @click="viewEvaluation(artifact)" 
                      class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                      :title="__('View Evaluation Report')"
                    >
                      {{ __('View Report') }}
                    </button>
                    
                                         <!-- Print Button -->
                     <button 
                       @click="printEvaluation(artifact)" 
                       class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                       :title="__('Print Report')"
                     >
                       🖨️ {{ __('Print') }}
                     </button>
                     
                     <!-- Generate Certificate Button -->
                     <button 
                       @click="generateCertificate(artifact)" 
                       class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs font-semibold"
                       :title="__('Generate Certificate')"
                     >
                       📜 {{ __('Certificate') }}
                     </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!filteredArtifacts.length">
                <td colspan="9" class="text-center text-gray-400 py-8">{{ __('No evaluated artifacts found.') }}</td>
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
import { ref, computed } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    artifacts: Object
  },
  
  setup(props) {
    const statusFilter = ref('')
    const typeFilter = ref('')

    const certifiedCount = computed(() => {
      return props.artifacts?.data?.filter(a => a.status === 'certified').length || 0
    })

    const diamondCount = computed(() => {
      return props.artifacts?.data?.filter(a => a.type === 'Colorless Diamonds').length || 0
    })

    const filteredArtifacts = computed(() => {
      let artifacts = props.artifacts?.data || []
      
      if (statusFilter.value) {
        artifacts = artifacts.filter(a => a.status === statusFilter.value)
      }
      
      if (typeFilter.value) {
        artifacts = artifacts.filter(a => a.type === typeFilter.value)
      }
      
      return artifacts
    })

    return {
      statusFilter,
      typeFilter,
      certifiedCount,
      diamondCount,
      filteredArtifacts
    }
  },

  methods: {
    __(key) {
      const translations = {
        'Evaluated Artifacts': 'القطع المقيمة',
        'Total': 'المجموع',
        'artifacts': 'قطعة',
        'Total Evaluated': 'إجمالي المقيم',
        'Certified': 'معتمد',
        'Diamond Evaluations': 'تقييمات الألماس',
        'Evaluated Artifacts List': 'قائمة القطع المقيمة',
        'All Status': 'جميع الحالات',
        'All Types': 'جميع الأنواع',
        'Code': 'الكود',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Status': 'الحالة',
        'Client': 'العميل',
        'Evaluated At': 'تاريخ التقييم',
        'Actions': 'الإجراءات',
        'View Report': 'عرض التقرير',
        'Print': 'طباعة',
        'Certificate': 'شهادة',
        'Generate Certificate': 'إنشاء شهادة',
        'View Evaluation Report': 'عرض تقرير التقييم',
        'Print Report': 'طباعة التقرير',
        'No evaluated artifacts found.': 'لا توجد قطع مقيمة.',
        'Page': 'صفحة',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
        
        // Artifact types
        'Colorless Diamonds': 'الألماس عديم اللون',
        'Colored Gemstones': 'الأحجار الكريمة الملونة',
        'Other Colored Gemstones': 'أحجار كريمة ملونة أخرى',
        'Jewellery': 'المجوهرات',
        
        // Status
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        
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

    getStatusClass(status) {
      return {
        'bg-green-100 text-green-800': status === 'evaluated',
        'bg-blue-100 text-blue-800': status === 'certified',
      }
    },

    getRowNumber(idx) {
      const currentPage = this.artifacts.current_page || 1
      const perPage = this.artifacts.per_page || 15
      return (currentPage - 1) * perPage + idx + 1
    },

    filterArtifacts() {
      // The computed property handles filtering automatically
    },

    viewEvaluation(artifact) {
      // Navigate to evaluation view page
      this.$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluation`)
    },

    printEvaluation(artifact) {
      // Open evaluation in new window for printing
      const url = `/dashboard/artifacts/${artifact.id}/evaluation`
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

    generateCertificate(artifact) {
      // Navigate to certificate generation
      this.$inertia.post(`/certificates/generate/${artifact.id}`, {}, {
        onSuccess: () => {
          alert('تم إنشاء الشهادة بنجاح!')
        },
        onError: (errors) => {
          console.error('Certificate generation errors:', errors)
          if (errors.error) {
            alert(errors.error)
          } else {
            alert('حدث خطأ أثناء إنشاء الشهادة. يرجى المحاولة مرة أخرى.')
          }
        }
      })
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