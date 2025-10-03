<template>
  <DashboardLayout :pageTitle="__('Create Quote')">
      <div class="max-w-7xl mx-auto px-4 min-h-screen">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
              {{ __('Create Quote for') }}: {{ customer?.name || customer?.display_name }}
            </h2>
            <p class="text-gray-600">
              {{ __('Create a new quote using Qoyod API with all available fields') }}
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
          <div v-if="customer?.company_name">
            <label class="block text-sm font-medium text-gray-700">{{ __('Company') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer.company_name }}</p>
          </div>
          <div v-if="customer?.address">
            <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ customer.address }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Qoyod ID') }}</label>
            <p class="mt-1 text-sm text-gray-900">{{ formatQoyodReferenceNumber(customer?.id) }}</p>
          </div>
        </div>
      </div>

      <!-- Quote Form -->
      <form @submit.prevent="createQuote" class="space-y-6">
        <!-- Basic Quote Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Quote Information') }}</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Quotation Number -->
            <div class="lg:col-span-1">
              <label for="quotation_number" class="block text-sm font-medium text-gray-700">
                {{ __('Quotation Number') }} *
              </label>
              <input
                type="text"
                id="quotation_number"
                v-model="form.quotation_number"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.quotation_number }"
                placeholder="e.g., QUO-2025-001"
                required
              />
              <p v-if="errors.quotation_number" class="mt-1 text-sm text-red-600">{{ errors.quotation_number }}</p>
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

            <!-- Expiry Date -->
            <div class="lg:col-span-1">
              <label for="expiry_date" class="block text-sm font-medium text-gray-700">
                {{ __('Expiry Date') }} *
              </label>
              <input
                type="date"
                id="expiry_date"
                v-model="form.expiry_date"
                :disabled="creating"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                :class="{ 'bg-gray-50': creating, 'border-red-300': errors.expiry_date }"
                required
              />
              <p v-if="errors.expiry_date" class="mt-1 text-sm text-red-600">{{ errors.expiry_date }}</p>
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
                placeholder="e.g., 1"
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

          <!-- Terms & Conditions -->
          <div class="mt-4">
            <label for="terms_conditions" class="block text-sm font-medium text-gray-700">
              {{ __('Terms & Conditions') }}
            </label>
            <textarea
              id="terms_conditions"
              v-model="form.terms_conditions"
              :disabled="creating"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'bg-gray-50': creating, 'border-red-300': errors.terms_conditions }"
              placeholder="{{ __('Enter terms and conditions for this quote') }}"
            ></textarea>
            <p v-if="errors.terms_conditions" class="mt-1 text-sm text-red-600">{{ errors.terms_conditions }}</p>
          </div>

          <!-- Notes -->
          <div class="mt-4">
            <label for="notes" class="block text-sm font-medium text-gray-700">
              {{ __('Notes') }}
            </label>
            <textarea
              id="notes"
              v-model="form.notes"
              :disabled="creating"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'bg-gray-50': creating, 'border-red-300': errors.notes }"
              placeholder="{{ __('Add any additional notes for this quote') }}"
            ></textarea>
            <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
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

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
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
                    :class="{ 'bg-gray-50': creating, 'border-red-300': errors[`line_items.${index}.product_id`] }"
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
                  <p v-if="errors[`line_items.${index}.product_id`]" class="mt-1 text-sm text-red-600">
                    {{ errors[`line_items.${index}.product_id`] }}
                  </p>
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
                    :class="{ 'bg-gray-50': creating }"
                    placeholder="{{ __('Item description') }}"
                  />
                </div>

                <!-- Quantity -->
                <div>
                  <label :for="`quantity_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Quantity') }} *
                    <span v-if="item.unit_price > 0" class="ml-2 text-xs text-blue-600">
                      <i class="fas fa-calculator"></i> {{ __('Auto-calculates price') }}
                    </span>
                  </label>
                  <input
                    type="number"
                    :id="`quantity_${index}`"
                    v-model="item.quantity"
                    :disabled="creating"
                    step="1"
                    min="1"
                    max="999"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'bg-gray-50': creating, 'border-red-300': errors[`line_items.${index}.quantity`] }"
                    placeholder="1"
                    required
                    @input="calculateTotal"
                  />
                  <p v-if="errors[`line_items.${index}.quantity`]" class="mt-1 text-sm text-red-600">
                    {{ errors[`line_items.${index}.quantity`] }}
                  </p>
                  <p v-if="item.unit_price > 0 && item.quantity > 0" class="mt-1 text-xs text-blue-600">
                    <i class="fas fa-equals mr-1"></i>
                    {{ item.quantity }} × {{ item.unit_price }} = {{ (item.quantity * item.unit_price).toFixed(2) }}
                  </p>
                </div>

                <!-- Unit Price -->
                <div>
                  <label :for="`unit_price_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Unit Price') }} *
                    <span v-if="item.unit_price > 0" class="ml-2 text-xs text-green-600">
                      <i class="fas fa-check-circle"></i> {{ __('Auto-filled') }}
                    </span>
                  </label>
                  <input
                    type="number"
                    :id="`unit_price_${index}`"
                    v-model="item.unit_price"
                    :disabled="creating"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 
                      'bg-gray-50': creating, 
                      'border-green-300 bg-green-50': item.unit_price > 0,
                      'border-red-300': errors[`line_items.${index}.unit_price`] 
                    }"
                    placeholder="0.00"
                    required
                    @input="calculateTotal"
                  />
                  <p v-if="errors[`line_items.${index}.unit_price`]" class="mt-1 text-sm text-red-600">
                    {{ errors[`line_items.${index}.unit_price`] }}
                  </p>
                  <p v-if="item.unit_price > 0" class="mt-1 text-xs text-green-600">
                    <i class="fas fa-magic mr-1"></i>
                    {{ __('Price filled automatically from selected product') }}
                  </p>
                </div>

                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-3">
                    <!-- Unit Type -->
                    <div>
                      <label :for="`unit_type_${index}`" class="block text-sm font-medium text-gray-700">
                        {{ __('Unit Type') }}
                        <span v-if="item.product_id" class="ml-2 text-xs text-purple-600">
                          <i class="fas fa-exchange-alt"></i> {{ __('Updates price') }}
                        </span>
                      </label>
                      <select
                        :id="`unit_type_${index}`"
                        v-model="item.unit_type"
                        @change="onUnitTypeChange(index, $event)"
                        :disabled="creating"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'bg-gray-50': creating, 'border-purple-300 bg-purple-50': item.unit_type }"
                      >
                        <option value="">-- {{ __('Select Unit Type') }} --</option>
                        <option value="piece">{{ __('Piece') }} (قطعة)</option>
                        <option value="gram">{{ __('Gram') }} (جرام)</option>
                        <option value="kilogram">{{ __('Kilogram') }} (كيلوجرام)</option>
                        <option value="carat">{{ __('Carat') }} (قيراط)</option>
                        <option value="meter">{{ __('Meter') }} (متر)</option>
                        <option value="centimeter">{{ __('Centimeter') }} (سنتيمتر)</option>
                        <option value="hour">{{ __('Hour') }} (ساعة)</option>
                        <option value="service">{{ __('Service') }} (خدمة)</option>
                        <option value="report">{{ __('Report') }} (تقرير)</option>
                      </select>
                      <p v-if="item.unit_type" class="mt-1 text-xs text-purple-600">
                        <i class="fas fa-exchange-alt mr-1"></i>
                        {{ __('Price updated based on unit type') }} ({{ getUnitTypePriceMultiplier(item.unit_type) }}x)
                      </p>
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
                        :class="{ 'bg-gray-50': creating }"
                        placeholder="0.00"
                        @input="calculateTotal"
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
                        :class="{ 'bg-gray-50': creating }"
                        @change="calculateTotal"
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
                        :class="{ 'bg-gray-50': creating }"
                        placeholder="15"
                      />
                    </div>

                    <!-- Is Inclusive -->
                    <div class="flex items-center">
                      <input
                        :id="`is_inclusive_${index}`"
                        type="checkbox"
                        v-model="item.is_inclusive"
                        :disabled="creating"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                      <label :for="`is_inclusive_${index}`" class="ml-2 block text-sm text-gray-700">
                        {{ __('Price is inclusive of tax') }}
                      </label>
                    </div>

                    <!-- Line Total -->
                    <div class="lg:col-span-2">
                      <label class="block text-sm font-medium text-gray-700">
                        {{ __('Line Total') }}
                      </label>
                      <div class="mt-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium">
                        {{ formatCurrency(calculateLineTotal(item)) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          <!-- Custom Fields -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Custom Fields') }}</h3>
            <div class="space-y-4">
              <div v-for="(field, index) in form.custom_fields" :key="index" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label :for="`custom_field_name_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Field Name') }}
                  </label>
                  <input
                    type="text"
                    :id="`custom_field_name_${index}`"
                    v-model="field.name"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'bg-gray-50': creating }"
                    placeholder="{{ __('Field name') }}"
                  />
                </div>
                <div>
                  <label :for="`custom_field_value_${index}`" class="block text-sm font-medium text-gray-700">
                    {{ __('Field Value') }}
                  </label>
                  <input
                    type="text"
                    :id="`custom_field_value_${index}`"
                    v-model="field.value"
                    :disabled="creating"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'bg-gray-50': creating }"
                    placeholder="{{ __('Field value') }}"
                  />
                </div>
              </div>
              <button
                type="button"
                @click="addCustomField"
                :disabled="creating"
                 class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400"
              >
                <i class="fas fa-plus mr-1"></i>
                {{ __('Add Custom Field') }}
              </button>
            </div>
          </div>

          <!-- Quote Summary -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Quote Summary') }}</h3>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">{{ __('Subtotal') }}:</span>
                <span class="font-medium">{{ formatCurrency(calculateSubtotal()) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">{{ __('Tax Total') }}:</span>
                <span class="font-medium">{{ formatCurrency(calculateTaxTotal()) }}</span>
              </div>
              <div class="border-t pt-2 flex justify-between text-lg font-bold">
                <span>{{ __('Total') }}:</span>
                <span class="text-blue-600">{{ formatCurrency(calculateGrandTotal()) }}</span>
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
                {{ creating ? __('Creating Quote...') : __('Create Quote') }}
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
        quotation_number: '',
        issue_date: '',
        expiry_date: '',
        status: 'Draft',
        terms_conditions: '',
        notes: '',
        inventory_id: 1,
        location_id: 1,
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
      return this.form.quotation_number &&
             this.form.issue_date &&
             this.form.expiry_date &&
             this.form.status &&
             this.form.inventory_id &&
             this.form.location_id &&
             this.form.line_items.length > 0 &&
             this.form.line_items.every(item => 
               item.product_id && 
               parseFloat(item.quantity) > 0 && 
               parseFloat(item.unit_price) >= 0
             );
    }
  },
  
  mounted() {
    // Set default dates
    const today = new Date();
    this.form.issue_date = today.toISOString().split('T')[0];
    
    const expiryDate = new Date(today);
    expiryDate.setDate(expiryDate.getDate() + 30);
    this.form.expiry_date = expiryDate.toISOString().split('T')[0];
    
    // Generate default quotation number
    if (!this.form.quotation_number) {
      const timestamp = Date.now().toString().slice(-6);
      this.form.quotation_number = `QUO-${new Date().getFullYear()}-${timestamp}`;
    }
    
    console.log('Create Quote Form mounted', {
      customer: this.customer,
      artifacts: this.artifacts,
      products: this.products
    });
  },
  
  methods: {
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
    
    addCustomField() {
      this.form.custom_fields.push({
        name: '',
        value: ''
      });
    },
    
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
      
      const discountedSubtotal = subtotal - discountAmount;
      const taxAmount = (item.tax_percent || 0) * discountedSubtotal / 100;
      
      return discountedSubtotal + taxAmount;
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
        const subtotal = item.quantity * item.unit_price;
        let discountAmount = 0;
        
        if (item.discount > 0) {
          if (item.discount_type === 'percentage') {
            discountAmount = subtotal * (item.discount / 100);
          } else {
            discountAmount = item.discount;
          }
        }
        
        const discountedSubtotal = subtotal - discountAmount;
        const taxAmount = (item.tax_percent || 0) * discountedSubtotal / 100;
        
        return total + taxAmount;
      }, 0);
    },
    
    calculateGrandTotal() {
      return this.calculateSubtotal() + this.calculateTaxTotal();
    },
    
    calculateTotal() {
      // Trigger reactivity for totals
      this.$forceUpdate();
    },
    
    formatCurrency(amount) {
      if (amount === null || amount === undefined) return '$0.00';
      return '$' + parseFloat(amount).toFixed(2);
    },
    
    formatQoyodReferenceNumber(id) {
      if (!id) return 'CUS000';
      return `CUS${id.toString().padStart(3, '0')}`;
    },
    
    __(key, replace = {}) {
      // Translation function for CreateQuote component
      const translations = {
        en: {
          'Create Quote': 'Create Quote',
          'Create Quote for': 'Create Quote for',
          'Create a new quote using Qoyod API with all available fields': 'Create a new quote using Qoyod API with all available fields',
          'Back to Artifacts': 'Back to Artifacts',
          'Customer Information': 'Customer Information',
          'Name': 'Name',
          'Email': 'Email',
          'Phone': 'Phone',
          'Company': 'Company',
          'Address': 'Address',
          'Qoyod ID': 'Qoyod ID',
          'Quote Information': 'Quote Information',
          'Quotation Number': 'Quotation Number',
          'Issue Date': 'Issue Date',
          'Expiry Date': 'Expiry Date',
          'Status': 'Status',
          'Inventory ID': 'Inventory ID',
          'Terms & Conditions': 'Terms & Conditions',
          'Notes': 'Notes',
          'Line Items': 'Line Items',
          'Add Item': 'Add Item',
          'Item': 'Item',
          'Product': 'Product',
          'Description': 'Description',
          'Quantity': 'Quantity',
          'Unit Price': 'Unit Price',
          'Unit Type': 'Unit Type',
          'Discount': 'Discount',
          'Discount Type': 'Discount Type',
          'Tax Percentage': 'Tax Percentage',
          'Price is inclusive of tax': 'Price is inclusive of tax',
          'Line Total': 'Line Total',
          'Custom Fields': 'Custom Fields',
          'Field Name': 'Field Name',
          'Field Value': 'Field Value',
          'Add Custom Field': 'Add Custom Field',
          'Quote Summary': 'Quote Summary',
          'Subtotal': 'Subtotal',
          'Tax Total': 'Tax Total',
          'Total': 'Total',
          'Cancel': 'Cancel',
          'Creating Quote...': 'Creating Quote...',
          'Create Quote': 'Create Quote',
          'Select Status': 'Select Status',
          'Draft': 'Draft',
          'Approved': 'Approved',
          'Select Product': 'Select Product',
          'Percentage (%)': 'Percentage (%)',
          'Amount': 'Amount',
          'Enter terms and conditions for this quote': 'Enter terms and conditions for this quote',
          'Add any additional notes for this quote': 'Add any additional notes for this quote',
          'e.g., QUO-2025-001': 'e.g., QUO-2025-001',
          'e.g., 1': 'e.g., 1',
          'Item description': 'Item description',
          'e.g., piece, kg, meter': 'e.g., piece, kg, meter',
          'Field name': 'Field name',
          'Field value': 'Field value',
          'Please fill in all required fields': 'Please fill in all required fields',
          'Error creating quote. Please check the form and try again.': 'Error creating quote. Please check the form and try again.',
          'An error occurred while creating the quote. Please try again.': 'An error occurred while creating the quote. Please try again.',
          'Auto-filled': 'Auto-filled',
          'Price filled automatically from selected product': 'Price filled automatically from selected product',
          'Auto-calculates price': 'Auto-calculates price',
          'Updates price': 'Updates price',
          'Select Unit Type': 'Select Unit Type',
          'Price updated based on unit type': 'Price updated based on unit type',
          'Piece': 'Piece',
          'Gram': 'Gram',
          'Kilogram': 'Kilogram',
          'Carat': 'Carat',
          'Meter': 'Meter',
          'Centimeter': 'Centimeter',
          'Hour': 'Hour',
          'Service': 'Service',
          'Report': 'Report'
        },
        ar: {
          'Create Quote': 'إنشاء عرض سعر',
          'Create Quote for': 'إنشاء عرض سعر لـ',
          'Create a new quote using Qoyod API with all available fields': 'إنشاء عرض سعر جديد باستخدام Qoyod API مع جميع الحقول المتاحة',
          'Back to Artifacts': 'العودة للقطع',
          'Location': 'الموقع',
          'Location ID': 'معرف الموقع',
          'Select Location': 'اختر الموقع',
          'Gemstones': 'الأحجار الكريمة',
          'Diamonds': 'الألماس',
          'Other Products': 'منتجات أخرى',
          'Customer Information': 'معلومات العميل',
          'Name': 'الاسم',
          'Email': 'البريد الإلكتروني',
          'Phone': 'الهاتف',
          'Company': 'الشركة',
          'Address': 'العنوان',
          'Qoyod ID': 'رقم قيود',
          'Quote Information': 'معلومات عرض السعر',
          'Quotation Number': 'رقم عرض السعر',
          'Issue Date': 'تاريخ الإصدار',
          'Expiry Date': 'تاريخ الاستحقاق',
          'Status': 'الحالة',
          'Inventory ID': 'معرف المخزون',
          'Terms & Conditions': 'الشروط والأحكام',
          'Notes': 'الملاحظات',
          'Line Items': 'بنود العرض',
          'Add Item': 'إضافة بند',
          'Item': 'البند',
          'Product': 'المنتج',
          'Description': 'الوصف',
          'Quantity': 'الكمية',
          'Unit Price': 'سعر الوحدة',
          'Unit Type': 'نوع الوحدة',
          'Discount': 'الخصم',
          'Discount Type': 'نوع الخصم',
          'Tax Percentage': 'نسبة الضريبة',
          'Price is inclusive of tax': 'السعر شامل الضريبة',
          'Line Total': 'المجموع الفرعي',
          'Custom Fields': 'الحقول المخصصة',
          'Field Name': 'اسم الحقل',
          'Field Value': 'قيمة الحقل',
          'Add Custom Field': 'إضافة حقل مخصص',
          'Quote Summary': 'ملخص عرض السعر',
          'Subtotal': 'المجموع الفرعي',
          'Tax Total': 'مجموع الضريبة',
          'Total': 'المجموع الكلي',
          'Cancel': 'إلغاء',
          'Creating Quote...': 'جاري إنشاء عرض السعر...',
          'Create Quote': 'إنشاء عرض السعر',
          'Select Status': 'اختر الحالة',
          'Draft': 'مسودة',
          'Approved': 'معتمد',
          'Select Product': 'اختر المنتج',
          'Percentage (%)': 'نسبة (%)',
          'Amount': 'مبلغ',
          'Enter terms and conditions for this quote': 'أدخل الشروط والأحكام لعرض السعر',
          'Add any additional notes for this quote': 'أضف أي ملاحظات إضافية لعرض السعر',
          'e.g., QUO-2025-001': 'مثال: QUO-2025-001',
          'e.g., 1': 'مثال: 1',
          'Item description': 'وصف البند',
          'e.g., piece, kg, meter': 'مثال: قطعة، كيلو، متر',
          'Field name': 'اسم الحقل',
          'Field value': 'قيمة الحقل',
          'Please fill in all required fields': 'يرجى ملء جميع الحقول المطلوبة',
          'Error creating quote. Please check the form and try again.': 'خطأ في إنشاء عرض السعر. يرجى التحقق من النموذج والمحاولة مرة أخرى.',
          'An error occurred while creating the quote. Please try again.': 'حدث خطأ أثناء إنشاء عرض السعر. يرجى المحاولة مرة أخرى.',
          'Auto-filled': 'تم إملاؤه تلقائياً',
          'Price filled automatically from selected product': 'تم ملء السعر تلقائياً من المنتج المختار',
          'Auto-calculates price': 'يحسب السعر تلقائياً',
          'Updates price': 'يحدث السعر',
          'Select Unit Type': 'اختر نوع الوحدة',
          'Price updated based on unit type': 'تم تحديث السعر بناءً على نوع الوحدة',
          'Piece': 'قطعة',
          'Gram': 'جرام',
          'Kilogram': 'كيلوجرام',
          'Carat': 'قيراط',
          'Meter': 'متر',
          'Centimeter': 'سنتيمتر',
          'Hour': 'ساعة',
          'Service': 'خدمة',
          'Report': 'تقرير'
        }
      };

      const locale = this.$page.props.locale || 'en';
      let translation = translations[locale]?.[key] || key;

      // Replace placeholders
      Object.keys(replace).forEach(placeholder => {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
      });

      return translation;
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
      }).map(product => ({
        ...product,
        price_info: this.getProductPriceInfo(product)
      }));
    },

    getDiamondProducts() {
      if (!this.products || !Array.isArray(this.products)) return [];
      
      return this.products.filter(product => {
        const name = (product.name || '').toLowerCase();
        const nameAr = (product.name_ar || '').toLowerCase();
        return name.includes('diamond') || name.includes('ماس') ||
               nameAr.includes('ألماس') || nameAr.includes('الماس');
      }).map(product => ({
        ...product,
        price_info: this.getProductPriceInfo(product)
      }));
    },

    getOtherProducts() {
      if (!this.products || !Array.isArray(this.products)) return [];
      
      const gemstones = this.getGemstoneProducts();
      const diamonds = this.getDiamondProducts();
      
      return this.products.filter(product => {
        return !gemstones.find(g => g.id === product.id) && 
               !diamonds.find(d => d.id === product.id);
      }).map(product => ({
        ...product,
        price_info: this.getProductPriceInfo(product)
      }));
    },

    getProductPriceInfo(product) {
      if (product.price && parseFloat(product.price) > 0) {
        return `(${parseFloat(product.price).toFixed(2)})`;
      }
      return '(يتطلب تشغيل)';
    },

    // Product change handler to auto-fill unit price ONLY
    onProductChange(index, event) {
      const productId = event.target.value;
      
      if (!productId) {
        // Reset only price when no product selected
        this.form.line_items[index].unit_price = 0;
        return;
      }

      // Get selected option element
      const selectedOption = event.target.options[event.target.selectedIndex];
      
      if (selectedOption) {
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const nameAr = selectedOption.dataset.nameAr || '';
        const unitType = selectedOption.dataset.unitType || '';
        const unitTypeName = selectedOption.dataset.unitTypeName || '';

        // Auto-fill ONLY unit price - leave description empty for manual input
        if (price > 0) {
          this.form.line_items[index].unit_price = price;
          console.log(`تم تحديث السعر تلقائياً لـ ${nameAr}: ${price}`);
        } else {
          // Reset price to 0 for products without price
          this.form.line_items[index].unit_price = 0;
          console.log(`تم اختيار ${nameAr} - يتطلب إدخال السعر يدوياً`);
        }

        // Auto-fill unit type if available from product data
        if (unitType && unitTypeName) {
          // Map Qoyod unit values to our dropdown values
          if (unitTypeName === 'قطعة') {
            this.form.line_items[index].unit_type = 'piece';
          } else {
            this.form.line_items[index].unit_type = unitTypeName.toLowerCase();
          }
          console.log(`تم تحديد نوع الوحدات تلقائياً: ${unitTypeName}`);
        }

        // Trigger recalculation
        this.calculateTotal();
      }
    },

    // Unit type change handler to update price automatically based on unit type
    onUnitTypeChange(index, event) {
      const unitType = event.target.value;
      
      if (!unitType || !this.form.line_items[index].product_id) {
        return;
      }

      // Get the original price from the selected product
      const originalPrice = this.form.line_items[index].unit_price;
      
      if (originalPrice > 0) {
        // Apply price multiplier based on unit type
        const multiplier = this.getUnitTypePriceMultiplier(unitType);
        const newPrice = originalPrice * multiplier;
        
        this.form.line_items[index].unit_price = newPrice;
        
        // Trigger recalculation
        this.calculateTotal();
        
        console.log(`تم تحديث السعر بناءً على نوع الوحدة: ${originalPrice} × ${multiplier} = ${newPrice}`);
      }
    },

    // Get price multiplier based on unit type
    getUnitTypePriceMultiplier(unitType) {
      const multipliers = {
        'piece': 1.0,      // قطعة - السعر الأساسي
        'gram': 0.01,      // جرام - أقل بكثير
        'kilogram': 10.0,  // كيلوجرام - أعلى بكثير
        'carat': 2.0,      // قيراط - ضعف السعر تقريباً
        'meter': 5.0,      // متر - أعلى
        'centimeter': 0.1, // سنتيمتر - أقل
        'hour': 50.0,      // ساعة خدمة - أعلى بكثير
        'service': 100.0,  // خدمة - ارتفاع كبير في السعر
        'report': 200.0    // تقرير - أعلى سعر
      };
      
      return multipliers[unitType] || 1.0;
    },

    async createQuote() {
      if (!this.isFormValid) {
        alert(this.__('Please fill in all required fields'));
        return;
      }
      
      this.creating = true;
      
      try {
        // Prepare the data
        const quoteData = {
          quotation_number: this.form.quotation_number,
          issue_date: this.form.issue_date,
          expiry_date: this.form.expiry_date,
          status: this.form.status,
          terms_conditions: this.form.terms_conditions || null,
          notes: this.form.notes || null,
          inventory_id: parseInt(this.form.inventory_id),
          location_id: parseInt(this.form.location_id),
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
        
        console.log('Creating quote with data:', quoteData);
        
        await this.$inertia.post(`/dashboard/customers/${this.customer.id}/store-quote`, quoteData, {
          onFinish: () => {
            this.creating = false;
          },
          onError: (errors) => {
            console.error('Error creating quote:', errors);
            alert(this.__('Error creating quote. Please check the form and try again.'));
          }
        });
        
      } catch (error) {
        console.error('Exception creating page:', error);
        alert(this.__('An error occurred while creating the quote. Please try again.'));
        this.creating = false;
      }
    }
  }
}
</script>
