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
                      @click="addArtifactToCustomer(customer)"
                      class="p-1 rounded-md text-purple-600 hover:text-purple-900 hover:bg-purple-50 transition-colors duration-200"
                      :title="__('Add Artifact')"
                    >
                      <i class="fas fa-gem"></i>
                    </button>
                    <button 
                      @click="viewCustomerArtifacts(customer)"
                      class="p-1 rounded-md text-orange-600 hover:text-orange-900 hover:bg-orange-50 transition-colors duration-200"
                      :title="__('View Artifacts')"
                    >
                      <i class="fas fa-list"></i>
                    </button>
                    <button 
                      @click="viewCustomer(customer)"
                      class="p-1 rounded-md text-green-600 hover:text-green-900 hover:bg-green-50 transition-colors duration-200"
                      :title="__('View Customer')"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button 
                      @click="editCustomer(customer)"
                      class="p-1 rounded-md text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition-colors duration-200"
                      :title="__('Edit Customer')"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      @click="deleteCustomer(customer)"
                      class="p-1 rounded-md text-red-600 hover:text-red-900 hover:bg-red-50 transition-colors duration-200"
                      :title="__('Delete Customer')"
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
                  
                  <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Basic Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                      </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Contact Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            {{ __('Phone Number') }}
                          </label>
                          <input
                            v-model="newCustomer.phone_number"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Secondary Phone') }}
                          </label>
                          <input
                            v-model="newCustomer.secondary_phone_number"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Website') }}
                          </label>
                          <input
                            v-model="newCustomer.website"
                            type="url"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Address Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Address Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Address') }}
                          </label>
                          <textarea
                            v-model="newCustomer.address"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          ></textarea>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('City') }}
                          </label>
                          <input
                            v-model="newCustomer.city"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('State') }}
                          </label>
                          <input
                            v-model="newCustomer.state"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Postal Code') }}
                          </label>
                          <input
                            v-model="newCustomer.postal_code"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Country') }}
                          </label>
                          <input
                            v-model="newCustomer.country"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Tax and Legal Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Tax and Legal Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Tax Number') }}
                          </label>
                          <input
                            v-model="newCustomer.tax_number"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Commercial Registration') }}
                          </label>
                          <input
                            v-model="newCustomer.commercial_registration_number"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Business Settings -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Business Settings') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Status') }}
                          </label>
                          <select
                            v-model="newCustomer.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="Active">{{ __('Active') }}</option>
                            <option value="Inactive">{{ __('Inactive') }}</option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Credit Limit') }}
                          </label>
                          <input
                            v-model="newCustomer.credit_limit"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                      
                      <div class="mt-4 space-y-3">
                        <div class="flex items-center">
                          <input
                            v-model="newCustomer.pos"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('POS Enabled') }}
                          </label>
                        </div>
                        
                        <div class="flex items-center">
                          <input
                            v-model="newCustomer.government_entity"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('Government Entity') }}
                          </label>
                        </div>
                        
                        <div class="flex items-center">
                          <input
                            v-model="newCustomer.allow_credit"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('Allow Credit') }}
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Additional Information') }}</h4>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">
                          {{ __('Notes') }}
                        </label>
                        <textarea
                          v-model="newCustomer.notes"
                          rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        ></textarea>
                      </div>
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

    <!-- Edit Customer Modal -->
    <div v-if="showEditCustomerModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditCustomerModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="updateCustomer">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ __('Edit Customer') }}
                  </h3>
                  
                  <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Basic Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Customer Name') }} *
                          </label>
                          <input
                            v-model="editCustomerData.name"
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
                            v-model="editCustomerData.organization"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Contact Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Email') }}
                          </label>
                          <input
                            v-model="editCustomerData.email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Phone Number') }}
                          </label>
                          <input
                            v-model="editCustomerData.phone_number"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Secondary Phone') }}
                          </label>
                          <input
                            v-model="editCustomerData.secondary_phone_number"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Website') }}
                          </label>
                          <input
                            v-model="editCustomerData.website"
                            type="url"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Address Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Address Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Address') }}
                          </label>
                          <textarea
                            v-model="editCustomerData.address"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          ></textarea>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('City') }}
                          </label>
                          <input
                            v-model="editCustomerData.city"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('State') }}
                          </label>
                          <input
                            v-model="editCustomerData.state"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Postal Code') }}
                          </label>
                          <input
                            v-model="editCustomerData.postal_code"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Country') }}
                          </label>
                          <input
                            v-model="editCustomerData.country"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Tax and Legal Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Tax and Legal Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Tax Number') }}
                          </label>
                          <input
                            v-model="editCustomerData.tax_number"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Commercial Registration') }}
                          </label>
                          <input
                            v-model="editCustomerData.commercial_registration_number"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                    </div>

                    <!-- Business Settings -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Business Settings') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Status') }}
                          </label>
                          <select
                            v-model="editCustomerData.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="Active">{{ __('Active') }}</option>
                            <option value="Inactive">{{ __('Inactive') }}</option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Credit Limit') }}
                          </label>
                          <input
                            v-model="editCustomerData.credit_limit"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                      </div>
                      
                      <div class="mt-4 space-y-3">
                        <div class="flex items-center">
                          <input
                            v-model="editCustomerData.pos"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('POS Enabled') }}
                          </label>
                        </div>
                        
                        <div class="flex items-center">
                          <input
                            v-model="editCustomerData.government_entity"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('Government Entity') }}
                          </label>
                        </div>
                        
                        <div class="flex items-center">
                          <input
                            v-model="editCustomerData.allow_credit"
                            type="checkbox"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                          />
                          <label class="ml-2 block text-sm text-gray-900">
                            {{ __('Allow Credit') }}
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Additional Information') }}</h4>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">
                          {{ __('Notes') }}
                        </label>
                        <textarea
                          v-model="editCustomerData.notes"
                          rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        ></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="editingCustomer"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <i v-if="editingCustomer" class="fas fa-spinner fa-spin mr-2"></i>
                {{ editingCustomer ? __('Updating...') : __('Update Customer') }}
              </button>
              <button
                type="button"
                @click="showEditCustomerModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                {{ __('Cancel') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Customer Modal -->
    <div v-if="showViewCustomerModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showViewCustomerModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ __('Customer Details') }}
                </h3>
                
                <div v-if="selectedCustomer" class="space-y-6">
                  <!-- Basic Information -->
                  <div class="border-b border-gray-200 pb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Basic Information') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Reference Number') }}</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ formatQoyodReferenceNumber(selectedCustomer.id) }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Customer Name') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.name || selectedCustomer.display_name || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Organization Name') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.organization || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <span 
                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
                          :class="(selectedCustomer.status && selectedCustomer.status.toLowerCase() === 'active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        >
                          {{ (selectedCustomer.status && selectedCustomer.status.toLowerCase() === 'active') ? __('Active') : __('Inactive') }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Contact Information -->
                  <div class="border-b border-gray-200 pb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Contact Information') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.email || selectedCustomer.email_address || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.phone_number || selectedCustomer.phone || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Secondary Phone') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.secondary_phone_number || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Website') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.website || '-' }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Address Information -->
                  <div class="border-b border-gray-200 pb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Address Information') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.address || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('City') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.city || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('State') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.state || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Postal Code') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.postal_code || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.country || '-' }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Tax and Legal Information -->
                  <div class="border-b border-gray-200 pb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Tax and Legal Information') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Tax Number') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.tax_number || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Commercial Registration') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.commercial_registration_number || '-' }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Business Settings -->
                  <div class="border-b border-gray-200 pb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Business Settings') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Credit Limit') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.credit_limit || '-' }}</p>
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Created Date') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedCustomer.created_at) }}</p>
                      </div>
                    </div>
                    
                    <div class="mt-4 space-y-2">
                      <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">{{ __('POS Enabled') }}:</span>
                        <span class="text-sm" :class="selectedCustomer.pos ? 'text-green-600' : 'text-gray-500'">
                          {{ selectedCustomer.pos ? __('Yes') : __('No') }}
                        </span>
                      </div>
                      <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">{{ __('Government Entity') }}:</span>
                        <span class="text-sm" :class="selectedCustomer.government_entity ? 'text-green-600' : 'text-gray-500'">
                          {{ selectedCustomer.government_entity ? __('Yes') : __('No') }}
                        </span>
                      </div>
                      <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-2">{{ __('Allow Credit') }}:</span>
                        <span class="text-sm" :class="selectedCustomer.allow_credit ? 'text-green-600' : 'text-gray-500'">
                          {{ selectedCustomer.allow_credit ? __('Yes') : __('No') }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Additional Information -->
                  <div>
                    <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Additional Information') }}</h4>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                      <p class="mt-1 text-sm text-gray-900">{{ selectedCustomer.notes || '-' }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showViewCustomerModal = false"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ __('Close') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Artifact Modal -->
    <div v-if="showAddArtifactModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddArtifactModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="submitArtifact">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ __('Add Artifact for Customer') }}: {{ selectedCustomerForArtifact?.name || selectedCustomerForArtifact?.display_name }}
                  </h3>
                  
                  <div class="space-y-6">
                    <!-- Artifact Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">{{ __('Artifact Information') }}</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Type') }} *
                          </label>
                          <select
                            v-model="newArtifact.type"
                            @change="resetServiceWhenTypeChanges"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>{{ __('Select Type') }}</option>
                            <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Service') }} *
                          </label>
                          <select
                            v-model="newArtifact.service"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>{{ __('Select Service') }}</option>
                            <option v-for="option in getServiceOptions(newArtifact.type)" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Weight') }} *
                          </label>
                          <div class="flex space-x-2">
                            <input
                              v-model="newArtifact.weight"
                              type="number"
                              step="0.01"
                              required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                              placeholder="0.00"
                            />
                            <select
                              v-model="newArtifact.weight_unit"
                              class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            >
                              <option v-for="option in weightUnitOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                              </option>
                            </select>
                          </div>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Delivery Type') }}
                          </label>
                          <select
                            v-model="newArtifact.delivery_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>{{ __('Select Delivery Type') }}</option>
                            <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Notes') }}
                          </label>
                          <textarea
                            v-model="newArtifact.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            :placeholder="__('Any additional notes about the artifact...')"
                          ></textarea>
                        </div>
                        
                        <div v-if="newArtifact.type && newArtifact.service && newArtifact.weight">
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Calculated Price') }}
                          </label>
                          <div class="flex items-center space-x-2 mt-1">
                            <input
                              v-model="newArtifact.price"
                              type="text"
                              readonly
                              class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                              placeholder="0.00"
                            />
                            <button
                              type="button"
                              @click="calculatePriceForNewArtifact"
                              class="px-3 py-2 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                              {{ __('Calculate') }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="addingArtifact"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <i v-if="addingArtifact" class="fas fa-spinner fa-spin mr-2"></i>
                {{ addingArtifact ? __('Adding...') : __('Add Artifact') }}
              </button>
              <button
                type="button"
                @click="showAddArtifactModal = false"
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
      showEditCustomerModal: false,
      showViewCustomerModal: false,
      editingCustomer: false,
      selectedCustomer: null,
      newCustomer: {
        name: '',
        organization: '',
        email: '',
        phone_number: '',
        secondary_phone_number: '',
        website: '',
        address: '',
        city: '',
        state: '',
        postal_code: '',
        country: '',
        tax_number: '',
        commercial_registration_number: '',
        status: 'Active',
        credit_limit: '',
        pos: false,
        government_entity: false,
        allow_credit: false,
        notes: ''
      },
      editCustomerData: {
        name: '',
        organization: '',
        email: '',
        phone_number: '',
        secondary_phone_number: '',
        website: '',
        address: '',
        city: '',
        state: '',
        postal_code: '',
        country: '',
        tax_number: '',
        commercial_registration_number: '',
        status: 'Active',
        credit_limit: '',
        pos: false,
        government_entity: false,
        allow_credit: false,
        notes: ''
      },
      // Artifact modal data
      showAddArtifactModal: false,
      addingArtifact: false,
      selectedCustomerForArtifact: null,
      newArtifact: {
        type: '',
        service: '',
        weight: '',
        weight_unit: 'ct',
        delivery_type: '',
        notes: '',
        price: ''
      },
      // Artifact options (copied from NewClient.vue)
      typeOptions: [
        { value: 'Colored Gemstones', label: this.$page.props.locale === 'ar' ? 'أحجار كريمة ملونة' : 'Colored Gemstones' },
        { value: 'Other Colored Gemstones', label: this.$page.props.locale === 'ar' ? 'أحجار كريمة ملونة أخرى' : 'Other Colored Gemstones' },
        { value: 'Colorless Diamonds', label: this.$page.props.locale === 'ar' ? 'ألماس عديم اللون' : 'Colorless Diamonds' },
        { value: 'Jewellery', label: this.$page.props.locale === 'ar' ? 'مجوهرات' : 'Jewellery' },
      ],
      weightUnitOptions: [
        { value: 'ct', label: this.$page.props.locale === 'ar' ? 'قيراط' : 'ct' },
        { value: 'gm', label: this.$page.props.locale === 'ar' ? 'جرام' : 'gm' },
      ],
      deliveryOptions: [
        { value: 'Regular', label: this.$page.props.locale === 'ar' ? 'عادي' : 'Regular' },
        { value: 'Express Service', label: this.$page.props.locale === 'ar' ? 'خدمة سريعة' : 'Express Service' },
        { value: 'Same Day', label: this.$page.props.locale === 'ar' ? 'نفس اليوم' : 'Same Day' },
        { value: '24 hours', label: this.$page.props.locale === 'ar' ? '24 ساعة' : '24 hours' },
        { value: '48 hours', label: this.$page.props.locale === 'ar' ? '48 ساعة' : '48 hours' },
        { value: '72 hours', label: this.$page.props.locale === 'ar' ? '72 ساعة' : '72 hours' },
      ]
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
        this.newCustomer = {
          name: '',
          organization: '',
          email: '',
          phone_number: '',
          secondary_phone_number: '',
          website: '',
          address: '',
          city: '',
          state: '',
          postal_code: '',
          country: '',
          tax_number: '',
          commercial_registration_number: '',
          status: 'Active',
          credit_limit: '',
          pos: false,
          government_entity: false,
          allow_credit: false,
          notes: ''
        }
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
      this.selectedCustomer = customer
      this.showViewCustomerModal = true
    },
    
    editCustomer(customer) {
      this.selectedCustomer = customer
      // Copy customer data to edit form
      this.editCustomerData = {
        name: customer.name || customer.display_name || '',
        organization: customer.organization || '',
        email: customer.email || customer.email_address || '',
        phone_number: customer.phone_number || customer.phone || '',
        secondary_phone_number: customer.secondary_phone_number || '',
        website: customer.website || '',
        address: customer.address || '',
        city: customer.city || '',
        state: customer.state || '',
        postal_code: customer.postal_code || '',
        country: customer.country || '',
        tax_number: customer.tax_number || '',
        commercial_registration_number: customer.commercial_registration_number || '',
        status: customer.status || 'Active',
        credit_limit: customer.credit_limit || '',
        pos: customer.pos || false,
        government_entity: customer.government_entity || false,
        allow_credit: customer.allow_credit || false,
        notes: customer.notes || ''
      }
      this.showEditCustomerModal = true
    },
    
    async updateCustomer() {
      this.editingCustomer = true
      try {
        this.$inertia.put(`/dashboard/customers/${this.selectedCustomer.id}`, this.editCustomerData, {
          onSuccess: () => {
            this.showEditCustomerModal = false
            this.selectedCustomer = null
          },
          onError: (errors) => {
            console.error('Error updating customer:', errors)
          }
        })
      } catch (error) {
        console.error('Error updating customer:', error)
      } finally {
        this.editingCustomer = false
      }
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
    
    // Artifact functions
    viewCustomerArtifacts(customer) {
      // Navigate to customer artifacts page
      this.$inertia.visit(`/dashboard/customers/${customer.id}/artifacts`)
    },
    
    addArtifactToCustomer(customer) {
      this.selectedCustomerForArtifact = customer
      this.newArtifact = {
        type: '',
        service: '',
        weight: '',
        weight_unit: 'ct',
        delivery_type: '',
        notes: '',
        price: ''
      }
      this.showAddArtifactModal = true
    },
    
    getServiceOptions(artifactType) {
      const locale = this.$page.props.locale || 'en'
      const allServices = [
        { value: 'Regular - ID Report', label: locale === 'ar' ? 'عادي - تقرير هوية' : 'Regular - ID Report' },
        { value: 'Regular - ID + Origin', label: locale === 'ar' ? 'عادي - هوية + أصل' : 'Regular - ID + Origin' },
        { value: 'Mini Card Report - ID Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير هوية' : 'Mini Card Report - ID Report' },
        { value: 'Mini Card Report - ID + Origin', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - هوية + أصل' : 'Mini Card Report - ID + Origin' },
        { value: 'Regular - Diamond Grading Report', label: locale === 'ar' ? 'عادي - تقرير تصنيف الألماس' : 'Regular - Diamond Grading Report' },
        { value: 'Mini Card Report - Mini Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مصغر' : 'Mini Card Report - Mini Report' },
        { value: 'Regular - Jewellery Report', label: locale === 'ar' ? 'عادي - تقرير المجوهرات' : 'Regular - Jewellery Report' },
        { value: 'Mini Card Report - Mini Jewellery Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مجوهرات مصغر' : 'Mini Card Report - Mini Jewellery Report' },
      ]

      // Filter services based on artifact type
      switch (artifactType) {
        case 'Colored Gemstones':
          return allServices.filter(service => 
            service.value.includes('ID Report') || service.value.includes('ID + Origin')
          )
        case 'Other Colored Gemstones':
          return allServices.filter(service => 
            service.value.includes('ID Report') && !service.value.includes('ID + Origin')
          )
        case 'Colorless Diamonds':
          return allServices.filter(service => 
            service.value.includes('Diamond Grading Report') || service.value.includes('Mini Report')
          )
        case 'Jewellery':
          return allServices.filter(service => 
            service.value.includes('Jewellery Report')
          )
        default:
          return []
      }
    },
    
    resetServiceWhenTypeChanges() {
      const availableServices = this.getServiceOptions(this.newArtifact.type)
      const currentServiceExists = availableServices.some(service => service.value === this.newArtifact.service)
      
      if (!currentServiceExists) {
        this.newArtifact.service = ''
      }
      
      // Update weight unit based on type
      if (this.newArtifact.type === 'Jewellery') {
        this.newArtifact.weight_unit = 'gm'
      } else {
        this.newArtifact.weight_unit = 'ct'
      }
    },
    
    async calculatePriceForNewArtifact() {
      if (!this.newArtifact.type || !this.newArtifact.service || !this.newArtifact.weight) {
        return
      }

      try {
        const csrfToken = this.$page.props.csrf_token || 
                         document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value

        const response = await fetch('/reception/calculate-price', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            type: this.newArtifact.type,
            service: this.newArtifact.service,
            weight: parseFloat(this.newArtifact.weight)
          })
        })

        const data = await response.json()
        const priceValue = parseFloat(data.price)
        
        if (data.price && priceValue && !isNaN(priceValue)) {
          let finalPrice = priceValue

          // Calculate price based on delivery type
          if (this.newArtifact.delivery_type === 'Same Day') {
            finalPrice = priceValue * 2
          } else if (this.newArtifact.delivery_type === '48 hours') {
            finalPrice = priceValue * 0.7
          } else if (this.newArtifact.delivery_type === '72 hours') {
            finalPrice = priceValue * 0.5
          }

          this.newArtifact.price = finalPrice.toFixed(2)
        } else {
          this.newArtifact.price = 'N/A'
        }
      } catch (error) {
        console.error('Error calculating price:', error)
        this.newArtifact.price = 'Error'
        alert(this.__('Error calculating price. Please try again.'))
      }
    },
    
    async submitArtifact() {
      this.addingArtifact = true
      try {
        // Validate required fields
        if (!this.newArtifact.type || !this.newArtifact.service || !this.newArtifact.weight) {
          alert(this.__('Please fill in all required fields (Type, Service, Weight)'))
          return
        }

        // Submit artifact to Laravel backend
        this.$inertia.post('/dashboard/customers/artifacts', {
          client_id: this.selectedCustomerForArtifact.id,
          type: this.newArtifact.type,
          service: this.newArtifact.service,
          weight: this.newArtifact.weight,
          weight_unit: this.newArtifact.weight_unit,
          delivery_type: this.newArtifact.delivery_type,
          notes: this.newArtifact.notes
        }, {
          onSuccess: () => {
            this.showAddArtifactModal = false
            this.selectedCustomerForArtifact = null
            // Reset form
            this.newArtifact = {
              type: '',
              service: '',
              weight: '',
              weight_unit: 'ct',
              delivery_type: '',
              notes: '',
              price: ''
            }
          },
          onError: (errors) => {
            console.error('Error adding artifact:', errors)
            alert(this.__('Error adding artifact. Please try again.'))
          }
        })
      } catch (error) {
        console.error('Error adding artifact:', error)
        alert(this.__('Error adding artifact. Please try again.'))
      } finally {
        this.addingArtifact = false
      }
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
          'Are you sure you want to delete this customer?': 'Are you sure you want to delete this customer?',
          'Basic Information': 'Basic Information',
          'Contact Information': 'Contact Information',
          'Address Information': 'Address Information',
          'Tax and Legal Information': 'Tax and Legal Information',
          'Business Settings': 'Business Settings',
          'Additional Information': 'Additional Information',
          'Phone Number': 'Phone Number',
          'Secondary Phone': 'Secondary Phone',
          'Website': 'Website',
          'Address': 'Address',
          'City': 'City',
          'State': 'State',
          'Postal Code': 'Postal Code',
          'Country': 'Country',
          'Tax Number': 'Tax Number',
          'Commercial Registration': 'Commercial Registration',
          'Credit Limit': 'Credit Limit',
          'POS Enabled': 'POS Enabled',
          'Government Entity': 'Government Entity',
          'Allow Credit': 'Allow Credit',
          'Notes': 'Notes',
          'Edit Customer': 'Edit Customer',
          'Update Customer': 'Update Customer',
          'Updating...': 'Updating...',
          'Customer Details': 'Customer Details',
          'Close': 'Close',
          'Created Date': 'Created Date',
          'Yes': 'Yes',
          'No': 'No',
          // Artifact translations
          'Add Artifact': 'Add Artifact',
          'Add Artifact for Customer': 'Add Artifact for Customer',
          'Artifact Information': 'Artifact Information',
          'Type': 'Type',
          'Service': 'Service',
          'Weight': 'Weight',
          'Delivery Type': 'Delivery Type',
          'Notes': 'Notes',
          'Select Type': 'Select Type',
          'Select Service': 'Select Service',
          'Select Delivery Type': 'Select Delivery Type',
          'Calculated Price': 'Calculated Price',
          'Calculate': 'Calculate',
          'Any additional notes about the artifact...': 'Any additional notes about the artifact...',
          'Please fill in all required fields (Type, Service, Weight)': 'Please fill in all required fields (Type, Service, Weight)',
          'Error calculating price. Please try again.': 'Error calculating price. Please try again.',
          'Error adding artifact. Please try again.': 'Error adding artifact. Please try again.',
          'View Artifacts': 'View Artifacts'
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
          'Are you sure you want to delete this customer?': 'هل أنت متأكد من حذف هذا العميل؟',
          'Basic Information': 'المعلومات الأساسية',
          'Contact Information': 'معلومات الاتصال',
          'Address Information': 'معلومات العنوان',
          'Tax and Legal Information': 'المعلومات الضريبية والقانونية',
          'Business Settings': 'إعدادات العمل',
          'Additional Information': 'معلومات إضافية',
          'Phone Number': 'رقم الهاتف',
          'Secondary Phone': 'الهاتف الثانوي',
          'Website': 'الموقع الإلكتروني',
          'Address': 'العنوان',
          'City': 'المدينة',
          'State': 'المنطقة',
          'Postal Code': 'الرمز البريدي',
          'Country': 'البلد',
          'Tax Number': 'الرقم الضريبي',
          'Commercial Registration': 'السجل التجاري',
          'Credit Limit': 'حد الائتمان',
          'POS Enabled': 'نقاط البيع مفعلة',
          'Government Entity': 'جهة حكومية',
          'Allow Credit': 'السماح بالائتمان',
          'Notes': 'ملاحظات',
          'Edit Customer': 'تعديل العميل',
          'Update Customer': 'تحديث العميل',
          'Updating...': 'جاري التحديث...',
          'Customer Details': 'تفاصيل العميل',
          'Close': 'إغلاق',
          'Created Date': 'تاريخ الإنشاء',
          'Yes': 'نعم',
          'No': 'لا',
          // Artifact translations
          'Add Artifact': 'إضافة قطعة',
          'Add Artifact for Customer': 'إضافة قطعة للعميل',
          'Artifact Information': 'معلومات القطعة',
          'Type': 'النوع',
          'Service': 'الخدمة',
          'Weight': 'الوزن',
          'Delivery Type': 'نوع التسليم',
          'Notes': 'ملاحظات',
          'Select Type': 'اختر النوع',
          'Select Service': 'اختر الخدمة',
          'Select Delivery Type': 'اختر نوع التسليم',
          'Calculated Price': 'السعر المحسوب',
          'Calculate': 'حساب',
          'Any additional notes about the artifact...': 'أي ملاحظات إضافية عن القطعة...',
          'Please fill in all required fields (Type, Service, Weight)': 'يرجى ملء جميع الحقول المطلوبة (النوع، الخدمة، الوزن)',
          'Error calculating price. Please try again.': 'خطأ في حساب السعر. يرجى المحاولة مرة أخرى.',
          'Error adding artifact. Please try again.': 'خطأ في إضافة القطعة. يرجى المحاولة مرة أخرى.',
          'View Artifacts': 'عرض القطع',
          'View Customer': 'عرض العميل',
          'Edit Customer': 'تحرير العميل',
          'Delete Customer': 'حذف العميل'
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
