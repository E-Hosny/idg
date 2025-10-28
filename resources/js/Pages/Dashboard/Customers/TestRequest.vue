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
                <!-- دمج خانة واحدة على اليمين -->
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 w-1/6 text-center align-middle bg-gray-50 print:bg-gray-100" rowspan="3">
                  <img src="/images/idg_logo.jpg" alt="IDG Logo" class="w-12 h-12 print:w-8 print:h-8 rounded-full mx-auto" />
                  <div class="text-xs print:text-[10px] mt-1 font-semibold">IDG</div>
                </td>
                <td class="px-2 py-3 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 text-center align-middle font-semibold print:text-sm">
                  <div>Approved by</div>
                </td>
                <td class="px-2 py-3 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 text-center align-middle font-semibold print:text-sm">
                  <div>Prepared by</div>
                </td>
                <!-- دمج 3 خانات على الشمال -->
                <td class="px-2 py-3 print:px-1 print:py-2 text-black w-1/2 text-center align-middle bg-gray-50 print:bg-gray-100" rowspan="3" colspan="3">
                  <div class="text-xl print:text-lg font-bold">Test Request</div>
                </td>
              </tr>
              <tr class="border-b-2 border-gray-300">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle print:text-sm font-medium">Sultan Aldosari</td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle print:text-sm font-medium">Enas Ibrahim</td>
              </tr>
              <tr class="border-b-2 border-gray-300">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle text-xs print:text-[10px] text-gray-700">Laboratory Manager</td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 text-center align-middle text-xs print:text-[10px] text-gray-700">Lab. Management Supervisor</td>
              </tr>
              <tr class="border-b-2 border-gray-300 bg-gray-100">
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div style="visibility: hidden;">Document Date</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Issue Date</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Issue No., Revision No</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Level</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black border-r-2 border-gray-300 w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Classification</div>
                </td>
                <td class="px-2 py-2 print:px-1 print:py-1 text-black w-1/6 font-bold text-center align-middle print:text-xs">
                  <div>Document Number</div>
                </td>
              </tr>
              <tr>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">&nbsp;</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">15/3/2025</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">002, 002</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Document</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black border-r-2 border-gray-300 text-center align-middle font-semibold print:text-sm">Control</td>
                <td class="px-2 py-3 print:px-1 print:py-2 text-black text-center align-middle font-semibold print:text-sm">HOT-F03</td>
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
          <div class="flex justify-center gap-4 flex-wrap">
            <button @click="downloadPdf" class="inline-flex items-center px-8 py-3 bg-red-700 text-white text-lg font-semibold hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 shadow-md hover:shadow-lg">
              <i class="fas fa-file-pdf mr-3 text-xl"></i>
              <span>Download PDF</span>
              <span class="mx-3">|</span>
              <span>تحميل PDF</span>
            </button>
            <button @click="printPage" class="inline-flex items-center px-8 py-3 bg-green-700 text-white text-lg font-semibold hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200 shadow-md hover:shadow-lg">
              <i class="fas fa-print mr-3 text-xl"></i>
              <span>Print Document</span>
              <span class="mx-3">|</span>
              <span>طباعة المستند</span>
            </button>
            <button @click="triggerFileUpload" class="inline-flex items-center px-8 py-3 bg-purple-700 text-white text-lg font-semibold hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200 shadow-md hover:shadow-lg">
              <i class="fas fa-upload mr-3 text-xl"></i>
              <span>Upload Signed Document</span>
              <span class="mx-3">|</span>
              <span>رفع المستند الموقع</span>
            </button>
            <input 
              ref="fileInput" 
              type="file" 
              accept=".pdf" 
              @change="handleFileUpload" 
              style="display: none;"
            />
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
                <span>Save | حفظ</span>
              </button>
              <button 
                @click="cancelEditingTestRequest" 
                class="inline-flex items-center px-6 py-3 bg-gray-700 text-white text-lg font-semibold hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200 shadow-md hover:shadow-lg"
              >
                <i class="fas fa-times mr-2 text-xl"></i>
                <span>Cancel | إلغاء</span>
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
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">تاريخ التسليم<br>Delivery Date</th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-semibold text-sm print:text-xs text-center">{{ __('Weight') }}</th>
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
                    <div class="text-sm print:text-xs text-black">{{ getFullType(artifact) }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.service || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.delivery_type || '-' }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div class="text-sm print:text-xs text-black">{{ artifact.expected_date ? formatDate(artifact.expected_date) : calculateExpectedDate(artifact.delivery_type) }}</div>
                  </td>
                  <td class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-center">
                    <div v-if="artifact.weight" class="text-sm print:text-xs text-black">
                      {{ artifact.weight }} {{ artifact.unit_type === 'carat' ? __('ct') : __('gm') }}
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
                  <td colspan="8" class="border border-gray-400 px-3 py-8 print:px-1 print:py-4 text-center">
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

        <!-- Terms and Conditions Section -->
        <div class="bg-white shadow-lg print:shadow-none border-2 border-gray-300 p-6 print:p-3 mb-6 print:mb-3">
          <div class="mb-4 print:mb-2 border-b-2 border-gray-400 pb-2">
            <h3 class="text-xl print:text-lg font-semibold text-black flex items-center">
              <i class="fas fa-file-contract mr-3 text-gray-600 text-xl print:hidden"></i>
              <span>Terms and Conditions | الشروط والأحكام</span>
            </h3>
          </div>
          
          <div class="overflow-x-auto print:overflow-visible">
            <table class="w-full border-collapse border-2 border-gray-400">
              <thead>
                <tr class="bg-gray-100 print:bg-gray-200">
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-bold text-sm print:text-xs text-left w-1/2">
                    Terms and Conditions:
                  </th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-bold text-sm print:text-xs text-right w-1/2" dir="rtl">
                    :الشروط والأحكام
                  </th>
                </tr>
              </thead>
              <tbody>
                <!-- Term 1 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    IDG Lab(herein after the laboratory) shall not be held responsible for any damage to the above listed Stone(s)/Diamond(s)/Jewellery or part of item, or loss of the stone(s)/ Diamond(s)/Jewellery or part of them while the relevant stone(s)/Diamond(s)/Jewellery are held by the laboratory, unless such damage is caused by IDG Lab or any of its employees
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    لا يتحمل مختبر IDG (ويشار إليه فيما بعد بـ "المختبر") أي مسؤولية عن أي ضرر يلحق بالأحجار/الألماس/المجوهرات المذكورة أعلاه أو أي جزء منها، أو عن فقدانها كلياً أو جزئياً أثناء تواجدها في حيازة المختبر، ما لم يكن الضرر ناتجاً عن المختبر أو أحد موظفيه.
                  </td>
                </tr>
                
                <!-- Term 2 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    IDG Lab is committed to issue Certificate Report(s) if the customer requests that, in one condition only: if the result of stone(s)/diamond(s)/ jewellery tested is natural, but if the testing results prove that stone is not natural, IDG Lab will only give an oral clarification if requested by customer and does not give any paper(s) document(s).
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    يلتزم مختبر IDG بإصدار شهادة/تقرير بناءً على طلب العميل، وذلك بشرط واحد فقط: إذا كانت نتيجة الاختبار للحجر/الألماس/المجوهرات طبيعية، أما إذا أثبتت النتائج أن الحجر غير طبيعي، فسيكتفي فقط بتقديم توضيح شفهي بناءً على طلب العميل، ولن يتم إصدار أي مستندات خطية.
                  </td>
                </tr>
                
                <!-- Term 3 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    - I/We are aware and agree to the special terms of the laboratory examination and certification of diamond(s), coloured stone(s), Jewelery, and Precious Metal(s). We confirm deposition of the above-mentioned stone(s)/diamond(s)/jewellery subject to the above-mentioned condition, and on that I/We signed.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    أنا/ نقر بأننا على علم ونوافق على الشروط الخاصة بفحص واعتماد الألماس/الأحجار الكريمة/المجوهرات/المعادن الثمينة في المختبر، ونؤكد إيداعنا لما يتبع الأحجار/الألماس/المجوهرات المذكورة أعلاه وفقاً للشروط المذكورة، وقد أقدمنا على ذلك.
                  </td>
                </tr>
                
                <!-- Term 4 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    The stone(s)/diamond(s)/jewellery represents only itself and will be delivered to customer with Certificate/Report(s) issue, unless return is being requested. The laboratory is not responsible for the source of stone(s)/diamond(s)/jewellery.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    تمثل الأحجار/الألماس/المجوهرات نفسها فقط وسيتم تسليمها للعميل مع الشهادة/التقرير، ما لم يطلب إرجاعها. ولا يتحمل المختبر أي مسؤولية تجاه مصدر الأحجار/الألماس/المجوهرات.
                  </td>
                </tr>
                
                <!-- Term 5 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    Any information provided to the laboratory, which has been obtained from sources other than the customer, is confidential and remains the exclusive property of the original source unless prior consent has been obtained from the original source.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    جميع المعلومات المقدمة للمختبر والتي تم الحصول عليها من مصادر غير العميل تعتبر معلومات سرية وتبقى ملكية حصرية للمصدر الأصلي ما لم يتم الحصول على موافقة مسبقة من ذلك المصدر الأصلي.
                  </td>
                </tr>
                
                <!-- Term 6 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    Tested items are returned to the customer along with the final report and it will be collected within 90 days. the right laboratory reserves the right to dispose of the item, retain it for research purposes, charge a storage fee without further and any liability to the customer
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    تعاد العناصر المختبرة إلى العميل مع التقرير النهائي، ويجب استلامها خلال 90 يوماً. يحتفظ المختبر بحقه في التخلص من العينة، أو الاحتفاظ بها لأغراض البحث، أو فرض رسوم تخزين دون أي تحمل إضافي أو مسؤولية.
                  </td>
                </tr>
                
                <!-- Term 7 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    Delivery time is estimated if there is any delay we will inform you
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    وقت التسليم تقديري، وفي حال حدوث أي تأخير سنقوم بإبلاغكم
                  </td>
                </tr>
                
                <!-- Term 8 -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top">
                    For more about IDGL's terms and conditions, please visit our website. https://idg-lab.com.sa/
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top text-right" dir="rtl">
                    للمزيد من المعلومات حول الشروط والأحكام الخاصة بمختبر IDG، يرجى زيارة موقعنا الإلكتروني https://idg-lab.com.sa/
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
              <i class="fas fa-truck mr-3 text-gray-600 text-xl print:hidden"></i>
              <span>Delivery Documentation | توثيق التسليم</span>
            </h3>
          </div>
          
          <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-2">
            <!-- Column 1: Delivery Information -->
            <div class="space-y-4 print:space-y-2">
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
                    <img src="/maram_sign.png" alt="Signature" class="max-h-full max-w-full object-contain">
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

    <!-- Add Artifact Modal -->
    <div v-if="showAddArtifactModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAddArtifactModal"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <form @submit.prevent="submitArtifact">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Add New Artifact | إضافة قطعة جديدة
                  </h3>
                  
                  <div class="space-y-6">
                    <!-- Artifact Information -->
                    <div class="border-b border-gray-200 pb-4">
                      <h4 class="text-md font-medium text-gray-900 mb-3">Artifact Information | معلومات القطعة</h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            Type | النوع *
                          </label>
                          <select
                            v-model="newArtifact.type"
                            @change="resetServiceWhenTypeChanges"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>Select Type | اختر النوع</option>
                            <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            النوع الفرعي | Subtype <span class="text-gray-400">(اختياري | Optional)</span>
                          </label>
                          <input
                            v-model="newArtifact.subtype"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="أدخل النوع الفرعي | Enter subtype"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            Service | الخدمة *
                          </label>
                          <select
                            v-model="newArtifact.service"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>Select Service | اختر الخدمة</option>
                            <option v-for="option in getServiceOptions(newArtifact.type)" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            Weight | الوزن *
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
                            Delivery Type | نوع التسليم
                          </label>
                          <select
                            v-model="newArtifact.delivery_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                            <option value="" disabled>Select Delivery Type | اختر نوع التسليم</option>
                            <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">
                              {{ option.label }}
                            </option>
                          </select>
                        </div>
                        
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-700">
                            Notes | ملاحظات
                          </label>
                          <textarea
                            v-model="newArtifact.notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Any additional notes | أي ملاحظات إضافية..."
                          ></textarea>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Price Calculation -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                      <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                          Calculated Price | السعر المحسوب:
                        </span>
                        <span class="text-lg font-semibold text-green-600">
                          {{ newArtifact.price ? newArtifact.price + ' SAR' : 'Please calculate | يرجى الحساب' }}
                        </span>
                      </div>
                      <div class="mt-2">
                        <button
                          @click="calculatePrice"
                          type="button"
                          :disabled="addingArtifact || !newArtifact.type || !newArtifact.service || !newArtifact.weight"
                          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          Calculate | احسب
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
                {{ addingArtifact ? 'Adding... | جاري الإضافة...' : 'Add Artifact | إضافة قطعة' }}
              </button>
              <button
                @click="closeAddArtifactModal"
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel | إلغاء
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeSuccessModal"></div>
        
        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <!-- Success Icon and Content -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                <i class="fas fa-check text-green-600 text-xl"></i>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
                  تم رفع المستند بنجاح!
                </h3>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  Document Uploaded Successfully!
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4">
                    تم رفع المستند الموقع بنجاح. سيتم تحويلك إلى صفحة طلبات الاختبار خلال ثوانٍ قليلة.
                  </p>
                  <p class="text-sm text-gray-500">
                    The signed document has been uploaded successfully. You will be redirected to the test requests page in a few seconds.
                  </p>
                </div>
                
                <!-- Countdown Timer -->
                <div class="mt-4 flex items-center justify-center">
                  <div class="bg-green-100 rounded-full p-3">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-600 rounded-full countdown-circle">
                      <span class="text-white font-bold text-sm" id="countdown">3</span>
                    </div>
                  </div>
                  <span class="ml-3 text-sm text-gray-600">ثوانٍ | seconds</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="redirectToTestRequestsList"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <i class="fas fa-arrow-right mr-2"></i>
              الانتقال الآن | Go Now
            </button>
            <button
              @click="closeSuccessModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <i class="fas fa-times mr-2"></i>
              إغلاق | Close
            </button>
          </div>
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
      },
      uploadingFile: false,
      showSuccessModal: false,
      showAddArtifactModal: false,
      addingArtifact: false,
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
      typeOptions: [
        { value: 'Colored Gemstones', label: 'Colored Gemstones | أحجار كريمة ملونة' },
        { value: 'Other Colored Gemstones', label: 'Other Colored Gemstones | أحجار كريمة ملونة أخرى' },
        { value: 'Colorless Diamonds', label: 'Colorless Diamonds | ألماس عديم اللون' },
        { value: 'Jewellery', label: 'Jewellery | مجوهرات' }
      ],
      weightUnitOptions: [
        { value: 'ct', label: 'قيراط | ct' },
        { value: 'gm', label: 'جرام | gm' }
      ],
      deliveryOptions: [
        { value: 'Regular', label: 'عادي | Regular' },
        { value: 'Express Service', label: 'خدمة سريعة | Express Service' },
        { value: 'Same Day', label: 'نفس اليوم | Same Day' },
        { value: '24 hours', label: '24 ساعة | 24 hours' },
        { value: '48 hours', label: '48 ساعة | 48 hours' },
        { value: '72 hours', label: '72 ساعة | 72 hours' }
      ]
    }
  },
  methods: {
    getFullType(artifact) {
      if (!artifact.type) return '-';
      return artifact.subtype ? `${artifact.type} - ${artifact.subtype}` : artifact.type;
    },
    
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
      // Open modal to add artifact
      this.showAddArtifactModal = true
    },
    
    closeAddArtifactModal() {
      this.showAddArtifactModal = false
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
    },
    
    getServiceOptions(type) {
      const serviceOptions = {
        'Colored Gemstones': [
          { value: 'Regular - ID Report', label: 'Regular - ID Report | تقرير معرف عادي' },
          { value: 'Regular - ID + Origin', label: 'Regular - ID + Origin | عادي - هوية + أصل' },
          { value: 'Mini Card Report - ID Report', label: 'Mini Card Report - ID Report | تقرير بطاقة مصغرة' },
          { value: 'Mini Card Report - ID + Origin', label: 'Mini Card Report - ID + Origin | تقرير بطاقة - هوية + أصل' }
        ],
        'Other Colored Gemstones': [
          { value: 'Regular - ID Report', label: 'Regular - ID Report | تقرير معرف عادي' },
          { value: 'Mini Card Report - ID Report', label: 'Mini Card Report - ID Report | تقرير بطاقة مصغرة' }
        ],
        'Colorless Diamonds': [
          { value: 'Regular - Diamond Grading Report', label: 'Regular - Diamond Grading Report | تقرير تصنيف الألماس' },
          { value: 'Mini Card Report - Mini Report', label: 'Mini Card Report - Mini Report | تقرير بطاقة مصغرة' }
        ],
        'Jewellery': [
          { value: 'Regular - Jewellery Report', label: 'Regular - Jewellery Report | تقرير المجوهرات' },
          { value: 'Mini Card Report - Mini Jewellery Report', label: 'Mini Card Report - Mini Jewellery Report | تقرير مجوهرات مصغر' }
        ]
      }
      
      return serviceOptions[type] || []
    },
    
    resetServiceWhenTypeChanges() {
      this.newArtifact.service = ''
      if (this.newArtifact.type === 'Jewellery') {
        this.newArtifact.weight_unit = 'gm'
      } else {
        this.newArtifact.weight_unit = 'ct'
      }
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
          alert('Error calculating price. Please try again.')
        }
      } catch (error) {
        console.error('Error calculating price:', error)
        alert('Error calculating price. Please try again.')
      }
    },
    
    async submitArtifact() {
      // Validate required fields
      if (!this.newArtifact.type || !this.newArtifact.service || !this.newArtifact.weight) {
        alert('Please fill in all required fields (Type, Service, Weight)')
        return
      }

      this.addingArtifact = true
      
      try {
        // Submit artifact to Laravel backend - same route as Artifacts.vue
        this.$inertia.post('/dashboard/customers/artifacts', {
          client_id: this.customer.id,
          test_request_id: this.testRequest.id,
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
            alert('Error adding artifact. Please try again.')
          }
        })
      } catch (error) {
        console.error('Error adding artifact:', error)
      } finally {
        this.addingArtifact = false
      }
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
      // Open dedicated print page in new tab
      const printUrl = `/dashboard/test-requests/${this.testRequest.id}/print`;
      window.open(printUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    },
    
    downloadPdf() {
      // Open print page with auto-download in new window
      const printUrl = `/dashboard/test-requests/${this.testRequest.id}/download-pdf`;
      window.open(printUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    },
    
    triggerFileUpload() {
      // Trigger the hidden file input
      this.$refs.fileInput.click();
    },
    
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) {
        return;
      }
      
      // Validate file type
      if (file.type !== 'application/pdf') {
        alert('يرجى اختيار ملف PDF فقط | Please select a PDF file only.');
        if (this.$refs.fileInput) {
          this.$refs.fileInput.value = '';
        }
        return;
      }
      
      // Validate file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        alert('حجم الملف يجب أن يكون أقل من 10 ميجابايت | File size must be less than 10MB.');
        if (this.$refs.fileInput) {
          this.$refs.fileInput.value = '';
        }
        return;
      }
      
      // Show confirmation
      if (!confirm(`هل أنت متأكد من رفع هذا الملف؟\nاسم الملف: ${file.name}\nحجم الملف: ${(file.size / 1024 / 1024).toFixed(2)} MB\n\nAre you sure you want to upload this file?\nFilename: ${file.name}\nFile size: ${(file.size / 1024 / 1024).toFixed(2)} MB`)) {
        if (this.$refs.fileInput) {
          this.$refs.fileInput.value = '';
        }
        return;
      }
      
      this.uploadingFile = true;
      
      // Use Inertia's post method with FormData
      const formData = new FormData();
      formData.append('signed_document', file);
      
      this.$inertia.post(
        `/dashboard/test-requests/${this.testRequest.id}/upload-signed`,
        formData,
        {
          forceFormData: true,
          preserveState: false,
          preserveScroll: true,
          onSuccess: (page) => {
            this.uploadingFile = false;
            if (this.$refs.fileInput) {
              this.$refs.fileInput.value = '';
            }
            // Show beautiful success popup
            this.showSuccessPopup();
          },
          onError: (errors) => {
            this.uploadingFile = false;
            if (this.$refs.fileInput) {
              this.$refs.fileInput.value = '';
            }
            console.error('Upload errors:', errors);
            
            // Show specific error message
            let errorMessage = 'فشل في رفع الملف | Failed to upload file';
            
            if (errors.signed_document) {
              errorMessage = Array.isArray(errors.signed_document) 
                ? errors.signed_document[0] 
                : errors.signed_document;
            } else if (errors.error) {
              errorMessage = errors.error;
            } else if (typeof errors === 'string') {
              errorMessage = errors;
            }
            
            alert(errorMessage);
          },
          onFinish: () => {
            this.uploadingFile = false;
          }
        }
      );
    },
    
    showSuccessPopup() {
      this.showSuccessModal = true;
      
      // Countdown timer
      let countdown = 3;
      const countdownElement = document.getElementById('countdown');
      
      const updateCountdown = () => {
        if (countdownElement) {
          countdownElement.textContent = countdown;
        }
        countdown--;
        
        if (countdown >= 0) {
          setTimeout(updateCountdown, 1000);
        } else {
          this.redirectToTestRequestsList();
        }
      };
      
      // Start countdown
      updateCountdown();
    },
    
    redirectToTestRequestsList() {
      this.showSuccessModal = false;
      // Navigate to test requests list page
      this.$inertia.visit(`/dashboard/customers/${this.customer.qoyod_customer_id}/test-requests`);
    },
    
    closeSuccessModal() {
      this.showSuccessModal = false;
    },
    
    calculateExpectedDate(deliveryType) {
      if (!deliveryType) return '-';
      
      const today = new Date();
      let targetDate = new Date(today);
      
      switch (deliveryType) {
        case 'Regular':
          // بعد 7 أيام عمل (تجنب الجمعة)
          targetDate = this.addBusinessDays(today, 7);
          break;
        case 'Express Service':
        case 'Same Day':
          // نفس اليوم - إذا كان جمعة، اجعله السبت
          targetDate = this.skipFridayIfNeeded(today);
          break;
        case '24 hours':
          // الغد (تجنب الجمعة)
          targetDate = this.addBusinessDays(today, 1);
          break;
        case '48 hours':
          // بعد الغد (تجنب الجمعة)
          targetDate = this.addBusinessDays(today, 2);
          break;
        case '72 hours':
          // بعد 3 أيام عمل (تجنب الجمعة)
          targetDate = this.addBusinessDays(today, 3);
          break;
        default:
          return '-';
      }
      
      const day = String(targetDate.getDate()).padStart(2, '0');
      const month = String(targetDate.getMonth() + 1).padStart(2, '0');
      const year = targetDate.getFullYear();
      return `${day}/${month}/${year}`;
    },

    // إضافة أيام عمل (تجنب الجمعة)
    addBusinessDays(startDate, days) {
      let date = new Date(startDate);
      let addedDays = 0;
      
      while (addedDays < days) {
        date.setDate(date.getDate() + 1);
        // تجنب الجمعة (5 = الجمعة في JavaScript)
        if (date.getDay() !== 5) {
          addedDays++;
        }
      }
      
      return date;
    },

    // تجنب الجمعة إذا كان التاريخ المحدد جمعة
    skipFridayIfNeeded(date) {
      let resultDate = new Date(date);
      // إذا كان جمعة (5)، انتقل إلى السبت (6)
      if (resultDate.getDay() === 5) {
        resultDate.setDate(resultDate.getDate() + 1);
      }
      return resultDate;
    }
  }
}
</script> 

<style>
/* Success Modal Animation */
.success-modal-enter-active, .success-modal-leave-active {
  transition: opacity 0.3s ease;
}

.success-modal-enter-from, .success-modal-leave-to {
  opacity: 0;
}

.success-modal-content-enter-active, .success-modal-content-leave-active {
  transition: all 0.3s ease;
}

.success-modal-content-enter-from, .success-modal-content-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

/* Countdown Animation */
@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

.countdown-circle {
  animation: pulse 1s infinite;
}

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
