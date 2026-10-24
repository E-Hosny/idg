<template>
  <DashboardLayout :page-title="t.pageTitle">
    <div class="space-y-6 max-w-4xl" :dir="isAr ? 'rtl' : 'ltr'">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <Link :href="$route('dashboard.surveys.index')" class="text-sm text-green-800 font-semibold hover:underline">
          {{ t.back }}
        </Link>
        <Link
          v-if="survey.qoyod_customer_id"
          :href="`/dashboard/customers/${survey.qoyod_customer_id}/test-requests`"
          class="text-sm text-gray-600 hover:underline"
        >
          {{ t.requests }}
        </Link>
      </div>

      <section class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ survey.customer_name || '—' }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ survey.recipient_email }}</p>
          </div>
          <span :class="survey.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" class="px-3 py-1 rounded-full text-sm font-semibold">
            {{ survey.status === 'completed' ? t.completed : t.pending }}
          </span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 text-sm">
          <div><dt class="text-gray-500">{{ t.record }}</dt><dd class="font-mono font-semibold">{{ survey.receiving_record_no || '—' }}</dd></div>
          <div><dt class="text-gray-500">{{ t.sentBy }}</dt><dd>{{ survey.sent_by || '—' }}</dd></div>
          <div><dt class="text-gray-500">{{ t.sentAt }}</dt><dd>{{ formatWhen(survey.sent_at) }}</dd></div>
          <div><dt class="text-gray-500">{{ t.repliedAt }}</dt><dd>{{ formatWhen(survey.completed_at) }}</dd></div>
          <div><dt class="text-gray-500">{{ t.language }}</dt><dd>{{ survey.language === 'en' ? 'English' : survey.language === 'ar' ? 'العربية' : '—' }}</dd></div>
          <div><dt class="text-gray-500">{{ t.mobile }}</dt><dd>{{ survey.mobile || survey.customer_phone || '—' }}</dd></div>
        </dl>
      </section>

      <section v-if="survey.status === 'completed'" class="space-y-4">
        <div v-for="item in ratings" :key="item.label" class="bg-white rounded-lg shadow p-5">
          <div class="flex justify-between gap-3 text-sm mb-2">
            <span class="font-semibold text-gray-800">{{ item.label }}</span>
            <span class="text-green-800 font-bold">{{ item.value }} / 5</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-700" :style="{ width: (item.value / 5) * 100 + '%' }"></div>
          </div>
          <p class="text-xs text-gray-500 mt-2">{{ scaleLabel(item.value) }}</p>
        </div>

        <section class="bg-white rounded-lg shadow p-6 space-y-3">
          <div>
            <p class="text-sm text-gray-500">{{ t.signer }}</p>
            <p class="font-semibold">{{ survey.signer_name || '—' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">{{ t.complaint }}</p>
            <p :class="survey.has_complaint ? 'text-red-700 font-bold' : 'font-semibold'">
              {{ survey.has_complaint ? t.yes : t.no }}
            </p>
          </div>
          <div v-if="survey.has_complaint">
            <p class="text-sm text-gray-500">{{ t.complaintDetails }}</p>
            <p class="whitespace-pre-wrap leading-7">{{ survey.complaint_details }}</p>
          </div>
        </section>
      </section>

      <section v-else class="bg-white rounded-lg shadow p-6 text-gray-600">
        {{ t.waiting }}
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  survey: { type: Object, required: true },
})

const page = usePage()
const isAr = computed(() => page.props.locale === 'ar')

const t = computed(() => isAr.value ? {
  pageTitle: 'تفاصيل الاستبيان',
  back: 'كل النتائج',
  requests: 'طلبات العميل',
  completed: 'تم الرد',
  pending: 'بانتظار الرد',
  record: 'سجل الاستلام',
  sentBy: 'أُرسل بواسطة',
  sentAt: 'تاريخ الإرسال',
  repliedAt: 'تاريخ الرد',
  language: 'لغة الاستبيان',
  mobile: 'الجوال',
  signer: 'الاسم / التوقيع',
  complaint: 'هل توجد شكوى؟',
  yes: 'نعم',
  no: 'لا',
  complaintDetails: 'تفاصيل الشكوى',
  waiting: 'تم إرسال الرابط ولم يصل رد العميل بعد.',
  service: 'جودة الخدمة',
  delivery: 'الالتزام بوقت تسليم التقارير',
  report: 'وضوح وجودة تقرير الاختبار',
  scale: ['', 'غير راضٍ إطلاقاً', 'غير راضٍ', 'محايد', 'راضٍ', 'راضٍ جداً'],
} : {
  pageTitle: 'Survey details',
  back: 'All results',
  requests: 'Customer requests',
  completed: 'Replied',
  pending: 'Waiting',
  record: 'Receiving record',
  sentBy: 'Sent by',
  sentAt: 'Sent at',
  repliedAt: 'Replied at',
  language: 'Survey language',
  mobile: 'Mobile',
  signer: 'Name / signature',
  complaint: 'Complaint?',
  yes: 'Yes',
  no: 'No',
  complaintDetails: 'Complaint details',
  waiting: 'The link was sent and the customer has not replied yet.',
  service: 'Service quality',
  delivery: 'Report delivery time',
  report: 'Report clarity and quality',
  scale: ['', 'Very dissatisfied', 'Dissatisfied', 'Neutral', 'Satisfied', 'Very satisfied'],
})

const ratings = computed(() => [
  { label: t.value.service, value: props.survey.service_quality_rating },
  { label: t.value.delivery, value: props.survey.delivery_time_rating },
  { label: t.value.report, value: props.survey.report_quality_rating },
])

function scaleLabel(value) {
  return t.value.scale[value] || ''
}

function formatWhen(value) {
  if (!value) return '—'
  const d = new Date(String(value).replace(' ', 'T'))
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleString(isAr.value ? 'ar-SA' : 'en-GB', { dateStyle: 'medium', timeStyle: 'short' })
}
</script>
