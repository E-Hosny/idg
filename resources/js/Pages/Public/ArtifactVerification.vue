<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <div class="flex items-center">
            <img src="/images/idg-logo.svg" alt="IDG Logo" class="h-12 w-auto mr-4">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Item Verification') }}</h1>
          </div>
          <div class="text-sm text-gray-500">
            {{ __('International Diamond Group') }}
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Verification Status -->
      <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>
            <h3 class="text-lg font-semibold text-green-800">{{ __('Verified Authentic') }}</h3>
            <p class="text-green-700">{{ __('This item has been authenticated by IDG experts.') }}</p>
          </div>
        </div>
      </div>

      <!-- Item Information -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
          <h2 class="text-xl font-semibold text-gray-900">{{ __('Item Information') }}</h2>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Item Code') }}</label>
              <p class="mt-1 text-lg font-semibold text-blue-600">{{ artifact.artifact_code }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
              <p class="mt-1 text-gray-900">{{ __(artifact.type) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Service') }}</label>
              <p class="mt-1 text-gray-900">{{ artifact.service }}</p>
            </div>
            <div v-if="artifact.weight">
              <label class="block text-sm font-medium text-gray-700">{{ __('Weight') }}</label>
              <p class="mt-1 text-gray-900">
                {{ artifact.weight }} 
                <span v-if="artifact.weight_unit" class="text-gray-600">{{ __(artifact.weight_unit) }}</span>
              </p>
            </div>
            <div v-if="artifact.client">
              <label class="block text-sm font-medium text-gray-700">{{ __('Client') }}</label>
              <p class="mt-1 text-gray-900">{{ artifact.client.full_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                {{ __(artifact.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Evaluation Details -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
          <h2 class="text-xl font-semibold text-gray-900">{{ __('Evaluation Details') }}</h2>
        </div>
        <div class="px-6 py-4">
          <!-- Diamond Evaluation -->
          <div v-if="artifact.type === 'Colorless Diamonds'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.shape">
              <label class="block text-sm font-medium text-gray-700">{{ __('Shape') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.shape }}</p>
            </div>
            <div v-if="evaluation.weight">
              <label class="block text-sm font-medium text-gray-700">{{ __('Carat Weight') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.weight }} {{ __('ct') }}</p>
            </div>
            <div v-if="evaluation.colour">
              <label class="block text-sm font-medium text-gray-700">{{ __('Color') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.colour }}</p>
            </div>
            <div v-if="evaluation.clarity">
              <label class="block text-sm font-medium text-gray-700">{{ __('Clarity') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.clarity }}</p>
            </div>
            <div v-if="evaluation.diameter">
              <label class="block text-sm font-medium text-gray-700">{{ __('Dimensions') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.diameter }} mm</p>
            </div>
            <div v-if="evaluation.stone_type">
              <label class="block text-sm font-medium text-gray-700">{{ __('Stone Type') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.stone_type }}</p>
            </div>
          </div>

          <!-- General Evaluation -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.weight">
              <label class="block text-sm font-medium text-gray-700">{{ __('Weight') }}</label>
              <p class="mt-1 text-gray-900">
                {{ evaluation.weight }} 
                <span v-if="artifact.weight_unit">{{ __(artifact.weight_unit) }}</span>
              </p>
            </div>
            <div v-if="evaluation.colour">
              <label class="block text-sm font-medium text-gray-700">{{ __('Color') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.colour }}</p>
            </div>
            <div v-if="evaluation.shape_cut">
              <label class="block text-sm font-medium text-gray-700">{{ __('Shape/Cut') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.shape_cut }}</p>
            </div>
            <div v-if="evaluation.measurements">
              <label class="block text-sm font-medium text-gray-700">{{ __('Dimensions') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.measurements }}</p>
            </div>
            <div v-if="evaluation.transparency">
              <label class="block text-sm font-medium text-gray-700">{{ __('Transparency') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.transparency }}</p>
            </div>
            <div v-if="evaluation.species_group">
              <label class="block text-sm font-medium text-gray-700">{{ __('Species/Group') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.species_group }}</p>
            </div>
            <div v-if="evaluation.variety">
              <label class="block text-sm font-medium text-gray-700">{{ __('Variety') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.variety }}</p>
            </div>
            <div v-if="evaluation.result">
              <label class="block text-sm font-medium text-gray-700">{{ __('Result') }}</label>
              <p class="mt-1 text-gray-900">{{ evaluation.result }}</p>
            </div>
          </div>

          <!-- Comments -->
          <div v-if="evaluation.comments" class="mt-6">
            <label class="block text-sm font-medium text-gray-700">{{ __('Comments') }}</label>
            <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded">{{ evaluation.comments }}</p>
          </div>

          <!-- Evaluation Date -->
          <div class="mt-6 pt-4 border-t">
            <div class="flex justify-between items-center text-sm text-gray-600">
              <span>{{ __('Evaluation completed on') }}: {{ formatDate(evaluation.created_at) }}</span>
              <span>{{ __('Verified by IDG') }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 text-center text-sm text-gray-500">
        <p>{{ __('This verification is authentic and issued by International Diamond Group (IDG).') }}</p>
        <p class="mt-1">{{ __('For questions or verification, please contact IDG directly.') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    artifact: Object,
    evaluation: Object,
    isPublic: {
      type: Boolean,
      default: true
    }
  },

  methods: {
    __(key) {
      const translations = {
        'Item Verification': 'التحقق من العنصر',
        'International Diamond Group': 'مجموعة الماس الدولية',
        'Verified Authentic': 'موثق ومعتمد',
        'This item has been authenticated by IDG experts.': 'تم توثيق هذا العنصر من قبل خبراء IDG.',
        'Items Information': 'معلومات العنصر',
        'Item Code': 'رمز العنصر',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Client': 'العميل',
        'Status': 'الحالة',
        'Evaluation Details': 'تفاصيل التقييم',
        'Shape': 'الشكل',
        'Carat Weight': 'وزن القيراط',
        'Color': 'اللون',
        'Clarity': 'النقاء',
        'Dimensions': 'الأبعاد',
        'Stone Type': 'نوع الحجر',
        'Shape/Cut': 'الشكل/القطع',
        'Transparency': 'الشفافية',
        'Species/Group': 'النوع/المجموعة',
        'Variety': 'الصنف',
        'Result': 'النتيجة',
        'Comments': 'ملاحظات',
        'Evaluation completed on': 'تم التقييم في',
        'Verified by IDG': 'معتمد من IDG',
        'This verification is authentic and issued by International Diamond Group (IDG).': 'هذا التحقق أصلي وصادر عن مجموعة الماس الدولية (IDG).',
        'For questions or verification, please contact IDG directly.': 'للاستفسارات أو التحقق، يرجى الاتصال بـ IDG مباشرة.',
        
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
      
      return this.$page?.props?.locale === 'ar' ? translations[key] || key : key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page?.props?.locale === 'ar' ? 'ar-EG' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  }
}
</script> 