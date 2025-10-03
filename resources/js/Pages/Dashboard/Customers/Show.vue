<template>
  <DashboardLayout :pageTitle="__('Customer Details')">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
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
                {{ __('Customer Details') }}
              </h1>
              <p class="text-sm text-gray-500 mt-1">
                {{ customer?.name || customer?.display_name || 'معلومات العميل' }}
              </p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <button 
              @click="viewArtifacts"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              <i class="fas fa-gem mr-2"></i>
              {{ __('View Artifacts') }}
            </button>
            <button 
              @click="viewQuotes"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
            >
              <i class="fas fa-file-invoice mr-2"></i>
              {{ __('View Quotes') }}
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Information Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Basic Information') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Name') }}</div>
                <div class="text-base text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Email') }}</div>
                <div class="text-base text-gray-900">{{ customer?.email || '-' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Phone') }}</div>
                <div class="text-base text-gray-900">{{ customer?.phone || '-' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Organization') }}</div>
                <div class="text-base text-gray-900">{{ customer?.organization || '-' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Customer Code') }}</div>
                <div class="text-base text-gray-900">{{ customer?.code || customer?.id || '-' }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500">{{ __('Status') }}</div>
                <div class="text-base text-gray-900">{{ customer?.status || 'Active' }}</div>
              </div>
            </div>
          </div>

          <!-- Recent Quotes -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-800">{{ __('Recent Quotes') }}</h3>
              <button 
                @click="viewQuotes"
                class="text-sm text-blue-600 hover:text-blue-800"
              >
                {{ __('View All') }}
              </button>
            </div>
            <div v-if="statistics?.recent_quotes?.length > 0">
              <div class="space-y-3">
                <div 
                  v-for="quote in statistics.recent_quotes" 
                  :key="quote.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div>
                    <div class="font-medium text-gray-900">{{ quote.quotation_number || 'Quote #' + quote.id }}</div>
                    <div class="text-sm text-gray-500">{{ formatDate(quote.created_at) }}</div>
                  </div>
                  <div class="text-right">
                    <div class="font-medium text-gray-900">{{ quote.total_amount || '$0.00' }}</div>
                    <span 
                      :class="getStatusClass(quote.status)"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ getStatusText(quote.status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-gray-500 text-center py-4">
              {{ __('No quotes found for this customer') }}
            </div>
          </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="space-y-6">
          <!-- Statistics Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Statistics') }}</h3>
            <div class="space-y-4">
              <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                  <i class="fas fa-gem text-blue-600"></i>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-500">{{ __('Total Artifacts') }}</div>
                  <div class="text-2xl font-bold text-gray-900">{{ statistics?.artifacts_count || 0 }}</div>
                </div>
              </div>
              <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                  <i class="fas fa-file-invoice text-green-600"></i>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-500">{{ __('Total Quotes') }}</div>
                  <div class="text-2xl font-bold text-gray-900">{{ statistics?.quotes_count || 0 }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Quick Actions') }}</h3>
            <div class="space-y-3">
              <button 
                @click="viewArtifacts"
                class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                <i class="fas fa-gem mr-2"></i>
                {{ __('Manage Artifacts') }}
              </button>
              <button 
                @click="viewQuotes"
                class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              >
                <i class="fas fa-file-invoice mr-2"></i>
                {{ __('Manage Quotes') }}
              </button>
              <button 
                @click="createQuote"
                class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
              >
                <i class="fas fa-plus mr-2"></i>
                {{ __('Create Quote') }}
              </button>
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
    customer: Object,
    statistics: Object
  },
  mounted() {
    console.log('Customer Show component mounted')
    console.log('Customer data:', this.customer)
    console.log('Statistics data:', this.statistics)
  },
  methods: {
    goBack() {
      this.$inertia.visit('/dashboard/customers')
    },
    
    viewArtifacts() {
      if (this.customer?.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`)
      }
    },
    
    viewQuotes() {
      if (this.customer?.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/quotes`)
      }
    },
    
    createQuote() {
      if (this.customer?.id) {
        this.$inertia.visit(`/dashboard/customers/${this.customer.id}/create-quote`)
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
    },
    
    getStatusText(status) {
      const statusMap = {
        'draft': __('Draft'),
        'sent': __('Sent'),
        'accepted': __('Accepted'),
        'declined': __('Declined'),
        'expired': __('Expired')
      }
      return statusMap[status] || status || __('Unknown')
    },
    
    getStatusClass(status) {
      const statusClasses = {
        'draft': 'bg-gray-100 text-gray-800',
        'sent': 'bg-blue-100 text-blue-800',
        'accepted': 'bg-green-100 text-green-800',
        'declined': 'bg-red-100 text-red-800',
        'expired': 'bg-yellow-100 text-yellow-800'
      }
      return statusClasses[status] || 'bg-gray-100 text-gray-800'
    }
  }
}
</script>
