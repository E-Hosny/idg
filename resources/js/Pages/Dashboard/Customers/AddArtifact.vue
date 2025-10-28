<template>
  <DashboardLayout :pageTitle="__('Add Artifact')">
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
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
                {{ __('Add Artifact') }}
              </h1>
              <p class="text-sm text-gray-500 mt-1">
                {{ __('Add a new artifact for') }} {{ customer?.name || customer?.display_name }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Info Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Customer Information') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Customer ID') }}</div>
            <div class="text-base text-gray-900" dir="ltr">{{ customer?.id || '-' }}</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Name') }}</div>
            <div class="text-base text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Email') }}</div>
            <div class="text-base text-gray-900" dir="ltr">{{ customer?.email || '-' }}</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Phone') }}</div>
            <div class="text-base text-gray-900" dir="ltr">{{ customer?.phone_number || '-' }}</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Organization') }}</div>
            <div class="text-base text-gray-900">{{ customer?.organization || '-' }}</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500">{{ __('Status') }}</div>
            <span :class="getStatusClass(customer?.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
              {{ customer?.status || 'Active' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Add Artifact Form -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">{{ __('Artifact Details') }}</h3>
        
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type -->
            <div>
              <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Type') }} *
              </label>
              <select
                id="type"
                v-model="form.type"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.type }"
                required
              >
                <option value="">{{ __('Select Type') }}</option>
                <option value="Diamonds">Diamonds</option>
                <option value="Colored Gemstones">Colored Gemstones</option>
                <option value="Pearls">Pearls</option>
                <option value="Jewelry">Jewelry</option>
                <option value="Antiques">Antiques</option>
                <option value="Other">Other</option>
              </select>
              <p v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type }}</p>
            </div>

            <!-- Subtype -->
            <div>
              <label for="subtype" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Subtype') }} <span class="text-gray-400">({{ __('Optional') }})</span>
              </label>
              <input
                id="subtype"
                v-model="form.subtype"
                type="text"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.subtype }"
                :placeholder="__('Enter subtype')"
              />
              <p v-if="errors.subtype" class="mt-1 text-sm text-red-600">{{ errors.subtype }}</p>
            </div>

            <!-- Service -->
            <div>
              <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Service') }} *
              </label>
              <select
                id="service"
                v-model="form.service"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.service }"
                required
              >
                <option value="">{{ __('Select Service') }}</option>
                <option value="Regular - ID Report">Regular - ID Report</option>
                <option value="Express - ID Report">Express - ID Report</option>
                <option value="Priority - ID Report">Priority - ID Report</option>
                <option value="Standard - Certification">Standard - Certification</option>
                <option value="Express - Certification">Express - Certification</option>
                <option value="Priority - Certification">Priority - Certification</option>
              </select>
              <p v-if="errors.service" class="mt-1 text-sm text-red-600">{{ errors.service }}</p>
            </div>

            <!-- Weight & Weight Unit -->
            <div>
              <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Weight') }} *
              </label>
              <input
                type="number"
                step="0.01"
                id="weight"
                v-model="form.weight"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.weight }"
                placeholder="0.00"
                required
              />
              <p v-if="errors.weight" class="mt-1 text-sm text-red-600">{{ errors.weight }}</p>
            </div>

            <!-- Weight Unit -->
            <div>
              <label for="weight_unit" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Weight Unit') }} *
              </label>
              <select
                id="weight_unit"
                v-model="form.weight_unit"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.weight_unit }"
                required
              >
                <option value="ct">Carats (ct)</option>
                <option value="g">Grams (g)</option>
                <option value="kg">Kilograms (kg)</option>
                <option value="mg">Milligrams (mg)</option>
              </select>
              <p v-if="errors.weight_unit" class="mt-1 text-sm text-red-600">{{ errors.weight_unit }}</p>
            </div>

            <!-- Delivery Type -->
            <div>
              <label for="delivery_type" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Delivery Type') }} *
              </label>
              <select
                id="delivery_type"
                v-model="form.delivery_type"
                :disabled="submitting"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.delivery_type }"
                required
              >
                <option value="">{{ __('Select Delivery Type') }}</option>
                <option value="Regular">Regular</option>
                <option value="Express">Express</option>
                <option value="Priority">Priority</option>
                <option value="Pickup">Customer Pickup</option>
              </select>
              <p v-if="errors.delivery_type" class="mt-1 text-sm text-red-600">{{ errors.delivery_type }}</p>
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Notes') }}
              </label>
              <textarea
                id="notes"
                v-model="form.notes"
                :disabled="submitting"
                rows="4"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': submitting, 'border-red-300': errors.notes }"
                placeholder="{{ __('Additional notes about the artifact...') }}"
              ></textarea>
              <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="mt-8 flex justify-end space-x-4">
            <button
              type="button"
              @click="goBack"
              :disabled="submitting"
              class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'opacity-50 cursor-not-allowed': submitting }"
            >
              {{ __('Cancel') }}
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <i v-if="submitting" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-plus mr-2"></i>
              {{ submitting ? __('Adding...') : __('Add Artifact') }}
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
    customer: Object
  },
  data() {
    return {
      form: {
        type: '',
        subtype: '',
        service: '',
        weight: '',
        weight_unit: 'ct',
        delivery_type: '',
        notes: ''
      },
      submitting: false,
      errors: {}
    }
  },
  methods: {
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`)
    },
    
    async submitForm() {
      this.submitting = true
      this.errors = {}
      
      try {
        this.$inertia.post(`/dashboard/customers/${this.customer.id}/store-artifact`, this.form, {
          onSuccess: () => {
            this.submitting = false
            // Success message will be handled by the redirect
          },
          onError: (errors) => {
            this.errors = errors
            this.submitting = false
          },
          onFinish: () => {
            this.submitting = false
          }
        })
      } catch (error) {
        console.error('Error adding artifact:', error)
        this.submitting = false
      }
    },
    
    getStatusClass(status) {
      const statusClasses = {
        'active': 'bg-green-100 text-green-800',
        'inactive': 'bg-gray-100 text-gray-800',
        'suspended': 'bg-red-100 text-red-800'
      }
      return statusClasses[status?.toLowerCase()] || 'bg-green-100 text-green-800'
    }
  },
  
  mounted() {
    console.log('AddArtifact component mounted')
    console.log('Customer data:', this.customer)
  }
}
</script>
