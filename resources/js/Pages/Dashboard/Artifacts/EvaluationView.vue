<template>
  <DashboardLayout :page-title="__('Evaluation Report')">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-6xl mx-auto mt-8">
      <div class="text-center mb-8 border-b pb-6">
        <h1 class="text-3xl font-bold text-green-800 mb-2">{{ __('Evaluation Report') }}</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700 text-left">
          <div>
            <p><strong>{{ __('Item Code') }}:</strong> {{ artifact.artifact_code }}</p>
            <p><strong>{{ __('Type') }}:</strong> {{ artifact.type }}</p>
            <p><strong>{{ __('Client') }}:</strong> {{ artifact.client?.full_name || '-' }}</p>
          </div>
          <div>
            <p><strong>{{ __('Evaluation Date') }}:</strong> {{ formatDate(evaluation.evaluation_date || evaluation.created_at) }}</p>
            <p><strong>{{ __('Evaluator') }}:</strong> {{ evaluation.evaluator?.name || '-' }}</p>
            <p><strong>{{ __('Result') }}:</strong> <span :class="resultClass">{{ evaluation.result || '-' }}</span></p>
          </div>
          <div>
            <p><strong>{{ __('Status') }}:</strong> {{ __(artifact.status) }}</p>
            <p><strong>{{ __('Service') }}:</strong> {{ artifact.service || '-' }}</p>
          </div>
        </div>
      </div>

      <div v-if="isJewellery" class="space-y-8">
        <section>
          <h2 class="section-title">1. Job Information</h2>
          <div class="grid-2">
            <div class="field"><label>Test Date</label><p>{{ formatDate(evaluation.test_date) }}</p></div>
            <div class="field"><label>Test Location</label><p>{{ renderValue(evaluation.test_location) }}</p></div>
            <div class="field"><label>Item/Product ID #</label><p>{{ renderValue(evaluation.item_product_id) }}</p></div>
            <div class="field"><label>Receiving Record #</label><p>{{ renderValue(evaluation.receiving_record) }}</p></div>
            <div class="field"><label>Prepared by</label><p>{{ renderValue(evaluation.prepared_by) }}</p></div>
            <div class="field"><label>Approved by</label><p>{{ renderValue(evaluation.approved_by) }}</p></div>
          </div>
        </section>

        <section>
          <h2 class="section-title">2. Product Information</h2>
          <div class="grid-3">
            <div class="field"><label>Product #</label><p>{{ renderValue(evaluation.product_number) }}</p></div>
            <div class="field"><label>Style #</label><p>{{ renderValue(evaluation.style_number) }}</p></div>
            <div class="field"><label>Ref #</label><p>{{ renderValue(evaluation.ref_number) }}</p></div>
            <div class="field"><label>Gross Wt</label><p>{{ renderValue(evaluation.gross_weight) }}</p></div>
            <div class="field"><label>Net Wt</label><p>{{ renderValue(evaluation.net_weight) }}</p></div>
          </div>
          <div class="field mt-3"><label>Product</label><p>{{ renderArray(evaluation.product_types) }}</p></div>
          <div class="field mt-3"><label>Metal</label><p>{{ renderArray(evaluation.metal_types) }}</p></div>
          <div class="field mt-3"><label>Stamp</label><p>{{ renderArray(evaluation.stamps) }}</p></div>
        </section>

        <section>
          <h2 class="section-title">3. Lab-Grown Diamond Screen</h2>
          <div class="grid-2">
            <div class="field"><label>HPHT Screen</label><p>{{ renderValue(evaluation.hpht_screen) }}</p></div>
            <div class="field"><label>HPHT Diamond Pcs</label><p>{{ renderValue(evaluation.hpht_diamond_pcs) }}</p></div>
            <div class="field"><label>CVD Check</label><p>{{ renderValue(evaluation.cvd_check) }}</p></div>
            <div class="field"><label>CVD Diamond Pcs</label><p>{{ renderValue(evaluation.cvd_diamond_pcs) }}</p></div>
            <div class="field"><label>Need to be Unmount?</label><p>{{ renderValue(evaluation.need_unmount) }}</p></div>
            <div class="field"><label>Reason</label><p>{{ renderValue(evaluation.unmount_reason) }}</p></div>
          </div>
        </section>

        <section>
          <h2 class="section-title">4. XRF Analyzer</h2>
          <div v-if="Array.isArray(evaluation.xrf_data) && evaluation.xrf_data.length" class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border px-2 py-1">#</th>
                  <th class="border px-2 py-1">Au %</th>
                  <th class="border px-2 py-1">Au ppm</th>
                  <th class="border px-2 py-1">Ag %</th>
                  <th class="border px-2 py-1">Ag ppm</th>
                  <th class="border px-2 py-1">Cu %</th>
                  <th class="border px-2 py-1">Cu ppm</th>
                  <th class="border px-2 py-1">Pt %</th>
                  <th class="border px-2 py-1">Pt ppm</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, idx) in evaluation.xrf_data" :key="idx">
                  <td class="border px-2 py-1 text-center">{{ idx + 1 }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.au_percent) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.au_ppm) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.ag_percent) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.ag_ppm) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.cu_percent) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.cu_ppm) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.pt_percent) }}</td>
                  <td class="border px-2 py-1">{{ renderValue(row.pt_ppm) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-else class="text-gray-500">-</p>
        </section>

        <section>
          <h2 class="section-title">5. Diamond/s</h2>
          <div class="grid-2">
            <div class="field"><label>Wt Type</label><p>{{ renderValue(evaluation.side_stones_weight_type) }}</p></div>
            <div class="field"><label>Diamond Wt</label><p>{{ renderValue(evaluation.side_stones_weight) }}</p></div>
            <div class="field"><label>Diamond Pcs</label><p>{{ renderValue(evaluation.side_stones_pieces) }}</p></div>
            <div class="field"><label>Side Shapes</label><p>{{ renderArray(evaluation.side_stones_shapes) }}</p></div>
            <div class="field"><label>Octagonal (detail)</label><p>{{ renderValue(evaluation.side_stones_shape_octagonal_detail) }}</p></div>
            <div class="field"><label>Side Colours</label><p>{{ renderArray(evaluation.side_stones_colours) }}</p></div>
            <div class="field"><label>Side Clarities</label><p>{{ renderArray(evaluation.side_stones_clarities) }}</p></div>
            <div class="field"><label>Centre Weight</label><p>{{ renderValue(evaluation.centre_stone_weight) }}</p></div>
            <div class="field"><label>Centre Shape</label><p>{{ renderValue(evaluation.centre_stone_shape) }}</p></div>
            <div class="field"><label>Centre Colour</label><p>{{ renderValue(evaluation.centre_stone_colour) }}</p></div>
            <div class="field"><label>Centre Clarity</label><p>{{ renderValue(evaluation.centre_stone_clarity) }}</p></div>
          </div>
        </section>

        <section>
          <h2 class="section-title">6. Coloured Gemstones</h2>
          <div class="grid-2">
            <div class="field"><label>Weight</label><p>{{ renderValue(evaluation.coloured_stones_weight) }}</p></div>
            <div class="field"><label>Shape</label><p>{{ colouredGemField(evaluation.coloured_stones_shape, 'shape') }}</p></div>
            <div class="field"><label>Cut</label><p>{{ colouredGemField(evaluation.coloured_stones_cut, 'cut') }}</p></div>
            <div class="field"><label>No. of Stones</label><p>{{ renderValue(evaluation.coloured_stones_count) }}</p></div>
            <div class="field"><label>Group</label><p>{{ renderValue(evaluation.coloured_stones_group) }}</p></div>
            <div class="field"><label>Species</label><p>{{ colouredGemField(evaluation.coloured_stones_species, 'species') }}</p></div>
            <div class="field"><label>Variety</label><p>{{ colouredGemField(evaluation.coloured_stones_variety, 'variety') }}</p></div>
            <div class="field"><label>Conclusion</label><p>{{ renderValue(evaluation.coloured_stones_conclusion) }}</p></div>
            <div class="field md:col-span-2"><label>Note</label><p>{{ renderValue(evaluation.coloured_stones_note) }}</p></div>
          </div>
        </section>

        <section><h2 class="section-title">7. Result</h2><p>{{ renderValue(evaluation.result) }}</p></section>
        <section><h2 class="section-title">8. Comments</h2><p class="whitespace-pre-wrap">{{ renderValue(evaluation.comments) }}</p></section>
        <section class="grid-3">
          <div class="field"><label>Grader Name</label><p>{{ renderValue(evaluation.grader_name) }}</p></div>
          <div class="field"><label>Grader Date</label><p>{{ formatDate(evaluation.grader_date) }}</p></div>
          <div class="field"><label>Grader Signature</label><p>{{ renderValue(evaluation.grader_signature) }}</p></div>
        </section>
        <section class="grid-3">
          <div class="field"><label>Analytical Name</label><p>{{ renderValue(evaluation.analytical_name) }}</p></div>
          <div class="field"><label>Analytical Date</label><p>{{ formatDate(evaluation.analytical_date) }}</p></div>
          <div class="field"><label>Analytical Signature</label><p>{{ renderValue(evaluation.analytical_signature) }}</p></div>
        </section>
        <section class="grid-3">
          <div class="field"><label>Image1 Taken By</label><p>{{ renderValue(evaluation.image1_taken_by) }}</p></div>
          <div class="field"><label>Image1 Date</label><p>{{ formatDate(evaluation.image1_date) }}</p></div>
          <div class="field"><label>Image1 Signature</label><p>{{ renderValue(evaluation.image1_signature) }}</p></div>
          <div class="field"><label>Image2 Taken By</label><p>{{ renderValue(evaluation.image2_taken_by) }}</p></div>
          <div class="field"><label>Image2 Date</label><p>{{ formatDate(evaluation.image2_date) }}</p></div>
          <div class="field"><label>Image2 Signature</label><p>{{ renderValue(evaluation.image2_signature) }}</p></div>
        </section>
        <section class="grid-4">
          <div class="field"><label>Retaining Place</label><p>{{ renderValue(evaluation.retaining_place) }}</p></div>
          <div class="field"><label>Retained By</label><p>{{ renderValue(evaluation.retaining_by) }}</p></div>
          <div class="field"><label>Retaining Date</label><p>{{ formatDate(evaluation.retaining_date) }}</p></div>
          <div class="field"><label>Retaining Signature</label><p>{{ renderValue(evaluation.retaining_signature) }}</p></div>
        </section>
        <section class="grid-3">
          <div class="field"><label>Report Done</label><p>{{ renderValue(evaluation.report_done) }}</p></div>
          <div class="field"><label>Label Done</label><p>{{ renderValue(evaluation.label_done) }}</p></div>
          <div class="field"><label>Report Done By</label><p>{{ renderValue(evaluation.report_done_by) }}</p></div>
          <div class="field"><label>Checked By</label><p>{{ renderValue(evaluation.checked_by) }}</p></div>
          <div class="field"><label>Report #</label><p>{{ renderValue(evaluation.report_number) }}</p></div>
        </section>
      </div>

      <div v-else class="space-y-8">
        <section class="grid-3">
          <div class="field"><label>Test Date</label><p>{{ formatDate(evaluation.test_date) }}</p></div>
          <div class="field"><label>Test Location</label><p>{{ renderValue(evaluation.test_location) }}</p></div>
          <div class="field"><label>Item/Product ID</label><p>{{ renderValue(evaluation.item_id) }}</p></div>
          <div class="field"><label>Weight</label><p>{{ renderValue(evaluation.weight) }}</p></div>
          <div class="field"><label>Colour</label><p>{{ renderValue(evaluation.colour) }}</p></div>
          <div class="field"><label>Transparency</label><p>{{ renderValue(evaluation.transparency) }}</p></div>
          <div class="field"><label>Lustre</label><p>{{ renderValue(evaluation.lustre) }}</p></div>
          <div class="field"><label>Tone</label><p>{{ renderValue(evaluation.tone) }}</p></div>
          <div class="field"><label>Phenomena</label><p>{{ renderValue(evaluation.phenomena) }}</p></div>
          <div class="field"><label>Saturation</label><p>{{ renderValue(evaluation.saturation) }}</p></div>
          <div class="field"><label>Measurements</label><p>{{ renderValue(evaluation.measurements) }}</p></div>
          <div class="field"><label>Shape</label><p>{{ renderValue(evaluation.shape) }}</p></div>
          <div class="field"><label>Cut</label><p>{{ renderValue(evaluation.cut) }}</p></div>
          <div class="field"><label>Shape/Cut (Combined)</label><p>{{ renderValue(evaluation.shape_cut) }}</p></div>
          <div class="field"><label>Pleochroism</label><p>{{ renderValue(evaluation.pleochroism) }}</p></div>
          <div class="field"><label>Optic Character</label><p>{{ renderValue(evaluation.optic_character) }}</p></div>
          <div class="field"><label>Refractive Index</label><p>{{ renderArray(evaluation.refractive_index) }}</p></div>
          <div class="field"><label>RI Result</label><p>{{ renderValue(evaluation.ri_result) }}</p></div>
          <div class="field"><label>Inclusion</label><p>{{ renderValue(evaluation.inclusion) }}</p></div>
          <div class="field"><label>Weight in Air</label><p>{{ renderValue(evaluation.weight_air) }}</p></div>
          <div class="field"><label>Weight in Water</label><p>{{ renderValue(evaluation.weight_water) }}</p></div>
          <div class="field"><label>S.G. Result</label><p>{{ renderValue(evaluation.sg_result) }}</p></div>
          <div class="field"><label>Fluorescence Long</label><p>{{ renderValue(evaluation.fluorescence_long) }}</p></div>
          <div class="field"><label>Fluorescence Short</label><p>{{ renderValue(evaluation.fluorescence_short) }}</p></div>
          <div class="field"><label>Stone Group</label><p>{{ renderValue(evaluation.stone_group) }}</p></div>
          <div class="field"><label>Species</label><p>{{ renderValue(evaluation.species) }}</p></div>
          <div class="field"><label>Variety</label><p>{{ renderValue(evaluation.variety) }}</p></div>
          <div class="field"><label>Species/Group</label><p>{{ renderValue(evaluation.species_group) }}</p></div>
          <div class="field"><label>Treatment</label><p>{{ renderValue(evaluation.treatment) }}</p></div>
          <div class="field"><label>Gemstone Type</label><p>{{ renderValue(evaluation.gemstone_type) }}</p></div>
        </section>

        <section><h2 class="section-title">Comments</h2><p class="whitespace-pre-wrap">{{ renderValue(evaluation.comments) }}</p></section>
        <section class="grid-3">
          <div class="field"><label>Grader Name</label><p>{{ renderValue(evaluation.grader_name) }}</p></div>
          <div class="field"><label>Grader Date</label><p>{{ formatDate(evaluation.grader_date) }}</p></div>
          <div class="field"><label>Analytical Interpretation</label><p class="whitespace-pre-wrap">{{ renderValue(evaluation.analytical_interpretation) }}</p></div>
          <div class="field"><label>Retaining Place</label><p>{{ renderValue(evaluation.retaining_place) }}</p></div>
          <div class="field"><label>Retained By</label><p>{{ renderValue(evaluation.retained_by) }}</p></div>
          <div class="field"><label>Retained Date</label><p>{{ formatDate(evaluation.retained_date) }}</p></div>
          <div class="field"><label>Report Done</label><p>{{ renderBoolean(evaluation.report_done) }}</p></div>
          <div class="field"><label>Label Done</label><p>{{ renderBoolean(evaluation.label_done) }}</p></div>
          <div class="field"><label>Checked By</label><p>{{ renderValue(evaluation.checked_by) }}</p></div>
          <div class="field"><label>Checked Date</label><p>{{ formatDate(evaluation.checked_date) }}</p></div>
        </section>

        <section>
          <h2 class="section-title">Stone Photography</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field"><label>Image 1</label><p><a v-if="evaluation.image1_path" :href="`/certificate-file/${evaluation.image1_path}`" target="_blank" class="text-blue-600 underline">{{ evaluation.image1_path }}</a><span v-else>-</span></p></div>
            <div class="field"><label>Image 2</label><p><a v-if="evaluation.image2_path" :href="`/certificate-file/${evaluation.image2_path}`" target="_blank" class="text-blue-600 underline">{{ evaluation.image2_path }}</a><span v-else>-</span></p></div>
          </div>
        </section>
      </div>

      <div class="flex justify-between items-center pt-6 border-t mt-8 no-print">
        <Link :href="$route('dashboard.artifacts')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
          {{ __('Back to Artifacts') }}
        </Link>
        <div class="flex space-x-4">
          <Link :href="route('artifacts.edit-evaluation', artifact.id)" class="px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
            🖊️ {{ __('Edit Evaluation') }}
          </Link>
          <button @click="printReport" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
import {
  colouredGemStoneShapes,
  colouredGemStoneCuts,
  colouredGemStoneSpecies,
  colouredGemStoneVarieties,
  labelFromListSelection,
} from '@/constants/jewelleryColouredGemOptions'

const route = window.route

export default {
  components: { DashboardLayout, Link },
  props: {
    artifact: Object,
    evaluation: Object
  },
  
  setup(props) {
    const isJewellery = computed(() => props.artifact?.type === 'Jewellery')
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
      isJewellery,
      resultClass
    }
  },

  methods: {
    renderValue(value) {
      if (value === null || value === undefined || value === '') return '-'
      return String(value)
    },
    renderArray(value) {
      if (!Array.isArray(value) || value.length === 0) return '-'
      return value.join(', ')
    },
    colouredGemField(stored, kind) {
      const map = {
        shape: colouredGemStoneShapes,
        cut: colouredGemStoneCuts,
        species: colouredGemStoneSpecies,
        variety: colouredGemStoneVarieties,
      }
      const list = map[kind] || []
      const t = labelFromListSelection(stored, list)
      if (t === null || t === undefined || t === '') {
        return '-'
      }
      return String(t)
    },
    renderBoolean(value) {
      if (value === null || value === undefined || value === '') return '-'
      return value ? 'Yes' : 'No'
    },
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
.section-title {
  @apply text-lg font-semibold text-green-700 mb-3;
}
.field label {
  @apply block text-gray-600 text-sm font-semibold;
}
.field p {
  @apply text-gray-900 break-words;
}
.grid-2 {
  @apply grid grid-cols-1 md:grid-cols-2 gap-4;
}
.grid-3 {
  @apply grid grid-cols-1 md:grid-cols-3 gap-4;
}
.grid-4 {
  @apply grid grid-cols-1 md:grid-cols-4 gap-4;
}
@media print {
  .no-print {
    display: none !important;
  }
}
</style> 