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
                  {{ formatDate(request.received_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ request.delivery_date ? formatDate(request.delivery_date) : '-' }}
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
                  <a
                    v-if="request.signed_document_path"
                    :href="`/storage/${request.signed_document_path}`"
                    target="_blank"
                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                    title="Download Signed | تحميل الموقع"
                  >
                    <i class="fas fa-download mr-1"></i>
                    Signed | موقع
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
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
  methods: {
    createNewRequest() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-requests/create`)
    },
    viewRequest(requestId) {
      this.$inertia.visit(`/dashboard/test-requests/${requestId}`)
    },
    downloadPdf(requestId) {
      window.open(`/dashboard/test-requests/${requestId}/download-pdf`, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes')
    },
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`)
    },
    formatDate(date) {
      if (!date) return '-'
      const d = new Date(date)
      const day = String(d.getDate()).padStart(2, '0')
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const year = d.getFullYear()
      return `${day}/${month}/${year}`
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

