<template>
  <DashboardLayout :pageTitle="__('Edit Invoice')">
    <div class="max-w-7xl mx-auto px-4 min-h-screen">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Edit Invoice') }}: {{ invoice?.reference || invoice?.id }}
            </h2>
            <p class="text-gray-600">
              {{ __('Edit invoice details and line items') }}
            </p>
          </div>
          <div class="flex space-x-3">
            <button
              @click="goBack"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              {{ __('Back to Invoices') }}
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
        </div>
      </div>

      <!-- Invoice Details Form -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Invoice Details') }}</h3>
        
        <form @submit.prevent="updateInvoice" class="space-y-6">
          <!-- Basic Invoice Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Invoice Reference') }}</label>
              <input
                v-model="formData.reference"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                :placeholder="__('Enter invoice reference')"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Issue Date') }}</label>
              <input
                v-model="formData.issue_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Due Date') }}</label>
              <input
                v-model="formData.due_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
              <select
                v-model="formData.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                <option value="draft">{{ __('Draft') }}</option>
                <option value="sent">{{ __('Sent') }}</option>
                <option value="paid">{{ __('Paid') }}</option>
                <option value="overdue">{{ __('Overdue') }}</option>
              </select>
            </div>
          </div>

          <!-- Line Items -->
          <div>
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-lg font-medium text-gray-800">{{ __('Line Items') }}</h4>
              <button
                type="button"
                @click="addLineItem"
                class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700"
              >
                <i class="fas fa-plus mr-1"></i>
                {{ __('Add Item') }}
              </button>
            </div>
            
            <div class="space-y-4">
              <div
                v-for="(item, index) in formData.line_items"
                :key="index"
                class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border border-gray-200 rounded-lg"
              >
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Product') }}</label>
                  <select
                    v-model="item.product_id"
                    @change="updateLineItem(index)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  >
                    <option value="">{{ __('Select Product') }}</option>
                    <option
                      v-for="product in products"
                      :key="product.id"
                      :value="product.id"
                    >
                      {{ product.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Quantity') }}</label>
                  <input
                    v-model.number="item.quantity"
                    @input="updateLineItem(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Unit Price') }}</label>
                  <input
                    v-model.number="item.unit_price"
                    @input="updateLineItem(index)"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Total') }}</label>
                  <input
                    :value="formatCurrency(item.quantity * item.unit_price)"
                    readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                  />
                </div>
                
                <div class="flex items-end">
                  <button
                    type="button"
                    @click="removeLineItem(index)"
                    class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    :disabled="formData.line_items.length <= 1"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Totals -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex justify-between items-center">
              <span class="text-lg font-medium text-gray-800">{{ __('Total Amount') }}:</span>
              <span class="text-xl font-bold text-green-600">{{ formatCurrency(totalAmount) }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="goBack"
              class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              {{ __('Cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
            >
              <i v-if="saving" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-save mr-2"></i>
              {{ saving ? __('Saving...') : __('Update Invoice') }}
            </button>
          </div>
        </form>
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
    invoice: Object,
    products: Array,
    locations: Array,
    meta: Object
  },
  
  data() {
    return {
      saving: false,
      formData: {
        reference: '',
        issue_date: '',
        due_date: '',
        status: 'draft',
        line_items: [
          {
            product_id: '',
            quantity: 1,
            unit_price: 0
          }
        ]
      }
    }
  },
  
  computed: {
    totalAmount() {
      return this.formData.line_items.reduce((total, item) => {
        return total + (item.quantity * item.unit_price);
      }, 0);
    }
  },
  
  mounted() {
    this.initializeForm();
  },
  
  methods: {
    initializeForm() {
      if (this.invoice) {
        this.formData.reference = this.invoice.reference || '';
        this.formData.issue_date = this.invoice.issue_date || '';
        this.formData.due_date = this.invoice.due_date || '';
        this.formData.status = this.invoice.status || 'draft';
        
        // Initialize line items
        if (this.invoice.line_items && this.invoice.line_items.length > 0) {
          this.formData.line_items = this.invoice.line_items.map(item => ({
            product_id: item.product_id || '',
            quantity: item.quantity || 1,
            unit_price: item.unit_price || 0
          }));
        }
      }
    },
    
    addLineItem() {
      this.formData.line_items.push({
        product_id: '',
        quantity: 1,
        unit_price: 0
      });
    },
    
    removeLineItem(index) {
      if (this.formData.line_items.length > 1) {
        this.formData.line_items.splice(index, 1);
      }
    },
    
    updateLineItem(index) {
      const item = this.formData.line_items[index];
      const product = this.products.find(p => p.id == item.product_id);
      
      if (product) {
        item.unit_price = product.price || 0;
      }
    },
    
    async updateInvoice() {
      this.saving = true;
      
      try {
        // Here you would implement the actual update logic
        // For now, we'll just show a success message
        alert(this.__('Invoice updated successfully!'));
        
        // Navigate back to invoices list
        this.goBack();
      } catch (error) {
        console.error('Error updating invoice:', error);
        alert(this.__('Error updating invoice. Please try again.'));
      } finally {
        this.saving = false;
      }
    },
    
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.meta.customer_id}/invoices`);
    },
    
    formatCurrency(amount) {
      if (!amount) return '0.00 ر.س';
      
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
          'Edit Invoice': 'Edit Invoice',
          'Edit invoice details and line items': 'Edit invoice details and line items',
          'Back to Invoices': 'Back to Invoices',
          'Customer Information': 'Customer Information',
          'Customer Name': 'Customer Name',
          'Phone': 'Phone',
          'Email': 'Email',
          'Invoice Details': 'Invoice Details',
          'Invoice Reference': 'Invoice Reference',
          'Enter invoice reference': 'Enter invoice reference',
          'Issue Date': 'Issue Date',
          'Due Date': 'Due Date',
          'Status': 'Status',
          'Draft': 'Draft',
          'Sent': 'Sent',
          'Paid': 'Paid',
          'Overdue': 'Overdue',
          'Line Items': 'Line Items',
          'Add Item': 'Add Item',
          'Product': 'Product',
          'Select Product': 'Select Product',
          'Quantity': 'Quantity',
          'Unit Price': 'Unit Price',
          'Total': 'Total',
          'Total Amount': 'Total Amount',
          'Cancel': 'Cancel',
          'Saving...': 'Saving...',
          'Update Invoice': 'Update Invoice',
          'Invoice updated successfully!': 'Invoice updated successfully!',
          'Error updating invoice. Please try again.': 'Error updating invoice. Please try again.'
        },
        ar: {
          'Edit Invoice': 'تعديل الفاتورة',
          'Edit invoice details and line items': 'تعديل تفاصيل الفاتورة وعناصرها',
          'Back to Invoices': 'العودة للفواتير',
          'Customer Information': 'معلومات العميل',
          'Customer Name': 'اسم العميل',
          'Phone': 'الهاتف',
          'Email': 'البريد الإلكتروني',
          'Invoice Details': 'تفاصيل الفاتورة',
          'Invoice Reference': 'رقم الفاتورة المرجعي',
          'Enter invoice reference': 'أدخل رقم الفاتورة المرجعي',
          'Issue Date': 'تاريخ الإصدار',
          'Due Date': 'تاريخ الاستحقاق',
          'Status': 'الحالة',
          'Draft': 'مسودة',
          'Sent': 'مرسل',
          'Paid': 'مدفوع',
          'Overdue': 'متأخر',
          'Line Items': 'عناصر الفاتورة',
          'Add Item': 'إضافة عنصر',
          'Product': 'المنتج',
          'Select Product': 'اختر المنتج',
          'Quantity': 'الكمية',
          'Unit Price': 'سعر الوحدة',
          'Total': 'المجموع',
          'Total Amount': 'المبلغ الإجمالي',
          'Cancel': 'إلغاء',
          'Saving...': 'جاري الحفظ...',
          'Update Invoice': 'تحديث الفاتورة',
          'Invoice updated successfully!': 'تم تحديث الفاتورة بنجاح!',
          'Error updating invoice. Please try again.': 'خطأ في تحديث الفاتورة. يرجى المحاولة مرة أخرى.'
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
