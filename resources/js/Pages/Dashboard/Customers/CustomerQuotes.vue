<template>
  <DashboardLayout>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <button 
              @click="goBack"
              class="p-2 text-gray-400 hover:text-gray-600 rounded-md"
            >
              <i class="fas fa-arrow-right"></i>
            </button>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">
                {{ __('Customer Quotes') }}
              </h1>
              <p class="text-sm text-gray-500 mt-1">
                {{ __('All quotes for') }} {{ customer.display_name || customer.name || 'N/A' }}
              </p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <button 
              @click="goToArtifacts"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
            >
              <i class="fas fa-gem mr-2"></i>
              {{ __('View Artifacts') }}
            </button>
            <button 
              @click="createNewQuote"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              <i class="fas fa-plus mr-2"></i>
              {{ __('Create Quote') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="px-6 pb-6">
      <!-- Customer Information Card -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Customer Information') }}
          </h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Customer Name') }}
              </label>
              <p class="text-sm text-gray-900 font-medium">
                {{ customer.display_name || customer.name || 'N/A' }}
              </p>
            </div>
            <div v-if="customer.email">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email') }}
              </label>
              <p class="text-sm text-gray-900">
                {{ customer.email }}
              </p>
            </div>
            <div v-if="customer.phone">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Phone') }}
              </label>
              <p class="text-sm text-gray-900">
                {{ customer.phone }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quotes Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
              {{ __('Quotes') }} ({{ quotes.length }})
            </h2>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Quote #') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Issue Date') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Expiry Date') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Status') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Total Amount') }}
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="quotes.length === 0">
                <td colspan="6" class="px-6 py-12 text-center">
                  <div class="text-gray-500">
                    <i class="fas fa-file-invoice text-4xl mb-4"></i>
                    <p class="text-lg font-medium">{{ __('No quotes found') }}</p>
                    <p class="text-sm">{{ __('Create your first quote for this customer') }}</p>
                    <button 
                      @click="createNewQuote"
                      class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                      <i class="fas fa-plus mr-2"></i>
                      {{ __('Create Quote') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-for="quote in quotes" :key="quote.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    #{{ quote.reference || quote.quotation_number || quote.id }}
                  </div>
                  <div class="text-sm text-gray-500">
                    ID: {{ quote.id }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(quote.issue_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(quote.expiry_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(quote.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ translateStatus(quote.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                  {{ formatCurrency(quote.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="viewQuote(quote.id)"
                      class="text-blue-600 hover:text-blue-900 p-1"
                      :title="__('View Quote')"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      @click="editQuote(quote.id)"
                      class="text-gray-600 hover:text-gray-900 p-1"
                      :title="__('Edit Quote')"
                    >
                      <i class="fas fa-edit"></i>
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
    quotes: {
      type: Array,
      default: () => []
    }
  },

  methods: {
    goBack() {
      this.$inertia.visit('/dashboard');
    },

    goToArtifacts() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`);
    },

    createNewQuote() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/create-quote`);
    },

    viewQuote(quoteId) {
      this.$inertia.visit(`/dashboard/quotes/${quoteId}`);
    },


    editQuote(quoteId) {
      // Placeholder for edit functionality
      alert(this.__('Quote editing feature will be implemented soon'));
    },

    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-CA'); // YYYY-MM-DD format
    },

    formatCurrency(amount) {
      if (!amount) return '$0.00';
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'SAR'
      }).format(amount);
    },

    translateStatus(status) {
      const statusTranslations = {
        'Draft': this.__('Draft'),
        'Approved': this.__('Approved'),
        'Pending': this.__('Pending'),
        'Rejected': this.__('Rejected')
      };
      return statusTranslations[status] || status;
    },

    getStatusBadgeClass(status) {
      const statusClasses = {
        'Draft': 'bg-gray-100 text-gray-800',
        'Approved': 'bg-green-100 text-green-800',
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Rejected': 'bg-red-100 text-red-800'
      };
      return statusClasses[status] || 'bg-gray-100 text-gray-800';
    },

    __(key) {
      const translations = {
        en: {
          'Customer Quotes': 'Customer Quotes',
          'All quotes for': 'All quotes for',
          'View Artifacts': 'View Artifacts',
          'Create Quote': 'Create Quote',
          'Customer Information': 'Customer Information',
          'Customer Name': 'Customer Name',
          'Email': 'Email',
          'Phone': 'Phone',
          'Quotes': 'Quotes',
          'Quote #': 'Quote #',
          'Issue Date': 'Issue Date',
          'Expiry Date': 'Expiry Date',
          'Status': 'Status',
          'Total Amount': 'Total Amount',
          'Actions': 'Actions',
          'No quotes found': 'No quotes found',
          'Create your first quote for this customer': 'Create your first quote for this customer',
          'View Quote': 'View Quote',
          'Download PDF': 'Download PDF',
          'Edit Quote': 'Edit Quote',
            'Download failed. Please try again.': 'Download failed. Please try again.',
          'Quote editing feature will be implemented soon': 'Quote editing feature will be implemented soon'
        },
        ar: {
          'Customer Quotes': 'عروض أسعار العميل',
          'All quotes for': 'جميع عروض الأسعار لـ',
          'View Artifacts': 'عرض القطع',
          'Create Quote': 'إنشاء عرض سعر',
          'Customer Information': 'معلومات العميل',
          'Customer Name': 'اسم العميل',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Quotes': 'عروض الأسعار',
          'Quote #': 'رقم عرض السعر',
          'Issue Date': 'تاريخ الإصدار',
          'Expiry Date': 'تاريخ الانتهاء',
          'Status': 'الحالة',
          'Total Amount': 'المبلغ الإجمالي',
          'Actions': 'الإجراءات',
          'No quotes found': 'لم يتم العثور على عروض أسعار',
          'Create your first quote for this customer': 'أنشئ عرض السعر الأول لهذا العميل',
          'View Quote': 'عرض عرض السعر',
          'Download PDF': 'تحميل PDF',
          'Edit Quote': 'تعديل عرض السعر',
            'Download failed. Please try again.': 'فشل التحميل. يرجى المحاولة مرة أخرى.',
          'Quote editing feature will be implemented soon': 'ميزة تعديل عرض السعر ستكون متاحة قريباً'
        }
      };

      const locale = this.$page.props.locale || 'en';
      return translations[locale]?.[key] || key;
    }
  },

  mounted() {
    console.log('CustomerQuotes component mounted with data:', {
      customer: this.customer,
      quotes: this.quotes
    });
  }
}
</script>

