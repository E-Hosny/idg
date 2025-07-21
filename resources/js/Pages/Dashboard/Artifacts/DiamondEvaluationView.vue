<template>
  <DashboardLayout :page-title="__('Diamond Evaluation Report')">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-6xl mx-auto mt-8">
      <!-- Header -->
      <div class="text-center mb-8 border-b pb-6">
        <h1 class="text-3xl font-bold text-green-800 mb-2">{{ __('Diamond Evaluation Report') }}</h1>
        <div class="flex justify-between items-center text-sm text-gray-600">
          <div>
            <p><strong>{{ __('Artifact Code') }}:</strong> {{ artifact.artifact_code }}</p>
            <p><strong>{{ __('Client') }}:</strong> {{ artifact.client?.full_name || '-' }}</p>
          </div>
          <div>
            <p><strong>{{ __('Evaluation Date') }}:</strong> {{ formatDate(evaluation.test_date) }}</p>
            <p><strong>{{ __('Evaluator') }}:</strong> {{ evaluation.evaluator?.name || '-' }}</p>
          </div>
          <div>
            <span :class="statusClass" class="px-3 py-1 rounded-full text-sm font-semibold">
              {{ __(evaluation.status) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Content Sections -->
      <div class="space-y-8">
        
        <!-- 1. Job Information -->
        <section v-if="hasJobInfo" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('1. Job Information') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-if="evaluation.test_date">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Test Date') }}</label>
              <p class="text-gray-800">{{ formatDate(evaluation.test_date) }}</p>
            </div>
            <div v-if="evaluation.test_location">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Test Location') }}</label>
              <p class="text-gray-800">{{ evaluation.test_location }}</p>
            </div>
            <div v-if="evaluation.item_product_id">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Item/Product ID') }}</label>
              <p class="text-gray-800">{{ evaluation.item_product_id }}</p>
            </div>
            <div v-if="evaluation.receiving_record">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Receiving Record') }}</label>
              <p class="text-gray-800">{{ evaluation.receiving_record }}</p>
            </div>
            <div v-if="evaluation.prepared_by">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Prepared by') }}</label>
              <p class="text-gray-800">{{ evaluation.prepared_by }}</p>
            </div>
            <div v-if="evaluation.approved_by">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Approved by') }}</label>
              <p class="text-gray-800">{{ evaluation.approved_by }}</p>
            </div>
          </div>
        </section>

        <!-- 2. Stone Information -->
        <section v-if="hasStoneInfo" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('2. Stone Information') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-if="evaluation.weight">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Weight (ct)') }}</label>
              <p class="text-gray-800">{{ evaluation.weight }}</p>
            </div>
            <div v-if="evaluation.shape">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Shape') }}</label>
              <p class="text-gray-800">{{ evaluation.shape }}</p>
            </div>
            <div v-if="evaluation.laser_inscription">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Laser Inscription') }}</label>
              <p class="text-gray-800">{{ __(evaluation.laser_inscription) }}</p>
            </div>
          </div>
        </section>

        <!-- 3. Lab-Grown Diamond Screen -->
        <section v-if="hasLabScreen" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('3. Lab-Grown Diamond Screen') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.hpht_screen">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('HPHT Screen') }}</label>
              <p class="text-gray-800">{{ __(evaluation.hpht_screen) }}</p>
            </div>
            <div v-if="evaluation.cvd_check">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('CVD Check') }}</label>
              <p class="text-gray-800">{{ __(evaluation.cvd_check) }}</p>
            </div>
          </div>
        </section>

        <!-- 4. Proportion Grade -->
        <section v-if="hasProportions" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('4. Proportion Grade') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-if="evaluation.diameter">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Diameter (mm)') }}</label>
              <p class="text-gray-800">{{ evaluation.diameter }}</p>
            </div>
            <div v-if="evaluation.total_depth">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Total Depth (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.total_depth }}</p>
            </div>
            <div v-if="evaluation.table_percentage">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Table (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.table_percentage }}</p>
            </div>
            <div v-if="evaluation.star_facet">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Star Facet (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.star_facet }}</p>
            </div>
            <div v-if="evaluation.crown_angle">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Crown Angle (°)') }}</label>
              <p class="text-gray-800">{{ evaluation.crown_angle }}</p>
            </div>
            <div v-if="evaluation.crown_height">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Crown Height (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.crown_height }}</p>
            </div>
            <div v-if="evaluation.girdle_thickness_min">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Girdle Thickness Min (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.girdle_thickness_min }}</p>
            </div>
            <div v-if="evaluation.girdle_thickness_max">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Girdle Thickness Max (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.girdle_thickness_max }}</p>
            </div>
            <div v-if="evaluation.pavilion_depth">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Pavilion Depth (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.pavilion_depth }}</p>
            </div>
            <div v-if="evaluation.pavilion_angle">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Pavilion Angle (°)') }}</label>
              <p class="text-gray-800">{{ evaluation.pavilion_angle }}</p>
            </div>
            <div v-if="evaluation.lower_girdle">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Lower Girdle (%)') }}</label>
              <p class="text-gray-800">{{ evaluation.lower_girdle }}</p>
            </div>
            <div v-if="evaluation.culet_size">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Culet Size') }}</label>
              <p class="text-gray-800">{{ evaluation.culet_size }}</p>
            </div>
            <div v-if="evaluation.girdle_condition">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Girdle Condition') }}</label>
              <p class="text-gray-800">{{ evaluation.girdle_condition }}</p>
            </div>
            <div v-if="evaluation.culet_condition">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Culet Condition') }}</label>
              <p class="text-gray-800">{{ evaluation.culet_condition }}</p>
            </div>
          </div>
        </section>

        <!-- 5. Grade Information -->
        <section v-if="hasGradeInfo" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('5. Grade Information') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-if="evaluation.polish">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Polish') }}</label>
              <p class="text-gray-800">{{ evaluation.polish }}</p>
            </div>
            <div v-if="evaluation.symmetry">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Symmetry') }}</label>
              <p class="text-gray-800">{{ evaluation.symmetry }}</p>
            </div>
            <div v-if="evaluation.cut">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Cut') }}</label>
              <p class="text-gray-800">{{ evaluation.cut }}</p>
            </div>
          </div>
        </section>

        <!-- 6. Visual Inspection -->
        <section v-if="hasVisualInfo" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('6. Visual Inspection') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.clarity">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Clarity') }}</label>
              <p class="text-gray-800 text-lg font-semibold">{{ evaluation.clarity }}</p>
            </div>
            <div v-if="evaluation.colour">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Colour') }}</label>
              <p class="text-gray-800 text-lg font-semibold">{{ evaluation.colour }}</p>
            </div>
          </div>
        </section>

        <!-- 7. Fluorescence -->
        <section v-if="hasFluorescence" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('7. Fluorescence') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.fluorescence_strength">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Strength') }}</label>
              <p class="text-gray-800">{{ evaluation.fluorescence_strength }}</p>
            </div>
            <div v-if="evaluation.fluorescence_colour">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Colour') }}</label>
              <p class="text-gray-800">{{ evaluation.fluorescence_colour }}</p>
            </div>
          </div>
        </section>

        <!-- 8. Result -->
        <section v-if="hasResult" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('8. Result') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="evaluation.result">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Result') }}</label>
              <p :class="resultClass" class="font-semibold text-lg">{{ __(evaluation.result) }}</p>
            </div>
            <div v-if="evaluation.stone_type">
              <label class="block text-gray-600 text-sm font-semibold">{{ __('Stone Type') }}</label>
              <p class="text-gray-800">{{ __(evaluation.stone_type) }}</p>
            </div>
          </div>
        </section>

        <!-- 9. Comments -->
        <section v-if="evaluation.comments" class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('9. Comments') }}</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-800 whitespace-pre-wrap">{{ evaluation.comments }}</p>
          </div>
        </section>

        <!-- 10. Signatures and Dates -->
        <section class="pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">{{ __('Signatures & Dates') }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Grader Info -->
            <div v-if="hasGraderInfo" class="bg-gray-50 p-4 rounded-lg">
              <h3 class="font-semibold text-gray-700 mb-3">{{ __('Grader') }}</h3>
              <div class="space-y-2">
                <p v-if="evaluation.grader_name"><strong>{{ __('Name') }}:</strong> {{ evaluation.grader_name }}</p>
                <p v-if="evaluation.grader_date"><strong>{{ __('Date') }}:</strong> {{ formatDate(evaluation.grader_date) }}</p>
                <p v-if="evaluation.grader_signature"><strong>{{ __('Signature') }}:</strong> {{ evaluation.grader_signature }}</p>
              </div>
            </div>

            <!-- Analytical Equipment -->
            <div v-if="hasAnalyticalInfo" class="bg-gray-50 p-4 rounded-lg">
              <h3 class="font-semibold text-gray-700 mb-3">{{ __('Analytical Equipment') }}</h3>
              <div class="space-y-2">
                <p v-if="evaluation.analytical_name"><strong>{{ __('Name') }}:</strong> {{ evaluation.analytical_name }}</p>
                <p v-if="evaluation.analytical_date"><strong>{{ __('Date') }}:</strong> {{ formatDate(evaluation.analytical_date) }}</p>
                <p v-if="evaluation.analytical_signature"><strong>{{ __('Signature') }}:</strong> {{ evaluation.analytical_signature }}</p>
              </div>
            </div>

            <!-- Reporting Info -->
            <div v-if="hasReportingInfo" class="bg-gray-50 p-4 rounded-lg">
              <h3 class="font-semibold text-gray-700 mb-3">{{ __('Reporting Information') }}</h3>
              <div class="space-y-2">
                <p v-if="evaluation.report_done"><strong>{{ __('Report Done') }}:</strong> {{ __(evaluation.report_done) }}</p>
                <p v-if="evaluation.label_done"><strong>{{ __('Label Done') }}:</strong> {{ __(evaluation.label_done) }}</p>
                <p v-if="evaluation.report_done_by"><strong>{{ __('Report Done by') }}:</strong> {{ evaluation.report_done_by }}</p>
                <p v-if="evaluation.report_date"><strong>{{ __('Report Date') }}:</strong> {{ formatDate(evaluation.report_date) }}</p>
                <p v-if="evaluation.checked_by"><strong>{{ __('Checked by') }}:</strong> {{ evaluation.checked_by }}</p>
                <p v-if="evaluation.report_number"><strong>{{ __('Report Number') }}:</strong> {{ evaluation.report_number }}</p>
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
          <Link 
            v-if="evaluation.status === 'draft'"
            :href="`/dashboard/artifacts/${artifact.id}/evaluate`"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
          >
            {{ __('Continue Editing') }}
          </Link>
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
    // Computed properties للتحقق من وجود البيانات
    const hasJobInfo = computed(() => {
      return props.evaluation?.test_date || props.evaluation?.test_location || 
             props.evaluation?.item_product_id || props.evaluation?.receiving_record ||
             props.evaluation?.prepared_by || props.evaluation?.approved_by
    })

    const hasStoneInfo = computed(() => {
      return props.evaluation?.weight || props.evaluation?.shape || props.evaluation?.laser_inscription
    })

    const hasLabScreen = computed(() => {
      return props.evaluation?.hpht_screen || props.evaluation?.cvd_check
    })

    const hasProportions = computed(() => {
      return props.evaluation?.diameter || props.evaluation?.total_depth || 
             props.evaluation?.table_percentage || props.evaluation?.star_facet ||
             props.evaluation?.crown_angle || props.evaluation?.crown_height
    })

    const hasGradeInfo = computed(() => {
      return props.evaluation?.polish || props.evaluation?.symmetry || props.evaluation?.cut
    })

    const hasVisualInfo = computed(() => {
      return props.evaluation?.clarity || props.evaluation?.colour
    })

    const hasFluorescence = computed(() => {
      return props.evaluation?.fluorescence_strength || props.evaluation?.fluorescence_colour
    })

    const hasResult = computed(() => {
      return props.evaluation?.result || props.evaluation?.stone_type
    })

    const hasGraderInfo = computed(() => {
      return props.evaluation?.grader_name || props.evaluation?.grader_date || props.evaluation?.grader_signature
    })

    const hasAnalyticalInfo = computed(() => {
      return props.evaluation?.analytical_name || props.evaluation?.analytical_date || props.evaluation?.analytical_signature
    })

    const hasReportingInfo = computed(() => {
      return props.evaluation?.report_done || props.evaluation?.label_done || 
             props.evaluation?.report_done_by || props.evaluation?.report_date ||
             props.evaluation?.checked_by || props.evaluation?.report_number
    })

    const statusClass = computed(() => {
      const status = props.evaluation?.status
      return {
        'bg-gray-100 text-gray-800': status === 'draft',
        'bg-blue-100 text-blue-800': status === 'completed',
        'bg-green-100 text-green-800': status === 'approved'
      }
    })

    const resultClass = computed(() => {
      const result = props.evaluation?.result
      return {
        'text-green-600': result === 'Pass',
        'text-red-600': result === 'Fail' || result === 'Reject',
        'text-gray-800': !result
      }
    })

    return {
      hasJobInfo,
      hasStoneInfo,
      hasLabScreen,
      hasProportions,
      hasGradeInfo,
      hasVisualInfo,
      hasFluorescence,
      hasResult,
      hasGraderInfo,
      hasAnalyticalInfo,
      hasReportingInfo,
      statusClass,
      resultClass
    }
  },

  methods: {
    __(key) {
      const translations = {
        // Main titles
        'Diamond Evaluation Report': 'تقرير تقييم الألماس',
        'Artifact Code': 'كود القطعة',
        'Client': 'العميل',
        'Evaluation Date': 'تاريخ التقييم',
        'Evaluator': 'المقيم',
        
        // Status
        'draft': 'مسودة',
        'completed': 'مكتمل',
        'approved': 'معتمد',
        
        // Sections
        '1. Job Information': '١. معلومات المهمة',
        '2. Stone Information': '٢. معلومات الحجر',
        '3. Lab-Grown Diamond Screen': '٣. فحص الألماس الصناعي',
        '4. Proportion Grade': '٤. درجة النسب',
        '5. Grade Information': '٥. معلومات التقدير',
        '6. Visual Inspection': '٦. الفحص البصري',
        '7. Fluorescence': '٧. التألق الفلوري',
        '8. Result': '٨. النتيجة',
        '9. Comments': '٩. التعليقات',
        'Signatures & Dates': 'التوقيعات والتواريخ',
        
        // Fields
        'Test Date': 'تاريخ الفحص',
        'Test Location': 'موقع الفحص',
        'Item/Product ID': 'معرف المنتج',
        'Receiving Record': 'سجل الاستلام',
        'Prepared by': 'أعده',
        'Approved by': 'اعتمده',
        'Weight (ct)': 'الوزن (قيراط)',
        'Shape': 'الشكل',
        'Laser Inscription': 'النقش بالليزر',
        'HPHT Screen': 'فحص HPHT',
        'CVD Check': 'فحص CVD',
        'Diameter (mm)': 'القطر (مم)',
        'Total Depth (%)': 'العمق الكلي (%)',
        'Table (%)': 'الطاولة (%)',
        'Star Facet (%)': 'وجه النجمة (%)',
        'Crown Angle (°)': 'زاوية التاج (°)',
        'Crown Height (%)': 'ارتفاع التاج (%)',
        'Girdle Thickness Min (%)': 'الحد الأدنى لسمك الحزام (%)',
        'Girdle Thickness Max (%)': 'الحد الأقصى لسمك الحزام (%)',
        'Pavilion Depth (%)': 'عمق الجناح (%)',
        'Pavilion Angle (°)': 'زاوية الجناح (°)',
        'Lower Girdle (%)': 'الحزام السفلي (%)',
        'Culet Size': 'حجم القاعدة',
        'Girdle Condition': 'حالة الحزام',
        'Culet Condition': 'حالة القاعدة',
        'Polish': 'التلميع',
        'Symmetry': 'التماثل',
        'Cut': 'القطع',
        'Clarity': 'النقاء',
        'Colour': 'اللون',
        'Strength': 'القوة',
        'Result': 'النتيجة',
        'Stone Type': 'نوع الحجر',
        'Grader': 'المقيم',
        'Analytical Equipment': 'المعدات التحليلية',
        'Reporting Information': 'معلومات التقرير',
        'Name': 'الاسم',
        'Date': 'التاريخ',
        'Signature': 'التوقيع',
        'Report Done': 'تم التقرير',
        'Label Done': 'تم التصنيف',
        'Report Done by': 'أعد التقرير',
        'Report Date': 'تاريخ التقرير',
        'Checked by': 'راجعه',
        'Report Number': 'رقم التقرير',
        
        // Values
        'Yes': 'نعم',
        'No': 'لا',
        'Natural': 'طبيعي',
        'Lab-Grown': 'صناعي',
        'Synthetic': 'صناعي',
        'Imitation': 'تقليد',
        'Treated': 'معالج',
        'Pass': 'نجح',
        'Fail': 'فشل',
        'Reject': 'مرفوض',
        
        // Actions
        'Back to Artifacts': 'العودة للقطع',
        'Print Report': 'طباعة التقرير',
        'Continue Editing': 'متابعة التحرير',
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
  
  .print-section {
    page-break-inside: avoid;
  }
}
</style> 