<template>
  <DashboardLayout :pageTitle="__('Customer Artifacts')">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Artifacts for') }}: {{ customer?.name || customer?.display_name }}
            </h2>
            <p class="text-gray-600">
              {{ __('View all artifacts added for this customer from Qoyod') }}
            </p>
          </div>
          <div class="flex space-x-4">
            <button
              @click="createInvoice"
              class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
            >
              <i class="fas fa-file-alt mr-2"></i>
              {{ __('Create Invoice') }}
            </button>
            <button
              @click="viewInvoices"
              class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
            >
              <i class="fas fa-list mr-2"></i>
              {{ __('View Invoices') }}
            </button>
            <button
              @click="createQuote"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <i class="fas fa-file-invoice mr-2"></i>
              {{ __('Create Quote') }}
            </button>
            <button
              @click="viewQuotes"
              class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 mr-3"
            >
              <i class="fas fa-list mr-2"></i>
              {{ __('View Quotes') }}
            </button>
            <button
              @click="createNewTestRequest"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <i class="fas fa-plus-circle mr-2"></i>
              {{ __('New Test Request') }}
            </button>
            <button
              @click="viewAllTestRequests"
              class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
            >
              <i class="fas fa-list-alt mr-2"></i>
              طلبات الاختبار
            </button>
            <button
              @click="addArtifact"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <i class="fas fa-plus mr-2"></i>
              {{ __('Add Artifact') }}
            </button>
            <button
              @click="goBack"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              {{ __('Back to Customers') }}
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
            <p class="mt-1 text-sm text-gray-900 font-mono" dir="ltr">{{ customer?.id ? formatQoyodReferenceNumber(customer.id) : 'CUS000' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Customer Name') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Organization') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.organization || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
            <p class="mt-1 text-sm text-gray-900" dir="ltr">{{ customer?.email || customer?.email_address || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
            <p class="mt-1 text-sm text-gray-900" dir="ltr">{{ customer?.phone_number || customer?.phone || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
            <span 
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
              :class="(customer?.status && customer?.status.toLowerCase() === 'active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              {{ (customer?.status && customer?.status.toLowerCase() === 'active') ? __('Active') : __('Inactive') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Artifacts Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">
            {{ __('Artifacts') }} ({{ artifacts.length }})
          </h3>
        </div>

        <div v-if="artifacts.length === 0" class="px-6 py-12 text-center">
          <i class="fas fa-gem text-gray-400 text-4xl mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Artifacts Found') }}</h3>
          <p class="text-gray-500 mb-4">{{ __('This customer has no artifacts yet.') }}</p>
          <button
            @click="addArtifact"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            <i class="fas fa-plus mr-2"></i>
            {{ __('Add First Artifact') }}
          </button>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Artifact Code') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Type') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Service') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Weight') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Price') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Delivery Type') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Status') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Created') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ __('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="artifact in artifacts" :key="artifact.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                  {{ artifact.artifact_code }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ getFullType(artifact) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ artifact.service || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ artifact.weight ? `${artifact.weight} ${artifact.weight_unit}` : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ artifact.price ? `${artifact.price} SAR` : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ artifact.delivery_type || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(artifact.status)"
                  >
                    {{ getStatusLabel(artifact.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(artifact.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button 
                      @click="editArtifact(artifact)"
                      class="p-1 rounded-md text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition-colors duration-200"
                      :title="__('Edit')"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      @click="deleteArtifact(artifact)"
                      class="p-1 rounded-md text-red-600 hover:text-red-900 hover:bg-red-50 transition-colors duration-200"
                      :title="__('Delete')"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Edit Artifact Modal -->
    <div v-if="showEditArtifactModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditArtifactModal = false"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="updateArtifact">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ __('Edit Artifact') }}: {{ selectedArtifact?.artifact_code }}
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
                            v-model="editArtifactData.type"
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
                            v-model="editArtifactData.service"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>{{ __('Select Service') }}</option>
                            <option v-for="option in getServiceOptions(editArtifactData.type)" :key="option.value" :value="option.value">
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
                              v-model="editArtifactData.weight"
                              type="number"
                              step="0.01"
                              required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                              placeholder="0.00"
                            />
                            <select
                              v-model="editArtifactData.weight_unit"
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
                            v-model="editArtifactData.delivery_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>{{ __('Select Delivery Type') }}</option>
                            <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Price') }}
                          </label>
                          <input
                            v-model="editArtifactData.price"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="0.00"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Status') }}
                          </label>
                          <select
                            v-model="editArtifactData.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="under_evaluation">{{ __('Under Evaluation') }}</option>
                            <option value="evaluated">{{ __('Evaluated') }}</option>
                            <option value="certified">{{ __('Certified') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                          </select>
                        </div>
                        
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-700">
                            {{ __('Notes') }}
                          </label>
                          <textarea
                            v-model="editArtifactData.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            :placeholder="__('Any additional notes about the artifact...')"
                          ></textarea>
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
                :disabled="updatingArtifact"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <i v-if="updatingArtifact" class="fas fa-spinner fa-spin mr-2"></i>
                {{ updatingArtifact ? __('Updating...') : __('Update Artifact') }}
              </button>
              <button
                type="button"
                @click="showEditArtifactModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                {{ __('Cancel') }}
              </button>
            </div>
          </form>
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
                            {{ __('Subtype') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                          </label>
                          <input
                            v-model="newArtifact.subtype"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            :placeholder="__('Enter subtype')"
                          />
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
                      </div>
                    </div>
                    
                    <!-- Price Calculation -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                          {{ __('Calculated Price') }}:
                        </span>
                        <span class="text-lg font-semibold text-green-600">
                          {{ newArtifact.price ? newArtifact.price + ' SAR' : __('Please calculate') }}
                        </span>
                      </div>
                      <div class="mt-2">
                        <button
                          @click="calculatePrice"
                          type="button"
                          :disabled="addingArtifact || !newArtifact.type || !newArtifact.service || !newArtifact.weight"
                          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          {{ __('Calculate') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="addingArtifact || !newArtifact.type || !newArtifact.service || !newArtifact.weight"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <i v-if="addingArtifact" class="fas fa-spinner fa-spin mr-2"></i>
                {{ addingArtifact ? __('Adding...') : __('Add Artifact') }}
              </button>
              <button
                @click="showAddArtifactModal = false"
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
    customer: {
      type: Object,
      required: true
    },
    artifacts: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      showEditArtifactModal: false,
      showAddArtifactModal: false,
      updatingArtifact: false,
      addingArtifact: false,
      selectedArtifact: null,
      selectedCustomerForArtifact: null,
      editArtifactData: {
        type: '',
        service: '',
        weight: '',
        weight_unit: 'ct',
        delivery_type: '',
        price: '',
        status: 'pending',
        notes: ''
      },
      newArtifact: {
        type: '',
        subtype: '',
        service: '',
        weight: '',
        weight_unit: 'ct',
        delivery_type: '',
        notes: '',
        price: ''
      },
      // Artifact options
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
    console.log('Artifacts component mounted')
    console.log('Customer data:', this.customer)
    console.log('Artifacts data:', this.artifacts)
  },
  methods: {
    getFullType(artifact) {
      if (!artifact.type) return '-';
      return artifact.subtype ? `${artifact.type} - ${artifact.subtype}` : artifact.type;
    },
    
    goBack() {
      // Return to the customers list page
      console.log('goBack clicked - navigating to customers list')
      this.$inertia.visit('/dashboard/customers')
    },
    
    addArtifact() {
      console.log('addArtifact clicked, customer ID:', this.customer?.id)
      if (this.customer?.id) {
        this.selectedCustomerForArtifact = this.customer
        this.showAddArtifactModal = true
      } else {
        console.log('No customer ID available')
      }
    },
    
      createQuote() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/create-quote`)
        }
      },

      viewQuotes() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/quotes`)
        }
      },

      viewInvoices() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/invoices`)
        }
      },

      createInvoice() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/create-invoice`)
        }
      },

      createNewTestRequest() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-requests/create`)
        }
      },

      viewAllTestRequests() {
        if (this.customer?.id) {
          this.$inertia.visit(`/dashboard/customers/${this.customer.id}/test-requests`)
        }
      },
    
    editArtifact(artifact) {
      this.selectedArtifact = artifact
      this.editArtifactData = {
        type: artifact.type || '',
        service: artifact.service || '',
        weight: artifact.weight || '',
        weight_unit: artifact.weight_unit || 'ct',
        delivery_type: artifact.delivery_type || '',
        price: artifact.price || '',
        status: artifact.status || 'pending',
        notes: artifact.notes || ''
      }
      this.showEditArtifactModal = true
    },
    
    async updateArtifact() {
      this.updatingArtifact = true
      try {
        this.$inertia.put(`/dashboard/artifacts/${this.selectedArtifact.id}`, this.editArtifactData, {
          onFinish: () => {
            this.updatingArtifact = false
            this.showEditArtifactModal = false
            this.selectedArtifact = null
          }
        })
      } catch (error) {
        console.error('Error updating artifact:', error)
        alert(this.__('Error updating artifact. Please try again.'))
        this.updatingArtifact = false
      }
    },
    
    deleteArtifact(artifact) {
      if (confirm(this.__('Are you sure you want to delete this artifact?'))) {
        this.$inertia.delete(`/dashboard/artifacts/${artifact.id}`)
      }
    },
    
    resetServiceWhenTypeChanges() {
      this.newArtifact.service = ''
    },
    
    getServiceOptions(type) {
      const serviceOptions = {
        'Colored Gemstones': [
          { value: 'Regular - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عادي' : 'Regular - ID Report' },
          { value: 'Express - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عاجل' : 'Express - ID Report' },
          { value: 'Standard - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة معيارية' : 'Standard - Certification' },
          { value: 'Express - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة عاجلة' : 'Express - Certification' }
        ],
        'Other Colored Gemstones': [
          { value: 'Regular - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عادي' : 'Regular - ID Report' },
          { value: 'Express - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عاجل' : 'Express - ID Report' },
          { value: 'Standard - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة معيارية' : 'Standard - Certification' },
          { value: 'Express - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة عاجلة' : 'Express - Certification' }
        ],
        'Colorless Diamonds': [
          { value: 'Regular - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عادي' : 'Regular - ID Report' },
          { value: 'Express - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عاجل' : 'Express - ID Report' },
          { value: 'Standard - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة معيارية' : 'Standard - Certification' },
          { value: 'Express - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة عاجلة' : 'Express - Certification' }
        ],
        'Jewellery': [
          { value: 'Regular - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عادي' : 'Regular - ID Report' },
          { value: 'Express - ID Report', label: this.$page.props.locale === 'ar' ? 'تقرير معرف عاجل' : 'Express - ID Report' },
          { value: 'Standard - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة معيارية' : 'Standard - Certification' },
          { value: 'Express - Certification', label: this.$page.props.locale === 'ar' ? 'شهادة عاجلة' : 'Express - Certification' }
        ]
      }
      
      return serviceOptions[type] || []
    },
    
    async calculatePrice() {
      try {
        const response = await fetch('/reception/calculate-price', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            type: this.newArtifact.type,
            service: this.newArtifact.service,
            weight: parseFloat(this.newArtifact.weight) || 0,
            weight_unit: this.newArtifact.weight_unit
          })
        })
        
        const data = await response.json()
        
        if (data.price) {
          const priceValue = parseFloat(data.price)
          
          if (!isNaN(priceValue)) {
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
        } else {
          this.newArtifact.price = 'N/A'
          alert(this.__('Error calculating price. Please try again.'))
        }
      } catch (error) {
        console.error('Error calculating price:', error)
        alert(this.__('Error calculating price. Please try again.'))
      }
    },
    
    async submitArtifact() {
      // Validate required fields
      if (!this.newArtifact.type || !this.newArtifact.service || !this.newArtifact.weight) {
        alert(this.__('Please fill in all required fields (Type, Service, Weight)'))
        return
      }

      this.addingArtifact = true
      
      try {
        // Submit artifact to Laravel backend
        this.$inertia.post('/dashboard/customers/artifacts', {
          client_id: this.selectedCustomerForArtifact.id,
          type: this.newArtifact.type,
          subtype: this.newArtifact.subtype,
          service: this.newArtifact.service,
          weight: this.newArtifact.weight,
          weight_unit: this.newArtifact.weight_unit,
          delivery_type: this.newArtifact.delivery_type,
          notes: this.newArtifact.notes,
          price: this.newArtifact.price
        }, {
          onSuccess: (page) => {
            this.showAddArtifactModal = false
            this.selectedCustomerForArtifact = null
            // Reset form
            this.newArtifact = {
              type: '',
              subtype: '',
              service: '',
              weight: '',
              weight_unit: 'ct',
              delivery_type: '',
              notes: '',
              price: ''
            }
            // No need for manual reload, Inertia will handle the redirect
          },
          onError: (errors) => {
            console.error('Error adding artifact:', errors)
            alert(this.__('Error adding artifact. Please try again.'))
          }
        })
      } catch (error) {
        console.error('Error adding artifact:', error)
      } finally {
        this.addingArtifact = false
      }
    },
    
    formatDate(dateString) {
      if (!dateString) return '-'
      return new Date(dateString).toLocaleDateString()
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
        'rejected': this.__('Rejected')
      }
      return labels[status] || status
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
      const availableServices = this.getServiceOptions(this.editArtifactData.type)
      const currentServiceExists = availableServices.some(service => service.value === this.editArtifactData.service)
      
      if (!currentServiceExists) {
        this.editArtifactData.service = ''
      }
      
      if (this.editArtifactData.type === 'Jewellery') {
        this.editArtifactData.weight_unit = 'gm'
      } else {
        this.editArtifactData.weight_unit = 'ct'
      }
    },
    
    __(key) {
      const translations = {
        en: {
          'Customer Artifacts': 'Customer Artifacts',
          'Artifacts for': 'Artifacts for',
          'View all artifacts added for this customer from Qoyod': 'View all artifacts added for this customer from Qoyod',
          'Back to Customers': 'Back to Customers',
          'Add Artifact': 'Add Artifact',
          'View Quotes': 'View Quotes',
          'Create Quote': 'Create Quote',
          'View Invoices': 'View Invoices',
          'Create Invoice': 'Create Invoice',
          'New Test Request': 'New Test Request',
          'Signed Test Requests': 'Signed Test Requests',
          'Customer Information': 'Customer Information',
          'Reference Number': 'Reference Number',
          'Customer Name': 'Customer Name',
          'Organization': 'Organization',
          'Email': 'Email',
          'Phone': 'Phone',
          'Status': 'Status',
          'Active': 'Active',
          'Inactive': 'Inactive',
          'Artifacts': 'Artifacts',
          'No Artifacts Found': 'No Artifacts Found',
          'This customer has no artifacts yet.': 'This customer has no artifacts yet.',
          'Add First Artifact': 'Add First Artifact',
          'Artifact Code': 'Artifact Code',
          'Type': 'Type',
          'Service': 'Service',
          'Weight': 'Weight',
          'Price': 'Price',
          'Created': 'Created',
          'Actions': 'Actions',
          'View': 'View',
          'Edit': 'Edit',
          'Delete': 'Delete',
          'Are you sure you want to delete this artifact?': 'Are you sure you want to delete this artifact?',
          'Error deleting artifact. Please try again.': 'Error deleting artifact. Please try again.',
          'Edit Artifact': 'Edit Artifact',
          'Updating...': 'Updating...',
          'Update Artifact': 'Update Artifact',
          'Error updating artifact. Please try again.': 'Error updating artifact. Please try again.',
          'Pending': 'Pending',
          'Under Evaluation': 'Under Evaluation',
          'Evaluated': 'Evaluated',
          'Certified': 'Certified',
          'Rejected': 'Rejected',
          'Artifact added successfully!': 'Artifact added successfully!',
          'Artifact Information': 'Artifact Information',
          'Delivery Type': 'Delivery Type',
          'Select Delivery Type': 'Select Delivery Type',
          'Notes': 'Notes',
          'Any additional notes about the artifact...': 'Any additional notes about the artifact...',
          'Calculated Price': 'Calculated Price',
          'Please calculate': 'Please calculate',
          'Calculate': 'Calculate',
          'Adding...': 'Adding...',
          'Cancel': 'Cancel',
          'Add Artifact for Customer': 'Add Artifact for Customer',
          'Select Type': 'Select Type',
          'Select Service': 'Select Service'
        },
        ar: {
          'Customer Artifacts': 'قطع العميل',
          'Artifacts for': 'القطع الخاصة بـ',
          'View all artifacts added for this customer from Qoyod': 'عرض جميع القطع المضافة لهذا العميل من قيود',
          'Back to Customers': 'العودة للعملاء',
          'Add Artifact': 'إضافة قطعة',
          'View Quotes': 'عرض عروض الأسعار',
          'Create Quote': 'إنشاء عرض سعر',
          'View Invoices': 'عرض الفواتير',
          'Create Invoice': 'إنشاء فاتورة',
          'New Test Request': 'طلب اختبار جديد',
          'Signed Test Requests': 'طلبات الاختبار',
          'Customer Information': 'معلومات العميل',
          'Reference Number': 'الرقم المرجعي',
          'Customer Name': 'اسم العميل',
          'Organization': 'المنشأة',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Status': 'الحالة',
          'Active': 'نشط',
          'Inactive': 'غير نشط',
          'Artifacts': 'القطع',
          'No Artifacts Found': 'لم يتم العثور على قطع',
          'This customer has no artifacts yet.': 'هذا العميل لا يملك قطع بعد.',
          'Add First Artifact': 'إضافة أول قطعة',
          'Artifact Code': 'كود القطعة',
          'Type': 'النوع',
          'Service': 'الخدمة',
          'Weight': 'الوزن',
          'Price': 'السعر',
          'Created': 'تاريخ الإنشاء',
          'Actions': 'الإجراءات',
          'View': 'عرض',
          'Edit': 'تعديل',
          'Delete': 'حذف',
          'Are you sure you want to delete this artifact?': 'هل أنت متأكد من حذف هذه القطعة؟',
          'Error deleting artifact. Please try again.': 'خطأ في حذف القطعة. يرجى المحاولة مرة أخرى.',
          'Edit Artifact': 'تعديل القطعة',
          'Updating...': 'جاري التحديث...',
          'Update Artifact': 'تحديث القطعة',
          'Error updating artifact. Please try again.': 'خطأ في تحديث القطعة. يرجى المحاولة مرة أخرى.',
          'Pending': 'في الانتظار',
          'Under Evaluation': 'قيد التقييم',
          'Evaluated': 'تم التقييم',
          'Certified': 'معتمد',
          'Rejected': 'مرفوض',
          'Artifact added successfully!': 'تم إضافة القطعة بنجاح!',
          'Artifact Information': 'معلومات القطعة',
          'Delivery Type': 'نوع التسليم',
          'Select Delivery Type': 'اختر نوع التسليم',
          'Notes': 'ملاحظات',
          'Any additional notes about the artifact...': 'أي ملاحظات إضافية عن القطعة...',
          'Calculated Price': 'السعر المحسوب',
          'Please calculate': 'الرجاء الحساب',
          'Calculate': 'احسب',
          'Adding...': 'جاري الإضافة...',
          'Cancel': 'إلغاء',
          'Add Artifact for Customer': 'إضافة قطعة للعميل',
          'Select Type': 'اختر النوع',
          'Select Service': 'اختر الخدمة'
        }
      }
      
      const locale = this.$page.props.locale || 'en'
      return translations[locale]?.[key] || key
    }
  }
}
</script>
