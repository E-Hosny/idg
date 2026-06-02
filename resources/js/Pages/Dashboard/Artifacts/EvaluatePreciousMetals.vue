<template>
  <DashboardLayout :page-title="pageTitle">
    <form @submit.prevent="submit" class="max-w-5xl mx-auto space-y-4">
      <div class="worksheet bg-white shadow border border-gray-500 p-3 md:p-4">
        <div class="flex items-center justify-between border-b border-gray-500 pb-1">
          <div class="text-lg font-bold">WR-F04</div>
          <div class="text-xl font-bold">{{ t.preciousMetalsWorksheet }}</div>
          <div class="text-xs text-gray-600">{{ artifact?.artifact_code || '' }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
          <section class="box">
            <h3 class="sec-title">1. {{ t.jobInformation }}</h3>
            <div class="row"><span>{{ t.testDate }}:</span><input v-model="form.test_date" type="date" class="line-input" /></div>
            <div class="row"><span>{{ t.testLocation }}:</span><input v-model="form.test_location" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.itemProductId }} #:</span><input v-model="form.item_product_id" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.receivingRecord }} #:</span><input v-model="form.receiving_record" type="text" class="line-input" /></div>
          </section>

          <section class="box">
            <h3 class="sec-title">2. {{ t.productInformation }}</h3>
            <div class="row"><span>{{ t.product }}:</span><input v-model="form.product" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.productNumber }} #:</span><input v-model="form.product_number" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.refNumber }} #:</span><input v-model="form.ref_number" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.grossWeight }}:</span><input v-model="form.gross_weight" type="number" step="0.0001" class="line-input" /></div>
            <div class="row"><span>{{ t.netWeight }}:</span><input v-model="form.net_weight" type="number" step="0.0001" class="line-input" /></div>
            <div class="pt-1 space-y-1">
              <div v-for="metal in purityMetals" :key="metal.key" class="flex flex-wrap items-center gap-2 text-xs">
                <span class="w-16">{{ metal.label }}:</span>
                <label v-for="opt in metal.options" :key="opt" class="inline-flex items-center gap-1">
                  <input type="checkbox" :value="opt" v-model="form.metal_purities[metal.key].selected" />
                  <span>{{ opt }}</span>
                </label>
              </div>
            </div>
          </section>
        </div>

        <section class="box mt-2">
          <h3 class="sec-title">3. {{ t.xrfAnalyzer }}</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-[10px] border border-gray-500">
              <thead>
                <tr>
                  <th class="cell head">{{ t.element }}</th>
                  <th v-for="n in readingNumbers" :key="n" colspan="2" class="cell head">{{ n }}</th>
                </tr>
                <tr>
                  <th class="cell head"></th>
                  <template v-for="n in readingNumbers" :key="'m-'+n">
                    <th class="cell head">%</th>
                    <th class="cell head">±</th>
                  </template>
                </tr>
              </thead>
              <tbody>
                <tr v-for="el in xrfElements" :key="el.symbol">
                  <td class="cell label">{{ el.name }} <span class="text-gray-600">{{ el.symbol }}</span></td>
                  <template v-for="n in readingNumbers" :key="el.symbol + '-' + n">
                    <td class="cell">
                      <input v-model="form.xrf_data[String(n)][el.symbol].percent" type="number" step="0.01" class="table-input" />
                    </td>
                    <td class="cell">
                      <input v-model="form.xrf_data[String(n)][el.symbol].error" type="number" step="0.01" class="table-input" />
                    </td>
                  </template>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-2">
          <section class="box">
            <h3 class="sec-title">4. {{ t.result }}</h3>
            <div class="grid grid-cols-2 gap-1 text-xs">
              <label v-for="flag in resultFlags" :key="flag" class="inline-flex items-center gap-1">
                <input type="checkbox" :value="flag" v-model="form.result_flags" />
                <span>{{ flag }}</span>
              </label>
            </div>
          </section>

          <section class="box md:col-span-2">
            <h3 class="sec-title">5. {{ t.comments }}</h3>
            <textarea v-model="form.comments" rows="2" class="line-textarea"></textarea>
          </section>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
          <section class="box">
            <h3 class="sec-title">6. {{ t.grader }}</h3>
            <div class="row"><span>{{ t.name }}:</span><input v-model="form.grader_name" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.date }}:</span><input v-model="form.grader_date" type="date" class="line-input" /></div>
            <div class="row"><span>{{ t.signature }}:</span><input v-model="form.grader_signature" type="text" class="line-input" /></div>
          </section>

          <section class="box">
            <h3 class="sec-title">12. {{ t.analyticalSection }}</h3>
            <textarea v-model="form.analytical_interpretation" rows="2" class="line-textarea"></textarea>
            <div class="row mt-1"><span>{{ t.name }}:</span><input v-model="form.analytical_name" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.date }}:</span><input v-model="form.analytical_date" type="date" class="line-input" /></div>
            <div class="row"><span>{{ t.signature }}:</span><input v-model="form.analytical_signature" type="text" class="line-input" /></div>
          </section>
        </div>

        <section class="box mt-2">
          <h3 class="sec-title">8. {{ t.productPhotography }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="row"><span>{{ t.image1 }}:</span><input v-model="form.image1_ref" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.image2 }}:</span><input v-model="form.image2_ref" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.takenBy }}:</span><input v-model="form.image_taken_by" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.date }}:</span><input v-model="form.image_date" type="date" class="line-input" /></div>
          </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
          <section class="box">
            <h3 class="sec-title">9. {{ t.retainingInformation }}</h3>
            <div class="row"><span>{{ t.place }}:</span><input v-model="form.retaining_place" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.retainedBy }}:</span><input v-model="form.retaining_by" type="text" class="line-input" /></div>
            <div class="row"><span>{{ t.date }}:</span><input v-model="form.retaining_date" type="date" class="line-input" /></div>
            <div class="row"><span>{{ t.signature }}:</span><input v-model="form.retaining_signature" type="text" class="line-input" /></div>
          </section>

          <section class="box">
            <h3 class="sec-title">10. {{ t.reportingInformation }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
              <div class="row"><span>{{ t.reportDone }}:</span><select v-model="form.report_done" class="line-input"><option :value="null"></option><option>Yes</option><option>No</option></select></div>
              <div class="row"><span>{{ t.labelDone }}:</span><select v-model="form.label_done" class="line-input"><option :value="null"></option><option>Yes</option><option>No</option></select></div>
              <div class="row"><span>{{ t.reportDoneBy }}:</span><input v-model="form.report_done_by" type="text" class="line-input" /></div>
              <div class="row"><span>{{ t.reportNumber }}:</span><input v-model="form.report_number" type="text" class="line-input" /></div>
              <div class="row"><span>{{ t.reportNumberPmr }}:</span><input v-model="form.report_number_pmr" type="text" class="line-input" /></div>
              <div class="row"><span>{{ t.checkedBy }}:</span><input v-model="form.checked_by" type="text" class="line-input" /></div>
              <div class="row"><span>{{ t.date }}:</span><input v-model="form.report_date" type="date" class="line-input" /></div>
              <div class="row"><span>{{ t.date }}:</span><input v-model="form.checked_date" type="date" class="line-input" /></div>
              <div class="row md:col-span-2"><span>{{ t.signature }}:</span><input v-model="form.report_signature" type="text" class="line-input" /></div>
              <div class="row md:col-span-2"><span>{{ t.signature }}:</span><input v-model="form.checked_signature" type="text" class="line-input" /></div>
            </div>
          </section>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Link :href="route('dashboard.artifacts')" class="px-4 py-2 border rounded-md text-gray-700">{{ t.cancel }}</Link>
        <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-green-700 text-white rounded-md hover:bg-green-800 disabled:opacity-50">
          {{ form.processing ? t.saving : t.save }}
        </button>
      </div>
    </form>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import {
  XRF_ELEMENTS,
  XRF_READING_NUMBERS,
  METAL_PURITY_OPTIONS,
  RESULT_FLAGS,
  buildFormDefaults,
} from '@/constants/preciousMetalsEvaluation'

const props = defineProps({
  artifact: { type: Object, required: true },
  existingEvaluation: { type: Object, default: null },
  isEditing: { type: Boolean, default: false },
})

const page = usePage()
const locale = computed(() => (page.props.locale === 'ar' ? 'ar' : 'en'))

const t = computed(() => {
  const ar = locale.value === 'ar'
  return {
    preciousMetalsWorksheet: ar ? 'ورقة عمل المعادن الثمينة' : 'Precious Metals Worksheet',
    jobInformation: ar ? 'معلومات العمل' : 'Job Information',
    productInformation: ar ? 'معلومات المنتج' : 'Product Information',
    xrfAnalyzer: ar ? 'محلل الأشعة السينية' : 'X-Ray Fluorescence (XRF) Analyzer',
    result: ar ? 'النتيجة' : 'Result',
    comments: ar ? 'ملاحظات' : 'Comments',
    grader: ar ? 'المُقيّم' : 'Grader',
    analyticalSection: ar ? 'التفسير الفني للنتيجة' : "Analytical equipment's used — Technical Interpretation",
    productPhotography: ar ? 'تصوير المنتج' : 'Product Photography',
    retainingInformation: ar ? 'معلومات الحفظ' : 'Retaining Information',
    reportingInformation: ar ? 'معلومات التقرير' : 'Reporting Information',
    testDate: ar ? 'تاريخ الاختبار' : 'Test Date',
    testLocation: ar ? 'موقع الاختبار' : 'Test Location',
    itemProductId: ar ? 'رقم المنتج/القطعة' : 'Item/Product ID #',
    receivingRecord: ar ? 'رقم سجل الاستلام' : 'Receiving Record #',
    product: ar ? 'المنتج' : 'Product',
    productNumber: ar ? 'رقم المنتج' : 'Product#',
    refNumber: ar ? 'المرجع' : 'Ref.#',
    grossWeight: ar ? 'الوزن الإجمالي' : 'Gross wt.',
    netWeight: ar ? 'الوزن الصافي' : 'Net Wt.',
    otherSpecify: ar ? 'أخرى (حدد)' : 'Other (specify)',
    element: ar ? 'العنصر' : 'Element',
    reading: ar ? 'قراءة' : 'Reading',
    technicalInterpretation: ar ? 'التفسير الفني' : 'Technical Interpretation',
    image1: ar ? 'صورة 1' : 'Image 1#',
    image2: ar ? 'صورة 2' : 'Image 2#',
    takenBy: ar ? 'تم التصوير بواسطة' : 'Taken by',
    place: ar ? 'المكان' : 'Place',
    retainedBy: ar ? 'حُفظ بواسطة' : 'Retained by',
    reportDone: ar ? 'تم التقرير؟' : 'Report Done?',
    labelDone: ar ? 'تم اللصق؟' : 'Label Done?',
    reportDoneBy: ar ? 'أُعد التقرير بواسطة' : 'Report done by',
    reportNumber: ar ? 'رقم التقرير' : 'Report#',
    reportNumberPmr: ar ? 'تقرير PMR' : 'Report # PMR',
    checkedBy: ar ? 'راجعه' : 'Checked by',
    name: ar ? 'الاسم' : 'Name',
    date: ar ? 'التاريخ' : 'Date',
    signature: ar ? 'التوقيع' : 'Signature',
    notes: ar ? 'ملاحظات' : 'Notes',
    cancel: ar ? 'إلغاء' : 'Cancel',
    save: ar ? 'حفظ التقييم' : 'Save Evaluation',
    saving: ar ? 'جاري الحفظ...' : 'Saving...',
  }
})

const pageTitle = computed(() =>
  props.isEditing
    ? (locale.value === 'ar' ? 'تعديل تقييم المعادن' : 'Edit Precious Metals Evaluation')
    : (locale.value === 'ar' ? 'تقييم المعادن الثمينة' : 'Precious Metals Evaluation')
)

const xrfElements = XRF_ELEMENTS
const readingNumbers = XRF_READING_NUMBERS
const resultFlags = RESULT_FLAGS

const purityMetals = computed(() => {
  const ar = locale.value === 'ar'
  return [
    { key: 'gold', label: ar ? 'ذهب' : 'Gold', options: METAL_PURITY_OPTIONS.gold },
    { key: 'platinum', label: ar ? 'بلاتين' : 'Platinum', options: METAL_PURITY_OPTIONS.platinum },
    { key: 'silver', label: ar ? 'فضة' : 'Silver', options: METAL_PURITY_OPTIONS.silver },
  ]
})

const initial = buildFormDefaults(props.artifact, props.existingEvaluation)
const form = useForm(initial)

function submit() {
  if (!props.artifact?.id) return
  if (props.isEditing) {
    form.put(route('artifacts.update-evaluation', props.artifact.id), {
      preserveScroll: true,
    })
    return
  }
  form.post(route('dashboard.artifacts.evaluate.store', props.artifact.id), {
    preserveScroll: true,
  })
}
</script>

<style scoped>
.worksheet {
  font-size: 11px;
}
.box {
  border: 1px solid #6b7280;
  padding: 4px;
}
.sec-title {
  font-size: 11px;
  font-weight: 700;
  border-bottom: 1px solid #6b7280;
  margin-bottom: 4px;
}
.row {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 2px;
}
.row span {
  min-width: 90px;
  white-space: nowrap;
}
.line-input {
  width: 100%;
  border: 0;
  border-bottom: 1px solid #9ca3af;
  background: transparent;
  font-size: 11px;
  padding: 1px 2px;
}
.line-textarea {
  width: 100%;
  border: 1px solid #9ca3af;
  font-size: 11px;
  padding: 2px 4px;
  resize: vertical;
}
.cell {
  border: 1px solid #6b7280;
  padding: 1px;
}
.head {
  font-weight: 700;
  text-align: center;
}
.label {
  white-space: nowrap;
}
.table-input {
  width: 100%;
  border: 0;
  background: transparent;
  text-align: center;
  font-size: 10px;
}
</style>
