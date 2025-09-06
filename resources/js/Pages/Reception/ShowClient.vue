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
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">HOT-F03</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Control</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Document</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">002, 001</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">15/3/2025</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black text-center align-middle font-semibold print:text-sm">3/15/2025</td>
              </tr>
            </tbody>
          </table>
        </div>


      </
      div>
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
                  <!-- Company Name -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Company name - اسم الشركة</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.company_name || 'Not specified' }}</span>
                    </div>
                  </div>
                  
                  <!-- Customer Name -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Customer name - اسم العميل</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.full_name }}</span>
                    </div>
                  </div>
                  
                  <!-- Customer Code -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Customer code - رمز العميل</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.customer_code }}</span>
                    </div>
                  </div>
                  
                  <!-- Mobile Number -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Mobile No - رقم الجوال</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.phone }}</span>
                    </div>
                  </div>
                  
                  <!-- Email -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Email - البريد الإلكتروني</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.email || 'Not provided' }}</span>
                    </div>
                  </div>
                </div>
                
                <!-- Column 2: Reception Information -->
                <div class="space-y-4 print:space-y-2">
                  <!-- Receiving Record Number -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Receiving Record No - رقم السجل</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.receiving_record_no || 'Not assigned' }}</span>
                    </div>
                  </div>
                  
                  <!-- Received By -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Received By - استلم بواسطة</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ received_by || 'Not specified' }}</span>
                    </div>
                  </div>
                  
                  <!-- Received Date -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Received Date - تاريخ الاستلام</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.received_date }}</span>
                    </div>
                  </div>
                  
                  <!-- Received In -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Received In - استلم في</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.received_in || 'Not specified' }}</span>
                    </div>
                  </div>
                  
                  <!-- Delivery Date -->
                  <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                    <div class="flex items-center justify-between">
                      <span class="font-bold text-black text-base print:text-sm">Delivery Date وقت التسليم</span>
                      <span class="text-black text-lg print:text-sm font-medium">{{ client.delivery_date || 'Not scheduled' }}</span>
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
                
                <button @click="editClient" class="inline-flex items-center px-8 py-3 bg-gray-700 text-white text-lg font-semibold hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg">
                  <i class="fas fa-edit mr-3 text-xl"></i>
                  <span>Edit Client</span>
                  <span class="mx-3">|</span>
                  <span>تعديل العميل</span>
            </button>
          </div>
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
              {{ __('Total') }}: {{ client.artifacts.length }} {{ __('items') }} | المجموع: {{ client.artifacts.length }} عنصر
            </div>
          </div>
          <div class="overflow-x-auto print:overflow-visible">
            <table class="min-w-full bg-white border-2 border-gray-300">
              <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">#</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">Item/Product ID - تعريف العينة للمنتج</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">Item type - نوع الصنف</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">{{ __('Service') }} | الخدمة</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">Delivery Type/Date - طريقة وتاريخ التسليم</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">{{ __('Weight') }} | الوزن</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400 print:hidden">{{ __('Price') }} | السعر</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400">{{ __('Notes') }} | ملاحظات</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm print:text-xs font-bold text-black border-r border-gray-400 print:hidden">{{ __('Status') }} | الحالة</th>
                  <th class="px-2 py-2 print:px-1 print:py-1 text-center text-sm font-bold text-black print:hidden">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody class="bg-white">
                <tr v-for="(artifact, idx) in client.artifacts" :key="artifact.id" class="border-b border-gray-300">
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs font-medium text-black">{{ idx + 1 }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs font-medium text-black">{{ artifact.artifact_code || '-' }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.type }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.service || '-' }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.delivery_type || '-' }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div v-if="artifact.weight" class="text-sm print:text-xs text-black">
                      {{ artifact.weight }} 
                         <span v-if="artifact.weight_unit" class="text-black text-xs print:text-10px ml-1">
                        {{ __(artifact.weight_unit) }}
                      </span>
                    </div>
                    <div v-else class="text-sm print:text-xs text-black">-</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 print:hidden text-center">
                    <div v-if="artifact.price" class="text-sm print:text-xs font-semibold text-black">
                      {{ artifact.price }} SAR
                    </div>
                    <div v-else class="text-sm print:text-xs text-black">-</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.notes || '-' }}</div>
                  </td>
                  <td class="px-2 py-2 print:px-1 print:py-1 border-r border-gray-400 text-center print:hidden">
                    <div class="text-sm print:text-xs font-medium text-black bg-gray-100 px-1 py-1 border border-gray-400 text-center">
                      {{ __(artifact.status) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 print:hidden">
                    <div class="flex gap-1">
                      <button @click="editArtifact(artifact.id)" class="p-2 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors duration-200 hover:shadow-sm" :title="__('Edit')">
                        <i class="fas fa-edit text-sm"></i>
                      </button>
                      <button @click="deleteArtifact(artifact.id, artifact.artifact_code)" class="p-2 bg-red-100 text-red-800 rounded-md hover:bg-red-200 transition-colors duration-200 hover:shadow-sm" :title="__('Delete')">
                        <i class="fas fa-trash text-sm"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!client.artifacts.length">
                  <td colspan="10" class="px-6 py-12 text-center print:colspan-7">
                    <div class="text-gray-400 text-center">
                      <i class="fas fa-inbox text-4xl mb-3 block"></i>
                      <div class="text-lg font-medium">{{ __('No items found.') }}</div>
                      <div class="text-sm">{{ __('This client has no items registered yet.') }}</div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Delivery Documentation Section -->
        <div class="bg-white shadow-lg print:shadow-none border-2 border-gray-300 p-6 print:p-3 mb-6 print:mb-3">
          <div class="mb-4 print:mb-2 border-b-2 border-gray-400 pb-2">
            <h3 class="text-xl print:text-lg font-semibold text-black flex items-center">
              <i class="fas fa-file-signature mr-3 text-gray-600 text-xl print:hidden"></i>
              <span>Delivery Documentation | توثيق الاستلام</span>
            </h3>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-2">
            <!-- Column 1: Delivery Information -->
            <div class="space-y-4 print:space-y-2">
              <!-- Delivered By -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Delivered by - سُلم بواسطة</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ $page.props.auth?.user?.name || 'System User' }}</span>
                </div>
              </div>
              
              <!-- Delivery Date -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Delivery Date - تاريخ التسليم</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ client.received_date || new Date().toLocaleDateString() }}</span>
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
              <!-- Received By -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Received by - أُستلم بواسطة</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ client.full_name || 'Client Name' }}</span>
                </div>
              </div>
              
              <!-- Reception Date -->
              <div class="bg-gray-50 border border-gray-300 p-3 print:p-2">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-black text-base print:text-sm">Reception Date - تاريخ الاستلام</span>
                  <span class="text-black text-lg print:text-sm font-medium">{{ getCurrentDate() }}</span>
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
          <Link :href="$route('reception.index')" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-base font-semibold hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg border-2 border-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>
            {{ __('Back to Clients') }}
          </Link>
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
    client: Object,
    received_by: String
  },


  methods: {
    __(key) {
      const t = {
        'Client Details': 'تفاصيل العميل',
        'View all information about the client and their items.': 'عرض جميع بيانات العميل وعناصره المسجلة',
        'Client Information': 'معلومات العميل',
        'Test Request': 'طلب اختبار',
        'Customer Name': 'اسم العميل',
        'Company Name': 'اسم الشركة',
        'Customer Code': 'كود العميل',
        'Mobile No': 'رقم الجوال',
        'Email': 'البريد الإلكتروني',
        'City/Address': 'المدينة/العنوان',
        'Received Date': 'تاريخ الاستلام',
        'Delivery Date': 'تاريخ التسليم المتوقع',
        'Items': 'العناصر',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'ct': 'قيراط',
        'gm': 'جرام',
        'Notes': 'ملاحظات',
        'Status': 'الحالة',
        'No items found.': 'لا توجد عناصر مسجلة',
        'Back to Clients': 'العودة لقائمة العملاء',
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض',
        'Not specified': 'غير محدد',
        'Receiving Record No': 'رقم سجل الاستلام',
        'Received By': 'تم الاستلام بواسطة',
        'Received In': 'استلم في',
        'Edit Client': 'تعديل العميل',
        'Actions': 'الإجراءات',
        'Edit': 'تعديل',
        'Delete': 'حذف',
        'Are you sure you want to delete item': 'هل أنت متأكد من أنك تريد حذف العنصر',
        'Error deleting item. Please try again.': 'خطأ في حذف العنصر. يرجى المحاولة مرة أخرى.'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    getCurrentDate() {
      const today = new Date()
      const day = String(today.getDate()).padStart(2, '0')
      const month = String(today.getMonth() + 1).padStart(2, '0')
      const year = today.getFullYear()
      return `${day}/${month}/${year}`
    },
    editClient() {
      this.$inertia.visit(this.$route('reception.edit-client', this.client.id))
    },
    editArtifact(artifactId) {
      this.$inertia.visit(this.$route('reception.edit-artifact', artifactId))
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
    },
    deleteArtifact(artifactId, artifactCode) {
      if (confirm(this.__('Are you sure you want to delete item') + ' "' + artifactCode + '"?')) {
        this.$inertia.delete(this.$route('reception.delete-artifact', artifactId), {
          onSuccess: () => {
            // Artifact will be automatically removed from the list
          },
          onError: (errors) => {
            if (errors.error) {
              alert(errors.error)
            } else {
              alert(this.__('Error deleting item. Please try again.'))
            }
          }
        })
      }
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

/* Additional print optimizations */
.printing {
  overflow: visible !important;
}

.printing * {
  visibility: visible;
}

.printing .print\\:hidden,
.printing .print\\:hidden * {
  visibility: hidden !important;
  display: none !important;
}
</style> 