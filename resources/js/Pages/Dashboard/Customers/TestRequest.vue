<template>
  <div class="min-h-screen bg-gray-200 print:bg-white print:min-h-0">
    <!-- Document header bar (matches official form strip: gray bar, navy serif title, logo in white box) -->
    <div class="bg-gray-200 p-4 print:bg-white print:p-2">
      <div class="max-w-7xl mx-auto print:max-w-none">
        <div
          class="test-request-doc-header-bar relative flex items-center justify-between mb-6 print:mb-3 min-h-[3.25rem] print:min-h-[2.75rem] px-5 print:px-4 py-2.5 print:py-2 bg-[#f2f2f2]"
        >
          <span
            class="test-request-doc-header-text z-10 shrink-0 text-xl print:text-lg whitespace-nowrap pr-2"
          >
            HOT - F03
          </span>
          <span
            class="test-request-doc-header-text absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xl print:text-lg text-center pointer-events-none max-w-[55%] truncate"
          >
            TEST Request
          </span>
          <div class="z-10 shrink-0 bg-white p-1.5 print:p-1 border border-gray-200">
            <img
              src="/images/idg_logo.jpg"
              alt="IDG Logo"
              class="block h-12 w-12 print:h-10 print:w-10 rounded-full object-cover"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content - Optimized for Landscape Print -->
    <div class="p-4 print:p-2">
      <div class="max-w-7xl mx-auto print:max-w-none">
        <!-- Classic Client Information Form -->
        <div class="bg-white border-2 border-gray-300 shadow-lg mb-6 print:shadow-none print:mb-3">
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

        <!-- Draft status + confirm (matches document chrome; single confirm CTA) -->
        <div
          v-if="isDraft"
          class="mb-6 print:hidden border-2 border-gray-400 bg-white px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
          <div class="flex items-start gap-3 text-left sm:text-start">
            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center border border-gray-400 bg-gray-100 text-gray-700">
              <i class="fas fa-file-alt"></i>
            </span>
            <div>
              <p class="text-base font-semibold text-black">
                Draft | مسودة
              </p>
              <p class="text-sm text-gray-600 mt-1 max-w-2xl">
                Confirm when ready to submit as pending and notify the lab.
                | أكّد عند الجاهزية لاعتماده معلّقاً وإشعار المختبر.
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="confirmTestRequest"
            :disabled="confirming"
            class="inline-flex items-center justify-center px-8 py-3 bg-green-700 text-white text-lg font-semibold hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-60 transition-colors duration-200 shadow-md hover:shadow-lg shrink-0"
          >
            <i class="fas fa-check mr-3 text-xl"></i>
            <span>{{ confirming ? 'Confirming...' : 'Confirm Request' }}</span>
            <span class="mx-3">|</span>
            <span>{{ confirming ? 'جارٍ الاعتماد...' : 'اعتماد الطلب' }}</span>
          </button>
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
              {{ __('Total') }}: {{ groupedArtifacts.length }} {{ __('items') }} | المجموع: {{ groupedArtifacts.length }} عنصر
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
                <tr v-for="(artifact, idx) in groupedArtifacts" :key="artifact.id" class="hover:bg-gray-50 print:hover:bg-transparent">
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
                    <div class="text-sm print:text-xs text-black">
                      {{ artifact.delivery_type === 'Specific Date' && artifact.specific_delivery_date 
                          ? formatDate(artifact.specific_delivery_date) 
                          : (artifact.expected_date ? formatDate(artifact.expected_date) : calculateExpectedDate(artifact.delivery_type)) 
                      }}
                    </div>
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
            <table class="w-full border-collapse border-2 border-gray-400 terms-conditions-table" dir="ltr">
              <thead>
                <tr class="bg-gray-100 print:bg-gray-200">
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-bold text-sm print:text-xs w-1/2 terms-col-en">
                    Terms and Conditions:
                  </th>
                  <th class="border border-gray-400 px-3 py-2 print:px-1 print:py-1 text-black font-bold text-sm print:text-xs w-1/2 terms-col-ar">
                    :الشروط والأحكام
                  </th>
                </tr>
              </thead>
              <tbody>
                <!-- Term 1 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    IDG Laboratory Reports represent an independent professional opinion based on non-destructive examinations performed on the submitted item(s) as received, using advanced laboratory equipment and the expert judgment of qualified gemologists. Reports are not guarantees, appraisals, or valuations. IDG Laboratory does not alter, clean, or remove settings without the Client's prior written authorization. Where the condition or mounting of an item limits examination, such limitations will be stated in the report, and IDG Laboratory shall not be liable for any resulting impact on the accuracy of the findings.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    تمثل تقارير مختبر IDG رأياً فنياً مستقلاً يستند إلى الفحص غير الإتلافي للعينات المقدمة بالحالة التي استلمها بها المختبر، وذلك باستخدام أجهزة مخبرية متقدمة والخبرة المهنية لأخصائيي الأحجار الكريمة المؤهلين. ولا تُعد هذه التقارير ضماناً أو تقييماً مالياً أو تثميناً للقيمة. لا يقوم مختبر IDG بتعديل أو تنظيف أو فك الأحجار أو أجزاء المجوهرات دون الحصول على موافقة خطية مسبقة من العميل. وفي حال كانت حالة العينة أو طريقة تركيبها تحد من إمكانية الفحص، فسيتم توضيح هذه القيود في التقرير، ولا يتحمل مختبر IDG أي مسؤولية عن أي تأثير قد تسببه تلك القيود على دقة أو شمولية نتائج الفحص.
                  </td>
                </tr>
                <!-- Term 2 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    IDG Laboratory issues Certificates / Reports only for items determined to be of natural origin. If examination indicates a synthetic origin, undisclosed treatment, or inconclusive results using non-destructive methods, IDG may issue a report stating the findings and any limitations or provide a Verification Letter. Any identified treatments will be disclosed in the Comments section of the report. IDG Laboratory shall not be liable for any consequences resulting from the Client's decision not to authorize additional testing.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يصدر مختبر IDG الشهادات أو التقارير فقط للعينات التي يثبت أنها ذات منشأ طبيعي. وإذا أظهر الفحص أن العينة ذات منشأ صناعي، أو تحتوي على معالجة غير مفصح عنها، أو تعذر التوصل إلى نتيجة حاسمة باستخدام طرق الفحص غير الإتلافية، فيجوز للمختبر إصدار تقرير يوضح نتائج الفحص والقيود ذات الصلة، أو إصدار خطاب تحقق حسب الحالة. يتم الإفصاح عن أي معالجات يتم اكتشافها في قسم الملاحظات في التقرير. ولا يتحمل مختبر IDG أي مسؤولية عن أي نتائج أو تبعات تترتب على قرار العميل بعدم الموافقة على إجراء اختبارات إضافية عند الحاجة.
                  </td>
                </tr>
                <!-- Term 3 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    The Client warrants that all submitted stone(s), diamond(s), jewellery, or articles are lawfully owned or submitted with the owner's authorization and are free from legal claims or disputes. IDG Laboratory's findings and Certificates/Reports apply only to the specific item(s) examined. Unless otherwise instructed in writing, the examined item(s) will be returned with the issued Certificate/Report. IDG Laboratory accepts no liability for the origin, ownership, or any legal disputes relating to the submitted item(s). The Client shall indemnify IDG Laboratory against any claims arising from false or incomplete ownership declarations.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يقر العميل ويضمن أن جميع الأحجار الكريمة أو الألماس أو المجوهرات أو القطع المقدمة مملوكة له ملكية نظامية أو تم تقديمها بموافقة وتفويض من مالكها، وأنها خالية من أي مطالبات أو نزاعات قانونية. وتنطبق نتائج الفحص والشهادات أو التقارير الصادرة عن مختبر IDG حصرياً على العينة أو العينات التي تم فحصها. وما لم يوجه العميل تعليمات خطية بخلاف ذلك، فسيتم إعادة العينة أو العينات المفحوصة مع الشهادة أو التقرير الصادر. ولا يتحمل مختبر IDG أي مسؤولية تتعلق بمنشأ العينة أو ملكيتها أو أي مطالبات أو نزاعات قانونية مرتبطة بها. كما يلتزم العميل بتعويض وإبراء ذمة مختبر IDG من أي مطالبات أو مسؤوليات تنشأ نتيجة تقديم معلومات غير صحيحة أو غير مكتملة بشأن ملكية العينة أو صلاحية تقديمها للمختبر.
                  </td>
                </tr>
                <!-- Term 4 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    Any information, documents, or materials provided to IDG Laboratory by a source other than the Client shall remain the confidential property of the original source. IDG Laboratory shall not disclose or use such information except as necessary to perform the requested services, with the original source's written authorization, or as required by law. The Client warrants that they are authorized to submit any third-party information and agrees to indemnify IDG Laboratory against any claims arising from unauthorized submission of such information. Unless otherwise instructed in writing, IDG Laboratory will return the examined item(s) with the issued Report(s). The Client must collect the item(s) within ninety (90) days of the Report issuance date. Unclaimed items may be subject to storage fees. Items remaining unclaimed for two (2) years may be disposed of in accordance with applicable law after reasonable attempts to contact the Client. IDG Laboratory shall not be liable for any loss or damage resulting from storage or the Client's failure to collect the item(s).
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    تظل أي معلومات أو مستندات أو مواد يتم تزويد مختبر IDG بها من قبل جهة غير العميل ملكاً سرياً للجهة الأصلية المقدمة لها. ولا يجوز لمختبر IDG الإفصاح عن تلك المعلومات أو استخدامها إلا بالقدر اللازم لتنفيذ الخدمات المطلوبة، أو بموجب موافقة خطية من الجهة الأصلية، أو إذا كان ذلك مطلوباً بموجب الأنظمة واللوائح المعمول بها. ويقر العميل ويضمن أنه مخول نظاماً بتقديم أي معلومات أو مستندات تخص أطرافاً أخرى، كما يلتزم بتعويض وإبراء ذمة مختبر IDG من أي مطالبات أو مسؤوليات تنشأ نتيجة تقديم معلومات أو مستندات تخص الغير دون الحصول على التفويض أو الصلاحية. ما لم يوجه العميل تعليمات خطية بخلاف ذلك، سيقوم مختبر IDG بإعادة العينة أو العينات المفحوصة مع التقرير أو التقارير الصادرة. ويلتزم العميل باستلام العينة أو العينات خلال تسعين (90) يوماً من تاريخ إصدار التقرير. ويجوز للمختبر فرض رسوم تخزين على العينات التي لا يتم استلامها خلال هذه المدة. أما العينات التي تبقى دون استلام لمدة سنتين (2)، فيجوز للمختبر التصرف فيها وفقاً للأنظمة واللوائح المعمول بها، وذلك بعد بذل محاولات معقولة للتواصل مع العميل. ولا يتحمل مختبر IDG أي مسؤولية عن أي خسائر أو أضرار تنتج عن التخزين أو فشل العميل في استلام العينة أو العينات.
                  </td>
                </tr>
                <!-- Term 5 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    IDG Lab's delivery timelines are estimates only. Turnaround may be delayed due to sample condition, extra analyses, client-approved work, specialist referrals, instrument downtime, logistics, customs, or force majeure. If a material delay occurs, the Lab will notify the client with a revised estimate. IDG Lab accepts no liability for any loss or cost arising from such delays unless otherwise agreed in writing.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    تُعد المدد الزمنية التي يحددها مختبر IDG لإنجاز الخدمات تقديرية فقط، وقد تتأخر عملية إصدار النتائج أو التقارير بسبب حالة العينة، أو الحاجة إلى إجراء تحاليل إضافية، أو أعمال يوافق عليها العميل، أو إحالة العينة إلى متخصصين، أو تعطل الأجهزة، أو مشكلات النقل أو التخليص الجمركي، أو حالات القوة القاهرة. وفي حال حدوث تأخير جوهري، سيقوم المختبر بإبلاغ العميل وتزويده بمدة زمنية تقديرية محدثة لإنجاز الخدمة. ولا يتحمل مختبر IDG أي مسؤولية عن أي خسائر أو تكاليف تنشأ نتيجة هذا التأخير، ما لم يتم الاتفاق على خلاف ذلك بموجب اتفاقية خطية.
                  </td>
                </tr>
                <!-- Term 6 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    The Client acknowledges that IDG Laboratory performs non-destructive examinations only. All findings, Certificates, Reports, and Verification Letters are based solely on non-destructive analyses and the professional judgment of qualified gemologists. IDG Laboratory does not warrant results that require destructive testing. Any destructive or invasive testing will only be performed with the Client's prior written authorization under a separate written agreement.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يقر العميل بأن مختبر IDG يجري الفحوصات باستخدام طرق غير إتلافية فقط. وتستند جميع النتائج والشهادات والتقارير وخطابات التحقق الصادرة عن المختبر حصرياً إلى الفحوصات غير الإتلافية وإلى الرأي المهني لأخصائيي الأحجار الكريمة المؤهلين. ولا يضمن مختبر IDG أي نتائج تتطلب إجراء اختبارات إتلافية. ولا يجوز إجراء أي اختبار إتلافي أو تدخل في العينة إلا بعد الحصول على موافقة خطية مسبقة من العميل وبموجب اتفاقية خطية منفصلة.
                  </td>
                </tr>
                <!-- Term 7 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    The IDG report bears the IDG Laboratory / SAAC / ILAC accreditation mark. The Client shall not reproduce, alter, edit, or use this report, any part of it, or the accreditation mark for advertising or promotional purposes without IDG Laboratory's prior written approval. IDG Laboratory reserves the right to declare a report void if unauthorized reproduction, alteration, or misuse is detected.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يحمل تقرير IDG شعار اعتماد مختبر IDG و SAAC و ILAC. ولا يجوز للعميل نسخ هذا التقرير أو إعادة إنتاجه أو تعديله أو تحريره أو استخدامه، كلياً أو جزئياً، أو استخدام شعار الاعتماد لأغراض إعلانية أو ترويجية دون الحصول على موافقة خطية مسبقة من مختبر IDG. ويحتفظ مختبر IDG بحقه في اعتبار التقرير لاغياً إذا تبين وجود أي نسخ أو تعديل أو استخدام غير مصرح به للتقرير أو لشعار الاعتماد.
                  </td>
                </tr>
                <!-- Term 8 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    Mounted items are examined at IDG Laboratory's discretion and only to the extent permitted by their setting. Some tests or measurements may be limited or not possible, and the report will state any examination limitations. IDG Laboratory shall not be liable for any reduction in accuracy resulting from such limitations.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يتم فحص القطع المركبة (المجوهرات المثبت بها أحجار) وفقاً لتقدير مختبر IDG، وبالقدر الذي تسمح به طريقة التركيب. وقد تكون بعض الاختبارات أو القياسات محدودة أو غير ممكنة بسبب التركيب، وسيتم توضيح جميع قيود الفحص في التقرير. ولا يتحمل مختبر IDG أي مسؤولية عن أي انخفاض في دقة النتائج أو محدودية الاستنتاجات الناتجة عن هذه القيود.
                  </td>
                </tr>
                <!-- Term 9 -->
                <tr class="border-b border-gray-300">
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    IDG Lab does not accept articles that are plated, coated, laminated or filled, as these fall outside the scope of ISO 23345:2021. Where coating or non-homogeneity is detected or suspected during the pre-test check, testing is stopped and the item is returned to the customer with the reason recorded. All reports state that results relate only to the items tested and to the surface analysed.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    لا يقبل مختبر IDG القطع المطلية أو المغطاة أو المصفحة أو المملوءة، لكونها تقع خارج نطاق تطبيق المواصفة ISO 23345:2021. وفي حال اكتشاف أو الاشتباه بوجود طلاء أو عدم تجانس في العينة أثناء الفحص الأولي قبل الاختبار، يتم إيقاف الفحص وإعادة القطعة إلى العميل، مع توثيق سبب الرفض. وتنص جميع التقارير على أن نتائج الفحص تنطبق فقط على العينة أو القطعة التي تم اختبارها، وعلى السطح الذي تم تحليله.
                  </td>
                </tr>
                <!-- Term 10 — acknowledgement -->
                <tr>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-en" dir="ltr">
                    By submitting the item(s), I/We acknowledge and accept IDG Laboratory's Terms and Conditions governing the examination, testing, and issuance of Certificates/Reports for Diamonds, Coloured Stones, Jewellery, and Precious Metals. My/Our signature on this submission form constitutes full and binding acceptance of these Terms and Conditions. All applicable terms and conditions are available on our official website: idg.sa.
                  </td>
                  <td class="border border-gray-400 px-3 py-3 print:px-2 print:py-2 text-black text-xs print:text-[10px] align-top leading-relaxed terms-col-ar" dir="rtl">
                    يُعد تقديم العينة أو العينات إلى مختبر IDG إقراراً/نقراً بأنني/بأننا قد اطلعنا على الشروط والأحكام الخاصة بالمختبر، وأوافق/نوافق عليها، والتي تنظم إجراءات الفحص والاختبار وإصدار الشهادات والتقارير الخاصة بالألماس والأحجار الكريمة الملونة والمجوهرات والمعادن الثمينة. ويعد توقيعي/توقيعنا على نموذج تقديم العينات موافقة نهائية وملزمة على جميع هذه الشروط والأحكام. جميع الشروط والأحكام المعمول بها متاحة على موقعنا الإلكتروني idg.sa.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Delivery Documentation: 2×6 grid (matches print form) -->
        <div class="bg-white shadow-lg print:shadow-none mb-6 print:mb-3 overflow-hidden print:overflow-visible">
          <table class="w-full border-collapse border-2 border-black delivery-doc-table page-break-inside-avoid" dir="ltr">
            <thead>
              <tr>
                <th
                  colspan="6"
                  class="bg-[#f5f5f5] border-b-2 border-black py-3 print:py-2 px-2 text-center font-bold text-black text-base print:text-sm font-serif"
                >
                  Delivery Documentation | توثيق التسليم
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle w-[15%]"
                >
                  Delivered by:<br />سلّم بواسطة
                </td>
                <td class="border border-black p-2 print:p-1.5 align-middle min-h-[3rem] print:min-h-[2.5rem] w-[17%] bg-white text-sm print:text-xs font-medium text-black text-center">
                  {{ customer?.company_representative_name || '-' }}
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle w-[13%]"
                >
                  Signature:<br />التوقيع
                </td>
                <td class="border border-black p-2 print:p-1.5 align-middle w-[20%] bg-white text-center">
                  <div class="min-h-[3.5rem] print:min-h-[2.75rem] bg-white border border-black border-opacity-30 mx-auto" />
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle w-[12%]"
                >
                  Date:<br />التاريخ
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 text-center font-bold text-black text-sm print:text-xs font-serif align-middle w-[13%] bg-white"
                >
                  {{ formatDate(testRequest?.created_at) }}
                </td>
              </tr>
              <tr>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle"
                >
                  Received by:<br />أستلم بواسطة
                </td>
                <td class="border border-black p-2 print:p-1.5 align-middle min-h-[3rem] bg-white text-sm print:text-xs font-medium text-black text-center">
                  {{ received_by || testRequest?.received_by || '-' }}
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle"
                >
                  Signature:<br />التوقيع
                </td>
                <td class="border border-black p-2 print:p-1.5 align-middle bg-white text-center">
                  <div
                    class="min-h-[3.5rem] print:min-h-[2.75rem] bg-white border border-black border-opacity-30 flex items-center justify-center p-1 mx-auto"
                  >
                    <img
                      src="/maram_sign.png"
                      alt=""
                      class="max-h-14 print:max-h-10 max-w-full object-contain mx-auto"
                    />
                  </div>
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 font-bold text-black text-sm print:text-xs font-serif text-center align-middle"
                >
                  Date:<br />التاريخ
                </td>
                <td
                  class="border border-black p-2 print:p-1.5 text-center font-bold text-black text-sm print:text-xs font-serif align-middle bg-white"
                >
                  {{ formatDate(testRequest?.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
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
                          <template v-if="newArtifact.type === ARTIFACT_TYPE_PRECIOUS_METALS">
                            <select
                              v-if="newArtifactSubtypeMode === 'list'"
                              v-model="newArtifact.subtype"
                              @change="handleSubtypeSelectionChange"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            >
                              <option value="" disabled>Select subtype | اختر النوع الفرعي</option>
                              <option v-for="option in localizedPreciousMetalsSubtypeOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                              </option>
                              <option value="__manual__">إدخال يدوي... | Manual entry...</option>
                            </select>
                            <div v-else class="space-y-2">
                              <input
                                v-model="newArtifact.subtype"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                placeholder="أدخل النوع الفرعي يدوياً | Enter subtype manually"
                              />
                              <button
                                type="button"
                                class="text-xs text-green-700 hover:text-green-900 underline"
                                @click="newArtifactSubtypeMode = 'list'"
                              >
                                العودة للقائمة | Back to list
                              </button>
                            </div>
                          </template>
                          <input
                            v-else
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
                            الوزن | Weight <span class="text-gray-400">(اختياري)</span>
                          </label>
                          <div class="flex space-x-2">
                            <input
                              v-model="newArtifact.weight"
                              type="number"
                              step="0.01"
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
                        
                        <div v-if="newArtifact.delivery_type === 'Specific Date'">
                          <label class="block text-sm font-medium text-gray-700">
                            التاريخ المحدد | Specific Date *
                          </label>
                          <input
                            v-model="newArtifact.specific_delivery_date"
                            type="date"
                            :required="newArtifact.delivery_type === 'Specific Date'"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          />
                        </div>
                        
                        <div>
                          <label class="block text-sm font-medium text-gray-700">
                            العدد | Quantity <span class="text-gray-400">(اختياري)</span>
                          </label>
                          <input
                            v-model="newArtifact.quantity"
                            type="number"
                            min="1"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                            :placeholder="__('Enter quantity (default: 1)')"
                          />
                          <p class="mt-1 text-xs text-gray-500">{{ __('If quantity > 1, items will be created with sub-codes (e.g., GR123-1, GR123-2)') }}</p>
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
                          :disabled="addingArtifact || !newArtifact.type || !newArtifact.service"
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
                :disabled="addingArtifact || !newArtifact.type || !newArtifact.service"
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
import {
  getTypeOptions,
  getServiceOptions as getServiceOptionsForType,
  getPreciousMetalsSubtypeOptions,
  preciousMetalsSubtypeValues,
  ARTIFACT_TYPE_PRECIOUS_METALS,
} from '@/constants/artifactTypes'

export default {
  components: { Link },
  props: {
    testRequest: Object,
    customer: Object,
    artifacts: Array,
    received_by: String,
    receiving_record_no: String
  },
  computed: {
    // Group artifacts by base code
    groupedArtifacts() {
      const groups = {}
      
      // Group artifacts by their base code (without sub-code suffix)
      this.artifacts.forEach(artifact => {
        const code = artifact.artifact_code
        // Check if code has a sub-code (e.g., JR6365974581-1)
        const hasSubCode = /-\d+$/.test(code)
        
        if (hasSubCode) {
          // Extract base code (e.g., JR6365974581 from JR6365974581-1)
          const baseCode = code.replace(/-\d+$/, '')
          
          if (!groups[baseCode]) {
            groups[baseCode] = {
              baseCode: baseCode,
              codes: [],
              items: []
            }
          }
          
          groups[baseCode].codes.push(code)
          groups[baseCode].items.push(artifact)
        } else {
          // Single artifact without sub-code, add as is
          groups[code] = {
            baseCode: code,
            codes: [code],
            items: [artifact]
          }
        }
      })
      
      return Object.values(groups).map(group => {
        const sortedCodes = group.codes.sort()
        const firstArtifact = group.items[0]

        let displayCode = group.baseCode
        if (sortedCodes.length > 1) {
          const numbers = sortedCodes.map(code => {
            const match = code.match(/-(\d+)$/)
            return match ? parseInt(match[1]) : null
          }).filter(n => n !== null).sort((a, b) => a - b)

          if (numbers.length > 0) {
            const minNum = numbers[0]
            const maxNum = numbers[numbers.length - 1]
            displayCode = `${group.baseCode} (${minNum} - ${maxNum})`
          }
        }

        return {
          ...firstArtifact,
          artifact_code: displayCode,
          ids: group.items.map(item => item.id),
          count: group.items.length
        }
      })
    },
    typeOptions() {
      return getTypeOptions(this.$page.props.locale === 'ar' ? 'ar' : 'en')
    },
    localizedPreciousMetalsSubtypeOptions() {
      return getPreciousMetalsSubtypeOptions(this.$page.props.locale === 'ar' ? 'ar' : 'en')
    },
    isDraft() {
      return this.testRequest?.status === 'draft'
    },
  },
  data() {
    return {
      editingTestRequest: false,
      confirming: false,
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
        specific_delivery_date: '',
        notes: '',
        price: '',
        quantity: 1 // Default to 1, will create sub-codes if > 1
      },
      ARTIFACT_TYPE_PRECIOUS_METALS,
      newArtifactSubtypeMode: 'list',
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
        { value: '72 hours', label: '72 ساعة | 72 hours' },
        { value: 'Specific Date', label: 'تاريخ محدد | Specific Date' }
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

    confirmTestRequest() {
      if (!this.testRequest?.id || this.confirming || !this.isDraft) {
        return
      }
      if (!confirm('اعتماد الطلب؟ سيتم إرسال الإشعارات للمختبر.\nConfirm this request? Lab notifications will be sent.')) {
        return
      }
      this.confirming = true
      this.$inertia.post(`/dashboard/test-requests/${this.testRequest.id}/confirm`, {}, {
        onFinish: () => {
          this.confirming = false
        }
      })
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
        price: '',
        quantity: 1 // Reset to default
      }
      this.newArtifactSubtypeMode = 'list'
    },
    
    getServiceOptions(type) {
      const locale = this.$page.props.locale === 'ar' ? 'ar' : 'en'
      return getServiceOptionsForType(type, locale)
    },

    resolveSubtypeMode(subtypeValue) {
      if (preciousMetalsSubtypeValues.includes(subtypeValue)) return 'list'
      return subtypeValue ? 'manual' : 'list'
    },

    handleSubtypeSelectionChange() {
      if (this.newArtifact.subtype === '__manual__') {
        this.newArtifact.subtype = ''
        this.newArtifactSubtypeMode = 'manual'
      }
    },

    resetServiceWhenTypeChanges() {
      this.newArtifact.service = ''
      if (this.newArtifact.type === ARTIFACT_TYPE_PRECIOUS_METALS) {
        this.newArtifactSubtypeMode = this.resolveSubtypeMode(this.newArtifact.subtype)
      } else {
        this.newArtifact.subtype = ''
        this.newArtifactSubtypeMode = 'list'
      }
      if (this.newArtifact.type === 'Jewellery' || this.newArtifact.type === ARTIFACT_TYPE_PRECIOUS_METALS) {
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
            // Note: 'Regular' and 'Specific Date' use base price (1x)
            if (this.newArtifact.delivery_type === 'Same Day') {
              finalPrice = priceValue * 2
            } else if (this.newArtifact.delivery_type === '48 hours') {
              finalPrice = priceValue * 0.7
            } else if (this.newArtifact.delivery_type === '72 hours') {
              finalPrice = priceValue * 0.5
            }
            // For 'Regular', 'Specific Date', and other types: use base price (finalPrice = priceValue)

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
      if (!this.newArtifact.type || !this.newArtifact.service) {
        alert('Please fill in all required fields (Type, Service)')
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
          specific_delivery_date: this.newArtifact.specific_delivery_date,
          notes: this.newArtifact.notes,
          price: this.newArtifact.price,
          quantity: this.newArtifact.quantity
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
              specific_delivery_date: '',
              notes: '',
              price: '',
              quantity: 1 // Reset to default
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
          'delivered': 'تم التسليم',
          'Quantity': 'العدد',
          'Optional': '(اختياري)',
          'Enter quantity (default: 1)': 'أدخل العدد (افتراضي: 1)',
          'If quantity > 1, items will be created with sub-codes (e.g., GR123-1, GR123-2)': 'إذا كان العدد > 1، ستُنشأ القطع بأكواد فرعية (مثل: GR123-1, GR123-2)'
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
/* Terms: English far-left LTR, Arabic far-right RTL (beats global RTL text-left/right flips) */
.terms-conditions-table .terms-col-en {
  direction: ltr !important;
  text-align: left !important;
  unicode-bidi: isolate;
}

.terms-conditions-table .terms-col-ar {
  direction: rtl !important;
  text-align: right !important;
  unicode-bidi: isolate;
}

/* Test request document header strip (solid top/sides, dotted bottom, black serif type) */
.test-request-doc-header-bar {
  border-top: 1px solid #000;
  border-left: 1px solid #000;
  border-right: 1px solid #000;
  border-bottom: 1px dotted #000;
}

.test-request-doc-header-text {
  font-family: "Times New Roman", Times, serif;
  font-weight: 700;
  color: #000;
}

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

  .delivery-doc-table {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  .delivery-doc-table tr {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  .delivery-doc-table .delivery-doc-heading {
    background: #f5f5f5 !important;
  }

  .delivery-doc-table img {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    max-height: 40px !important;
  }

  /* Document header strip (screen design preserved in print) */
  .test-request-doc-header-bar {
    background: #f2f2f2 !important;
  }

  .test-request-doc-header-text {
    color: #000 !important;
  }

  .test-request-doc-header-bar .bg-white {
    background: #ffffff !important;
  }
  
  /* Ensure text is readable */
  .text-white {
    color: black !important;
  }
}
</style>
