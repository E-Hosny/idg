<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Page Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <div>
             <h1 class="text-3xl font-bold text-gray-900">
               Test Requests | طلبات الاختبار
             </h1>
             <p class="mt-1 text-sm text-gray-600">
               Customer | العميل: {{ customer.name || customer.display_name }}
             </p>
          </div>
          <div class="flex gap-3">
            <button
              @click="createNewRequest"
              class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-base font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200 shadow-md hover:shadow-lg"
            >
              <i class="fas fa-plus-circle mr-2 text-lg"></i>
              <span>New Test Request</span>
              <span class="mx-2">|</span>
              <span>طلب اختبار جديد</span>
            </button>
            <button
              @click="goBack"
              class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-base font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              <span>Back</span>
              <span class="mx-2">|</span>
              <span>رجوع</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Test Requests List -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">
            All Test Requests | كل طلبات الاختبار
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Total | المجموع: {{ testRequests.length }} requests | طلب
          </p>
        </div>

        <div v-if="testRequests.length === 0" class="px-6 py-12 text-center">
          <div class="text-gray-400 mb-4">
            <i class="fas fa-inbox text-6xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">
            No test requests yet | لا توجد طلبات اختبار بعد
          </h3>
          <p class="text-gray-600 mb-6">
            Create your first test request to get started | أنشئ أول طلب اختبار للبدء
          </p>
          <button
            @click="createNewRequest"
            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <i class="fas fa-plus-circle mr-2"></i>
            Create First Request | إنشاء أول طلب
          </button>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Request No | رقم الطلب
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Receiving Record No | رقم سجل الاستلام
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created | تاريخ ووقت الإنشاء
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Received Date | تاريخ الاستلام
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Delivery Date | تاريخ التسليم
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status | الحالة
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Signed Document | المستند الموقع
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions | الإجراءات
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="request in testRequests" :key="request.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #{{ request.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                  {{ request.receiving_record_no }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDateTime(request.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDateTimeOrDate(request.received_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ request.delivery_date ? formatDateTimeOrDate(request.delivery_date) : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(request.status)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ getStatusLabel(request.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span v-if="request.signed_document_path" class="text-green-600 font-semibold">
                    <i class="fas fa-check-circle mr-1"></i>
                    Signed | موقع
                  </span>
                  <span v-else class="text-gray-400">
                    <i class="fas fa-times-circle mr-1"></i>
                    Not Signed | غير موقع
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                  <button
                    @click="viewRequest(request.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    title="View Request | عرض الطلب"
                  >
                    <i class="fas fa-eye mr-1"></i>
                    View | عرض
                  </button>
                  <button
                    @click="downloadPdf(request.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    title="Download PDF | تحميل PDF"
                  >
                    <i class="fas fa-file-pdf mr-1"></i>
                    PDF
                  </button>
                  <button
                    @click="deleteRequest(request.id)"
                    class="inline-flex items-center px-3 py-1.5 bg-red-700 text-white text-xs font-semibold rounded hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500"
                    title="Delete | حذف"
                  >
                    <i class="fas fa-trash mr-1"></i>
                    Delete | حذف
                  </button>
                  <a
                    v-if="request.signed_document_path"
                    :href="`/certificate-file/${request.signed_document_path}`"
                    target="_blank"
                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    title="Download Signed | تحميل الموقع"
                  >
                    <i class="fas fa-download mr-1"></i>
                    Signed | موقع
                  </a>
                  <button
                    type="button"
                    @click="openLabDeliveryModal(request)"
                    :class="labDeliveryButtonClass(request)"
                    :title="request.lab_delivery_signed_document_path ? 'التسليم للمختبر — تم رفع الملف الموقع' : 'التسليم للمختبر'"
                  >
                    <i :class="request.lab_delivery_signed_document_path ? 'fas fa-check-circle' : 'fas fa-flask'" class="mr-1"></i>
                    <span>التسليم للمختبر</span>
                    <span
                      v-if="request.lab_delivery_signed_document_path"
                      class="mr-1 text-[10px] font-bold uppercase tracking-wide opacity-90"
                    >موقع</span>
                  </button>
                  <button
                    type="button"
                    @click="openRedeliveryModal(request)"
                    :class="redeliveryButtonClass(request)"
                    :title="redeliveryStatusTitle(request)"
                  >
                    <i :class="redeliveryRowIcon(request)" class="mr-1"></i>
                    <span>إعادة للاستقبال</span>
                    <span v-if="(request.redeliveries || []).length" class="mr-1 text-[10px] opacity-90">({{ redeliveryLatestRemainingAtCreation(request) }})</span>
                    <span
                      v-if="redeliveryAllBatchesSigned(request)"
                      class="mr-1 text-[10px] font-bold uppercase tracking-wide opacity-90"
                    >موقع</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Lab delivery modal -->
    <div
      v-if="labModalOpen && selectedLabRequest"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      role="dialog"
      aria-modal="true"
      @click.self="closeLabDeliveryModal"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start gap-3">
          <div>
            <h3 class="text-lg font-bold text-gray-900">التسليم للمختبر</h3>
            <p class="text-sm text-gray-600 mt-1">
              {{ selectedLabRequest.receiving_record_no }}
              <span class="text-gray-400">|</span>
              #{{ selectedLabRequest.id }}
            </p>
          </div>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-700 p-1 rounded"
            aria-label="إغلاق"
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
            <span>الملف للطباعة</span>
          </a>

          <div class="rounded-lg border border-dashed border-gray-300 p-4 bg-gray-50/80">
            <p class="text-xs font-medium text-gray-700 mb-2">رفع الملف بعد التوقيع (PDF)</p>
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
                اختيار ملف
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
                رفع الملف
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
            <span>عرض الملف الموقع</span>
          </a>
          <p v-else class="text-center text-xs text-gray-500">لا يوجد ملف موقع بعد — ارفع ملف PDF بعد التوقيع</p>
        </div>
      </div>
    </div>

    <!-- Redelivery: multiple batches per test request -->
    <div
      v-if="redeliveryModalOpen && selectedRedeliveryRow"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      role="dialog"
      aria-modal="true"
      @click.self="closeRedeliveryModal"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-start gap-3 flex-shrink-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900">إعادة التسليم للاستقبال</h3>
            <p class="text-sm text-gray-600 mt-1">
              {{ selectedRedeliveryRow.receiving_record_no }}
              <span class="text-gray-400">|</span>
              #{{ selectedRedeliveryRow.id }}
            </p>
          </div>
          <button type="button" class="text-gray-400 hover:text-gray-700 p-1 rounded" aria-label="إغلاق" @click="closeRedeliveryModal">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        <div class="px-6 py-6 space-y-6 overflow-y-auto flex-1">
          <div class="rounded-lg border border-gray-200 p-4 bg-white">
            <h4 class="text-sm font-bold text-gray-800 mb-3">مستند إعادة تسليم جديد</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">القطع المسلّمة في هذا المستند</label>
                <input v-model.number="newRedeliveryForm.delivered_pieces_count" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المتبقي بعد هذا المستند</label>
                <input v-model.number="newRedeliveryForm.remaining_pieces_count" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
              </div>
            </div>
            <p class="text-xs text-gray-500 mb-3">مثال: أول مستند 7 و 3، ثم مستند ثانٍ للمتبقي 3 و 0.</p>
            <button
              type="button"
              :disabled="redeliveryCreating"
              class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-slate-800 hover:bg-slate-900 disabled:opacity-50"
              @click="submitNewRedeliveryBatch"
            >
              <i v-if="redeliveryCreating" class="fas fa-spinner fa-spin ml-2"></i>
              <i v-else class="fas fa-plus-circle ml-2"></i>
              إنشاء المستند
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
            <h4 class="text-sm font-bold text-gray-800">مستندات إعادة التسليم</h4>
            <div
              v-for="batch in selectedRedeliveryRow.redeliveries"
              :key="batch.id"
              class="rounded-lg border border-gray-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50/50"
            >
              <div class="text-sm text-gray-700">
                <span class="font-mono font-semibold">#{{ batch.id }}</span>
                <span class="mx-2 text-gray-300">|</span>
                مسلّمة: <strong>{{ batch.delivered_pieces_count }}</strong>
                <span class="mx-2 text-gray-300">·</span>
                متبقي: <strong>{{ batch.remaining_pieces_count }}</strong>
                <span v-if="batch.signed_document_path" class="mr-2 text-emerald-600 font-semibold text-xs">(موقع)</span>
              </div>
              <div class="flex flex-wrap gap-2">
                <a
                  :href="`/dashboard/test-requests/${selectedRedeliveryRow.id}/redeliveries/${batch.id}/print`"
                  @click.prevent="openPrintPage(`/dashboard/test-requests/${selectedRedeliveryRow.id}/redeliveries/${batch.id}/print`)"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-700 text-white text-xs font-semibold hover:bg-slate-800"
                >
                  <i class="fas fa-print ml-1"></i>
                  طباعة
                </a>
                <button
                  v-if="!batch.signed_document_path"
                  type="button"
                  :disabled="redeliveryUploading"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 disabled:opacity-50"
                  @click="pickRedeliveryUploadBatch(batch.id)"
                >
                  <i class="fas fa-cloud-upload-alt ml-1"></i>
                  رفع الملف الموثق
                </button>
                <a
                  v-if="batch.signed_document_path"
                  :href="`/certificate-file/${batch.signed_document_path}`"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold hover:bg-violet-700"
                >
                  <i class="fas fa-file-pdf ml-1"></i>
                  عرض
                </a>
              </div>
            </div>
          </div>
          <p v-else class="text-center text-sm text-gray-500">لا توجد مستندات بعد — أدخل الأعداد واضغط إنشاء المستند.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TestRequestsList',
  props: {
    customer: {
      type: Object,
      required: true
    },
    testRequests: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
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
  methods: {
    openPrintPage(url, preOpenedWindow = null) {
      let targetWindow = preOpenedWindow
      if (!targetWindow || targetWindow.closed) {
        targetWindow = window.open('', '_blank')
      }

      if (targetWindow) {
        targetWindow.location.href = url
        return true
      }

      window.location.assign(url)
      return false
    },

    redeliveryBatches(row) {
      return row.redeliveries && row.redeliveries.length ? row.redeliveries : []
    },
    /** Latest batch first — remaining pieces as recorded when that document was created. */
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
    redeliveryButtonClass(request) {
      const base = 'inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded focus:outline-none focus:ring-2 transition-shadow'
      const batches = this.redeliveryBatches(request)
      if (batches.length === 0) {
        return `${base} bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500`
      }
      if (this.redeliveryAllBatchesSigned(request)) {
        return `${base} bg-violet-600 text-white ring-2 ring-violet-300 ring-offset-1 hover:bg-violet-700 focus:ring-violet-400 shadow-sm`
      }
      return `${base} bg-amber-500 text-white ring-2 ring-amber-200 ring-offset-1 hover:bg-amber-600 focus:ring-amber-400`
    },
    redeliveryRowIcon(request) {
      if (this.redeliveryAllBatchesSigned(request)) return 'fas fa-check-circle'
      if (this.redeliveryBatches(request).length) return 'fas fa-exclamation-circle'
      return 'fas fa-undo-alt'
    },
    redeliveryStatusTitle(request) {
      if (this.redeliveryAllBatchesSigned(request)) return 'إعادة التسليم — تم رفع كل الملفات'
      if (this.redeliveryHasUnsignedBatch(request)) return 'يوجد مستندات بانتظار رفع الملف الموقع'
      return 'إعادة التسليم للاستقبال'
    },
    openRedeliveryModal(request) {
      this.selectedRedeliveryRow = { ...request }
      this.newRedeliveryForm = {
        delivered_pieces_count: request.evaluated_pieces_count ?? 0,
        remaining_pieces_count: request.pending_pieces_count ?? 0
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
      const queuedPrintWindow = window.open('', '_blank')
      if (Number.isNaN(d) || Number.isNaN(r) || d < 0 || r < 0) {
        if (queuedPrintWindow && !queuedPrintWindow.closed) {
          queuedPrintWindow.close()
        }
        alert('أدخل أعدادًا صحيحة')
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
          this.openPrintPage(printUrl, queuedPrintWindow)
        } else if (queuedPrintWindow && !queuedPrintWindow.closed) {
          queuedPrintWindow.close()
        }
      } catch (error) {
        if (queuedPrintWindow && !queuedPrintWindow.closed) {
          queuedPrintWindow.close()
        }
        const msg = error?.response?.data?.message || 'فشل إنشاء المستند | Failed to create redelivery document.'
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
        alert('حجم الملف يجب أن يكون أقل من 10 ميجابايت | File size must be less than 10MB.')
        e.target.value = ''
        return
      }
      if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        alert('يُسمح بملفات PDF فقط | Only PDF files are allowed.')
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
          const rows = page?.props?.testRequests || this.testRequests
          const updated = rows.find((x) => x.id === tid)
          if (updated) {
            this.selectedRedeliveryRow = { ...updated }
          }
        },
        onError: (errors) => {
          this.redeliveryUploading = false
          e.target.value = ''
          const msg = errors.signed_document || errors.error || 'فشل الرفع | Upload failed'
          alert(Array.isArray(msg) ? msg[0] : msg)
        },
        onFinish: () => {
          this.redeliveryUploading = false
        }
      })
    },
    labDeliveryButtonClass(request) {
      const base = 'inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded focus:outline-none focus:ring-2 transition-shadow'
      if (request.lab_delivery_signed_document_path) {
        return `${base} bg-emerald-600 text-white ring-2 ring-emerald-300 ring-offset-1 hover:bg-emerald-700 focus:ring-emerald-400 shadow-sm`
      }
      return `${base} bg-teal-600 text-white hover:bg-teal-700 focus:ring-teal-500`
    },
    openLabDeliveryModal(request) {
      this.selectedLabRequest = { ...request }
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
        alert('يرجى اختيار ملف PDF أولاً | Please choose a PDF file first.')
        return
      }
      if (file.size > 10 * 1024 * 1024) {
        alert('حجم الملف يجب أن يكون أقل من 10 ميجابايت | File size must be less than 10MB.')
        return
      }
      if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        alert('يُسمح بملفات PDF فقط | Only PDF files are allowed.')
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
          const rows = page?.props?.testRequests || this.testRequests
          const updated = rows.find((r) => r.id === id)
          if (updated) {
            this.selectedLabRequest = { ...updated }
          }
        },
        onError: (errors) => {
          this.labUploading = false
          const msg = errors.lab_delivery_signed_document || errors.error || 'فشل الرفع | Upload failed'
          alert(Array.isArray(msg) ? msg[0] : msg)
        },
        onFinish: () => {
          this.labUploading = false
        }
      })
    },
    createNewRequest() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-requests/create`)
    },
    viewRequest(requestId) {
      this.$inertia.visit(`/dashboard/test-requests/${requestId}`)
    },
    downloadPdf(requestId) {
      this.openPrintPage(`/dashboard/test-requests/${requestId}/download-pdf`)
    },
    deleteRequest(requestId) {
      if (confirm('Are you sure you want to delete this test request? | هل أنت متأكد من حذف طلب الاختبار هذا؟')) {
        this.$inertia.delete(`/dashboard/test-requests/${requestId}`, {
          onSuccess: () => {
            console.log('Test request deleted successfully')
          },
          onError: (errors) => {
            console.error('Error deleting test request:', errors)
            alert('Failed to delete test request. | فشل حذف طلب الاختبار.')
          }
        })
      }
    },
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`)
    },
    formatDate(date) {
      if (!date) return '-'
      const d = this.parseDateValue(date)
      const day = String(d.getDate()).padStart(2, '0')
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const year = d.getFullYear()
      return `${day}/${month}/${year}`
    },

    /** Date-only fields (Y-m-d) — avoid UTC midnight shifting the calendar day */
    parseDateValue(date) {
      if (typeof date === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(date.trim())) {
        const [y, m, day] = date.trim().split('-').map(Number)
        return new Date(y, m - 1, day)
      }
      return new Date(date)
    },

    /** Full datetime (e.g. created_at); 12-hour clock with AM/PM / ص م */
    formatDateTime(dateString) {
      if (!dateString) return '-'
      const d = new Date(dateString)
      if (Number.isNaN(d.getTime())) return '-'
      const locale = this.$page?.props?.locale === 'ar' ? 'ar-SA' : 'en-GB'
      return d.toLocaleString(locale, {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      })
    },

    /** Date-only column: show calendar date; if value is a full ISO datetime, show date + time */
    formatDateTimeOrDate(value) {
      if (!value) return '-'
      const s = typeof value === 'string' ? value.trim() : ''
      if (/^\d{4}-\d{2}-\d{2}$/.test(s)) {
        return this.formatDate(s)
      }
      return this.formatDateTime(value)
    },
    getStatusClass(status) {
      const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'under_evaluation': 'bg-blue-100 text-blue-800',
        'evaluated': 'bg-purple-100 text-purple-800',
        'certified': 'bg-indigo-100 text-indigo-800',
        'delivered': 'bg-green-100 text-green-800',
        'signed': 'bg-green-100 text-green-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    getStatusLabel(status) {
      const labels = {
        'pending': 'قيد الانتظار | Pending',
        'under_evaluation': 'قيد التقييم | Under Evaluation',
        'evaluated': 'تم التقييم | Evaluated',
        'certified': 'معتمد | Certified',
        'delivered': 'تم التسليم | Delivered',
        'signed': 'موقع | Signed'
      }
      return labels[status] || status
    }
  }
}
</script>

