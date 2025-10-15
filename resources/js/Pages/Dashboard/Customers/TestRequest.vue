<template>
  <div class="min-h-screen bg-gray-200 print:bg-white print:min-h-0">
    <!-- Fixed Header Section - Full Width - Optimized for print -->
    <div class="bg-gray-200 p-4 print:bg-white print:p-2">
      <div class="max-w-7xl mx-auto print:max-w-none">
        <!-- Title Section - Hidden in print -->
        <div class="text-center mb-4 print:hidden">
          <h1 class="text-xl font-bold text-black">TEST Request</h1>
        </div>
        
        <!-- Header Table - Optimized for A4 Landscape Print -->
        <div class="border-2 border-gray-300 bg-white print:bg-white mb-6 print:mb-3 shadow-lg print:shadow-none">
          <table class="w-full border-collapse">
            <tbody>
              <tr class="border-b-2 border-gray-300">
                <!-- دمج 3 خانات على الشمال -->
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 w-1/2 text-center align-middle bg-gray-50 print:bg-gray-100" rowspan="3" colspan="3">
                  <div class="text-xl print:text-lg font-bold">Test Request</div>
                </td>
                <td class="px-2 py-3 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 text-center align-middle font-semibold print:text-sm">
                  <div>Prepared by</div>
                </td>
                <td class="px-2 py-3 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 text-center align-middle font-semibold print:text-sm">
                  <div>Approved by</div>
                </td>
                <!-- دمج خانة واحدة على اليمين -->
                <td class="px-2 py-3 print:px-1 print:py-2 text-black w-1/6 text-center align-middle bg-gray-50 print:bg-gray-100" rowspan="3">
                  <img src="/images/idg_logo.jpg" alt="IDG Logo" class="w-12 h-12 print:w-8 print:h-8 rounded-full mx-auto" />
                  <div class="text-xs print:text-[10px] mt-1 font-semibold">IDG</div>
                </td>
              </tr>
              <tr class="border-b-2 border-gray-300">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle print:text-sm font-medium">Enas Ibrahim</td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle print:text-sm font-medium">Sultan Aldosari</td>
              </tr>
              <tr class="border-b-2 border-gray-300">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle text-xs print:text-[10px] text-gray-700">Lab. Management Supervisor</td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle text-xs print:text-[10px] text-gray-700">Laboratory Manager</td>
              </tr>
              <tr class="border-b-2 border-gray-300 bg-gray-100">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Number</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Classification</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Level</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Issue No., Revision No</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Date</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Issue Date</div>
                </td>
              </tr>
              <tr>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">{{ testRequest?.receiving_record_no || 'HOT-F03' }}</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Control</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Document</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">002, 001</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">{{ getCurrentDate() }}</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black text-center align-middle font-semibold print:text-sm">{{ getCurrentDate() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Main Content - Optimized for Landscape Print -->
    <div class="p-4 print:p-2">
      <div class="max-w-7xl mx-auto print:max-w-none">
        <!-- Classic Client Information Form -->
        <div class="bg-white border-2 border-gray-300 shadow-lg mb-6 print:shadow-none print:mb-3">
          <!-- Header - Hidden in print to save space -->
          <div class="bg-gray-100 border-b-2 border-black p-4 text-center print:hidden">
            <h1 class="text-3xl font-bold text-black mb-1">Test Request</h1>
            <h2 class="text-2xl font-bold text-gray-800">طلب اختبار</h2>
          </div>
          
          <!-- Client Information -->
          <div class="p-6 print:p-3">
            <!-- Client Information Section -->
            <div class="mb-8">
              <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-2">
                <!-- Column 1: Basic Information -->
                <div class="space-y-4 print:space-y-2">
                  <!-- Customer Name -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Customer Name') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ customer.full_name || customer.name || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Customer Code -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Customer Code') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ customer.customer_code || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Mobile -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Mobile No') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ customer.phone || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Email -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Email') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ customer.email || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Address -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('City/Address') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ customer.address || '-' }}</span>
                    </div>
                  </div>
                </div>
                
                <!-- Column 2: Test Request Information -->
                <div class="space-y-4 print:space-y-2">
                  <!-- Receiving Record No -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Receiving Record No') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ testRequest?.receiving_record_no || receiving_record_no || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Received Date -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Received Date') }}</span>
                      <span v-if="!editingTestRequest" class="text-black text-lg print:text-sm font-medium">{{ formatDate(testRequest?.received_date) }}</span>
                      <input 
                        v-else 
                        v-model="editTestRequestData.received_date" 
                        type="date" 
                        class="text-black text-lg print:text-sm font-medium bg-white border border-gray-300 px-2 py-1 rounded"
                      />
                    </div>
                  </div>
                  
                  <!-- Delivery Date -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Delivery Date') }}</span>
                      <span v-if="!editingTestRequest" class="text-black text-lg print:text-sm font-medium">{{ testRequest?.delivery_date ? formatDate(testRequest.delivery_date) : '-' }}</span>
                      <input 
                        v-else 
                        v-model="editTestRequestData.delivery_date" 
                        type="date" 
                        class="text-black text-lg print:text-sm font-medium bg-white border border-gray-300 px-2 py-1 rounded"
                      />
                    </div>
                  </div>
                  
                  <!-- Received By -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Received By') }}</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ received_by || '-' }}</span>
                    </div>
                  </div>
                  
                  <!-- Received In -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">{{ __('Received In') }}</span>
                      <span v-if="!editingTestRequest" class="text-black text-lg print:text-sm font-medium">{{ testRequest?.received_in || '-' }}</span>
                      <input 
                        v-else 
                        v-model="editTestRequestData.received_in" 
                        type="text" 
                        class="text-black text-lg print:text-sm font-medium bg-white border border-gray-300 px-2 py-1 rounded"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons - Hidden when printing -->
        <div class="text-center border-t-2 border-gray-300 pt-6 print:hidden">
          <div class="flex justify-center gap-4">
            <button @click="printPage" class="inline-flex items-center px-8 py-3 bg-green-700 text-white text-lg font-semibold hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 shadow-md hover:shadow-lg">
              <i class="fas fa-print mr-3 text-xl"></i>
              <span>Print Document</span>
              <span class="mx-3">|</span>
              <span>طباعة المستند</span>
            </button>
            <button 
              v-if="!editingTestRequest" 
              @click="startEditingTestRequest" 
              class="inline-flex items-center px-8 py-3 bg-blue-700 text-white text-lg font-semibold hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 shadow-md hover:shadow-lg"
            >
              <i class="fas fa-edit mr-3 text-xl"></i>
              <span>Edit Test Request</span>
              <span class="mx-3">|</span>
              <span>تعديل طلب الاختبار</span>
            </button>
            <div v-else class="flex gap-2">
              <button 
                @click="saveTestRequest" 
                class="inline-flex items-center px-6 py-3 bg-green-700 text-white text-lg font-semibold hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 shadow-md hover:shadow-lg"
              >
                <i class="fas fa-save mr-2 text-xl"></i>
                <span>Save</span>
              </button>
              <button 
                @click="cancelEditingTestRequest" 
                class="inline-flex items-center px-6 py-3 bg-gray-700 text-white text-lg font-semibold hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg"
              >
                <i class="fas fa-times mr-2 text-xl"></i>
                <span>Cancel</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Items Table - Optimized for A4 Landscape printing -->
        <div class="bg-white shadow-lg print:shadow-none border-2 border-gray-300 p-6 print:p-3 mb-6 print:mb-3">
          <div class="flex justify-between items-center mb-4 print:mb-2 border-b-2 border-gray-400 pb-2">
            <h3 class="text-xl print:text-lg font-semibold text-black flex items-center">
              <i class="fas fa-gem mr-3 text-gray-600 text-xl print:hidden"></i>
              <span>Items | العناصر</span>
            </h3>
            <div class="text-base print:text-sm text-black bg-gray-100 px-3 py-1 print:px-2 print:py-1 border border-gray-300">
              {{ __('Total') }}: {{ artifacts.length }} {{ __('items') }} | المجموع: {{ artifacts.length }} عنصر
            </div>
          </div>
          
          <div class="overflow-x-auto print:overflow-visible">
            <table class="w-full border-collapse border-2 border-gray-400">
              <thead>
                <tr class="bg-gray-100 print:bg-gray-200">
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">#</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">Code</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Type') }}</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Service') }}</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">Delivery Type</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Weight') }}</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Price') }}</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Notes') }}</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Status') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(artifact, idx) in artifacts" :key="artifact.id" class="hover:bg-gray-50 print:hover:bg-transparent">
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs font-medium text-black">{{ idx + 1 }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs font-medium text-black">{{ artifact.artifact_code || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.type }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.service || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.delivery_type || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div v-if="artifact.weight" class="text-sm print:text-xs text-black">
                      {{ artifact.weight }} {{ artifact.unit_type === 'carat' ? __('ct') : __('gm') }}
                    </div>
                    <div v-else class="text-sm print:text-xs text-black">-</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div v-if="artifact.price" class="text-sm print:text-xs font-semibold text-black">
                      SAR {{ artifact.price }}
                    </div>
                    <div v-else class="text-sm print:text-xs text-black">-</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.notes || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs font-medium text-black bg-gray-100 px-1 py-1 border border-gray-400 text-center">
                      {{ __(artifact.status || 'pending') }}
                    </div>
                  </td>
                </tr>
                <tr v-if="artifacts.length === 0">
                  <td colspan="9" class="border border-gray-400 px-3 py-8 print:px-1 print:py-4 text-center">
                    <div class="text-gray-400 text-center">
                      <i class="fas fa-gem text-4xl mb-4 print:hidden"></i>
                      <div class="text-lg font-medium">{{ __('No items found.') }}</div>
                      <div class="text-sm">{{ __('This customer has no items registered yet.') }}</div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Add Artifact Button -->
          <div class="mt-4 text-center print:hidden">
            <button @click="addArtifact" class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-base font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 shadow-md hover:shadow-lg">
              <i class="fas fa-plus mr-2"></i>
              Add New Artifact | إضافة قطعة جديدة
            </button>
          </div>
        </div>

        <!-- Delivery Documentation Section -->
        <div class="bg-white shadow-lg print:shadow-none border-2 border-gray-300 p-6 print:p-3 mb-6 print:mb-3">
          <div class="mb-4 print:mb-2 border-b-2 border-gray-400 pb-2">
            <h3 class="text-xl print:text-lg font-semibold text-black flex items-center">
              <i class="fas fa-truck mr-3 text-gray-600 text-xl print:hidden"></i>
              <span>Delivery Documentation | توثيق التسليم</span>
            </h3>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-2">
            <!-- Column 1: Delivery Information -->
            <div class="space-y-4 print:space-y-2">
              <!-- Delivery Date -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">{{ __('Delivery Date') }}</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ testRequest?.delivery_date ? formatDate(testRequest.delivery_date) : '-' }}</span>
                </div>
              </div>
              
              <!-- Delivery Method -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Delivery Method</span>
                  <span class="text-black text-lg print:text-sm font-medium">-</span>
                </div>
              </div>
              
              <!-- Delivery Signature -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Delivery Signature - توقيع التسليم</span>
                  <div class="bg-white border border-gray-300 w-48 h-16 print:w-36 print:h-12 flex items-center justify-center text-gray-400 text-xs">
                    <!-- Empty signature space -->
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Column 2: Reception Information -->
            <div class="space-y-4 print:space-y-2">
              <!-- Reception Date -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">{{ __('Received Date') }}</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ formatDate(testRequest?.received_date) }}</span>
                </div>
              </div>
              
              <!-- Reception Signature -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Reception Signature - توقيع الاستلام</span>
                  <div class="bg-white border border-gray-300 w-48 h-16 print:w-36 print:h-12 flex items-center justify-center text-gray-400 text-xs">
                    <!-- Empty signature space -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end print:hidden">
          <button @click="goBack" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-base font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg border-2 border-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>
            العودة للعملاء
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: { Link },
  props: {
    testRequest: Object,
    customer: Object,
    artifacts: Array,
    received_by: String,
    receiving_record_no: String
  },
  data() {
    return {
      editingTestRequest: false,
      editTestRequestData: {
        received_in: '',
        delivery_date: '',
        received_date: '',
        status: 'pending',
        notes: ''
      }
    }
  },
  methods: {
    goBack() {
      this.$inertia.visit('/dashboard/customers')
    },
    
    // Test Request editing methods
    startEditingTestRequest() {
      this.editingTestRequest = true
      this.editTestRequestData = {
        received_in: this.testRequest?.received_in || '',
        delivery_date: this.testRequest?.delivery_date || '',
        received_date: this.testRequest?.received_date || '',
        status: this.testRequest?.status || 'pending',
        notes: this.testRequest?.notes || ''
      }
    },
    
    cancelEditingTestRequest() {
      this.editingTestRequest = false
      this.editTestRequestData = {}
    },
    
    saveTestRequest() {
      if (this.testRequest?.id) {
        this.$inertia.put(`/dashboard/test-requests/${this.testRequest.id}`, this.editTestRequestData, {
          onFinish: () => {
            this.editingTestRequest = false
          }
        })
      }
    },
    
    addArtifact() {
      // Navigate to add artifact page
      this.$inertia.visit(`/dashboard/customers/${this.customer.qoyod_customer_id}/add-artifact`)
    },
    __(key) {
      const translations = {
        en: {
          'Customer Name': 'Customer Name',
          'Customer Code': 'Customer Code',
          'Mobile No': 'Mobile No',
          'Email': 'Email',
          'City/Address': 'City/Address',
          'Receiving Record No': 'Receiving Record No',
          'Received Date': 'Received Date',
          'Delivery Date': 'Delivery Date',
          'Received By': 'Received By',
          'Received In': 'Received In',
          'Type': 'Type',
          'Service': 'Service',
          'Weight': 'Weight',
          'Price': 'Price',
          'Notes': 'Notes',
          'Status': 'Status',
          'Total': 'Total',
          'items': 'items',
          'No items found.': 'No items found.',
          'This customer has no items registered yet.': 'This customer has no items registered yet.',
          'ct': 'ct',
          'gm': 'gm',
          'pending': 'Pending',
          'under_evaluation': 'Under Evaluation',
          'evaluated': 'Evaluated',
          'certified': 'Certified',
          'delivered': 'Delivered'
        },
        ar: {
          'Customer Name': 'اسم العميل',
          'Customer Code': 'كود العميل',
          'Mobile No': 'رقم الجوال',
          'Email': 'البريد الإلكتروني',
          'City/Address': 'المدينة/العنوان',
          'Receiving Record No': 'رقم سجل الاستلام',
          'Received Date': 'تاريخ الاستلام',
          'Delivery Date': 'تاريخ التسليم',
          'Received By': 'تم الاستلام بواسطة',
          'Received In': 'استلم في',
          'Type': 'النوع',
          'Service': 'الخدمة',
          'Weight': 'الوزن',
          'Price': 'السعر',
          'Notes': 'ملاحظات',
          'Status': 'الحالة',
          'Total': 'المجموع',
          'items': 'عنصر',
          'No items found.': 'لا توجد عناصر مسجلة',
          'This customer has no items registered yet.': 'لا توجد عناصر مسجلة لهذا العميل بعد',
          'ct': 'قيراط',
          'gm': 'جرام',
          'pending': 'قيد الاستلام',
          'under_evaluation': 'قيد التقييم',
          'evaluated': 'تم التقييم',
          'certified': 'معتمد',
          'delivered': 'تم التسليم'
        }
      }
      
      const locale = this.$page.props.locale || 'en'
      return translations[locale][key] || key
    },
    getCurrentDate() {
      const today = new Date()
      const day = String(today.getDate()).padStart(2, '0')
      const month = String(today.getMonth() + 1).padStart(2, '0')
      const year = today.getFullYear()
      return `${day}/${month}/${year}`
    },
    formatDate(date) {
      if (!date) return this.getCurrentDate()
      
      // Handle different date formats
      let dateObj
      if (typeof date === 'string') {
        // Handle ISO string format
        if (date.includes('T')) {
          dateObj = new Date(date)
        } else {
          // Handle YYYY-MM-DD format
          dateObj = new Date(date)
        }
      } else {
        dateObj = new Date(date)
      }
      
      // Check if date is valid
      if (isNaN(dateObj.getTime())) {
        return this.getCurrentDate()
      }
      
      const day = String(dateObj.getDate()).padStart(2, '0')
      const month = String(dateObj.getMonth() + 1).padStart(2, '0')
      const year = dateObj.getFullYear()
      return `${day}/${month}/${year}`
    },
    printPage() {
      // Hide navigation and other elements before printing
      const navigation = document.querySelector('nav');
      const sidebar = document.querySelector('.sidebar');
      
      // Store original display values
      const originalNavDisplay = navigation ? navigation.style.display : '';
      const originalSidebarDisplay = sidebar ? sidebar.style.display : '';
      
      // Hide elements
      if (navigation) navigation.style.display = 'none';
      if (sidebar) sidebar.style.display = 'none';
      
      // Add print-specific styles
      document.body.classList.add('printing');
      
      // Trigger print
      window.print();
      
      // Restore original display values after printing
      setTimeout(() => {
        if (navigation) navigation.style.display = originalNavDisplay;
        if (sidebar) sidebar.style.display = originalSidebarDisplay;
        document.body.classList.remove('printing');
      }, 1000);
    }
  }
}
</script> 

<style>
/* Print-specific styles */
@media print {
  /* Hide unwanted elements */
  .print\\:hidden {
    display: none !important;
  }
  
  /* Remove shadows and rounded corners for print */
  .print\\:shadow-none {
    box-shadow: none !important;
  }
  
  /* Ensure black borders for print */
  .print\\:border-black {
    border-color: black !important;
  }
  
  /* Remove overflow for print */
  .print\\:overflow-visible {
    overflow: visible !important;
  }
  
  /* Adjust padding for print */
  .print\\:p-4 {
    padding: 1rem !important;
  }
  
  /* Page setup - A4 Landscape */
  @page {
    margin: 0.4in 0.3in;
    size: A4 landscape;
  }
  
  /* Body styles for print */
  body {
    font-size: 10pt;
    line-height: 1.2;
    color: black !important;
    background: white !important;
  }
  
  /* Print-specific text sizes */
  .print\\:text-xs {
    font-size: 9pt !important;
  }
  
  .print\\:text-sm {
    font-size: 10pt !important;
  }
  
  .print\\:text-lg {
    font-size: 12pt !important;
  }
  
  .print\\:text-9px {
    font-size: 8pt !important;
  }
  
  .print\\:text-10px {
    font-size: 9pt !important;
  }
  
  /* Print-specific spacing */
  .print\\:px-1 {
    padding-left: 0.2rem !important;
    padding-right: 0.2rem !important;
  }
  
  .print\\:py-1 {
    padding-top: 0.2rem !important;
    padding-bottom: 0.2rem !important;
  }
  
  .print\\:mb-2 {
    margin-bottom: 0.4rem !important;
  }
  
  .print\\:mb-3 {
    margin-bottom: 0.6rem !important;
  }
  
  .print\\:gap-2 {
    gap: 0.4rem !important;
  }
  
  .print\\:space-y-2 > * + * {
    margin-top: 0.4rem !important;
  }
  
  /* Grid adjustments for landscape */
  .print\\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  }
  
  .print\\:max-w-none {
    max-width: none !important;
  }
  
  /* Ensure page breaks work properly */
  .page-break-before {
    page-break-before: always;
  }
  
  .page-break-after {
    page-break-after: always;
  }
  
  .page-break-inside-avoid {
    page-break-inside: avoid;
  }
  
  /* Table styles for print */
  table {
    page-break-inside: auto;
  }
  
  tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }
  
  /* Ensure borders are visible in print */
  table, th, td {
    border-collapse: collapse;
    border: 1px solid black !important;
  }
  
  /* Header styles for print */
  h1, h2, h3 {
    color: black !important;
    page-break-after: avoid;
  }
  
  /* Remove background colors for print */
  * {
    background: transparent !important;
    color: black !important;
    text-shadow: none !important;
    filter: none !important;
    -ms-filter: none !important;
  }
  
  /* Keep essential backgrounds */
  .bg-gray-100, .bg-gray-50 {
    background: #f5f5f5 !important;
  }
  
  /* Ensure text is readable */
  .text-white {
    color: black !important;
  }
}
</style>
