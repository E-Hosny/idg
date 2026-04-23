<template>
  <DashboardLayout :pageTitle="__('Items List')">
    <div class="max-w-7xl mx-auto">
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
              <th class="px-4 py-3 text-center font-bold align-middle whitespace-nowrap">{{ __('Lab delivery') }}</th>
              <th class="px-4 py-3 text-center font-bold align-middle whitespace-nowrap">{{ __('Redelivery to reception') }}</th>
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
              <td class="px-4 py-3 text-center align-middle">
                <button
                  type="button"
                  @click.stop="openLabDeliveryModal(row)"
                  :class="labDeliveryButtonClass(row)"
                  :title="row.lab_delivery_signed_document_path ? __('Lab delivery signed hint') : __('Lab delivery')"
                >
                  <i :class="row.lab_delivery_signed_document_path ? 'fas fa-check-circle' : 'fas fa-flask'" class="mr-1"></i>
                  <span>{{ __('Lab delivery') }}</span>
                  <span
                    v-if="row.lab_delivery_signed_document_path"
                    class="mr-1 text-[10px] font-bold uppercase tracking-wide opacity-90"
                  >{{ __('Signed short') }}</span>
                </button>
              </td>
              <td class="px-4 py-3 text-center align-middle">
                <button
                  type="button"
                  @click.stop="openRedeliveryModal(row)"
                  :class="redeliveryButtonClass(row)"
                  :title="redeliveryStatusTitle(row)"
                >
                  <i :class="redeliveryRowIcon(row)" class="mr-1"></i>
                  <span>{{ __('Redelivery short') }}</span>
                  <span v-if="(row.redeliveries || []).length" class="mr-1 text-[10px] opacity-90">({{ redeliveryLatestRemainingAtCreation(row) }})</span>
                  <span
                    v-if="redeliveryAllBatchesSigned(row)"
                    class="mr-1 text-[10px] font-bold uppercase tracking-wide opacity-90"
                  >{{ __('Signed short') }}</span>
                </button>
              </td>
            </tr>
            <tr v-if="!receivingRecords.data.length">
              <td colspan="8" class="text-center text-gray-400 py-4">{{ getNoDataMessage() }}</td>
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

    <!-- Lab delivery (same flow as customer test-requests list) -->
    <div
      v-if="labModalOpen && selectedLabRequest"
      class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50"
      role="dialog"
      aria-modal="true"
      @click.self="closeLabDeliveryModal"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start gap-3">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ __('Lab delivery') }}</h3>
            <p class="text-sm text-gray-600 mt-1">
              {{ selectedLabRequest.receiving_record_no }}
              <span class="text-gray-400">|</span>
              #{{ selectedLabRequest.id }}
            </p>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-700 p-1 rounded"
            :aria-label="__('Close')"
            @click="closeLabDeliveryModal"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        <div class="px-6 py-6 space-y-5">
          <a
            :href="`/dashboard/test-requests/${selectedLabRequest.id}/print-lab`"
            @click.prevent="openPrintPage(`/dashboard/test-requests/${selectedLabRequest.id}/print-lab`)"
            class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900 transition-colors"
          >
            <i class="fas fa-print"></i>
            <span>{{ __('Print file') }}</span>
          </a>

          <div class="rounded-lg border border-dashed border-gray-300 p-4 bg-gray-50/80">
            <p class="text-xs font-medium text-gray-700 mb-2">{{ __('Upload signed PDF') }}</p>
            <input
              ref="labDeliveryFileInput"
              type="file"
              accept="application/pdf,.pdf"
              class="hidden"
              @change="onLabDeliveryFileChange"
            >
            <div class="flex flex-wrap gap-2 items-center">
              <button
                type="button"
                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 hover:bg-gray-50"
                @click="$refs.labDeliveryFileInput && $refs.labDeliveryFileInput.click()"
              >
                <i class="fas fa-folder-open ml-2 text-gray-500"></i>
                {{ __('Choose file') }}
              </button>
              <span v-if="labPendingFileName" class="text-xs text-gray-600 truncate max-w-[180px]" :title="labPendingFileName">{{ labPendingFileName }}</span>
              <button
                type="button"
                :disabled="!labPendingFileName || labUploading"
                class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                @click="submitLabDeliveryUpload"
              >
                <i v-if="labUploading" class="fas fa-spinner fa-spin ml-2"></i>
                <i v-else class="fas fa-cloud-upload-alt ml-2"></i>
                {{ __('Upload file') }}
              </button>
            </div>
          </div>

          <a
            v-if="selectedLabRequest.lab_delivery_signed_document_path"
            :href="`/certificate-file/${selectedLabRequest.lab_delivery_signed_document_path}`"
            target="_blank"
            rel="noopener"
            class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors"
          >
            <i class="fas fa-file-pdf"></i>
            <span>{{ __('View signed file') }}</span>
          </a>
          <p v-else class="text-center text-xs text-gray-500">{{ __('No signed file yet') }}</p>
        </div>
      </div>
    </div>

    <!-- Redelivery from lab → reception (multiple batches per test request) -->
    <div
      v-if="redeliveryModalOpen && selectedRedeliveryRow"
      class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50"
      role="dialog"
      aria-modal="true"
      @click.self="closeRedeliveryModal"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start gap-3 flex-shrink-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ __('Redelivery to reception') }}</h3>
            <p class="text-sm text-gray-600 mt-1">
              {{ selectedRedeliveryRow.receiving_record_no }}
              <span class="text-gray-400">|</span>
              #{{ selectedRedeliveryRow.id }}
            </p>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-700 p-1 rounded"
            :aria-label="__('Close')"
            @click="closeRedeliveryModal"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        <div class="px-6 py-6 space-y-6 overflow-y-auto flex-1">
          <!-- New batch: user enters counts for this document (e.g. 7 delivered, 3 remaining — later another doc for the 3) -->
          <div class="rounded-lg border border-gray-200 p-4 bg-white">
            <h4 class="text-sm font-bold text-gray-800 mb-3">{{ __('New redelivery document') }}</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Delivered pieces label') }}</label>
                <input
                  v-model.number="newRedeliveryForm.delivered_pieces_count"
                  type="number"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                >
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Remaining pieces label') }}</label>
                <input
                  v-model.number="newRedeliveryForm.remaining_pieces_count"
                  type="number"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                >
              </div>
            </div>
            <p class="text-xs text-gray-500 mb-3">{{ __('Redelivery counts hint') }}</p>
            <button
              type="button"
              :disabled="redeliveryCreating"
              class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-800 hover:bg-slate-900 disabled:opacity-50"
              @click="submitNewRedeliveryBatch"
            >
              <i v-if="redeliveryCreating" class="fas fa-spinner fa-spin ml-2"></i>
              <i v-else class="fas fa-plus-circle ml-2"></i>
              {{ __('Create then print from list') }}
            </button>
          </div>

          <input
            ref="redeliveryBatchFileInput"
            type="file"
            accept="application/pdf,.pdf"
            class="hidden"
            @change="onRedeliveryBatchFileChange"
          >

          <div v-if="(selectedRedeliveryRow.redeliveries || []).length" class="space-y-3">
            <h4 class="text-sm font-bold text-gray-800">{{ __('Redelivery batches list') }}</h4>
            <div
              v-for="batch in selectedRedeliveryRow.redeliveries"
              :key="batch.id"
              class="rounded-lg border border-gray-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50/50"
            >
              <div class="text-sm text-gray-700">
                <span class="font-mono font-semibold">#{{ batch.id }}</span>
                <span class="mx-2 text-gray-300">|</span>
                {{ __('Delivered pieces label') }}: <strong>{{ batch.delivered_pieces_count }}</strong>
                <span class="mx-2 text-gray-300">·</span>
                {{ __('Remaining pieces label') }}: <strong>{{ batch.remaining_pieces_count }}</strong>
                <span v-if="batch.signed_document_path" class="mr-2 text-emerald-600 font-semibold text-xs">({{ __('Signed short') }})</span>
              </div>
              <div class="flex flex-wrap gap-2">
                <a
                  :href="`/dashboard/test-requests/${selectedRedeliveryRow.id}/redeliveries/${batch.id}/print`"
                  @click.prevent="openPrintPage(`/dashboard/test-requests/${selectedRedeliveryRow.id}/redeliveries/${batch.id}/print`)"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-700 text-white text-xs font-semibold hover:bg-slate-800"
                >
                  <i class="fas fa-print ml-1"></i>
                  {{ __('Print file') }}
                </a>
                <button
                  v-if="!batch.signed_document_path"
                  type="button"
                  :disabled="redeliveryUploading"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 disabled:opacity-50"
                  @click="pickRedeliveryUploadBatch(batch.id)"
                >
                  <i class="fas fa-cloud-upload-alt ml-1"></i>
                  {{ __('Upload documented file') }}
                </button>
                <a
                  v-if="batch.signed_document_path"
                  :href="`/certificate-file/${batch.signed_document_path}`"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold hover:bg-violet-700"
                >
                  <i class="fas fa-file-pdf ml-1"></i>
                  {{ __('View signed file') }}
                </a>
              </div>
            </div>
          </div>
          <p v-else class="text-center text-sm text-gray-500">{{ __('No redelivery batches yet') }}</p>
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
      },
      labModalOpen: false,
      selectedLabRequest: null,
      labUploading: false,
      labPendingFileName: '',
      redeliveryModalOpen: false,
      selectedRedeliveryRow: null,
      newRedeliveryForm: {
        delivered_pieces_count: 0,
        remaining_pieces_count: 0
      },
      redeliveryCreating: false,
      redeliveryUploading: false,
      redeliveryUploadTargetId: null
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
    openPrintPage(url, preOpenedWindow = null) {
      if (preOpenedWindow && !preOpenedWindow.closed) {
        preOpenedWindow.location.href = url
        return true
      }
      // Open print page in the same tab to avoid popup blockers on restricted devices.
      window.location.href = url
      return false
    },

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
        'All Items': 'جميع العناصر',
        'Lab delivery': 'التسليم للمختبر',
        'Lab delivery signed hint': 'التسليم للمختبر — تم رفع الملف الموقع',
        'Signed short': 'موقع',
        'Close': 'إغلاق',
        'Print file': 'الملف للطباعة',
        'Upload signed PDF': 'رفع الملف بعد التوقيع (PDF)',
        'Choose file': 'اختيار ملف',
        'Upload file': 'رفع الملف',
        'Upload documented file': 'رفع الملف الموثق',
        'View signed file': 'عرض الملف الموقع',
        'No signed file yet': 'لا يوجد ملف موقع بعد — ارفع ملف PDF بعد التوقيع',
        'Redelivery to reception': 'إعادة التسليم للاستقبال',
        'Redelivery short': 'إعادة للاستقبال',
        'Redelivery signed hint': 'إعادة التسليم — تم رفع الملف الموقع',
        'New redelivery document': 'مستند إعادة تسليم جديد',
        'Delivered pieces label': 'القطع المسلّمة في هذا المستند',
        'Remaining pieces label': 'المتبقي بعد هذا المستند',
        'Redelivery counts hint': 'مثال: أول مستند 7 و 3، ثم مستند ثانٍ للمتبقي 3 و 0.',
        'Create then print from list': 'إنشاء المستند',
        'Redelivery batches list': 'مستندات إعادة التسليم',
        'No redelivery batches yet': 'لا توجد مستندات بعد — أدخل الأعداد واضغط إنشاء المستند.',
        'Redelivery pending upload hint': 'يوجد مستندات بانتظار رفع الملف الموقع'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },

    redeliveryBatches(row) {
      return row.redeliveries && row.redeliveries.length ? row.redeliveries : []
    },
    /** Latest batch first — value stored on document creation, not live inventory. */
    redeliveryLatestRemainingAtCreation(row) {
      const b = this.redeliveryBatches(row)
      if (!b.length) return ''
      const n = b[0].remaining_pieces_count
      return n == null ? '' : n
    },
    redeliveryAllBatchesSigned(row) {
      const b = this.redeliveryBatches(row)
      return b.length > 0 && b.every((x) => x.signed_document_path)
    },
    redeliveryHasUnsignedBatch(row) {
      return this.redeliveryBatches(row).some((x) => !x.signed_document_path)
    },
    redeliveryButtonClass(row) {
      const base = 'inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded focus:outline-none focus:ring-2 transition-shadow'
      const batches = this.redeliveryBatches(row)
      if (batches.length === 0) {
        return `${base} bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500`
      }
      if (this.redeliveryAllBatchesSigned(row)) {
        return `${base} bg-violet-600 text-white ring-2 ring-violet-300 ring-offset-1 hover:bg-violet-700 focus:ring-violet-400 shadow-sm`
      }
      return `${base} bg-amber-500 text-white ring-2 ring-amber-200 ring-offset-1 hover:bg-amber-600 focus:ring-amber-400`
    },
    redeliveryRowIcon(row) {
      if (this.redeliveryAllBatchesSigned(row)) return 'fas fa-check-circle'
      if (this.redeliveryBatches(row).length) return 'fas fa-exclamation-circle'
      return 'fas fa-undo-alt'
    },
    redeliveryStatusTitle(row) {
      if (this.redeliveryAllBatchesSigned(row)) return this.__('Redelivery signed hint')
      if (this.redeliveryHasUnsignedBatch(row)) return this.__('Redelivery pending upload hint')
      return this.__('Redelivery to reception')
    },
    openRedeliveryModal(row) {
      this.selectedRedeliveryRow = { ...row }
      this.newRedeliveryForm = {
        delivered_pieces_count: row.evaluated_pieces_count ?? 0,
        remaining_pieces_count: row.pending_pieces_count ?? 0
      }
      this.redeliveryUploadTargetId = null
      this.redeliveryModalOpen = true
      this.$nextTick(() => {
        if (this.$refs.redeliveryBatchFileInput) {
          this.$refs.redeliveryBatchFileInput.value = ''
        }
      })
    },
    closeRedeliveryModal() {
      this.redeliveryModalOpen = false
      this.selectedRedeliveryRow = null
      this.redeliveryCreating = false
      this.redeliveryUploading = false
      this.redeliveryUploadTargetId = null
      if (this.$refs.redeliveryBatchFileInput) {
        this.$refs.redeliveryBatchFileInput.value = ''
      }
    },
    async submitNewRedeliveryBatch() {
      const tid = this.selectedRedeliveryRow.id
      const d = Number(this.newRedeliveryForm.delivered_pieces_count)
      const r = Number(this.newRedeliveryForm.remaining_pieces_count)
      if (Number.isNaN(d) || Number.isNaN(r) || d < 0 || r < 0) {
        alert(this.$page.props.locale === 'ar' ? 'أدخل أعدادًا صحيحة' : 'Enter valid counts.')
        return
      }
      this.redeliveryCreating = true
      try {
        const res = await window.axios.post(
          `/dashboard/test-requests/${tid}/redeliveries`,
          {
            delivered_pieces_count: d,
            remaining_pieces_count: r
          },
          {
            headers: {
              Accept: 'application/json'
            }
          }
        )

        const redelivery = res?.data?.redelivery
        const printUrl = res?.data?.print_url || (redelivery?.id ? `/dashboard/test-requests/${tid}/redeliveries/${redelivery.id}/print` : null)

        if (redelivery && this.selectedRedeliveryRow) {
          const existing = this.selectedRedeliveryRow.redeliveries || []
          this.selectedRedeliveryRow = {
            ...this.selectedRedeliveryRow,
            redeliveries: [redelivery, ...existing]
          }
        }

        if (printUrl) {
          this.openPrintPage(printUrl)
        }
      } catch (error) {
        const msg = error?.response?.data?.message || (this.$page.props.locale === 'ar' ? 'فشل إنشاء المستند' : 'Failed to create redelivery document.')
        alert(msg)
      } finally {
        this.redeliveryCreating = false
      }
    },
    pickRedeliveryUploadBatch(batchId) {
      this.redeliveryUploadTargetId = batchId
      this.$nextTick(() => {
        if (this.$refs.redeliveryBatchFileInput) {
          this.$refs.redeliveryBatchFileInput.value = ''
          this.$refs.redeliveryBatchFileInput.click()
        }
      })
    },
    onRedeliveryBatchFileChange(e) {
      const file = e.target.files && e.target.files[0]
      const batchId = this.redeliveryUploadTargetId
      const tid = this.selectedRedeliveryRow && this.selectedRedeliveryRow.id
      if (!file || !batchId || !tid) {
        if (e.target) e.target.value = ''
        return
      }
      if (file.size > 10 * 1024 * 1024) {
        alert(this.$page.props.locale === 'ar' ? 'حجم الملف يجب أن يكون أقل من 10 ميجابايت' : 'File size must be less than 10MB.')
        e.target.value = ''
        return
      }
      if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        alert(this.$page.props.locale === 'ar' ? 'يُسمح بملفات PDF فقط' : 'Only PDF files are allowed.')
        e.target.value = ''
        return
      }
      const formData = new FormData()
      formData.append('signed_document', file)
      this.redeliveryUploading = true
      this.$inertia.post(`/dashboard/test-requests/${tid}/redeliveries/${batchId}/upload-signed`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (page) => {
          this.redeliveryUploading = false
          e.target.value = ''
          const rows = page?.props?.receivingRecords?.data || this.receivingRecords.data
          const updated = rows.find((x) => x.id === tid)
          if (updated) {
            this.selectedRedeliveryRow = { ...updated }
          }
        },
        onError: (errors) => {
          this.redeliveryUploading = false
          e.target.value = ''
          const msg = errors.signed_document || errors.error || (this.$page.props.locale === 'ar' ? 'فشل الرفع' : 'Upload failed')
          alert(Array.isArray(msg) ? msg[0] : msg)
        },
        onFinish: () => {
          this.redeliveryUploading = false
        }
      })
    },

    labDeliveryButtonClass(row) {
      const base = 'inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded focus:outline-none focus:ring-2 transition-shadow'
      if (row.lab_delivery_signed_document_path) {
        return `${base} bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 hover:bg-emerald-700 focus:ring-emerald-400 shadow-sm`
      }
      return `${base} bg-teal-600 text-white hover:bg-teal-700 focus:ring-teal-500`
    },
    openLabDeliveryModal(row) {
      this.selectedLabRequest = { ...row }
      this.labPendingFileName = ''
      this.labModalOpen = true
      this.$nextTick(() => {
        if (this.$refs.labDeliveryFileInput) {
          this.$refs.labDeliveryFileInput.value = ''
        }
      })
    },
    closeLabDeliveryModal() {
      this.labModalOpen = false
      this.selectedLabRequest = null
      this.labPendingFileName = ''
      this.labUploading = false
      if (this.$refs.labDeliveryFileInput) {
        this.$refs.labDeliveryFileInput.value = ''
      }
    },
    onLabDeliveryFileChange(e) {
      const f = e.target.files && e.target.files[0]
      this.labPendingFileName = f ? f.name : ''
    },
    submitLabDeliveryUpload() {
      const input = this.$refs.labDeliveryFileInput
      const file = input && input.files && input.files[0]
      if (!file) {
        alert(this.$page.props.locale === 'ar' ? 'يرجى اختيار ملف PDF أولاً' : 'Please choose a PDF file first.')
        return
      }
      if (file.size > 10 * 1024 * 1024) {
        alert(this.$page.props.locale === 'ar' ? 'حجم الملف يجب أن يكون أقل من 10 ميجابايت' : 'File size must be less than 10MB.')
        return
      }
      if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        alert(this.$page.props.locale === 'ar' ? 'يُسمح بملفات PDF فقط' : 'Only PDF files are allowed.')
        return
      }
      const id = this.selectedLabRequest.id
      const formData = new FormData()
      formData.append('lab_delivery_signed_document', file)
      this.labUploading = true
      this.$inertia.post(`/dashboard/test-requests/${id}/upload-lab-delivery-signed`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (page) => {
          this.labUploading = false
          this.labPendingFileName = ''
          if (this.$refs.labDeliveryFileInput) {
            this.$refs.labDeliveryFileInput.value = ''
          }
          const rec = page?.props?.receivingRecords
          const rows = rec?.data || this.receivingRecords.data
          const updated = rows.find((r) => r.id === id)
          if (updated) {
            this.selectedLabRequest = { ...updated }
          }
        },
        onError: (errors) => {
          this.labUploading = false
          const msg = errors.lab_delivery_signed_document || errors.error || (this.$page.props.locale === 'ar' ? 'فشل الرفع' : 'Upload failed')
          alert(Array.isArray(msg) ? msg[0] : msg)
        },
        onFinish: () => {
          this.labUploading = false
        }
      })
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
