<template>
  <DashboardLayout page-title="Customers">
    <div class="space-y-6">
      <!-- Header -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">
                {{ __('Customers') }}
              </h2>
              <p class="mt-1 text-sm text-gray-600">
                {{ __('Manage your customers from Qoyod') }}
              </p>
            </div>
            <div class="flex space-x-3">
              <button 
                @click="refreshCustomers"
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
              >
                <i class="fas fa-sync-alt mr-2" :class="{ 'fa-spin': loading }"></i>
                {{ __('Refresh') }}
              </button>
              <button 
                @click="showAddCustomerModal = true"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <i class="fas fa-plus mr-2"></i>
                {{ __('Add Customer') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400"></i>
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="__('Search customers...')"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                />
              </div>
            </div>
            <div class="flex space-x-2">
              <select
                v-model="statusFilter"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              >
                <option value="">{{ __('All Status') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Customers Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Reference Number') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Customer') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Organization') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Email') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Phone') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Status') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Created') }}
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="flex items-center justify-center">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mr-3"></i>
                    <span class="text-gray-500">{{ __('Loading customers...') }}</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="filteredCustomers.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="text-gray-500">
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <p class="text-lg font-medium">{{ __('No customers found') }}</p>
                    <p class="text-sm">{{ __('Get started by adding your first customer or refreshing from Qoyod') }}</p>
                  </div>
                </td>
              </tr>
              <tr v-else v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="text-sm font-mono text-gray-900">
                    {{ formatQoyodReferenceNumber(customer.id) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-user text-green-600"></i>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ customer.name || customer.display_name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ customer.organization || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ customer.email || customer.email_address || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ customer.phone || customer.phone_number || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="(customer.status && customer.status.toLowerCase() === 'active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ (customer.status && customer.status.toLowerCase() === 'active') ? __('Active') : __('Inactive') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(customer.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <button 
                      @click="viewCustomer(customer)"
                      class="text-green-600 hover:text-green-900"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button 
                      @click="editCustomer(customer)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      @click="deleteCustomer(customer)"
                      class="text-red-600 hover:text-red-900"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="px-6 py-4 border-t border-gray-200">
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ error }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="customers.length > 0" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              {{ __('Showing') }} {{ filteredCustomers.length }} {{ __('of') }} {{ meta.total || customers.length }} {{ __('customers') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Customer Modal -->
    <div v-if="showAddCustomerModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddCustomerModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="addCustomer">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ __('Add New Customer') }}
                  </h3>
                  
                  <div class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        {{ __('Customer Name') }} *
                      </label>
                      <input
                        v-model="newCustomer.name"
                        type="text"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        {{ __('Organization Name') }}
                      </label>
                      <input
                        v-model="newCustomer.organization"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        {{ __('Email') }}
                      </label>
                      <input
                        v-model="newCustomer.email"
                        type="email"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">
                        {{ __('Phone') }}
                      </label>
                      <input
                        v-model="newCustomer.phone"
                        type="tel"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="addingCustomer"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <i v-if="addingCustomer" class="fas fa-spinner fa-spin mr-2"></i>
                {{ addingCustomer ? __('Adding...') : __('Add Customer') }}
              </button>
              <button
                type="button"
                @click="showAddCustomerModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                {{ __('Cancel') }}
              </button>
            </div>
          </form>
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
    customers: {
      type: Array,
      default: () => []
    },
    meta: {
      type: Object,
      default: () => ({})
    },
    currentPage: {
      type: Number,
      default: 1
    },
    error: {
      type: String,
      default: null
    }
  },
  data() {
    return {
      loading: false,
      searchQuery: '',
      statusFilter: '',
      showAddCustomerModal: false,
      addingCustomer: false,
      newCustomer: {
        name: '',
        organization: '',
        email: '',
        phone: ''
      }
    }
  },
  mounted() {
    console.log('Customers data:', this.customers);
    console.log('Meta data:', this.meta);
    console.log('Error:', this.error);
  },
  computed: {
    filteredCustomers() {
      let filtered = this.customers

      // Filter by search query
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(customer => 
          (customer.name && customer.name.toLowerCase().includes(query)) ||
          (customer.display_name && customer.display_name.toLowerCase().includes(query)) ||
          (customer.organization && customer.organization.toLowerCase().includes(query)) ||
          (customer.email && customer.email.toLowerCase().includes(query)) ||
          (customer.email_address && customer.email_address.toLowerCase().includes(query)) ||
          (customer.phone && customer.phone.toLowerCase().includes(query)) ||
          (customer.phone_number && customer.phone_number.toLowerCase().includes(query))
        )
      }

      // Filter by status
      if (this.statusFilter) {
        filtered = filtered.filter(customer => 
          customer.status && customer.status.toLowerCase() === this.statusFilter
        )
      }

      return filtered
    }
  },
  methods: {
    async refreshCustomers() {
      this.loading = true
      try {
        // Reload the page to fetch fresh data from Qoyod
        this.$inertia.reload()
      } catch (error) {
        console.error('Error refreshing customers:', error)
      } finally {
        this.loading = false
      }
    },
    
    async addCustomer() {
      this.addingCustomer = true
      try {
        // Submit form to Laravel backend
        this.$inertia.post('/dashboard/customers', this.newCustomer, {
          onSuccess: () => {
        // Reset form
        this.newCustomer = { name: '', organization: '', email: '', phone: '' }
            this.showAddCustomerModal = false
          },
          onError: (errors) => {
            console.error('Error adding customer:', errors)
          }
        })
      } catch (error) {
        console.error('Error adding customer:', error)
      } finally {
        this.addingCustomer = false
      }
    },
    
    viewCustomer(customer) {
      // TODO: Implement customer view
      console.log('View customer:', customer)
    },
    
    editCustomer(customer) {
      // TODO: Implement customer edit
      console.log('Edit customer:', customer)
    },
    
    deleteCustomer(customer) {
      if (confirm(this.__('Are you sure you want to delete this customer?'))) {
        this.$inertia.delete(`/dashboard/customers/${customer.id}`, {
          onSuccess: () => {
            // Customer deleted successfully
          },
          onError: (errors) => {
            console.error('Error deleting customer:', errors)
          }
        })
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    },
    
    formatQoyodReferenceNumber(id) {
      // Format as CUS + padded number (e.g., CUS001, CUS004)
      return `CUS${id.toString().padStart(3, '0')}`
    },
    
    formatReferenceNumber(id) {
      // Convert number to Arabic numerals
      const arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩']
      return id.toString().replace(/\d/g, (digit) => arabicNumerals[parseInt(digit)])
    },
    
    __(key, replace = {}) {
      // Simple translation function
      const translations = {
        en: {
          'Customers': 'Customers',
          'Manage your customers from Qoyod': 'Manage your customers from Qoyod',
          'Refresh': 'Refresh',
          'Add Customer': 'Add Customer',
          'Search customers...': 'Search customers...',
          'All Status': 'All Status',
          'Active': 'Active',
          'Inactive': 'Inactive',
          'Customer': 'Customer',
          'Email': 'Email',
          'Phone': 'Phone',
          'Status': 'Status',
          'Created': 'Created',
          'Actions': 'Actions',
          'Loading customers...': 'Loading customers...',
          'No customers found': 'No customers found',
          'Get started by adding your first customer or refreshing from Qoyod': 'Get started by adding your first customer or refreshing from Qoyod',
          'Showing': 'Showing',
          'of': 'of',
          'customers': 'customers',
          'Add New Customer': 'Add New Customer',
          'Customer Name': 'Customer Name',
          'Organization Name': 'Organization Name',
          'Organization': 'Organization',
          'Reference Number': 'Reference Number',
          'Adding...': 'Adding...',
          'Cancel': 'Cancel',
          'Are you sure you want to delete this customer?': 'Are you sure you want to delete this customer?'
        },
        ar: {
          'Customers': 'العملاء',
          'Manage your customers from Qoyod': 'إدارة عملائك من قيود',
          'Refresh': 'تحديث',
          'Add Customer': 'إضافة عميل',
          'Search customers...': 'البحث في العملاء...',
          'All Status': 'جميع الحالات',
          'Active': 'نشط',
          'Inactive': 'غير نشط',
          'Customer': 'العميل',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Status': 'الحالة',
          'Created': 'تاريخ الإنشاء',
          'Actions': 'الإجراءات',
          'Loading customers...': 'جاري تحميل العملاء...',
          'No customers found': 'لم يتم العثور على عملاء',
          'Get started by adding your first customer or refreshing from Qoyod': 'ابدأ بإضافة أول عميل أو تحديث من قيود',
          'Showing': 'عرض',
          'of': 'من',
          'customers': 'عملاء',
          'Add New Customer': 'إضافة عميل جديد',
          'Customer Name': 'اسم العميل',
          'Organization Name': 'اسم المنشأة',
          'Organization': 'المنشأة',
          'Reference Number': 'الرقم المرجعي',
          'Adding...': 'جاري الإضافة...',
          'Cancel': 'إلغاء',
          'Are you sure you want to delete this customer?': 'هل أنت متأكد من حذف هذا العميل؟'
        }
      }

      const locale = this.$page.props.locale || 'en'
      let translation = translations[locale]?.[key] || key

      // Replace placeholders
      Object.keys(replace).forEach(placeholder => {
        translation = translation.replace(`:${placeholder}`, replace[placeholder])
      })

      return translation
    }
  }
}
</script>
