<template>
  <DashboardLayout :page-title="__('Certificate')">
    <div class="max-w-5xl mx-auto">
      <!-- Certificate Display -->
      <div id="certificate" class="bg-white shadow-lg print:shadow-none print:p-0 relative">
        
        <!-- Green Header Bar -->
        <div class="bg-green-700" style="background-color: #047857;">
          <div class="flex justify-between items-center px-8 py-4">
            <!-- Left: IDG Logo and Lab Info -->
            <div class="flex items-center space-x-4">
              <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center p-1">
                <img src="/images/idg_logo.jpg" alt="IDG" class="w-14 h-14 rounded-full object-cover" />
              </div>
              <div class="text-white">
                <h1 class="text-xl font-bold">IDG Laboratory</h1>
                <h2 class="text-lg">Gemstone Report</h2>
              </div>
            </div>
            
            <!-- Right: Dates -->
            <div class="text-white text-sm space-y-1">
              <div><strong>{{ __('Received Date') }}:</strong> {{ formatDate(certificate.received_date) }}</div>
              <div><strong>{{ __('Report Date') }}:</strong> {{ formatDate(certificate.report_date) }}</div>
              <div><strong>{{ __('Test Date') }}:</strong> {{ formatDate(certificate.test_date) }}</div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="px-8 py-6 bg-white">
          <!-- Report Number and Test Method -->
          <div class="flex justify-between items-center mb-6">
            <div class="bg-gray-100 px-4 py-2 rounded">
              <div class="text-sm"><strong>{{ __('Report No') }}.</strong> {{ certificate.certificate_number }}</div>
              <div class="text-sm"><strong>{{ __('Test Method') }}:</strong> {{ certificate.test_method || 'SOPOI' }}</div>
            </div>
            
            <!-- IDENTIFICATION Box -->
            <div class="text-center">
              <div class="border-2 border-gray-400 px-8 py-4 bg-gray-50">
                <div class="text-xs text-gray-600 mb-1 font-semibold">{{ __('IDENTIFICATION') }}</div>
                <div class="text-2xl font-bold text-gray-800">{{ certificate.identification }}</div>
                <div class="w-16 h-px bg-gray-400 mx-auto mt-2"></div>
              </div>
            </div>
          </div>

          <!-- Gemstone Properties Table -->
          <div class="mb-6">
            <table class="w-full border-collapse border border-gray-300 text-sm">
              <tbody>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Shape/Cut') }}:</td>
                  <td class="px-3 py-2">{{ certificate.shape_cut || 'Oval/Mix' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Species') }}:</td>
                  <td class="px-3 py-2">{{ certificate.species || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Weight') }}:</td>
                  <td class="px-3 py-2 font-bold">{{ certificate.weight ? `${certificate.weight} Ct` : '0.00 Ct' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Variety') }}:</td>
                  <td class="px-3 py-2">{{ certificate.variety || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Color') }}:</td>
                  <td class="px-3 py-2">{{ certificate.color || 'Blue' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Group') }}:</td>
                  <td class="px-3 py-2">{{ certificate.group || '-' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Dimensions') }}:</td>
                  <td class="px-3 py-2">{{ certificate.dimensions || '0.00 x 0.00 x 0.00 mm' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Origin Opinion') }}:</td>
                  <td class="px-3 py-2">{{ certificate.origin_opinion || 'Not requested' }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Transparency') }}:</td>
                  <td class="px-3 py-2">{{ certificate.transparency || 'Transparent' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Conclusion') }}:</td>
                  <td class="px-3 py-2 font-bold text-green-700">{{ certificate.conclusion || 'Natural' }}</td>
                </tr>
                <tr>
                  <td class="border-r border-gray-300 px-3 py-2 font-semibold bg-gray-50">{{ __('Phenomena') }}:</td>
                  <td class="px-3 py-2">{{ certificate.phenomena || '-' }}</td>
                  <td class="border-l border-gray-300 px-3 py-2"></td>
                  <td class="px-3 py-2"></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Comments Section -->
          <div class="mb-6">
            <div class="text-sm">
              <strong>{{ __('Comments') }}:</strong> {{ certificate.comments || '----------' }}
            </div>
          </div>
        </div>

        <!-- Green Footer Bar -->
        <div class="bg-green-700 px-8 py-4">
          <div class="flex justify-between items-center">
            <!-- Left: Contact Info -->
            <div class="text-white text-xs space-y-1">
              <div><strong>{{ __('Contact') }}</strong></div>
              <div>IDG Laboratory, Saudi Arabia</div>
              <div>www.idg-lab.com.sa</div>
            </div>
            
            <!-- Center: QR Code and Signature -->
            <div class="flex items-center space-x-8">
              <!-- QR Code -->
              <div class="w-16 h-16 bg-white flex items-center justify-center p-1">
                <img 
                  v-if="certificate.qr_code_url" 
                  :src="certificate.qr_code_url" 
                  alt="QR Code" 
                  class="w-full h-full object-contain"
                />
                <div 
                  v-else 
                  class="w-12 h-12 bg-gray-300 flex items-center justify-center text-xs font-bold"
                >
                  QR<br/>CODE
                </div>
              </div>
              
              <!-- Signature -->
              <div class="text-center text-white">
                <div class="w-24 h-12 border-b border-white mb-2 flex items-end justify-center">
                  <span class="text-lg font-script">{{ __('Signature') }}</span>
                </div>
                <div class="text-xs">{{ __('Signature') }}</div>
              </div>
            </div>
            
            <!-- Right: Empty space for balance -->
            <div class="w-24"></div>
          </div>
          
          <!-- Bottom Text -->
          <div class="text-center text-white text-xs mt-4 border-t border-green-600 pt-2">
            <div>{{ __('Visit website for Terms & Conditions') }}</div>
            <div class="font-semibold">www.idg-lab.com.sa</div>
          </div>
        </div>
      </div>

      <!-- Back Cover Preview (Not printed - only for preview) -->
      <div class="certificate-back-preview mt-8">
        <div class="bg-gradient-to-br from-green-900 via-green-700 to-green-600 relative overflow-hidden" 
             style="width: 100%; height: 600px; border-radius: 12px;">
          
          <!-- Decorative Border -->
          <div class="absolute inset-4 border-2 border-yellow-400 border-opacity-30 rounded-lg">
            <div class="absolute inset-2 border border-yellow-400 border-opacity-20 rounded-md"></div>
          </div>
          
          <!-- Main Content -->
          <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10">
            
            <!-- Logo Section -->
            <div class="mb-8">
              <div class="relative w-48 h-48 mx-auto mb-6">
                <!-- Outer decorative circles -->
                <div class="absolute -inset-4 border border-yellow-400 border-opacity-40 rounded-full"></div>
                <div class="absolute -inset-6 border border-yellow-400 border-opacity-20 rounded-full"></div>
                
                <!-- Main logo circle -->
                <div class="w-48 h-48 rounded-full bg-white bg-opacity-10 border-2 border-yellow-400 border-opacity-80 flex items-center justify-center backdrop-blur-sm">
                  <!-- Inner dotted circle -->
                  <div class="absolute inset-2 border-2 border-dotted border-yellow-400 border-opacity-60 rounded-full"></div>
                  
                  <!-- Logo -->
                  <img src="/images/idg_logo.jpg" alt="IDG" class="w-32 h-32 rounded-full object-cover z-10" />
                </div>
              </div>
              
              <h1 class="text-5xl font-bold text-yellow-400 text-center mb-4 tracking-wider" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                IDG Laboratory
              </h1>
            </div>
            
            <!-- Report Type -->
            <div class="text-2xl font-light text-white text-opacity-90 tracking-widest uppercase mb-12">
              {{ getReportType() }}
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-16 text-center">
              <div class="text-lg text-white text-opacity-80 mb-2">www.idg-lab.com.sa</div>
              <div class="text-base text-white text-opacity-60">Riyadh, Saudi Arabia</div>
            </div>
          </div>
          
          <!-- Decorative rotating elements -->
          <div class="absolute -top-1/2 -right-1/2 w-full h-full bg-gradient-radial from-yellow-400 from-0% via-transparent via-70% to-transparent opacity-10 animate-spin-slow"></div>
          <div class="absolute -bottom-1/2 -left-1/2 w-full h-full bg-gradient-radial from-yellow-400 from-0% via-transparent via-70% to-transparent opacity-5 animate-spin-reverse-slow"></div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-between items-center mt-8 print:hidden">
        <Link 
          :href="`/dashboard/artifacts/${certificate.artifact.id}/evaluation`"
          class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          {{ __('Back to Evaluation') }}
        </Link>
        
        <div class="flex space-x-4">
          <button 
            v-if="certificate.status === 'draft'"
            @click="issueCertificate"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
          >
            {{ __('Issue Certificate') }}
          </button>
          
          <button 
            @click="printCertificate"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            {{ __('Print Certificate') }}
          </button>
          
          <button 
            @click="downloadPDF"
            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
          >
            {{ __('Download PDF') }}
          </button>
          
          <button 
            v-if="!certificate.qr_code_url"
            @click="regenerateQR"
            class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
          >
            {{ __('Generate QR Code') }}
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: { DashboardLayout, Link },
  props: {
    certificate: Object
  },

  methods: {
    __(key) {
      const translations = {
        'Certificate': 'الشهادة',
        'Received Date': 'تاريخ الاستلام',
        'Report Date': 'تاريخ التقرير',
        'Test Date': 'تاريخ الفحص',
        'Report No': 'رقم التقرير',
        'Test Method': 'طريقة الفحص',
        'IDENTIFICATION': 'التعريف',
        'Shape/Cut': 'الشكل/القطع',
        'Weight': 'الوزن',
        'Color': 'اللون',
        'Dimensions': 'الأبعاد',
        'Transparency': 'الشفافية',
        'Phenomena': 'الظواهر',
        'Species': 'النوع',
        'Variety': 'الصنف',
        'Group': 'المجموعة',
        'Origin Opinion': 'رأي المنشأ',
        'Conclusion': 'الخلاصة',
        'Comments': 'التعليقات',
        'Signature': 'التوقيع',
        'Contact Info': 'معلومات الاتصال',
        'Address': 'العنوان',
        'Website': 'الموقع الإلكتروني',
        'Gemstone Properties': 'خصائص الحجر الكريم',
        'Authorized Gemologist': 'أخصائي الأحجار المعتمد',
        'Contact Information': 'معلومات الاتصال',
        'Phone': 'الهاتف',
        'Email': 'البريد الإلكتروني',
        'Certification Details': 'تفاصيل الشهادة',
        'This certificate is valid only for the described item': 'هذه الشهادة صالحة فقط للقطعة الموصوفة',
        'Visit website for Terms & Conditions': 'زر الموقع للشروط والأحكام',
        'Contact': 'الاتصال',
        'Back to Evaluation': 'العودة للتقييم',
        'Issue Certificate': 'إصدار الشهادة',
        'Print Certificate': 'طباعة الشهادة',
        'Download PDF': 'تحميل PDF',
        'Generate QR Code': 'توليد رمز QR'
      }
      
      return this.$page.props.locale === 'ar' ? translations[key] || key : key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },

    issueCertificate() {
      this.$inertia.post(`/certificates/${this.certificate.id}/issue`, {}, {
        onSuccess: () => {
          alert('تم إصدار الشهادة بنجاح!')
        },
        onError: (errors) => {
          console.error('Certificate issue errors:', errors)
          alert('حدث خطأ أثناء إصدار الشهادة.')
        }
      })
    },

    printCertificate() {
      // Hide the back cover when printing (will be handled by PDF)
      const backCover = document.querySelector('.certificate-back-preview')
      if (backCover) {
        backCover.style.display = 'none'
      }
      window.print()
      if (backCover) {
        backCover.style.display = 'block'
      }
    },

    downloadPDF() {
      window.open(`/certificates/${this.certificate.id}/pdf`, '_blank')
    },

    regenerateQR() {
      this.$inertia.post(`/certificates/${this.certificate.id}/regenerate-qr`, {}, {
        onSuccess: () => {
          alert('تم توليد رمز QR بنجاح!')
          location.reload()
        },
        onError: (errors) => {
          console.error('QR regeneration errors:', errors)
          alert('حدث خطأ أثناء توليد رمز QR.')
        }
      })
    },

    getReportType() {
      const identification = this.certificate?.identification?.toUpperCase()
      
      switch (identification) {
        case 'DIAMOND':
          return 'Diamond Report'
        case 'SAPPHIRE':
        case 'RUBY':
        case 'EMERALD':
          return 'Gemstone Report'
        case 'JEWELLERY':
        case 'JEWELRY':
          return 'Jewelry Report'
        default:
          return 'Gemstone Report'
      }
    }
  }
}
</script>

<style scoped>
/* Custom Gold Color */
.bg-gold-500 {
  background-color: #d4af37;
}

/* Elegant Animations */
.hover\:bg-green-50:hover {
  transition: background-color 0.2s ease-in-out;
}

/* Certificate Paper Effect */
#certificate {
  background-image: 
    radial-gradient(circle at 25px 25px, lightgray 2%, transparent 0%), 
    radial-gradient(circle at 75px 75px, lightgray 2%, transparent 0%);
  background-size: 100px 100px;
  background-position: 0 0, 50px 50px;
}

/* Elegant Shadow Effects */
.shadow-elegant {
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.1), 
    0 2px 4px -1px rgba(0, 0, 0, 0.06),
    0 0 0 1px rgba(0, 0, 0, 0.05);
}

/* Print Styles */
@media print {
  .print\:hidden {
    display: none !important;
  }
  
  .print\:shadow-none {
    box-shadow: none !important;
  }
  
  .print\:p-0 {
    padding: 0 !important;
  }
  
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  #certificate {
    background: white !important;
    box-shadow: none !important;
  }
  
  /* Ensure gradients print correctly */
  .bg-gradient-to-r,
  .bg-gradient-to-br,
  .bg-gradient-to-l {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  /* Page breaks */
  .page-break-inside-avoid {
    page-break-inside: avoid;
  }
  
  /* Print margins */
  @page {
    margin: 0.5in;
    size: A4;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .gap-12 {
    gap: 2rem;
  }
  
  .p-12 {
    padding: 2rem;
  }
}

/* Custom animations for back cover */
.animate-spin-slow {
  animation: spin 20s linear infinite;
}

.animate-spin-reverse-slow {
  animation: spin-reverse 25s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes spin-reverse {
  from { transform: rotate(360deg); }
  to { transform: rotate(0deg); }
}

/* Gradient radial utility */
.bg-gradient-radial {
  background-image: radial-gradient(var(--tw-gradient-stops));
}

/* Hide back cover in print */
@media print {
  .certificate-back-preview {
    display: none !important;
  }
  
  .animate-spin-slow,
  .animate-spin-reverse-slow {
    animation: none !important;
  }
}
</style> 