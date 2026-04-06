<template>
  <DashboardLayout :pageTitle="__('Items List')">
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ getPageTitle() }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ receivingRecords.total || 0 }} {{ __('receiving records') }}
        </div>
      </div>

      <!-- Filters: receiving record + customer code -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Filter') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Receiving Request No') }}
            </label>
            <input
              v-model="filters.receiving_record_no"
              type="text"
              @input="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
              :placeholder="__('Enter receiving record number')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Customer Code') }}
            </label>
            <input
              v-model="filters.customer_code"
              type="text"
              @input="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 font-mono"
              :placeholder="__('Enter customer code e.g. CUS067')"
            />
          </div>
          <div class="flex items-end">
            <button
              type="button"
              @click="clearFilters"
              class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
            >
              {{ __('Clear Filters') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
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

      <!-- Receiving records table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full bg-white rounded shadow border">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="px-4 py-3 text-center font-bold align-middle">#</th>
              <th class="px-4 py-3 text-center font-bold align-middle">{{ __('Receiving Request No') }}</th>
              <th class="px-4 py-3 text-center font-bold align-middle">{{ __('Customer Code') }}</th>
              <th class="px-4 py-3 text-center font-bold align-middle">{{ __('Created At') }}</th>
              <th class="px-4 py-3 text-center font-bold align-middle">{{ __('Pending pieces') }}</th>
              <th class="px-4 py-3 text-center font-bold align-middle">{{ __('Evaluated pieces') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, idx) in receivingRecords.data"
              :key="row.id"
              class="border-b hover:bg-gray-50 transition"
            >
              <td class="px-4 py-3 text-center align-middle">{{ (receivingRecords.current_page - 1) * receivingRecords.per_page + idx + 1 }}</td>
              <td class="px-4 py-3 text-center align-middle">
                <Link
                  :href="$route('dashboard.artifacts.receiving', row.id)"
                  class="inline-block text-green-700 hover:text-green-900 font-semibold underline underline-offset-2"
                >
                  {{ row.receiving_record_no }}
                </Link>
              </td>
              <td class="px-4 py-3 text-center align-middle font-mono">{{ formatCustomerCode(row.qoyod_customer_id) }}</td>
              <td class="px-4 py-3 text-center align-middle whitespace-nowrap">{{ formatDateTime(row.created_at) }}</td>
              <td class="px-4 py-3 text-center align-middle text-yellow-700 font-semibold">{{ row.pending_pieces_count }}</td>
              <td class="px-4 py-3 text-center align-middle text-green-700 font-semibold">{{ row.evaluated_pieces_count }}</td>
            </tr>
            <tr v-if="!receivingRecords.data.length">
              <td colspan="6" class="text-center text-gray-400 py-4">{{ getNoDataMessage() }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="receivingRecords.last_page > 1" class="flex justify-between items-center mt-6">
        <div class="text-sm text-gray-600">
          {{ __('Showing') }} {{ ((receivingRecords.current_page - 1) * receivingRecords.per_page) + 1 }}
          {{ __('to') }} {{ Math.min(receivingRecords.current_page * receivingRecords.per_page, receivingRecords.total) }}
          {{ __('of') }} {{ receivingRecords.total }} {{ __('receiving records') }}
        </div>
        <div class="flex space-x-2">
          <Link
            v-if="receivingRecords.prev_page_url"
            :href="receivingRecords.prev_page_url"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            {{ __('Previous') }}
          </Link>
          <span
            v-for="page in getPageNumbers()"
            :key="'p-' + page"
            class="px-4 py-2 border rounded-md transition"
            :class="page === receivingRecords.current_page
              ? 'bg-green-600 text-white border-green-600'
              : 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer'"
            @click="page !== '...' && page !== receivingRecords.current_page && goToPage(page)"
          >
            {{ page }}
          </span>
          <Link
            v-if="receivingRecords.next_page_url"
            :href="receivingRecords.next_page_url"
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
    receivingRecords: {
      type: Object,
      required: true
    },
    viewType: {
      type: String,
      default: 'all'
    },
    stats: {
      type: Object,
      default: () => ({
        pending: 0,
        under_evaluation: 0,
        evaluated: 0,
        certified: 0
      })
    },
    filters: {
      type: Object,
      default: () => ({
        receiving_record_no: '',
        customer_code: ''
      })
    }
  },

  data() {
    const urlParams = new URLSearchParams(window.location.search)
    return {
      filterTimeout: null,
      filters: {
        receiving_record_no: urlParams.get('receiving_record_no') || '',
        customer_code: urlParams.get('customer_code') || ''
      }
    }
  },

  computed: {
    pendingCount() {
      return this.stats?.pending || 0
    },
    underEvaluationCount() {
      return this.stats?.under_evaluation || 0
    },
    evaluatedCount() {
      return this.stats?.evaluated || 0
    },
    certifiedCount() {
      return this.stats?.certified || 0
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

    __(key) {
      const t = {
        'Items List': 'قائمة العناصر',
        'Pending Items': 'العناصر المعلقة',
        'receiving records': 'سجلات استلام',
        'Customer Code': 'كود العميل',
        'Created At': 'تاريخ ووقت الإنشاء',
        'Pending pieces': 'عدد القطع المعلقة',
        'Evaluated pieces': 'عدد القطع المقيمة',
        'Receiving Request No': 'رقم طلب الاستلام',
        'Filter': 'فلترة',
        'Enter receiving record number': 'أدخل رقم سجل الاستلام',
        'Enter customer code e.g. CUS067': 'مثال: CUS067 أو رقم العميل فقط',
        'Clear Filters': 'مسح الفلاتر',
        'Total': 'المجموع',
        'Pending': 'معلق',
        'Under Evaluation': 'قيد التقييم',
        'Evaluated': 'تم التقييم',
        'Certified': 'معتمد',
        'Showing': 'عرض',
        'to': 'إلى',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
        'No items found.': 'لا توجد سجلات.',
        'No pending items found.': 'لا توجد سجلات بقطع معلقة.',
        'All Items': 'جميع العناصر'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },

    getPageTitle() {
      if (this.viewType === 'pending') {
        return this.__('Pending Items')
      }
      return this.__('Items List')
    },

    getNoDataMessage() {
      if (this.viewType === 'pending') {
        return this.__('No pending items found.')
      }
      return this.__('No items found.')
    },

    getPageNumbers() {
      const current = this.receivingRecords.current_page
      const last = this.receivingRecords.last_page
      const pages = []
      if (last > 0) pages.push(1)
      const start = Math.max(2, current - 1)
      const end = Math.min(last - 1, current + 1)
      if (start > 2) pages.push('...')
      for (let i = start; i <= end; i++) {
        if (i !== 1 && i !== last) pages.push(i)
      }
      if (end < last - 1) pages.push('...')
      if (last > 1) pages.push(last)
      return pages
    },

    goToPage(page) {
      if (page === '...' || page === this.receivingRecords.current_page) return
      const url = new URL(window.location.href)
      url.searchParams.set('page', page)
      this.$inertia.visit(url.toString())
    },

    applyFilters() {
      clearTimeout(this.filterTimeout)
      this.filterTimeout = setTimeout(() => {
        const url = new URL(window.location.href)
        if (this.filters.receiving_record_no) {
          url.searchParams.set('receiving_record_no', this.filters.receiving_record_no)
        } else {
          url.searchParams.delete('receiving_record_no')
        }
        if (this.filters.customer_code) {
          url.searchParams.set('customer_code', this.filters.customer_code)
        } else {
          url.searchParams.delete('customer_code')
        }
        url.searchParams.set('page', '1')
        this.$inertia.visit(url.toString(), { preserveState: true, preserveScroll: true })
      }, 400)
    },

    clearFilters() {
      this.filters = { receiving_record_no: '', customer_code: '' }
      const url = new URL(window.location.href)
      url.searchParams.delete('receiving_record_no')
      url.searchParams.delete('customer_code')
      url.searchParams.set('page', '1')
      this.$inertia.visit(url.toString(), { preserveState: true, preserveScroll: true })
    }
  }
}
</script>
