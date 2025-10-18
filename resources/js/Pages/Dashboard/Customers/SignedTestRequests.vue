<template>
  <DashboardLayout :pageTitle="__('Signed Test Requests')">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Signed Test Requests for') }}: {{ customer?.name || customer?.display_name }}
            </h2>
            <p class="text-gray-600">
              {{ __('View all signed test requests for this customer') }}
            </p>
          </div>
          <div class="flex space-x-4">
            <button
              @click="goBack"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              {{ __('Back to Artifacts') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-400"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
              {{ $page.props.flash.success }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-400"></i>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">
              {{ $page.props.flash.error }}
            </p>
          </div>
        </div>
      </div>

      <!-- Customer Info Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Customer Information') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Reference Number') }}</label>
            <p class="mt-1 text-sm text-gray-900 font-mono">{{ customer?.id ? formatQoyodReferenceNumber(customer.id) : 'CUS000' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Customer Name') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Organization') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.organization || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Signed Requests Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ __('Signed Test Requests') }} ({{ signedRequests.length }})
          </h3>
        </div>

        <div v-if="signedRequests.length === 0" class="px-6 py-12 text-center">
          <i class="fas fa-file-signature text-gray-400 text-4xl mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Signed Requests Found') }}</h3>
          <p class="text-gray-500 mb-4">{{ __('This customer has no signed test requests yet.') }}</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Receiving Record No') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Received Date') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Delivery Date') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Received By') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Status') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Uploaded Date') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="request in signedRequests" :key="request.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                  {{ request.receiving_record_no }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(request.received_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ request.delivery_date ? formatDate(request.delivery_date) : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ request.received_by || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(request.status)"
                  >
                    {{ getStatusLabel(request.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(request.updated_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button 
                      @click="downloadSignedDocument(request)"
                      class="p-1 rounded-md text-green-600 hover:text-green-900 hover:bg-green-50 transition-colors duration-200"
                      :title="__('Download')"
                    >
                      <i class="fas fa-download"></i>
                    </button>
                    <button 
                      @click="viewRequest(request)"
                      class="p-1 rounded-md text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition-colors duration-200"
                      :title="__('View')"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  components: {
    DashboardLayout
  },
  props: {
    customer: {
      type: Object,
      required: true
    },
    signedRequests: {
      type: Array,
      default: () => []
    }
  },
  mounted() {
    console.log('SignedTestRequests component mounted')
    console.log('Customer data:', this.customer)
    console.log('Signed requests data:', this.signedRequests)
  },
  methods: {
    goBack() {
      if (this.customer?.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`)
      } else {
        this.$inertia.visit('/dashboard/customers')
      }
    },
    
    downloadSignedDocument(request) {
      if (request.signed_document_path) {
        window.location.href = `/storage/${request.signed_document_path}`;
      } else {
        alert(this.__('No signed document available for download.'));
      }
    },
    
    viewRequest(request) {
      if (this.customer?.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-request`)
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()
      return `${day}/${month}/${year}`
    },
    
    formatQoyodReferenceNumber(id) {
      if (!id) return 'CUS000'
      return `CUS${id.toString().padStart(3, '0')}`
    },
    
    getStatusClass(status) {
      const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'under_evaluation': 'bg-blue-100 text-blue-800',
        'evaluated': 'bg-green-100 text-green-800',
        'certified': 'bg-green-100 text-green-800',
        'signed': 'bg-purple-100 text-purple-800',
        'rejected': 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    
    getStatusLabel(status) {
      const labels = {
        'pending': this.__('Pending'),
        'under_evaluation': this.__('Under Evaluation'),
        'evaluated': this.__('Evaluated'),
        'certified': this.__('Certified'),
        'signed': this.__('Signed'),
        'rejected': this.__('Rejected')
      }
      return labels[status] || status
    },
    
    __(key) {
      const translations = {
        en: {
          'Signed Test Requests': 'Signed Test Requests',
          'Signed Test Requests for': 'Signed Test Requests for',
          'View all signed test requests for this customer': 'View all signed test requests for this customer',
          'Back to Artifacts': 'Back to Artifacts',
          'Customer Information': 'Customer Information',
          'Reference Number': 'Reference Number',
          'Customer Name': 'Customer Name',
          'Organization': 'Organization',
          'No Signed Requests Found': 'No Signed Requests Found',
          'This customer has no signed test requests yet.': 'This customer has no signed test requests yet.',
          'Receiving Record No': 'Receiving Record No',
          'Received Date': 'Received Date',
          'Delivery Date': 'Delivery Date',
          'Received By': 'Received By',
          'Status': 'Status',
          'Uploaded Date': 'Uploaded Date',
          'Actions': 'Actions',
          'Download': 'Download',
          'View': 'View',
          'No signed document available for download.': 'No signed document available for download.',
          'Pending': 'Pending',
          'Under Evaluation': 'Under Evaluation',
          'Evaluated': 'Evaluated',
          'Certified': 'Certified',
          'Signed': 'Signed',
          'Rejected': 'Rejected'
        },
        ar: {
          'Signed Test Requests': 'طلبات الاختبار الموقعة',
          'Signed Test Requests for': 'طلبات الاختبار الموقعة لـ',
          'View all signed test requests for this customer': 'عرض جميع طلبات الاختبار الموقعة لهذا العميل',
          'Back to Artifacts': 'العودة للقطع',
          'Customer Information': 'معلومات العميل',
          'Reference Number': 'الرقم المرجعي',
          'Customer Name': 'اسم العميل',
          'Organization': 'المنشأة',
          'No Signed Requests Found': 'لا توجد طلبات موقعة',
          'This customer has no signed test requests yet.': 'هذا العميل لا يملك طلبات اختبار موقعة بعد.',
          'Receiving Record No': 'رقم سجل الاستلام',
          'Received Date': 'تاريخ الاستلام',
          'Delivery Date': 'تاريخ التسليم',
          'Received By': 'تم الاستلام بواسطة',
          'Status': 'الحالة',
          'Uploaded Date': 'تاريخ الرفع',
          'Actions': 'الإجراءات',
          'Download': 'تحميل',
          'View': 'عرض',
          'No signed document available for download.': 'لا يوجد مستند موقع متاح للتحميل.',
          'Pending': 'قيد الانتظار',
          'Under Evaluation': 'قيد التقييم',
          'Evaluated': 'تم التقييم',
          'Certified': 'معتمد',
          'Signed': 'موقع',
          'Rejected': 'مرفوض'
        }
      }
      
      const locale = this.$page.props.locale || 'en'
      return translations[locale]?.[key] || key
    }
  }
}
</script>

