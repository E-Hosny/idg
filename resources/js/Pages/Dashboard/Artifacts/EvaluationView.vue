<template>
  <DashboardLayout :page-title="__('Evaluation Report')">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto mt-8">
      <!-- Header -->
      <div class="text-center mb-8 border-b pb-6">
        <h1 class="text-3xl font-bold text-green-800 mb-2">{{ __('Evaluation Report') }}</h1>
        <div class="flex justify-between items-center text-sm text-gray-600">
          <div>
            <p><strong>{{ __('Artifact Code') }}:</strong> {{ artifact.artifact_code }}</p>
            <p><strong>{{ __('Type') }}:</strong> {{ artifact.type }}</p>
            <p><strong>{{ __('Client') }}:</strong> {{ artifact.client?.full_name || '-' }}</p>
          </div>
          <div>
            <p><strong>{{ __('Evaluation Date') }}:</strong> {{ formatDate(evaluation.evaluation_date) }}</p>
            <p><strong>{{ __('Evaluator') }}:</strong> {{ evaluation.evaluator?.name || '-' }}</p>
          </div>
          <div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
              {{ __('Completed') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Evaluation Content -->
      <div class="space-y-8">
        
        <!-- Artifact Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('Artifact Information') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Code') }}</label>
              <p class="text-gray-800">{{ artifact.artifact_code }}</p>
            </div>
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Type') }}</label>
              <p class="text-gray-800">{{ artifact.type }}</p>
            </div>
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Service') }}</label>
              <p class="text-gray-800">{{ artifact.service }}</p>
            </div>
            <div v-if="artifact.weight">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Weight') }}</label>
              <p class="text-gray-800">{{ artifact.weight }} {{ __(artifact.weight_unit || 'ct') }}</p>
            </div>
            <div v-if="artifact.price">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Price') }}</label>
              <p class="text-gray-800">{{ artifact.price }} {{ __('SAR') }}</p>
            </div>
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Status') }}</label>
              <p class="text-gray-800 font-semibold">{{ __(artifact.status) }}</p>
            </div>
          </div>
        </section>

        <!-- Evaluation Results -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('Evaluation Results') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Result') }}</label>
              <p :class="resultClass" class="font-semibold text-lg">{{ evaluation.result || '-' }}</p>
            </div>
            <div>
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Evaluation Date') }}</label>
              <p class="text-gray-800">{{ formatDate(evaluation.evaluation_date) }}</p>
            </div>
          </div>
        </section>

        <!-- Comments -->
        <section v-if="evaluation.comments" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('Comments') }}</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-800 whitespace-pre-wrap">{{ evaluation.comments }}</p>
          </div>
        </section>

        <!-- Evaluator Information -->
        <section class="pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('Evaluator Information') }}</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-600 text-sm font-semibold">{{ __('Evaluator Name') }}</label>
                <p class="text-gray-800">{{ evaluation.evaluator?.name || '-' }}</p>
              </div>
              <div>
                <label class="block text-gray-600 text-sm font-semibold">{{ __('Evaluation Date') }}</label>
                <p class="text-gray-800">{{ formatDate(evaluation.evaluation_date) }}</p>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-between items-center pt-6 border-t">
        <Link 
          :href="$route('dashboard.artifacts')" 
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          {{ __('Back to Artifacts') }}
        </Link>
        
        <div class="flex space-x-4">
          <button 
            @click="printReport"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            {{ __('Print Report') }}
          </button>
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
    artifact: Object,
    evaluation: Object
  },
  
  setup(props) {
    const resultClass = computed(() => {
      const result = props.evaluation?.result?.toLowerCase()
      return {
        'text-green-600': result?.includes('pass') || result?.includes('approved'),
        'text-red-600': result?.includes('fail') || result?.includes('reject'),
        'text-yellow-600': result?.includes('pending'),
        'text-gray-800': !result
      }
    })

    return {
      resultClass
    }
  },

  methods: {
    __(key) {
      const translations = {
        'Evaluation Report': 'تقرير التقييم',
        'Artifact Code': 'كود القطعة',
        'Type': 'النوع',
        'Client': 'العميل',
        'Evaluation Date': 'تاريخ التقييم',
        'Evaluator': 'المقيم',
        'Completed': 'مكتمل',
        'Artifact Information': 'معلومات القطعة',
        'Code': 'الكود',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'Status': 'الحالة',
        'ct': 'قيراط',
        'gm': 'جرام',
        'SAR': 'ريال',
        'Evaluation Results': 'نتائج التقييم',
        'Result': 'النتيجة',
        'Comments': 'التعليقات',
        'Evaluator Information': 'معلومات المقيم',
        'Evaluator Name': 'اسم المقيم',
        'Back to Artifacts': 'العودة للقطع',
        'Print Report': 'طباعة التقرير',
        
        // Status values
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض'
      }
      
      return this.$page.props.locale === 'ar' ? translations[key] || key : key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },

    printReport() {
      window.print()
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