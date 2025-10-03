<template>
  <DashboardLayout :pageTitle="__('Customer Invoices')">
    <div class="max-w-7xl mx-auto px-4 min-h-screen">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Invoices for') }}: {{ customer?.name || customer?. display_name }}
            </h2>
            <p class="text-gray-600">
              {{ __('View all invoices created for this customer') }}
            </p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="createInvoice"
              class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
            >
              <i class="fas fa-plus mr-2"></i>
              {{ __('Create Invoice') }}
            </button>
            <button
              @click="goBack"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              {{ __('Back to Customer') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Customer Info Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Customer Information') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Customer Name') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.phone || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.email || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Qoyod ID') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.id || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Invoices List -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ __('Invoices') }} ({{ customerInvoices.length }})
          </h3>
          <div class="flex space-x-2">
            <button
              @click="refreshInvoices"
              class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700"
            >
              <i class="fas fa-refresh mr-1"></i>
              {{ __('Refresh') }}
            </button>
            <button
              @click="createInvoice"
              class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700"
            >
              <i class="fas fa-plus mr-1"></i>
              {{ __('Create New Invoice') }}
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingInvoices" class="text-center py-8">
          <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
          <p class="mt-2 text-gray-600">{{ __('Loading invoices...') }}</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="customerInvoices.length === 0" class="text-center py-8">
          <i class="fas fa-file-invoice text-6xl text-gray-300"></i>
          <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('No invoices found') }}</h3>
          <p class="mt-2 text-gray-600">{{ __('No invoices have been created for this customer yet.') }}</p>
          <p class="mt-1 text-sm text-gray-500">{{ __('Debug: Customer ID') }} = {{ customer?.id }} - {{ __('Name:') }} {{ customer?.name || customer?.display_name || 'Unknown' }}</p>
          <div class="mt-4 space-x-3">
            <button
              @click="testQoyodConnection"
              class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
            >
              <i class="fas fa-bug mr-2"></i>
              {{ __('Test Qoyod Invoices') }}
            </button>
            <button
              @click="testQoyodQuotes"
              class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700"
            >
              <i class="fas fa-quote-left mr-2"></i>
              {{ __('Test Customer Quotes') }}
            </button>
            <button
              @click="createInvoice"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              <i class="fas fa-plus mr-2"></i>
              {{ __('Create First Invoice') }}
            </button>
          </div>
        </div>

        <!-- Invoices Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-600">
              <tr>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Invoice Reference') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Client Name') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Issue Date') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Due Date') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Total Value') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Balance') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Status') }}
                </th>
                <th class="px-6 py-3 text-center font-medium text-white uppercase tracking-wider">
                  {{ __('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="invoice in customerInvoices" :key="invoice.id" class="hover:bg-green-50 transition-colors duration-200 border-b border-green-100">
                <!-- Invoice Reference -->
                <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                  {{ invoice.reference || `INV${invoice.id}` }}
                </td>

                <!-- Client Name -->
                <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                  {{ invoice.customer_name || invoice.contact_name || invoice.display_name || 'Unknown Customer' }}
                </td>

                <!-- Issue Date -->
                <td class="px-6 py-4 text-center text-sm text-gray-900">
                  {{ formatDate(invoice.issue_date || invoice.created_at || invoice.date) }}
                </td>

                <!-- Due Date -->
                <td class="px-6 py-4 text-center text-sm text-gray-900">
                  {{ formatDate(invoice.due_date) }}
                </td>

                <!-- Total Value -->
                <td class="px-6 py-4 text-center text-sm text-gray-900 font-medium">
                  {{ formatCurrency(invoice.total || invoice.total_amount || invoice.amount) }}
                </td>

                <!-- Balance (مثل الصورة - يبدو نفس إجمالي المبلغ) -->
                <td class="px-6 py-4 text-center text-sm text-gray-900">
                  {{ formatCurrency(invoice.due_amount || invoice.total || invoice.balance) }}
                </td>

                <!-- Status -->
                <td class="px-6 py-4 text-center text-sm">
                  <span :class="getStatusBadgeClass(invoice.status)" class="text-xs font-medium">
                    {{ invoice.status_label || invoice.status || __('Draft') }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-center text-sm font-medium">
                  <div class="flex justify-center space-x-3">
                    <!-- View -->
                    <button @click="viewInvoice(invoice)" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" :title="__('View')">
                      <i class="fas fa-eye text-lg"></i>
                    </button>
                    
                    <!-- Edit -->
                    <button class="text-green-600 hover:text-green-800 transition-colors duration-200" :title="__('Edit')">
                      <i class="fas fa-edit text-lg"></i>
                    </button>
                    
                    <!-- Delete -->
                    <button @click="deleteInvoice(invoice)" class="text-red-600 hover:text-red-800 transition-colors duration-200" :title="__('Delete')" :disabled="deleting">
                      <i class="fas fa-trash text-lg"></i>
                    </button>
                    
                    <!-- Download -->
                    <button @click="downloadInvoicePdf(invoice)" class="text-purple-600 hover:text-purple-800 transition-colors duration-200" :title="__('Download')">
                      <i class="fas fa-download text-lg"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <!-- Summary message like in the picture -->
          <div class="mt-4 text-center text-sm text-green-700 bg-green-50 py-3 px-4 rounded-lg border border-green-200">
            {{ __('Displaying') }} {{ customerInvoices.length }} {{ __('sales invoices') }}
          </div>
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
    customer: Object,
    invoices: Array,
    meta: Object,
    errors: Object
  },
  
  data() {
    return {
      loadingInvoices: false,
      deleting: false,
    }
  },
  
  computed: {
    customerInvoices() {
      return this.invoices || [];
    }
  },
  
  mounted() {
    // Component is ready, stop initial loading
    this.loadingInvoices = false;
  },
  
  methods: {
    async loadInvoices() {
      this.loadingInvoices = true;
      
      try {
        // Simply reload the entire page to get fresh data
        await this.$inertia.reload({
          preserveState: false,
          preserveScroll: false,
          onFinish: () => {
            this.loadingInvoices = false;
          }
        });
      } catch (error) {
        console.error('Error loading invoices:', error);
        this.loadingInvoices = false;
      }
    },
    
    async refreshInvoices() {
      this.loadingInvoices = true;
      
      // Clear cache and reload the page
      await this.$inertia.reload({
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
          this.loadingInvoices = false;
        }
      });
    },
    
    async testQoyodConnection() {
      alert(`${this.__('Testing Qoyod invoices for customer')} ID: ${this.customer?.id}\n\n${this.__('Check browser console and Laravel logs for debug information.')}`);
      
      try {
        // Open a new window to test API directly
        const testUrl = `/dashboard/api/test-qoyod?customer_id=${this.customer?.id}`;
        window.open(testUrl, '_blank');
      } catch (error) {
        console.error('Error testing Qoyod connection:', error);
      }
    },
    
    async testQoyodQuotes() {
      alert(`${this.__('Testing customer quotes for')} ID: ${this.customer?.id}\n\n${this.__('This will show how quotes are fetched and filtered for the same customer.')}`);
      
      try {
        // Open a new window to test quotes API
        const testUrl = `/dashboard/api/test-qoyod-quotes?customer_id=${this.customer?.id}`;
        window.open(testUrl, '_blank');
      } catch (error) {
        console.error('Error testing quotes connection:', error);
      }
    },
    
    async createInvoice() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/create-invoice`);
    },
    
    async viewInvoice(invoice) {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/invoices/${invoice.id}`);
    },
    
    async downloadInvoicePdf(invoice) {
      try {
        const response = await this.$inertia.get(`/dashboard/customers/${this.customer.id}/invoices/${invoice.id}/pdf`, {}, {
          preserveState: false,
          preserveScroll: false,
        });
      } catch (error) {
        console.error('Error downloading invoice PDF:', error);
        alert(this.__('Error downloading invoice PDF'));
      }
    },
    
    async deleteInvoice(invoice) {
      if (!confirm(this.__('Are you sure you want to delete this invoice?'))) {
        return;
      }
      
      this.deleting = true;
      
      try {
        await this.$inertia.delete(`/dashboard/customers/${this.customer.id}/invoices/${invoice.id}`, {
          preserveState: false,
          preserveScroll: false,
          onSuccess: () => {
            this.refreshInvoices();
          },
          onError: (errors) => {
            console.error('Error deleting invoice:', errors);
            alert(this.__('Error deleting invoice'));
          }
        });
      } catch (error) {
        console.error('Error deleting invoice:', error);
        alert(this.__('Error deleting invoice'));
      } finally {
        this.deleting = false;
      }
    },
    
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`);
    },
    
    getStatusBadgeClass(status) {
      const statusClasses = {
        'draft': 'bg-green-100 text-green-800 rounded-full px-3 py-1 border border-green-200',
        'Draft': 'bg-green-100 text-green-800 rounded-full px-3 py-1 border border-green-200',
        'مسودة': 'bg-green-100 text-green-800 rounded-full px-3 py-1 border border-green-200',
        'approved': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'Approved': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'معتمد': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'sent': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'Sent': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'مرسل': 'bg-blue-100 text-blue-800 rounded-full px-3 py-1 border border-blue-200',
        'paid': 'bg-emerald-100 text-emerald-800 rounded-full px-3 py-1 border border-emerald-200',
        'Paid': 'bg-emerald-100 text-emerald-800 rounded-full px-3 py-1 border border-emerald-200',
        'مدفوع': 'bg-emerald-100 text-emerald-800 rounded-full px-3 py-1 border border-emerald-200',
        'overdue': 'bg-red-100 text-red-800 rounded-full px-3 py-1 border border-red-200',
        'Overdue': 'bg-red-100 text-red-800 rounded-full px-3 py-1 border border-red-200',
        'متأخر': 'bg-red-100 text-red-800 rounded-full px-3 py-1 border border-red-200',
      };
      
      return statusClasses[status] || 'bg-gray-100 text-gray-800';
    },
    
    formatDate(date) {
      if (!date) return '-';
      
      const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      };
      
      const locale = this.$page.props.locale === 'ar' ? 'ar-SA' : 'en-US';
      return new Date(date).toLocaleDateString(locale, options);
    },
    
    formatCurrency(amount) {
      if (!amount) return '-';
      
      return new Intl.NumberFormat(this.$page.props.locale === 'ar' ? 'ar-SA' : 'en-US', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount);
    },

    __(key, replace = {}) {
      // Translation function
      const translations = {
        en: {
          'Customer Invoices': 'Customer Invoices',
          'Invoices for': 'Invoices for',
          'View all invoices created for this customer': 'View all invoices created for this customer',
          'Create Invoice': 'Create Invoice',
          'Back to Customer': 'Back to Customer',
          'Customer Information': 'Customer Information',
          'Customer Name': 'Customer Name',
          'Phone': 'Phone',
          'Email': 'Email',
          'Qoyod ID': 'Qoyod ID',
          'Invoices': 'Invoices',
          'Refresh': 'Refresh',
          'View All Invoices': 'View All Invoices',
          'Create New Invoice': 'Create New Invoice',
          'Loading invoices...': 'Loading invoices...',
          'No invoices found': 'No invoices found',
          'No invoices have been created for this customer yet.': 'No invoices have been created for this customer yet.',
          'Create First Invoice': 'Create First Invoice',
          'Actions': 'Actions',
          'Creation Date': 'Creation Date',
          'Due Date': 'Due Date',
          'Due Amount': 'Due Amount',
          'Paid Amount': 'Paid Amount',
          'Total': 'Total',
          'Invoice Reference': 'Invoice Reference',
          'Client Name': 'Client Name',
          'Issue Date': 'Issue Date',
          'Total Value': 'Total Value',
          'Balance': 'Balance',
          'Status': 'Status',
          'Download': 'Download',
          'View': 'View',
          'Edit': 'Edit',
          'Draft': 'Draft',
          'Name:': 'Name:',
          'View Invoice': 'View Invoice',
          'Download PDF': 'Download PDF',
          'Delete Invoice': 'Delete Invoice',
          'Are you sure you want to delete this invoice?': 'Are you sure you want to delete this invoice?',
          'Error deleting invoice': 'Error deleting invoice',
          'Error downloading invoice PDF': 'Error downloading invoice PDF',
          'Debug: Customer ID': 'Debug: Customer ID',
          'Test Qoyod Invoices': 'Test Qoyod Invoices',
          'Testing Qoyod invoices for customer': 'Testing Qoyod invoices for customer',
          'Test Customer Quotes': 'Test Customer Quotes',
          'Testing customer quotes for': 'Testing customer quotes for',
          'This will show how quotes are fetched and filtered for the same customer.': 'This will show how quotes are fetched and filtered for the same customer.',
          'Displaying': 'يتم عرض',
          'sales invoices': 'فواتير مبيعات',
          'Check browser console and Laravel logs for debug information.': 'Check browser console and Laravel logs for debug information.'
        },
        ar: {
          'Customer Invoices': 'فواتير العميل',
          'Invoices for': 'الفاتوري الخاصة بـ',
          'View all invoices created for this customer': 'عرض جميع الفواتير التي تم إنشاؤها لهذا العميل',
          'Create Invoice': 'إنشاء فاتورة',
          'Back to Customer': 'العودة للعميل',
          'Customer Information': 'معلومات العميل',
          'Customer Name': 'اسم العميل',
          'Phone': 'الهاتف',
          'Email': 'البريد الإلكتروني',
          'Qoyod ID': 'رقم قيود',
          'Invoices': 'الفواتير',
          'Refresh': 'تحديث',
          'View All Invoices': 'عرض جميع الفواتير',
          'Create New Invoice': 'إنشاء فاتورة جديدة',
          'Loading invoices...': 'جاري تحميل الفواتير...',
          'No invoices found': 'لم يتم العثور على فواتير',
          'No invoices have been created for this customer yet.': 'لم يتم إنشاء أي فواتير لهذا العميل حتى الآن.',
          'Create First Invoice': 'إنشاء أول فاتورة',
          'Actions': 'الإجراءات',
          'Creation Date': 'تاريخ الإنشاء',
          'Due Date': 'تاريخ الاستحقاق',
          'Due Amount': 'المبلغ المستحق',
          'Paid Amount': 'المبلغ المدفوع',
          'Total': 'المجموع',
          'Invoice Reference': 'رقم الفاتورة المرجعي',
          'Client Name': 'اسم العميل',
          'Issue Date': 'تاريخ الإصدار',
          'Total Value': 'القيمة الإجمالية',
          'Balance': 'الرصيد',
          'Status': 'الحالة',
          'Download': 'تحميل',
          'View': 'عرض',
          'Edit': 'تعديل',
          'Draft': 'مسودة',
          'Name:': 'الاسم:',
          'View Invoice': 'عرض الفاتورة',
          'Download PDF': 'تحميل PDF',
          'Delete Invoice': 'حذف الفاتورة',
          'Are you sure you want to delete this invoice?': 'هل أنت متأكد من حذف هذه الفاتورة؟',
          'Error deleting invoice': 'خطأ في حذف الفاتورة',
          'Error downloading invoice PDF': 'خطأ في تحميل PDF الفاتورة',
          'Debug: Customer ID': 'تشخيص: معرف العميل',
          'Test Qoyod Invoices': 'اختبار فواتير قيود',
          'Testing Qoyod invoices for customer': 'اختبار فواتير قيود للعميل',
          'Test Customer Quotes': 'اختبار عروض السعر',
          'Testing customer quotes for': 'اختبار عروض السعر للعميل',
          'This will show how quotes are fetched and filtered for the same customer.': 'هذا سيُظهر كيفية جلب وتصفية عروض السعر لنفس العميل.',
          'Displaying': 'يتم عرض',
          'sales invoices': 'فواتير مبيعات',
          'Check browser console and Laravel logs for debug information.': 'تحقق من console المتصفح وسجلات Laravel لمعلومات التشخيص.'
        }
      };

      const locale = this.$page.props.locale || 'en';
      let translation = translations[locale]?.[key] || key;

      // Replace placeholders
      Object.keys(replace).forEach(placeholder => {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
      });

      return translation;
    }
  }
}
</script>
