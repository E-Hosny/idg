<template>
  <DashboardLayout :pageTitle="__('Artifacts List')">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ getPageTitle() }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('artifacts') }}
        </div>
      </div>

      <!-- Statistics Cards (only for All Artifacts view) -->
      <div v-if="viewType === 'all'" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Pending') }}</h3>
          <p class="text-3xl font-bold text-yellow-600">{{ pendingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Under Evaluation') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ underEvaluationCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Evaluated') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ evaluatedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Certified') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ certifiedCount }}</p>
        </div>
      </div>

      <!-- Artifacts Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
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
              <th class="px-4 py-2 text-left font-bold">{{ __('Created At') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(artifact, idx) in artifacts.data" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ idx + 1 }}</td>
              <td class="px-4 py-2">{{ artifact.artifact_code }}</td>
              <td class="px-4 py-2">{{ artifact.type }}</td>
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
                <span :class="{
                  'text-yellow-600 font-semibold': artifact.status === 'pending',
                  'text-blue-600 font-semibold': artifact.status === 'under_evaluation',
                  'text-green-600 font-semibold': artifact.status === 'certified' || artifact.status === 'evaluated',
                  'text-red-600 font-semibold': artifact.status === 'rejected',
                }">
                  {{ __(artifact.status) }}
                </span>
              </td>
              <td class="px-4 py-2">{{ artifact.client ? artifact.client.full_name : '-' }}</td>
              <td class="px-4 py-2">{{ formatDate(artifact.created_at) }}</td>
              <td class="px-4 py-2">
                <div class="flex space-x-2">
                  <!-- زر التقييم للقطع غير المقيمة -->
                  <button 
                    v-if="artifact.status !== 'evaluated' && artifact.status !== 'certified'"
                    @click.stop="$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluate`)" 
                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                  >
                    {{ artifact.status === 'under_evaluation' ? __('Continue') : __('Evaluate') }}
                  </button>
                  
                  <!-- زر عرض التقييم للقطع المقيمة أو قيد التقييم -->
                  <button 
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified' || artifact.status === 'under_evaluation'"
                    @click.stop="viewEvaluation(artifact)" 
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                  >
                    {{ (artifact.status === 'evaluated' || artifact.status === 'certified') ? __('View Report') : __('View Evaluation') }}
                  </button>
                  
                  <!-- زر الطباعة للقطع المقيمة -->
                  <button 
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified'"
                    @click.stop="printEvaluation(artifact)" 
                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-xs font-semibold"
                    :title="__('Print Report')"
                  >
                    🖨️
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!artifacts.data.length">
              <td colspan="9" class="text-center text-gray-400 py-4">{{ getNoDataMessage() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  components: { DashboardLayout },
  props: {
    artifacts: Object,
    viewType: {
      type: String,
      default: 'all'
    }
  },

  computed: {
    pendingCount() {
      return this.artifacts?.data?.filter(a => a.status === 'pending').length || 0
    },
    underEvaluationCount() {
      return this.artifacts?.data?.filter(a => a.status === 'under_evaluation').length || 0
    },
    evaluatedCount() {
      return this.artifacts?.data?.filter(a => a.status === 'evaluated').length || 0
    },
    certifiedCount() {
      return this.artifacts?.data?.filter(a => a.status === 'certified').length || 0
    }
  },

  methods: {
    __(key) {
      const t = {
        'Artifacts List': 'قائمة القطع',
        'Pending Artifacts': 'القطع المعلقة',
        'Code': 'الكود',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'ct': 'قيراط',
        'gm': 'جرام',
        'Status': 'الحالة',
        'Client': 'العميل',
        'Created At': 'تاريخ الإنشاء',
        'No artifacts found.': 'لا توجد قطع',
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض',
        'Evaluate': 'تقييم',
        'Continue': 'متابعة',
        'View Evaluation': 'عرض التقييم',
        'View Report': 'عرض التقرير',
        'Print Report': 'طباعة التقرير',
        'All Artifacts': 'جميع القطع',
        'Total': 'المجموع',
        'artifacts': 'قطعة',
        'Pending': 'معلق',
        'Under Evaluation': 'قيد التقييم',
        'No pending artifacts found.': 'لا توجد قطع معلقة.',
        'Actions': 'الإجراءات',
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },
    
    viewEvaluation(artifact) {
      // توجيه لصفحة عرض التقييم حسب نوع القطعة
      this.$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluation`)
    },

    printEvaluation(artifact) {
      // فتح صفحة التقييم في نافذة جديدة للطباعة
      const url = `/dashboard/artifacts/${artifact.id}/evaluation`
      const printWindow = window.open(url, '_blank')
      
      // انتظار تحميل الصفحة ثم تشغيل الطباعة
      if (printWindow) {
        printWindow.addEventListener('load', () => {
          setTimeout(() => {
            printWindow.print()
          }, 500)
        })
      }
    },

    getPageTitle() {
      if (this.viewType === 'pending') {
        return this.__('Pending Artifacts')
      }
      return this.__('Artifacts List')
    },

    getNoDataMessage() {
      if (this.viewType === 'pending') {
        return this.__('No pending artifacts found.')
      }
      return this.__('No artifacts found.')
    }
  }
}
</script> 