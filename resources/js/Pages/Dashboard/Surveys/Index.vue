<template>
  <DashboardLayout :page-title="t.pageTitle">
    <div class="space-y-6" :dir="isAr ? 'rtl' : 'ltr'">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="card in cards" :key="card.label" class="bg-white rounded-lg shadow p-4">
          <p class="text-sm text-gray-500">{{ card.label }}</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ card.value }}</p>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="avg in averages" :key="avg.label">
          <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>{{ avg.label }}</span>
            <span class="font-semibold text-gray-900">{{ avg.value ?? '—' }} / 5</span>
          </div>
          <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-700 rounded-full" :style="{ width: avg.percent + '%' }"></div>
          </div>
        </div>
      </div>

      <form class="bg-white rounded-lg shadow p-4 flex flex-wrap gap-3 items-end" @submit.prevent="applyFilters">
        <div class="flex-1 min-w-[180px]">
          <label class="block text-xs text-gray-500 mb-1">{{ t.search }}</label>
          <input v-model="search" type="search" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" :placeholder="t.searchPlaceholder" />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t.status }}</label>
          <select v-model="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="all">{{ t.all }}</option>
            <option value="completed">{{ t.completed }}</option>
            <option value="pending">{{ t.pending }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t.complaint }}</label>
          <select v-model="complaint" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            <option value="all">{{ t.all }}</option>
            <option value="yes">{{ t.yes }}</option>
            <option value="no">{{ t.no }}</option>
          </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-green-800 text-white rounded-md text-sm font-semibold hover:bg-green-900">
          {{ t.filter }}
        </button>
      </form>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="surveys.data.length === 0" class="px-6 py-12 text-center text-gray-500">
          {{ t.empty }}
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-500">
              <tr>
                <th class="px-4 py-3 text-start font-medium">{{ t.customer }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.request }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.email }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.sentAt }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.status }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.scores }}</th>
                <th class="px-4 py-3 text-start font-medium">{{ t.complaint }}</th>
                <th class="px-4 py-3 text-start font-medium"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="row in surveys.data" :key="row.id" class="hover:bg-gray-50">
                <td class="px-4 py-3">
                  <div class="font-semibold text-gray-900">{{ row.customer_name || '—' }}</div>
                  <div class="text-xs text-gray-500">{{ row.sent_by }}</div>
                </td>
                <td class="px-4 py-3 font-mono text-xs">{{ row.receiving_record_no || '—' }}</td>
                <td class="px-4 py-3">{{ row.recipient_email }}</td>
                <td class="px-4 py-3 whitespace-nowrap">{{ formatWhen(row.sent_at) }}</td>
                <td class="px-4 py-3">
                  <span :class="row.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                    {{ row.status === 'completed' ? t.completed : t.pending }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span v-if="row.status === 'completed'">{{ row.service_quality_rating }} / {{ row.delivery_time_rating }} / {{ row.report_quality_rating }}</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
                <td class="px-4 py-3">
                  <span v-if="row.has_complaint === true" class="text-red-700 font-semibold">{{ t.yes }}</span>
                  <span v-else-if="row.has_complaint === false" class="text-gray-600">{{ t.no }}</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
                <td class="px-4 py-3">
                  <Link :href="$route('dashboard.surveys.show', row.id)" class="text-green-800 font-semibold hover:underline">
                    {{ t.details }}
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="surveys.links && surveys.links.length > 3" class="px-4 py-3 flex flex-wrap gap-1 border-t">
          <template v-for="(link, i) in surveys.links" :key="i">
            <span
              v-if="!link.url"
              class="px-3 py-1 text-sm rounded border bg-gray-50 text-gray-400"
              v-html="link.label"
            />
            <Link
              v-else
              :href="link.url"
              class="px-3 py-1 text-sm rounded border"
              :class="link.active ? 'bg-green-800 text-white border-green-800' : 'bg-white text-gray-700'"
              v-html="link.label"
            />
          </template>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  surveys: { type: Object, required: true },
  filters: { type: Object, required: true },
  stats: { type: Object, required: true },
})

const page = usePage()
const isAr = computed(() => page.props.locale === 'ar')
const search = ref(props.filters.search || '')
const status = ref(props.filters.status || 'all')
const complaint = ref(props.filters.complaint || 'all')

const t = computed(() => {
  if (!isAr.value) {
    return {
      pageTitle: 'Survey Results',
      search: 'Search',
      searchPlaceholder: 'Customer, email, mobile, record no.',
      status: 'Status',
      all: 'All',
      completed: 'Replied',
      pending: 'Waiting',
      complaint: 'Complaint',
      yes: 'Yes',
      no: 'No',
      filter: 'Filter',
      empty: 'No surveys yet.',
      customer: 'Customer',
      request: 'Receiving record',
      email: 'Email',
      sentAt: 'Sent',
      scores: 'Quality / Time / Report',
      details: 'Details',
      sent: 'Sent',
      responseRate: 'Response rate',
      complaints: 'Complaints',
      service: 'Service quality',
      delivery: 'Delivery time',
      report: 'Report quality',
    }
  }
  return {
    pageTitle: 'نتائج الاستبيانات',
    search: 'بحث',
    searchPlaceholder: 'العميل، البريد، الجوال، رقم السجل',
    status: 'الحالة',
    all: 'الكل',
    completed: 'تم الرد',
    pending: 'بانتظار الرد',
    complaint: 'شكوى',
    yes: 'نعم',
    no: 'لا',
    filter: 'تصفية',
    empty: 'لا توجد استبيانات بعد.',
    customer: 'العميل',
    request: 'سجل الاستلام',
    email: 'البريد',
    sentAt: 'تاريخ الإرسال',
    scores: 'الجودة / الوقت / التقرير',
    details: 'التفاصيل',
    sent: 'المُرسل',
    responseRate: 'نسبة الرد',
    complaints: 'الشكاوى',
    service: 'جودة الخدمة',
    delivery: 'وقت التسليم',
    report: 'جودة التقرير',
  }
})

const cards = computed(() => [
  { label: t.value.sent, value: props.stats.sent },
  { label: t.value.completed, value: props.stats.completed },
  { label: t.value.pending, value: props.stats.pending },
  { label: t.value.responseRate, value: `${props.stats.response_rate}%` },
  { label: t.value.complaints, value: props.stats.complaints },
])

function percent(value) {
  if (value == null) return 0
  return Math.max(0, Math.min(100, (Number(value) / 5) * 100))
}

const averages = computed(() => [
  { label: t.value.service, value: props.stats.service_quality, percent: percent(props.stats.service_quality) },
  { label: t.value.delivery, value: props.stats.delivery_time, percent: percent(props.stats.delivery_time) },
  { label: t.value.report, value: props.stats.report_quality, percent: percent(props.stats.report_quality) },
])

function applyFilters() {
  router.get(route('dashboard.surveys.index'), {
    search: search.value,
    status: status.value,
    complaint: complaint.value,
  }, { preserveState: true, replace: true })
}

function formatWhen(value) {
  if (!value) return '—'
  const d = new Date(value.replace(' ', 'T'))
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleString(isAr.value ? 'ar-SA' : 'en-GB', { dateStyle: 'medium', timeStyle: 'short' })
}
</script>
