<template>
  <DashboardLayout :page-title="pageTitle">
    <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6 space-y-6">
      <header class="border-b pb-4">
        <h1 class="text-xl font-bold text-green-800">WR-F04 — {{ t.worksheetTitle }}</h1>
        <p class="text-sm text-gray-600 mt-1">
          {{ artifact?.artifact_code }} · {{ artifact?.type }}
          <span v-if="artifact?.subtype"> · {{ localizedSubtype }}</span>
        </p>
      </header>

      <template v-if="evaluation">
        <section v-for="block in textSections" :key="block.title" class="border-b pb-4">
          <h2 class="font-semibold text-green-700 mb-2">{{ block.title }}</h2>
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div v-for="row in block.rows" :key="row.label">
              <dt class="text-gray-500">{{ row.label }}</dt>
              <dd class="font-medium whitespace-pre-wrap">{{ display(row.value) }}</dd>
            </div>
          </dl>
        </section>

        <section v-if="metalPurityRows.length" class="border-b pb-4">
          <h2 class="font-semibold text-green-700 mb-2">{{ t.metalPurities }}</h2>
          <dl class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div v-for="row in metalPurityRows" :key="row.metal">
              <dt class="text-gray-500">{{ row.metal }}</dt>
              <dd class="font-medium">{{ row.value }}</dd>
            </div>
          </dl>
        </section>

        <section class="border-b pb-4 overflow-x-auto">
          <h2 class="font-semibold text-green-700 mb-2">{{ t.xrfAnalyzer }}</h2>
          <table class="min-w-full text-xs border border-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="border px-2 py-1 text-left">{{ t.element }}</th>
                <th
                  v-for="n in readingNumbers"
                  :key="'h-' + n"
                  colspan="2"
                  class="border px-2 py-1 text-center"
                >
                  {{ t.reading }} {{ n }}
                </th>
              </tr>
              <tr>
                <th class="border px-2 py-1"></th>
                <template v-for="n in readingNumbers" :key="'sub-' + n">
                  <th class="border px-1 py-1">%</th>
                  <th class="border px-1 py-1">±</th>
                </template>
              </tr>
            </thead>
            <tbody>
              <tr v-for="el in xrfElements" :key="el.symbol">
                <td class="border px-2 py-1 font-medium">{{ el.symbol }} ({{ el.name }})</td>
                <template v-for="n in readingNumbers" :key="el.symbol + '-' + n">
                  <td class="border px-1 py-1 text-center">{{ xrfCell(n, el.symbol, 'percent') }}</td>
                  <td class="border px-1 py-1 text-center">{{ xrfCell(n, el.symbol, 'error') }}</td>
                </template>
              </tr>
            </tbody>
          </table>
        </section>

        <div class="flex gap-4 text-sm">
          <Link
            v-if="artifact?.id"
            :href="route('artifacts.edit-evaluation', artifact.id)"
            class="text-green-700 underline"
          >
            {{ t.edit }}
          </Link>
          <Link :href="route('dashboard.artifacts')" class="text-gray-600 underline">{{ t.back }}</Link>
        </div>
      </template>

      <p v-else class="text-gray-500">{{ t.noData }}</p>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { XRF_ELEMENTS, XRF_READING_NUMBERS } from '@/constants/preciousMetalsEvaluation'
import { getPreciousMetalsSubtypeOptions } from '@/constants/artifactTypes'

const props = defineProps({
  artifact: Object,
  evaluation: Object,
})

const page = usePage()
const ar = computed(() => page.props.locale === 'ar')

const t = computed(() => ({
  worksheetTitle: ar.value ? 'ورقة عمل المعادن الثمينة' : 'Precious Metals Worksheet',
  metalPurities: ar.value ? 'نقاء المعادن' : 'Metal Purities',
  xrfAnalyzer: ar.value ? 'محلل الأشعة السينية (XRF)' : 'X-Ray Fluorescence (XRF)',
  element: ar.value ? 'العنصر' : 'Element',
  reading: ar.value ? 'قراءة' : 'Reading',
  edit: ar.value ? 'تعديل التقييم' : 'Edit evaluation',
  back: ar.value ? 'العودة للقطع' : 'Back to items',
  noData: ar.value ? 'لا توجد بيانات تقييم.' : 'No evaluation data.',
}))

const pageTitle = computed(() => (ar.value ? 'عرض تقييم المعادن' : 'Precious Metals Evaluation'))

const xrfElements = XRF_ELEMENTS
const readingNumbers = XRF_READING_NUMBERS

const localizedSubtype = computed(() => {
  if (!props.artifact?.subtype) return ''
  const opts = getPreciousMetalsSubtypeOptions(ar.value ? 'ar' : 'en')
  return opts.find((o) => o.value === props.artifact.subtype)?.label || props.artifact.subtype
})

function display(v) {
  if (v === null || v === undefined || v === '') return '—'
  if (Array.isArray(v)) return v.length ? v.join(', ') : '—'
  return String(v)
}

function formatPurity(metalKey, data) {
  if (!data) return '—'
  const parts = [...(data.selected || [])]
  if (data.other) parts.push(data.other)
  const labels = { gold: ar.value ? 'ذهب' : 'Gold', platinum: ar.value ? 'بلاتين' : 'Platinum', silver: ar.value ? 'فضة' : 'Silver' }
  const label = labels[metalKey] || metalKey
  return parts.length ? `${label}: ${parts.join(', ')}` : '—'
}

const metalPurityRows = computed(() => {
  const mp = props.evaluation?.metal_purities
  if (!mp) return []
  return [
    { metal: ar.value ? 'ذهب' : 'Gold', value: formatPurity('gold', mp.gold) },
    { metal: ar.value ? 'بلاتين' : 'Platinum', value: formatPurity('platinum', mp.platinum) },
    { metal: ar.value ? 'فضة' : 'Silver', value: formatPurity('silver', mp.silver) },
  ].filter((r) => r.value !== '—')
})

function xrfCell(reading, symbol, field) {
  const data = props.evaluation?.xrf_data?.[String(reading)]?.[symbol]
  const v = data?.[field]
  return v === null || v === undefined || v === '' ? '—' : v
}

const textSections = computed(() => {
  const e = props.evaluation
  if (!e) return []
  const L = (en, arLabel) => (ar.value ? arLabel : en)
  return [
    {
      title: L('Job Information', 'معلومات العمل'),
      rows: [
        { label: L('Test Date', 'تاريخ الاختبار'), value: e.test_date },
        { label: L('Test Location', 'موقع الاختبار'), value: e.test_location },
        { label: L('Item/Product ID #', 'رقم المنتج/القطعة'), value: e.item_product_id },
        { label: L('Receiving Record #', 'رقم سجل الاستلام'), value: e.receiving_record },
      ],
    },
    {
      title: L('Product Information', 'معلومات المنتج'),
      rows: [
        { label: L('Product', 'المنتج'), value: e.product },
        { label: L('Product#', 'رقم المنتج'), value: e.product_number },
        { label: L('Ref.#', 'المرجع'), value: e.ref_number },
        { label: L('Gross wt.', 'الوزن الإجمالي'), value: e.gross_weight },
        { label: L('Net Wt.', 'الوزن الصافي'), value: e.net_weight },
      ],
    },
    {
      title: L('Result', 'النتيجة'),
      rows: [
        { label: L('Status', 'الحالة'), value: e.result_flags },
        { label: L('Comments', 'ملاحظات'), value: e.comments },
      ],
    },
    {
      title: L('Grader', 'المُقيّم'),
      rows: [
        { label: L('Name', 'الاسم'), value: e.grader_name },
        { label: L('Date', 'التاريخ'), value: e.grader_date },
        { label: L('Signature', 'التوقيع'), value: e.grader_signature },
      ],
    },
    {
      title: L('Technical Interpretation', 'التفسير الفني'),
      rows: [
        { label: L('Interpretation', 'التفسير'), value: e.analytical_interpretation },
        { label: L('Name', 'الاسم'), value: e.analytical_name },
        { label: L('Date', 'التاريخ'), value: e.analytical_date },
        { label: L('Signature', 'التوقيع'), value: e.analytical_signature },
      ],
    },
    {
      title: L('Product Photography', 'تصوير المنتج'),
      rows: [
        { label: L('Image 1#', 'صورة 1'), value: e.image1_ref },
        { label: L('Image 2#', 'صورة 2'), value: e.image2_ref },
        { label: L('Taken by', 'تم التصوير بواسطة'), value: e.image_taken_by },
        { label: L('Date', 'التاريخ'), value: e.image_date },
        { label: L('Signature', 'التوقيع'), value: e.image_signature },
      ],
    },
    {
      title: L('Retaining Information', 'معلومات الحفظ'),
      rows: [
        { label: L('Place', 'المكان'), value: e.retaining_place },
        { label: L('Retained by', 'حُفظ بواسطة'), value: e.retaining_by },
        { label: L('Date', 'التاريخ'), value: e.retaining_date },
        { label: L('Signature', 'التوقيع'), value: e.retaining_signature },
      ],
    },
    {
      title: L('Reporting Information', 'معلومات التقرير'),
      rows: [
        { label: L('Report Done?', 'تم التقرير؟'), value: e.report_done },
        { label: L('Report notes', 'ملاحظات التقرير'), value: e.report_done_notes },
        { label: L('Label Done?', 'تم اللصق؟'), value: e.label_done },
        { label: L('Label notes', 'ملاحظات اللصق'), value: e.label_done_notes },
        { label: L('Report done by', 'أُعد التقرير بواسطة'), value: e.report_done_by },
        { label: L('Date', 'التاريخ'), value: e.report_date },
        { label: L('Signature', 'التوقيع'), value: e.report_signature },
        { label: L('Report#', 'رقم التقرير'), value: e.report_number },
        { label: L('Report # PMR', 'تقرير PMR'), value: e.report_number_pmr },
        { label: L('Checked by', 'راجعه'), value: e.checked_by },
        { label: L('Checked date', 'تاريخ المراجعة'), value: e.checked_date },
        { label: L('Checked signature', 'توقيع المراجعة'), value: e.checked_signature },
      ],
    },
  ]
})
</script>
