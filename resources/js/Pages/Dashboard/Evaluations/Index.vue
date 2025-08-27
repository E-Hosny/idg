<template>
  <DashboardLayout :pageTitle="__('Evaluations')">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Evaluations') }}</h2>
      
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Total Evaluations') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ evaluations.total || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Completed') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ completedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Drafts') }}</h3>
          <p class="text-3xl font-bold text-yellow-600">{{ draftCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Diamond Evaluations') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ diamondCount }}</p>
        </div>
      </div>

      <!-- Evaluations Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold text-gray-800">{{ __('All Evaluations') }}</h3>
          <div class="text-sm text-gray-600">
            {{ __('Showing') }} {{ evaluations.data?.length || 0 }} {{ __('of') }} {{ evaluations.total || 0 }} {{ __('evaluations') }}
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded shadow border">
            <thead>
              <tr class="bg-gray-100 border-b">
                <th class="px-4 py-2 text-left font-bold">#</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Item Code') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Evaluator') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Date') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Status') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Result') }}</th>
                <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(evaluation, idx) in evaluations.data" :key="`${evaluation.type}-${evaluation.id}`" class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-2">{{ idx + 1 }}</td>
                <td class="px-4 py-2">
                  <span class="font-medium">{{ evaluation.artifact?.artifact_code }}</span>
                </td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ evaluation.artifact?.type }}</span>
                    <span v-if="evaluation.type === 'diamond'" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                      {{ __('Diamond') }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-2">{{ evaluation.evaluator?.name || '-' }}</td>
                <td class="px-4 py-2">{{ formatDate(evaluation.evaluation_date) }}</td>
                <td class="px-4 py-2">
                  <span :class="getStatusClass(evaluation.status)" class="px-2 py-1 text-xs rounded-full font-semibold">
                    {{ __(evaluation.status) }}
                  </span>
                </td>
                <td class="px-4 py-2">
                  <span v-if="evaluation.result" :class="getResultClass(evaluation.result)" class="font-medium">
                    {{ __(evaluation.result) }}
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-2">
                  <button 
                    @click="viewEvaluation(evaluation)" 
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                  >
                    {{ __('View') }}
                  </button>
                </td>
              </tr>
              <tr v-if="!evaluations.data?.length">
                <td colspan="8" class="text-center text-gray-400 py-8">{{ __('No evaluations found.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="evaluations.last_page > 1" class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-600">
            {{ __('Page') }} {{ evaluations.current_page }} {{ __('of') }} {{ evaluations.last_page }}
          </div>
          <div class="flex space-x-2">
            <Link 
              v-if="evaluations.current_page > 1"
              :href="route('dashboard.evaluations', { page: evaluations.current_page - 1 })"
              class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              {{ __('Previous') }}
            </Link>
            <Link 
              v-if="evaluations.current_page < evaluations.last_page"
              :href="route('dashboard.evaluations', { page: evaluations.current_page + 1 })"
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
    evaluations: Object
  },
  
  setup(props) {
    const completedCount = computed(() => {
      return props.evaluations?.data?.filter(e => e.status === 'completed' || e.status === 'approved').length || 0
    })

    const draftCount = computed(() => {
      return props.evaluations?.data?.filter(e => e.status === 'draft').length || 0
    })

    const diamondCount = computed(() => {
      return props.evaluations?.data?.filter(e => e.type === 'diamond').length || 0
    })

    return {
      completedCount,
      draftCount,
      diamondCount
    }
  },

  methods: {
    __(key) {
      const translations = {
        'Evaluations': 'التقييمات',
        'Total Evaluations': 'إجمالي التقييمات',
        'Completed': 'مكتمل',
        'Drafts': 'مسودات',
        'Diamond Evaluations': 'تقييمات الألماس',
        'All Evaluations': 'جميع التقييمات',
        'Showing': 'عرض',
        'of': 'من',
        'evaluations': 'تقييم',
        'Item Code': 'كود العنصر',
        'Type': 'النوع',
        'Client': 'العميل',
        'Evaluator': 'المقيم',
        'Date': 'التاريخ',
        'Status': 'الحالة',
        'Result': 'النتيجة',
        'Actions': 'الإجراءات',
        'Diamond': 'ألماس',
        'View': 'عرض',
        'No evaluations found.': 'لا توجد تقييمات.',
        'Page': 'صفحة',
        'Previous': 'السابق',
        'Next': 'التالي',
        
        // Status values
        'draft': 'مسودة',
        'completed': 'مكتمل',
        'approved': 'معتمد',
        
        // Result values  
        'Pass': 'نجح',
        'Fail': 'فشل',
        'Reject': 'مرفوض'
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
        'bg-gray-100 text-gray-800': status === 'draft',
        'bg-blue-100 text-blue-800': status === 'completed',
        'bg-green-100 text-green-800': status === 'approved'
      }
    },

    getResultClass(result) {
      return {
        'text-green-600': result === 'Pass',
        'text-red-600': result === 'Fail' || result === 'Reject',
        'text-gray-800': !result
      }
    },

    viewEvaluation(evaluation) {
      // توجيه لصفحة عرض التقييم
      this.$inertia.visit(`/dashboard/artifacts/${evaluation.artifact.id}/evaluation`)
    },

    route(name, params = {}) {
      // Helper function for route generation
      let url = ''
      switch(name) {
        case 'dashboard.evaluations':
          url = '/dashboard/evaluations'
          if (params.page) {
            url += `?page=${params.page}`
          }
          break
      }
      return url
    }
  }
}
</script> 