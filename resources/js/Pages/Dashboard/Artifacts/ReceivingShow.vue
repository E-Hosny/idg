<template>
  <DashboardLayout :pageTitle="__('Items in receipt')">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <Link
            :href="$route('dashboard.artifacts')"
            class="text-sm text-green-700 hover:text-green-900 font-medium inline-flex items-center gap-2 mb-2"
          >
            <span aria-hidden="true">←</span> {{ __('Back to receiving list') }}
          </Link>
          <h2 class="text-2xl font-bold text-gray-800">
            {{ testRequest.receiving_record_no }}
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ __('Customer Code') }}:
            <span class="font-mono font-semibold">{{ formatCustomerCode(testRequest.qoyod_customer_id) }}</span>
            · {{ __('Created At') }}: {{ formatDateTime(testRequest.created_at) }}
          </p>
        </div>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('items') }}
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Filter') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Code') }}</label>
            <input
              v-model="localFilters.code"
              type="text"
              @input="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
              :placeholder="__('Enter code')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
            <select
              v-model="localFilters.status"
              @change="applyFiltersImmediate"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
            >
              <option value="">{{ __('All statuses') }}</option>
              <option value="pending">{{ __('Pending') }} / {{ __('Under Evaluation') }}</option>
              <option value="evaluated">{{ __('Evaluated') }} / {{ __('Certified') }}</option>
            </select>
          </div>
          <div class="flex items-end md:col-span-2">
            <button
              type="button"
              @click="clearFilters"
              class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
            >
              {{ __('Clear Filters') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Items table -->
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
              <th class="px-4 py-2 text-left font-bold">{{ __('Created At') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(artifact, idx) in artifacts.data" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ (artifacts.current_page - 1) * artifacts.per_page + idx + 1 }}</td>
              <td class="px-4 py-2 font-mono text-sm">{{ artifact.artifact_code }}</td>
              <td class="px-4 py-2">{{ getFullType(artifact) }}</td>
              <td class="px-4 py-2">{{ artifact.service }}</td>
              <td class="px-4 py-2">
                <span v-if="artifact.weight">
                  {{ artifact.weight }}
                  <span v-if="artifact.weight_unit" class="text-gray-600 text-sm">{{ __(artifact.weight_unit) }}</span>
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-2">
                <span
                  :class="{
                    'text-yellow-600 font-semibold': artifact.status === 'pending',
                    'text-blue-600 font-semibold': artifact.status === 'under_evaluation',
                    'text-green-600 font-semibold': artifact.status === 'certified' || artifact.status === 'evaluated',
                    'text-red-600 font-semibold': artifact.status === 'rejected',
                  }"
                >
                  {{ __(artifact.status) }}
                </span>
              </td>
              <td class="px-4 py-2 whitespace-nowrap text-sm">{{ formatDateTime(artifact.created_at) }}</td>
              <td class="px-4 py-2">
                <div class="flex flex-wrap gap-2">
                  <button
                    v-if="artifact.status !== 'evaluated' && artifact.status !== 'certified'"
                    type="button"
                    @click="$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluate`)"
                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                  >
                    {{ artifact.status === 'under_evaluation' ? __('Continue') : __('Evaluate') }}
                  </button>
                  <button
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified' || artifact.status === 'under_evaluation'"
                    type="button"
                    @click="viewEvaluation(artifact)"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                  >
                    {{ artifact.status === 'evaluated' || artifact.status === 'certified' ? __('View Report') : __('View Evaluation') }}
                  </button>
                  <button
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified'"
                    type="button"
                    @click="printEvaluation(artifact)"
                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-xs font-semibold"
                    :title="__('Print Report')"
                  >
                    🖨️
                  </button>
                  <button
                    v-if="artifact.status === 'evaluated'"
                    type="button"
                    @click="generateCertificate(artifact)"
                    class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs font-semibold"
                    :title="__('Generate Certificate')"
                  >
                    📜
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!artifacts.data.length">
              <td colspan="8" class="text-center text-gray-400 py-4">{{ __('No items found.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="artifacts.last_page > 1" class="flex justify-between items-center">
        <div class="text-sm text-gray-600">
          {{ __('Showing') }} {{ ((artifacts.current_page - 1) * artifacts.per_page) + 1 }}
          {{ __('to') }} {{ Math.min(artifacts.current_page * artifacts.per_page, artifacts.total) }}
          {{ __('of') }} {{ artifacts.total }} {{ __('items') }}
        </div>
        <div class="flex space-x-2">
          <Link
            v-if="artifacts.prev_page_url"
            :href="artifacts.prev_page_url"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            {{ __('Previous') }}
          </Link>
          <Link
            v-if="artifacts.next_page_url"
            :href="artifacts.next_page_url"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            {{ __('Next') }}
          </Link>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: { DashboardLayout, Link },
  props: {
    testRequest: {
      type: Object,
      required: true
    },
    artifacts: {
      type: Object,
      required: true
    },
    filters: {
      type: Object,
      default: () => ({ code: '', status: '' })
    }
  },

  data() {
    const urlParams = new URLSearchParams(window.location.search)
    return {
      filterTimeout: null,
      localFilters: {
        code: urlParams.get('code') || this.filters.code || '',
        status: urlParams.get('status') || this.filters.status || ''
      }
    }
  },

  methods: {
    formatCustomerCode(id) {
      if (id == null || id === '') return '-'
      return `CUS${String(id).padStart(3, '0')}`
    },

    formatDateTime(dateString) {
      if (!dateString) return '-'
      const d = new Date(dateString)
      const isAr = this.$page.props.locale === 'ar'
      const locale = isAr ? 'ar-SA' : 'en-GB'
      return d.toLocaleString(locale, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      })
    },

    getFullType(artifact) {
      if (!artifact.type) return '-'
      return artifact.subtype ? `${artifact.type} - ${artifact.subtype}` : artifact.type
    },

    __(key) {
      const t = {
        'Items in receipt': 'عناصر سجل الاستلام',
        'Back to receiving list': 'العودة لقائمة سجلات الاستلام',
        'Customer Code': 'كود العميل',
        'Created At': 'تاريخ ووقت الإنشاء',
        'Total': 'المجموع',
        'items': 'عنصر',
        'Filter': 'فلترة',
        'Code': 'الكود',
        'Enter code': 'أدخل الكود',
        'Status': 'الحالة',
        'All statuses': 'كل الحالات',
        'Pending': 'معلق',
        'Under Evaluation': 'قيد التقييم',
        'Evaluated': 'تم التقييم',
        'Certified': 'معتمد',
        'Clear Filters': 'مسح الفلاتر',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'ct': 'قيراط',
        'gm': 'جرام',
        'Actions': 'الإجراءات',
        'No items found.': 'لا توجد عناصر',
        'Showing': 'عرض',
        'to': 'إلى',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
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
        'Generate Certificate': 'إنشاء شهادة'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },

    viewEvaluation(artifact) {
      this.$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluation`)
    },

    printEvaluation(artifact) {
      const url = `/dashboard/artifacts/${artifact.id}/evaluation`
      const printWindow = window.open(url, '_blank')
      if (printWindow) {
        printWindow.addEventListener('load', () => {
          setTimeout(() => printWindow.print(), 500)
        })
      }
    },

    generateCertificate(artifact) {
      this.$inertia.post(`/certificates/${artifact.id}/generate`, {}, {
        onSuccess: () => alert('تم إنشاء الشهادة بنجاح!'),
        onError: (errors) => {
          if (errors.error) alert(errors.error)
          else alert('حدث خطأ أثناء إنشاء الشهادة.')
        }
      })
    },

    applyFilters() {
      clearTimeout(this.filterTimeout)
      this.filterTimeout = setTimeout(() => this.pushFilters(), 400)
    },

    applyFiltersImmediate() {
      this.pushFilters()
    },

    pushFilters() {
      const base = window.location.pathname
      const params = new URLSearchParams()
      if (this.localFilters.code) params.set('code', this.localFilters.code)
      if (this.localFilters.status) params.set('status', this.localFilters.status)
      params.set('page', '1')
      const qs = params.toString()
      this.$inertia.visit(qs ? `${base}?${qs}` : base, { preserveState: true, preserveScroll: true })
    },

    clearFilters() {
      this.localFilters = { code: '', status: '' }
      this.$inertia.visit(window.location.pathname, { preserveState: true, preserveScroll: true })
    }
  }
}
</script>
