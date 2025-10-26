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
                {{ __('Quote Details') }}
              </h1>
              <p class="text-sm text-gray-500 mt-1">
                {{ __('Quote ID') }}: #{{ quote.id || 'N/A' }}
              </p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <!-- Quote Status Badge -->
            <span :class="getStatusBadgeClass(quote.status)" class="px-3 py-1 rounded-full text-sm font-medium">
              {{ translateStatus(quote.status) }}
            </span>
            <!-- Action Buttons -->
            <button 
              @click="viewQoyodQuote"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 mr-3"
              :disabled="isLoadingQoyod"
            >
              <i class="fas fa-eye mr-2" v-if="!isLoadingQoyod"></i>
              <i class="fas fa-spinner fa-spin mr-2" v-if="isLoadingQoyod"></i>
              {{ isLoadingQoyod ? __('Loading...') : __('View') }}
            </button>
            <button 
              @click="downloadQoyodQuote"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-3"
              :disabled="isLoadingDownload"
            >
              <i class="fas fa-download mr-2" v-if="!isLoadingDownload"></i>
              <i class="fas fa-spinner fa-spin mr-2" v-if="isLoadingDownload"></i>
              {{ isLoadingDownload ? __('Loading...') : __('Download') }}
            </button>
            <button 
              @click="backToQuotes"
              class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              {{ __('Back to Quotes') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="px-6 pb-6 min-h-screen">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="lg:col-span-2">
          
          <!-- Quote Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Quote Information') }}
              </h2>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Quote Number') }}
                  </label>
                  <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    {{ quote.reference || quote.quotation_number || 'N/A' }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Issue Date') }}
                  </label>
                  <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    {{ formatDate(quote.issue_date) }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Expiry Date') }}
                  </label>
                  <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    {{ formatDate(quote.expiry_date) }}
                  </p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Status') }}
                  </label>
                  <span :class="getStatusBadgeClass(quote.status)" class="inline-block px-3 py-1 rounded-md text-sm font-medium">
                    {{ translateStatus(quote.status) }}
                  </span>
                </div>
              </div>
              
              <!-- Notes -->
              <div class="mt-6" v-if="quote.notes">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ __('Notes') }}
                </label>
                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-md whitespace-pre-line">
                  {{ quote.notes }}
                </p>
              </div>
            </div>
          </div>

          <!-- Line Items -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Quote Items') }}
              </h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Item') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Product') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Description') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Quantity') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Unit Price') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ __('Subtotal') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in quote.line_items || []" :key="index">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                      <div>
                        <div class="font-medium">{{ getProductName(item.product_id) }}</div>
                        <div class="text-xs text-gray-400 mt-1">ID: {{ item.product_id }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                      <div class="space-y-1">
                        <div>{{ item.description || '-' }}</div>
                        <div class="text-xs text-gray-500" v-if="item.unit_type">
                          {{ __('Unit Type') }}: {{ item.unit_type }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ item.quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatCurrency(item.unit_price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatCurrency(item.quantity * item.unit_price) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          
          <!-- Customer Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Customer Information') }}
              </h2>
            </div>
            <div class="p-6">
              <div v-if="customer">
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Customer Name') }}
                  </label>
                  <p class="text-sm text-gray-900 font-medium">
                    {{ customer.display_name || customer.name || 'N/A' }}
                  </p>
                </div>
                <div class="mb-4" v-if="customer.email">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Email') }}
                  </label>
                  <p class="text-sm text-gray-900">
                    {{ customer.email }}
                  </p>
                </div>
                <div class="mb-4" v-if="customer.phone">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Phone') }}
                  </label>
                  <p class="text-sm text-gray-900">
                    {{ customer.phone }}
                  </p>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <p class="text-sm text-gray-500">{{ __('Customer information not available') }}</p>
              </div>
            </div>
          </div>

          <!-- Quote Summary -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Quote Summary') }}
              </h2>
            </div>
            <div class="p-6">
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">{{ __('Subtotal') }}</span>
                  <span class="text-sm text-gray-900">{{ calculateSubtotal() }}</span>
                </div>
                <div class="flex justify-between" v-if="calculateTax() > 0">
                  <span class="text-sm text-gray-600">{{ __('Tax') }}</span>
                  <span class="text-sm text-gray-900">{{ formatCurrency(calculateTax()) }}</span>
                </div>
                <div class="border-t border-gray-200 pt-3">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-900">{{ __('Total') }}</span>
                    <span class="text-lg font-semibold text-blue-600">{{ formatCurrency(quote.total_amount) }}</span>
                  </div>
                </div>
              </div>
            </div>
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
    quote: {
      type: Object,
      required: true
    },
    customer: {
      type: Object,
      default: null
    },
    products: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      isLoadingQoyod: false,
      isLoadingDownload: false
    }
  },

  methods: {
    goBack() {
      this.$inertia.visit('/dashboard');
    },

    backToQuotes() {
      if (this.customer && this.customer.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/quotes`);
      } else {
        // Fallback to dashboard if customer info is not available
        this.$inertia.visit('/dashboard');
      }
    },

    async viewQoyodQuote() {
      // Check if quote has an ID (either local or qoyod_id)
      if (!this.quote.id) {
        alert(this.__('Quote not found in Qoyod'));
        return;
      }

      this.isLoadingQoyod = true;
      
      try {
        // Call our backend endpoint to get the PDF from Qoyod
        const response = await fetch(`/dashboard/quotes/${this.quote.id}/qoyod-pdf`, {
          method: 'GET',
          headers: {
            'Accept': 'application/pdf',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get the PDF blob
        const blob = await response.blob();
        
        // Create a URL for the blob and open it in a new tab
        const url = window.URL.createObjectURL(blob);
        const newTab = window.open(url, '_blank');
        
        // Clean up the URL after a short delay
        setTimeout(() => {
          window.URL.revokeObjectURL(url);
        }, 1000);

      } catch (error) {
        console.error('Error fetching Qoyod PDF:', error);
        alert(this.__('Failed to load PDF from Qoyod. Please try again.'));
      } finally {
        this.isLoadingQoyod = false;
      }
    },

    async downloadQoyodQuote() {
      // Check if quote has an ID (either local or qoyod_id)
      if (!this.quote.id) {
        alert(this.__('Quote not found in Qoyod'));
        return;
      }

      this.isLoadingDownload = true;
      
      try {
        // Call our backend endpoint to get the PDF from Qoyod
        const response = await fetch(`/dashboard/quotes/${this.quote.id}/qoyod-pdf`, {
          method: 'GET',
          headers: {
            'Accept': 'application/pdf',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get the PDF blob
        const blob = await response.blob();
        
        // Create a download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `quote-${this.quote.id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up the URL after a short delay
        setTimeout(() => {
          window.URL.revokeObjectURL(url);
        }, 1000);

      } catch (error) {
        console.error('Error downloading Qoyod PDF:', error);
        alert(this.__('Failed to download PDF from Qoyod. Please try again.'));
      } finally {
        this.isLoadingDownload = false;
      }
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

    getProductName(productId) {
      // Find product in the products array
      const product = this.products.find(p => p.id == productId);
      if (product) {
        // Display Arabic name if available, otherwise English name
        return product.name_ar || product.name || this.__('Product') + ' #' + productId;
      }
      
      // Fallback to product ID if not found
      return this.__('Product') + ' #' + productId;
    },

    calculateSubtotal() {
      if (!this.quote.line_items) return this.formatCurrency(0);
      
      const subtotal = this.quote.line_items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_price);
      }, 0);
      
      return this.formatCurrency(subtotal);
    },

    calculateTax() {
      // Simple 15% tax calculation
      if (!this.quote.line_items) return 0;
      
      const subtotal = this.quote.line_items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_price);
      }, 0);
      
      return subtotal * 0.15;
    },

    __(key) {
      const translations = {
        en: {
          'Quote Details': 'Quote Details',
          'Quote ID': 'Quote ID',
          'Draft': 'Draft',
          'Back to Quotes': 'Back to Quotes',
          'View': 'View',
          'Download': 'Download',
          'Loading...': 'Loading...',
          'Quote not found in Qoyod': 'Quote not found in Qoyod',
          'Failed to load PDF from Qoyod. Please try again.': 'Failed to load PDF from Qoyod. Please try again.',
          'Failed to download PDF from Qoyod. Please try again.': 'Failed to download PDF from Qoyod. Please try again.',
          'Quote Information': 'Quote Information',
          'Quote Number': 'Quote Number',
          'Issue Date': 'Issue Date',
          'Expiry Date': 'Expiry Date',
          'Status': 'Status',
          'Notes': 'Notes',
          'Quote Items': 'Quote Items',
          'Item': 'Item',
          'Product': 'Product',
          'Description': 'Description',
          'Quantity': 'Quantity',
          'Unit Price': 'Unit Price',
          'Subtotal': 'Subtotal',
          'Customer Information': 'Customer Information',
          'Customer Name': 'Customer Name',
          'Email': 'Email',
          'Phone': 'Phone',
          'Customer information not available': 'Customer information not available',
          'Quote Summary': 'Quote Summary',
          'Tax': 'Tax',
          'Total': 'Total',
          'Product': 'Product',
          'Unit Type': 'Unit Type'
        },
        ar: {
          'Quote Details': 'تفاصيل عرض السعر',
          'Quote ID': 'رقم عرض السعر',
          'Draft': 'مسودة',
          'Back to Quotes': 'العودة لعروض السعر',
          'View': 'عرض',
          'Download': 'تحميل',
          'Loading...': 'جاري التحميل...',
          'Quote not found in Qoyod': 'عرض السعر غير موجود في قيود',
          'Failed to load PDF from Qoyod. Please try again.': 'فشل في تحميل PDF من قيود. يرجى المحاولة مرة أخرى.',
          'Failed to download PDF from Qoyod. Please try again.': 'فشل في تحميل PDF من قيود. يرجى المحاولة مرة أخرى.',
          'Quote Information': 'معلومات عرض السعر',
          'Quote Number': 'رقم عرض السعر',
          'Issue Date': 'تاريخ الإصدار',
          'Expiry Date': 'تاريخ الانتهاء',
          'Status': 'الحالة',
          'Notes': 'الملاحظات',
          'Quote Items': 'بنود عرض السعر',
          'Item': 'البند',
          'Product': 'المنتج',
          'Description': 'الوصف',
          'Quantity': 'الكمية',
          'Unit Price': 'سعر الوحدة',
          'Subtotal': 'المجموع الفرعي',
          'Customer Information': 'معلومات العميل',
          'Customer Name': 'اسم العميل',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Customer information not available': 'معلومات العميل غير متاحة',
          'Quote Summary': 'ملخص عرض السعر',
          'Tax': 'الضريبة',
          'Total': 'المجموع',
          'Product': 'المنتج',
          'Unit Type': 'نوع الوحدة'
        }
      };

      const locale = this.$page.props.locale || 'en';
      return translations[locale]?.[key] || key;
    }
  },

  mounted() {
    console.log('ShowQuote component mounted with data:', {
      quote: this.quote,
      customer: this.customer,
      products: this.products
    });
  }
}
</script>
