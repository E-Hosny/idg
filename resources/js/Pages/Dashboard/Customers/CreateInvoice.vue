<template>
  <DashboardLayout :pageTitle="__('Create Invoice')">
      <div class="max-w-7xl mx-auto px-4 min-h-screen">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Create Invoice for') }}: {{ customer?.name || customer?.display_name }}
            </h2>
            <p class="text-gray-600">
              {{ __('Create a new invoice using Qoyod API with all available fields') }}
            </p>
          </div>
          <div class="flex space-x-3">
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

      <!-- Customer Info Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Customer Information') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.name || customer?.display_name || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.email || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.phone || '-' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Qoyod ID') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer?.id || '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Invoice Form -->
      <form @submit.prevent="createInvoice" class="space-y-6">
        <!-- Basic Invoice Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Invoice Information') }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Reference Number -->
            <div class="lg:col-span-1">
              <label for="reference" class="block text-sm font-medium text-gray-700">
                {{ __('Invoice Reference') }}
              </label>
              <input
                type="text"
                id="reference"
                v-model="form.reference"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :placeholder="__('Invoice reference number')"
              />
            </div>

            <!-- Issue Date -->
            <div class="lg:col-span-1">
              <label for="issue_date" class="block text-sm font-medium text-gray-700">
                {{ __('Issue Date') }} *
              </label>
              <input
                type="date"
                id="issue_date"
                v-model="form.issue_date"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.issue_date }"
                required
              />
              <p v-if="errors.issue_date" class="mt-1 text-sm text-red-600">{{ errors.issue_date }}</p>
            </div>

            <!-- Due Date -->
            <div class="lg:col-span-1">
              <label for="due_date" class="block text-sm font-medium text-gray-700">
                {{ __('Due Date') }} *
              </label>
              <input
                type="date"
                id="due_date"
                v-model="form.due_date"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.due_date }"
                required
              />
              <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date }}</p>
            </div>

            <!-- Delivery Date -->
            <div class="lg:col-span-1">
              <label for="delivery_date" class="block text-sm font-medium text-gray-700">
                {{ __('Delivery Date') }} *
              </label>
              <input
                type="date"
                id="delivery_date"
                v-model="form.delivery_date"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.delivery_date }"
                required
              />
              <p v-if="errors.delivery_date" class="mt-1 text-sm text-red-600">{{ errors.delivery_date }}</p>
            </div>

            <!-- Status -->
            <div class="lg:col-span-1">
              <label for="status" class="block text-sm font-medium text-gray-700">
                {{ __('Status') }} *
              </label>
              <select
                id="status"
                v-model="form.status"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.status }"
                required
              >
                <option value="">{{ __('Select Status') }}</option>
                <option value="Draft">{{ __('Draft') }}</option>
                <option value="Approved">{{ __('Approved') }}</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
            </div>

            <!-- Contact ID -->
            <div class="lg:col-span-1">
              <label for="contact_id" class="block text-sm font-medium text-gray-700">
                {{ __('Contact ID') }} *
              </label>
              <input
                type="number"
                id="contact_id"
                v-model="form.contact_id"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.contact_id }"
                :placeholder="__('Customer ID')"
                required
              />
              <p v-if="errors.contact_id" class="mt-1 text-sm text-red-600">{{ errors.contact_id }}</p>
            </div>

            <!-- Inventory ID -->
            <div class="lg:col-span-1">
              <label for="inventory_id" class="block text-sm font-medium text-gray-700">
                {{ __('Inventory ID') }} *
              </label>
              <input
                type="number"
                id="inventory_id"
                v-model="form.inventory_id"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.inventory_id }"
                :placeholder="__('1')"
                required
              />
              <p v-if="errors.inventory_id" class="mt-1 text-sm text-red-600">{{ errors.inventory_id }}</p>
            </div>

            <!-- Location -->
            <div class="lg:col-span-1">
              <label for="location_id" class="block text-sm font-medium text-gray-700">
                {{ __('Location') }} *
              </label>
              <select
                id="location_id"
                v-model="form.location_id"  
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.location_id }"
                required
              >
                <option value="">{{ __('Select Location') }}</option>
                <option
                  v-for="location in locations"
                  :key="location.id"
                  :value="location.id"
              >
                  {{ location.ar_name || location.name }} {{ location.name_en && location.name_en !== location.ar_name ? `(${location.name_en})` : '' }}
                </option>
              </select>
              <p v-if="errors.location_id" class="mt-1 text-sm text-red-600">{{ errors.location_id }}</p>
            </div>

          </div>

          <!-- Description -->
          <div class="mt-4">
            <label for="description" class="block text-sm font-medium text-gray-700">
              {{ __('Description') }}
            </label>
            <textarea
              id="description"
              v-model="form.description"
              :disabled="creating"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'bg-gray-50': creating, 'border-red-300': errors.description }"
              :placeholder="__('Enter invoice description')"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
          </div>

          <!-- Draft if out of stock -->
          <div class="mt-4">
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="form.draft_if_out_of_stock"
                :disabled="creating"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <span class="ml-2 block text-sm text-gray-700">
                {{ __('Create as draft if products are out of stock') }}
              </span>
            </label>
          </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ __('Line Items') }} *</h3>
            <button
              type="button"
              @click="addLineItem"
              :disabled="creating"
              class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400"
            >
              <i class="fas fa-plus mr-1"></i>
              {{ __('Add Item') }}
            </button>
          </div>

          <!-- Line Items List -->
          <div v-if="form.line_items.length > 0" class="space-y-4">
            <div
              v-for="(item, index) in form.line_items"
              :key="index"
              class="border border-gray-200 rounded-lg p-4 bg-gray-50"
            >
              <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-800">{{ __('Item') }} {{ index + 1 }}</h4>
                <button
                  type="button"
                  @click="removeLineItem(index)"
                  :disabled="creating || form.line_items.length === 1"
                  class="text-red-600 hover:text-red-800 disabled:text-gray-400"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Product Selection -->
                <div>
                  <label :for="`product_id_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Product') }} *
                  </label>
                  <select
                    :id="`product_id_${index}`"
                    v-model="item.product_id"
                    @change="onProductChange(index, $event)"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  >
                    <option value="">{{ __('Select Product') }}</option>
                    <optgroup v-if="getGemstoneProducts().length > 0" :label="__('Gemstones')">
                      <option
                        v-for="product in getGemstoneProducts()"
                        :key="product.id"
                        :value="product.id"
                        :data-price="product.price"
                        :data-unit-type="product.unit_type || ''"
                        :data-unit-type-name="product.unit || ''"
                        :data-name-ar="product.name_ar"
                        :data-name-en="product.name_en"
                      >
                        ID: {{ product.id }} - {{ product.name_ar || product.name }} {{ product.price > 0 ? `(${product.price})` : '(يتطلب تشغيل)' }}
                      </option>
                    </optgroup>
                    <optgroup v-if="getDiamondProducts().length > 0" :label="__('Diamonds')">
                      <option
                        v-for="product in getDiamondProducts()"
                        :key="product.id"
                        :value="product.id"
                        :data-price="product.price"
                        :data-unit-type="product.unit_type || ''"
                        :data-unit-type-name="product.unit || ''"
                        :data-name-ar="product.name_ar"
                        :data-name-en="product.name_en"
                      >
                        ID: {{ product.id }} - {{ product.name_ar || product.name}} {{ product.price > 0 ? `(${product.price})` : '(يتطلب تشغيل)' }}
                      </option>
                    </optgroup>
                    <optgroup v-if="getOtherProducts().length > 0" :label="__('Other Products')">
                      <option
                        v-for="product in getOtherProducts()"
                        :key="product.id"
                        :value="product.id"
                        :data-price="product.price"
                        :data-unit-type="product.unit_type || ''"
                        :data-unit-type-name="product.unit || ''"
                        :data-name-ar="product.name_ar"
                        :data-name-en="product.name_en"
                      >
                        ID: {{ product.id }} - {{ product.name_ar || product.name }} {{ product.price > 0 ? `(${product.price})` : '' }}
                      </option>
                    </optgroup>
                  </select>
                </div>

                <!-- Description -->
                <div>
                  <label :for="`description_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Description') }}
                  </label>
                  <input
                    type="text"
                    :id="`description_${index}`"
                    v-model="item.description"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="__('Item description')"
                  />
                </div>

                <!-- Quantity -->
                <div>
                  <label :for="`quantity_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Quantity') }} *
                  </label>
                  <input
                    type="number"
                    :id="`quantity_${index}`"
                    v-model="item.quantity"
                    :disabled="creating"
                    step="0.01"
                    min="0.01"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="__('0.01')"
                    required
                  />
                </div>

                <!-- Unit Price -->
                <div>
                  <label :for="`unit_price_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Unit Price') }} *
                  </label>
                  <input
                    type="number"
                    :id="`unit_price_${index}`"
                    v-model="item.unit_price"
                    :disabled="creating"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="__('0.00')"
                    required
                  />
                </div>

                <!-- Unit Type -->
                <div>
                  <label :for="`unit_type_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Unit Type') }}
                  </label>
                  <select
                    :id="`unit_type_${index}`"
                    v-model="item.unit_type"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">-- {{ __('Select Unit Type') }} --</option>
                    <option value="piece">{{ __('Piece') }}</option>
                    <option value="gram">{{ __('Gram') }}</option>
                    <option value="kilogram">{{ __('Kilogram') }}</option>
                    <option value="carat">{{ __('Carat') }}</option>
                    <option value="meter">{{ __('Meter') }}</option>
                    <option value="centimeter">{{ __('Centimeter') }}</option>
                  </select>
                </div>

                <!-- Discount -->
                <div>
                  <label :for="`discount_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Discount') }}
                  </label>
                  <input
                    type="number"
                    :id="`discount_${index}`"
                    v-model="item.discount"
                    :disabled="creating"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="__('0.00')"
                  />
                </div>

                <!-- Discount Type -->
                <div>
                  <label :for="`discount_type_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Discount Type') }}
                  </label>
                  <select
                    :id="`discount_type_${index}`"
                    v-model="item.discount_type"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="percentage">{{ __('Percentage (%)') }}</option>
                    <option value="amount">{{ __('Amount') }}</option>
                  </select>
                </div>

                <!-- Tax Percent -->
                <div>
                  <label :for="`tax_percent_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Tax Percentage') }}
                  </label>
                  <input
                    type="number"
                    :id="`tax_percent_${index}`"
                    v-model="item.tax_percent"
                    :disabled="creating"
                    min="0"
                    max="100"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="__('15')"
                  />
                </div>

                <!-- Is Inclusive -->
                <div class="lg:col-span-3">
                  <label class="flex items-center">
                    <input
                      :id="`is_inclusive_${index}`"
                      type="checkbox"
                      v-model="item.is_inclusive"
                      :disabled="creating"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <span class="ml-2 block text-sm text-gray-700">
                      {{ __('Price is inclusive of tax') }}
                    </span>
                  </label>
                </div>
                
                <!-- Line Total Display -->
                <div class="lg:col-span-2">
                  <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="text-right">
                      <span class="text-sm text-blue-600 font-medium">{{ __('Line Total') }}:</span>
                      <span class="text-lg font-bold text-blue-800 ml-2">
                        {{ formatCurrency(calculateLineTotal(item)) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Invoice Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Invoice Summary') }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Subtotal -->
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Subtotal') }}</label>
              <div class="mt-1 text-2xl font-bold text-gray-900">{{ formatCurrency(calculateSubtotal()) }}</div>
            </div>
            
            <!-- Tax Total -->
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Tax Total') }}</label>
              <div class="mt-1 text-2xl font-bold text-gray-900">{{ formatCurrency(calculateTaxTotal()) }}</div>
            </div>
            
            <!-- Total -->
            <div>
              <label class="block text-sm font-medium text-gray-700">{{ __('Total') }}</label>
              <div class="mt-1 text-3xl font-bold text-blue-600">{{ formatCurrency(calculateTotal()) }}</div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="goBack"
              :disabled="creating"
              class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400"
            >
              {{ __('Cancel') }}
            </button>
            <button
              type="submit"
              :disabled="creating || !isFormValid"
              class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center"
            >
              <i v-if="creating" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-file-invoice mr-2"></i>
              {{ creating ? __('Creating Invoice...') : __('Create Invoice') }}
            </button>
          </div>
        </div>
      </form>
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
    artifacts: Array,
    products: Array,
    locations: Array,
    errors: Object
  },
  
  data() {
    return {
      creating: false,
      form: {
        reference: '',
        description: '',
        issue_date: '',
        due_date: '',
        delivery_date: '',
        status: 'Draft',
        contact_id: null,
        inventory_id: 1,
        location_id: '',
        draft_if_out_of_stock: false,
        line_items: [
          {
            product_id: '',
            description: '',
            quantity: 1,
            unit_price: 0,
            unit_type: '',
            discount: 0,
            discount_type: 'percentage',
            tax_percent: 15,
            is_inclusive: false
          }
        ],
        custom_fields: []
      }
    }
  },
  
  computed: {
    isFormValid() {
      return this.form.issue_date &&
             this.form.due_date &&
             this.form.status &&
             this.form.contact_id &&
             this.form.inventory_id &&
             this.form.line_items.length > 0 &&
             this.form.line_items.every(item => 
               item.product_id && 
               parseFloat(item.quantity) > 0 && 
               parseFloat(item.unit_price) >= 0
             );
    }
  },
  
  mounted() {
    // Generate automatic reference number like Qoyod (INV-2025-001, INV-2025-002, etc.)
    this.generateReferenceNumber();
    
    // Set default dates
    const today = new Date();
    this.form.issue_date = today.toISOString().split('T')[0];
    
    // Set due date to 30 days from now (like Qoyod default)
    const dueDate = new Date(today);
    dueDate.setDate(dueDate.getDate() + 30);
    this.form.due_date = dueDate.toISOString().split('T')[0];
    
    // Set delivery date to today (like Qoyod default)
    this.form.delivery_date = today.toISOString().split('T')[0];
    
    // Set customer ID
    if (this.customer && this.customer.id) {
      this.form.contact_id = parseInt(this.customer.id);
    }
    
    // Auto-fill customer data in description
    if (this.customer) {
      this.form.description = this.__('Invoice for customer') + ': ' + (this.customer.name || this.customer.display_name || '');
    }
    
    // Set default inventory ID to 1 (common default)
    this.form.inventory_id = 1;
    
    // Set default location if available
    if (this.locations && this.locations.length > 0) {
      this.form.location_id = this.locations[0].id;
    }
    
    console.log('Create Invoice Form mounted with auto-filled data', {
      customer: this.customer,
      reference: this.form.reference,
      issue_date: this.form.issue_date,
      due_date: this.form.due_date,
      artifacts: this.artifacts,
      products: this.products
    });
  },
  
  methods: {
    generateReferenceNumber() {
      // Generate reference number like "INV-2025-001", "INV-2025-002", etc.
      const today = new Date();
      const year = today.getFullYear();
      
      // Create a timestamp or sequence number (simple version)
      const timestamp = Date.now().toString().slice(-6);
      
      // Format: INV-YYYY-XXXXXX or INV-YYYY-XXX
      this.form.reference = `INV-${year}-${timestamp}`;
    },
    
    goBack() {
      this.$inertia.visit(`/dashboard/customers/${this.customer.id}/artifacts`);
    },
    
    addLineItem() {
      this.form.line_items.push({
        product_id: '',
        description: '',
        quantity: 1,
        unit_price: 0,
        unit_type: '',
        discount: 0,
        discount_type: 'percentage',
        tax_percent: 15,
        is_inclusive: false
      });
    },
    
    removeLineItem(index) {
      if (this.form.line_items.length > 1) {
        this.form.line_items.splice(index, 1);
      }
    },

    // Pricing calculation functions
    calculateLineTotal(item) {
      const subtotal = item.quantity * item.unit_price;
      let discountAmount = 0;
      
      if (item.discount > 0) {
        if (item.discount_type === 'percentage') {
          discountAmount = subtotal * (item.discount / 100);
        } else {
          discountAmount = item.discount;
        }
      }
      
      const lineTotal = subtotal - discountAmount;
      const taxAmount = item.tax_percent ? (lineTotal * item.tax_percent / 100) : 0;
      
      return item.is_inclusive ? lineTotal : lineTotal + taxAmount;
    },
    
    calculateSubtotal() {
      return this.form.line_items.reduce((total, item) => {
        const subtotal = item.quantity * item.unit_price;
        let discountAmount = 0;
        
        if (item.discount > 0) {
          if (item.discount_type === 'percentage') {
            discountAmount = subtotal * (item.discount / 100);
          } else {
            discountAmount = item.discount;
          }
        }
        
        return total + (subtotal - discountAmount);
      }, 0);
    },
    
    calculateTaxTotal() {
      return this.form.line_items.reduce((total, item) => {
        if (!item.tax_percent) return total;
        
        const subtotal = item.quantity * item.unit_price;
        let discountAmount = 0;
        
        if (item.discount > 0) {
          if (item.discount_type === 'percentage') {
            discountAmount = subtotal * (item.discount / 100);
          } else {
            discountAmount = item.discount;
          }
        }
        
        const lineTotal = subtotal - discountAmount;
        const taxAmount = lineTotal * item.tax_percent / 100;
        
        return total + taxAmount;
      }, 0);
    },
    
    calculateTotal() {
      return this.calculateSubtotal() + this.calculateTaxTotal();
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat(this.$page.props.locale === 'ar' ? 'ar-SA' : 'en-US', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount);
    },
    
    // Product filtering functions
    getGemstoneProducts() {
      if (!this.products || !Array.isArray(this.products)) return [];
      
      return this.products.filter(product => {
        const name = (product.name || '').toLowerCase();
        const nameAr = (product.name_ar || '').toLowerCase();
        return name.includes('gem') || name.includes('stone') || 
               nameAr.includes('حجر') || nameAr.includes('كريمة') ||
               name.includes('colored') || name.includes('gemstone');
      });
    },

    getDiamondProducts() {
      if (!this.products || !Array.isArray(this.products)) return [];
      
      return this.products.filter(product => {
        const name = (product.name || '').toLowerCase();
        const nameAr = (product.name_ar || '').toLowerCase();
        return name.includes('diamond') || 
               nameAr.includes('ألماس') || nameAr.includes('الماس');
      });
    },

    getOtherProducts() {
      if (!this.products || !Array.isArray(this.products)) return [];
      
      const gemstones = this.getGemstoneProducts();
      const diamonds = this.getDiamondProducts();
      
      return this.products.filter(product => {
        return !gemstones.find(g => g.id === product.id) && 
               !diamonds.find(d => d.id === product.id);
      });
    },
    
    onProductChange(index, event) {
      const productId = event.target.value;
      
      if (!productId) {
        this.form.line_items[index].unit_price = 0;
        return;
      }

      const selectedOption = event.target.options[event.target.selectedIndex];
      
      if (selectedOption) {
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const nameAr = selectedOption.dataset.nameAr || '';
        const unitType = selectedOption.dataset.unitType || '';
        const unitTypeName = selectedOption.dataset.unitTypeName || '';

        if (price > 0) {
          this.form.line_items[index].unit_price = price;
          console.log(`تم تحديث السعر تلقائياً لـ ${nameAr}: ${price}`);
        } else {
          this.form.line_items[index].unit_price = 0;
          console.log(`تم اختيار ${nameAr} - يتطلب إدخال السعر يدوياً`);
        }

        if (unitType && unitTypeName) {
          if (unitTypeName === 'قطعة') {
            this.form.line_items[index].unit_type = 'piece';
          } else {
            this.form.line_items[index].unit_type = unitTypeName.toLowerCase();
          }
          console.log(`تم تحديد نوع الوحدات تلقائياً: ${unitTypeName}`);
        }
      }
    },

    async createInvoice() {
      if (!this.isFormValid) {
        alert(this.__('Please fill in all required fields'));
        return;
      }
      
      this.creating = true;
      
      try {
        const invoiceData = {
          reference: this.form.reference,
          description: this.form.description,
          issue_date: this.form.issue_date,
          due_date: this.form.due_date,
          delivery_date: this.form.delivery_date,
          status: this.form.status,
          contact_id: parseInt(this.form.contact_id),
          inventory_id: parseInt(this.form.inventory_id),
          location_id: parseInt(this.form.location_id),
          draft_if_out_of_stock: this.form.draft_if_out_of_stock,
          line_items: this.form.line_items.map(item => ({
            product_id: parseInt(item.product_id),
            description: item.description || null,
            quantity: parseFloat(item.quantity),
            unit_price: parseFloat(item.unit_price),
            unit_type: item.unit_type || null,
            discount: item.discount ? parseFloat(item.discount) : null,
            discount_type: item.discount ? item.discount_type : null,
            tax_percent: item.tax_percent ? parseInt(item.tax_percent) : null,
            is_inclusive: Boolean(item.is_inclusive)
          })),
          custom_fields: this.form.custom_fields.filter(field => 
            field.name && field.value
          ).map(field => ({
            name: field.name,
            value: field.value
          }))
        };
        
        console.log('Creating invoice with data:', invoiceData);
        
        await this.$inertia.post(`/dashboard/customers/${this.customer.id}/store-invoice`, invoiceData, {
          onFinish: () => {
            this.creating = false;
          },
          onError: (errors) => {
            console.error('Error creating invoice:', errors);
            alert(this.__('Error creating invoice. Please check the form and try again.'));
          }
        });
        
      } catch (error) {
        console.error('Exception creating invoice:', error);
        alert(this.__('An error occurred while creating the invoice. Please try again.'));
        this.creating = false;
      }
    },

    __(key, replace = {}) {
      // Translation function for CreateInvoice component
      const translations = {
        en: {
          'Create Invoice': 'Create Invoice',
          'Create Invoice for': 'Create Invoice for',
          'Create a new invoice using Qoyod API with all available fields': 'Create a new invoice using Qoyod API with all available fields',
          'Back to Artifacts': 'Back to Artifacts',
          'Customer Information': 'Customer Information',
          'Name': 'Name',
          'Email': 'Email',
          'Phone': 'Phone',
          'Qoyod ID': 'Qoyod ID',
          'Invoice Information': 'Invoice Information',
          'Invoice Reference': 'Invoice Reference',
          'Issue Date': 'Issue Date',
          'Due Date': 'Due Date',
          'Delivery Date': 'Delivery Date',
          'Status': 'Status',
          'Contact ID': 'Contact ID',
          'Inventory ID': 'Inventory ID',
          'Location': 'Location',
          'Select Location': 'Select Location',
          'Description': 'Description',
          'Create as draft if products are out of stock': 'Create as draft if products are out of stock',
          'Line Items': 'Line Items',
          'Add Item': 'Add Item',
          'Item': 'Item',
          'Product': 'Product',
          'Quantity': 'Quantity',
          'Unit Price': 'Unit Price',
          'Unit Type': 'Unit Type',
          'Discount': 'Discount',
          'Discount Type': 'Discount Type',
          'Tax Percentage': 'Tax Percentage',
          'Price is inclusive of tax': 'Price is inclusive of tax',
          'Cancel': 'Cancel',
          'Creating Invoice...': 'Creating Invoice...',
          'Create Invoice': 'Create Invoice',
          'Line Total': 'Line Total',
          'Invoice Summary': 'Invoice Summary',
          'Subtotal': 'Subtotal',
          'Tax Total': 'Tax Total',
          'Total': 'Total',
          'Gemstones': 'Gemstones',
          'Diamonds': 'Diamonds',
          'Other Products': 'Other Products',
          'Select Status': 'Select Status',
          'Draft': 'Draft',
          'Approved': 'Approved',
          'Customer ID': 'Customer ID',
          'Select Product': 'Select Product',
          'Item description': 'Item description',
          'Select Unit Type': 'Select Unit Type',
          'Piece': 'Piece',
          'Gram': 'Gram',
          'Kilogram': 'Kilogram',
          'Carat': 'Carat',
          'Meter': 'Meter',
          'Centimeter': 'Centimeter',
          'Percentage (%)': 'Percentage (%)',
          'Amount': 'Amount',
          'Enter invoice description': 'Enter invoice description',
          'Invoice for customer': 'Invoice for customer',
          'Invoice reference number': 'Invoice reference number',
          '1': '1',
          '0.01': '0.01',
          '0.00': '0.00',
          '15': '15',
          'Please fill in all required fields': 'Please fill in all required fields',
          'Error creating invoice. Please check the form and try again.': 'Error creating invoice. Please check the form and try again.',
          'An error occurred while creating the invoice. Please try again.': 'An error occurred while creating the invoice. Please try again.'
        },
        ar: {
          'Create Invoice': 'إنشاء فاتورة',
          'Create Invoice for': 'إنشاء فاتورة لـ',
          'Create a new invoice using Qoyod API with all available fields': 'إنشاء فاتورة جديدة باستخدام Qoyod API مع جميع الحقول المتاحة',
          'Back to Artifacts': 'العودة للقطع',
          'Customer Information': 'معلومات العميل',
          'Name': 'الاسم',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Qoyod ID': 'رقم قيود',
          'Invoice Information': 'معلومات الفاتورة',
          'Invoice Reference': 'رقم الفاتورة',
          'Issue Date': 'تاريخ الإصدار',
          'Due Date': 'تاريخ الاستحقاق',
          'Delivery Date': 'تاريخ التسليم',
          'Status': 'الحالة',
          'Contact ID': 'معرف جهة الاتصال',
          'Inventory ID': 'معرف المخزون',
          'Location': 'الموقع',
          'Select Location': 'اختر الموقع',
          'Description': 'الوصف',
          'Create as draft if products are out of stock': 'إنشاء كمسودة إذا كانت المنتجات نافدة من المخزون',
          'Line Items': 'بنود الفاتورة',
          'Add Item': 'إضافة بند',
          'Item': 'البند',
          'Product': 'المنتج',
          'Quantity': 'الكمية',
          'Unit Price': 'سعر الوحدة',
          'Unit Type': 'نوع الوحدة',
          'Discount': 'الخصم',
          'Discount Type': 'نوع الخصم',
          'Tax Percentage': 'نسبة الضريبة',
          'Price is inclusive of tax': 'السعر شامل الضريبة',
          'Cancel': 'إلغاء',
          'Creating Invoice...': 'جاري إنشاء الفاتورة...',
          'Create Invoice': 'إنشاء فاتورة',
          'Line Total': 'المجموع الفرعي',
          'Invoice Summary': 'ملخص الفاتورة',
          'Subtotal': 'المجموع الفرعي',
          'Tax Total': 'مجموع الضريبة',
          'Total': 'المجموع الكلي',
          'Gemstones': 'الأحجار الكريمة',
          'Diamonds': 'الألماس',
          'Other Products': 'منتجات أخرى',
          'Select Status': 'اختر الحالة',
          'Draft': 'مسودة',
          'Approved': 'معتمد',
          'Customer ID': 'معرف العميل',
          'Select Product': 'اختر المنتج',
          'Item description': 'وصف البند',
          'Select Unit Type': 'اختر نوع الوحدة',
          'Piece': 'قطعة',
          'Gram': 'جرام',
          'Kilogram': 'كيلوجرام',
          'Carat': 'قيراط',
          'Meter': 'متر',
          'Centimeter': 'سنتيمتر',
          'Percentage (%)': 'نسبة (%)',
          'Amount': 'مبلغ',
          'Enter invoice description': 'أدخل وصف الفاتورة',
          'Invoice for customer': 'فاتورة للعميل',
          'Invoice reference number': 'رقم الفاتورة المرجعي',
          '1': '1',
          '0.01': '0.01',
          '0.00': '0.00',
          '15': '15',
          'Please fill in all required fields': 'يرجى ملء جميع الحقول المطلوبة',
          'Error creating invoice. Please check the form and try again.': 'خطأ في إنشاء الفاتورة. يرجى التحقق من النموذج والمحاولة مرة أخرى.',
          'An error occurred while creating the invoice. Please try again.': 'حدث خطأ أثناء إنشاء الفاتورة. يرجى المحاولة مرة أخرى.'
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
