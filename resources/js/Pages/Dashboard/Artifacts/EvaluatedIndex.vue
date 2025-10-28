<template>
  <DashboardLayout :pageTitle="__('Evaluated Items')">
    <div class="max-w-full mx-auto px-2">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('Evaluated Items') }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('items') }}
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Total Evaluated') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ artifacts.total || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Certified') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ certifiedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Diamond Evaluations') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ diamondCount }}</p>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold text-gray-800">{{ __('Evaluated Items List') }}</h3>
          <div class="flex space-x-4">
            <!-- Filter Options -->
            <select v-model="statusFilter" @change="filterArtifacts" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">{{ __('All Status') }}</option>
              <option value="evaluated">{{ __('Evaluated') }}</option>
              <option value="certified">{{ __('Certified') }}</option>
            </select>
            
            <select v-model="typeFilter" @change="filterArtifacts" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option value="">{{ __('All Types') }}</option>
              <option value="Colorless Diamonds">{{ __('Colorless Diamonds') }}</option>
              <option value="Colored Gemstones">{{ __('Colored Gemstones') }}</option>
              <option value="Other Colored Gemstones">{{ __('Other Colored Gemstones') }}</option>
              <option value="Jewellery">{{ __('Jewellery') }}</option>
            </select>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full bg-white rounded shadow border" style="min-width: 1200px;">
            <thead>
              <tr class="bg-gray-100 border-b">
                <th class="px-3 py-2 text-left font-bold w-12">#</th>
                <th class="px-4 py-2 text-left font-bold w-32">{{ __('Code') }}</th>
                <th class="px-4 py-2 text-left font-bold w-36">{{ __('Receiving Request No') }}</th>
                <th class="px-4 py-2 text-left font-bold w-40">{{ __('Type') }}</th>
                <th class="px-4 py-2 text-left font-bold w-32">{{ __('Service') }}</th>
                <th class="px-4 py-2 text-left font-bold w-20">{{ __('Weight') }}</th>
                <th class="px-4 py-2 text-left font-bold w-24">{{ __('Status') }}</th>
                <th class="px-4 py-2 text-left font-bold w-28">{{ __('Evaluated At') }}</th>
                <th class="px-6 py-2 text-left font-bold w-72">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(artifact, idx) in filteredArtifacts" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
                <td class="px-3 py-2">{{ getRowNumber(idx) }}</td>
                <td class="px-4 py-2">
                  <span class="font-medium text-blue-600">{{ artifact.artifact_code }}</span>
                </td>
                <td class="px-4 py-2">{{ artifact.test_request?.receiving_record_no || '-' }}</td>
                <td class="px-4 py-2">
                  <div class="flex items-center gap-2">
                    <span class="text-sm">{{ artifact.type }}</span>
                    <span v-if="artifact.type === 'Colorless Diamonds'" class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                      💎
                    </span>
                  </div>
                </td>
                <td class="px-4 py-2">{{ artifact.service }}</td>
                <td class="px-4 py-2">
                  <span v-if="artifact.weight">
                    {{ artifact.weight }} 
                    <span v-if="artifact.weight_unit" class="text-gray-600 text-sm">
                      {{ __(artifact.weight_unit) }}
                    </span>
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-4 py-2">
                  <span :class="getStatusClass(artifact.status)" class="px-2 py-1 text-xs rounded-full font-semibold">
                    {{ __(artifact.status) }}
                  </span>
                </td>
                <td class="px-4 py-2">{{ formatDate(artifact.updated_at) }}</td>
                <td class="px-6 py-3 w-72">
                  <div class="flex flex-wrap gap-1.5 items-center justify-start">
                    <!-- View Evaluation Button -->
                    <button 
                      @click="viewEvaluation(artifact)" 
                      class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium whitespace-nowrap"
                      :title="__('View Evaluation Report')"
                    >
                      👁️ {{ __('View Report') }}
                    </button>

                    <!-- Edit Evaluation Button -->
                    <button 
                      @click="editEvaluation(artifact)" 
                      class="px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-xs font-medium whitespace-nowrap"
                      :title="__('Edit Evaluation Report')"
                    >
                      🖊️ {{ __('Edit') }}
                    </button>
                    
                    <!-- Print Button -->
                     <button 
                       @click="printEvaluation(artifact)" 
                       class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium whitespace-nowrap"
                       :title="__('Print Report')"
                     >
                       🖨️ {{ __('Print') }}
                     </button>
                     
                     <!-- Generate Certificate Button -->
                     <button 
                       @click="generateCertificate(artifact)" 
                       class="px-2 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs font-medium whitespace-nowrap"
                       :title="__('Generate Certificate')"
                     >
                       📜 {{ __('Certificate') }}
                     </button>

                     <!-- Generate QR Code Button -->
                     <button 
                       @click="downloadQRCode(artifact)" 
                       class="px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-xs font-medium whitespace-nowrap"
                       :title="__('Download QR Code PNG')"
                     >
                       📱 {{ __('Generate QR') }}
                     </button>

                                         <!-- Upload Certificate Button -->
                    <template v-if="hasUploadedCertificate(artifact)">
                      <!-- Certificate Already Uploaded - Show options -->
                      <div class="flex flex-col space-y-1">
                        <!-- View Certificate Button -->
                        <button 
                          @click="viewUploadedCertificate(artifact)" 
                          class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium whitespace-nowrap"
                          :title="__('View Uploaded Certificate')"
                        >
                          ✅ {{ __('Uploaded') }}
                        </button>
                        
                        <!-- Delete Certificate Button -->
                        <button 
                          @click="confirmDeleteCertificate(artifact)" 
                          class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium whitespace-nowrap"
                          :title="__('Delete Certificate')"
                        >
                          🗑️ {{ __('Delete') }}
                        </button>
                        
                        <!-- Replace Certificate Button -->
                        <button 
                          @click="showUploadModal(artifact)" 
                          class="px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-xs font-medium whitespace-nowrap"
                          :title="__('Replace Certificate')"
                        >
                          🔄 {{ __('Replace') }}
                        </button>
                      </div>
                    </template>
                    <template v-else>
                      <!-- Upload Certificate Button -->
                      <button 
                        @click="showUploadModal(artifact)" 
                        class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs font-medium whitespace-nowrap"
                        :title="__('Upload Certificate')"
                      >
                        📄 {{ __('Upload Certificate') }}
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
              <tr v-if="!filteredArtifacts.length">
                <td colspan="10" class="text-center text-gray-400 py-8">{{ __('No evaluated items found.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="artifacts.last_page > 1" class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-600">
            {{ __('Page') }} {{ artifacts.current_page }} {{ __('of') }} {{ artifacts.last_page }}
          </div>
          <div class="flex space-x-2">
            <Link 
              v-if="artifacts.prev_page_url"
              :href="artifacts.prev_page_url"
              class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              {{ __('Previous') }}
            </Link>
            <Link 
              v-if="artifacts.next_page_url"
              :href="artifacts.next_page_url"
              class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-50"
            >
              {{ __('Next') }}
            </Link>
          </div>
        </div>
      </div>

      <!-- Upload Certificate Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
              {{ hasUploadedCertificate(selectedArtifact) ? __('Replace Certificate') : __('Upload Certificate') }}
            </h3>
            <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">
              {{ hasUploadedCertificate(selectedArtifact) ? __('Replace certificate for item') : __('Upload certificate for item') }}: <strong>{{ selectedArtifact?.artifact_code }}</strong>
            </p>
            
            <!-- Warning for replacement -->
            <div v-if="hasUploadedCertificate(selectedArtifact)" class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-sm">
              ⚠️ {{ __('Warning: This will replace the existing certificate. The old certificate will be permanently deleted.') }}
            </div>
            
            <p class="text-xs text-yellow-600 mb-4">
              {{ __('Please ensure the QR code has been generated and added to the certificate before uploading.') }}
            </p>
          </div>

          <form @submit.prevent="uploadCertificate" enctype="multipart/form-data">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Certificate File (PDF)') }}
              </label>
              <input 
                type="file" 
                ref="fileInput"
                @change="handleFileSelect"
                accept=".pdf"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                required
              >
              <p class="text-xs text-gray-500 mt-1">{{ __('Max file size: 100MB') }}</p>
            </div>

            <div v-if="uploadError" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ uploadError }}
            </div>

            <div class="flex justify-end space-x-3">
              <button 
                type="button" 
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                {{ __('Cancel') }}
              </button>
              <button 
                type="submit" 
                :disabled="uploading || !selectedFile"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ uploading ? (hasUploadedCertificate(selectedArtifact) ? __('Replacing...') : __('Uploading...')) : (hasUploadedCertificate(selectedArtifact) ? __('Replace') : __('Upload')) }}
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
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    artifacts: Object
  },
  
  setup(props) {
    const statusFilter = ref('')
    const typeFilter = ref('')
    const showModal = ref(false)
    const selectedArtifact = ref(null)
    const selectedFile = ref(null)
    const uploading = ref(false)
    const uploadError = ref('')

    const certifiedCount = computed(() => {
      return props.artifacts?.data?.filter(a => a.status === 'certified').length || 0
    })

    const diamondCount = computed(() => {
      return props.artifacts?.data?.filter(a => a.type === 'Colorless Diamonds').length || 0
    })

    const filteredArtifacts = computed(() => {
      let artifacts = props.artifacts?.data || []
      
      if (statusFilter.value) {
        artifacts = artifacts.filter(a => a.status === statusFilter.value)
      }
      
      if (typeFilter.value) {
        artifacts = artifacts.filter(a => a.type === typeFilter.value)
      }
      
      return artifacts
    })

    return {
      statusFilter,
      typeFilter,
      certifiedCount,
      diamondCount,
      filteredArtifacts,
      showModal,
      selectedArtifact,
      selectedFile,
      uploading,
      uploadError
    }
  },

  methods: {
    __(key) {
      const translations = {
        'Evaluated Items': 'العناصر المقيمة',
        'Total': 'المجموع',
        'items': 'عنصر',
        'Total Evaluated': 'إجمالي المقيم',
        'Certified': 'معتمد',
        'Diamond Evaluations': 'تقييمات الألماس',
        'Evaluated Items List': 'قائمة العناصر المقيمة',
        'All Status': 'جميع الحالات',
        'All Types': 'جميع الأنواع',
        'Code': 'الكود',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Status': 'الحالة',
        'Client': 'العميل',
        'Evaluated At': 'تاريخ التقييم',
        'Actions': 'الإجراءات',
        'View Report': 'عرض التقرير',
        'Print': 'طباعة',
        'Certificate': 'شهادة',
        'Generate Certificate': 'إنشاء شهادة',
        'View Evaluation Report': 'عرض تقرير التقييم',
        'Print Report': 'طباعة التقرير',
        'Generate QR': 'إنشاء رمز QR',
        'Upload Certificate': 'رفع شهادة',
        'Download QR Code PNG': 'تحميل رمز QR بصيغة PNG',
        'Upload certificate for item': 'رفع شهادة للعنصر',
        'Please ensure the QR code has been generated and added to the certificate before uploading.': 'يرجى التأكد من إنشاء رمز QR وإضافته للشهادة قبل الرفع.',
        'Certificate File (PDF)': 'ملف الشهادة (PDF)',
        'Max file size: 100MB': 'الحد الأقصى لحجم الملف: 100 ميجابايت',
        'Cancel': 'إلغاء',
        'Upload': 'رفع',
        'Uploading...': 'جاري الرفع...',
        'No evaluated items found.': 'لا توجد عناصر مقيمة.',
        'Page': 'صفحة',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
        
        // Upload status translations
        'Uploaded': 'مرفوعة',
        'View Uploaded Certificate': 'عرض الشهادة المرفوعة',
        
        // Artifact types
        'Colorless Diamonds': 'الألماس عديم اللون',
        'Colored Gemstones': 'الأحجار الكريمة الملونة',
        'Other Colored Gemstones': 'أحجار كريمة ملونة أخرى',
        'Jewellery': 'المجوهرات',
        
        // Status
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        
        // Weight units
        'ct': 'قيراط',
        'gm': 'جرام',
        
        // Receiving Request No
        'Receiving Request No': 'رقم طلب الاستلام',
      }
      
      return this.$page.props.locale === 'ar' ? translations[key] || key : key
    },

    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },

    getStatusClass(status) {
      return {
        'bg-green-100 text-green-800': status === 'evaluated',
        'bg-blue-100 text-blue-800': status === 'certified',
      }
    },

    getRowNumber(idx) {
      const currentPage = this.artifacts.current_page || 1
      const perPage = this.artifacts.per_page || 15
      return (currentPage - 1) * perPage + idx + 1
    },

    filterArtifacts() {
      // The computed property handles filtering automatically
    },

    viewEvaluation(artifact) {
      // Navigate to evaluation view page
      this.$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluation`)
    },

    editEvaluation(artifact) {
      // Navigate to evaluation edit page
      this.$inertia.visit(`/artifacts/${artifact.id}/edit-evaluation`)
    },

    printEvaluation(artifact) {
      // Open evaluation in new window for printing
      const url = `/dashboard/artifacts/${artifact.id}/evaluation`
      const printWindow = window.open(url, '_blank')
      
      // Wait for the page to load then trigger print
      if (printWindow) {
        printWindow.addEventListener('load', () => {
          setTimeout(() => {
            printWindow.print()
          }, 500)
        })
      }
    },

    generateCertificate(artifact) {
      // Navigate to certificate generation
      this.$inertia.post(`/certificates/${artifact.id}/generate`, {}, {
        onSuccess: () => {
          alert('تم إنشاء الشهادة بنجاح!')
        },
        onError: (errors) => {
          console.error('Certificate generation errors:', errors)
          if (errors.error) {
            alert(errors.error)
          } else {
            alert('حدث خطأ أثناء إنشاء الشهادة. يرجى المحاولة مرة أخرى.')
          }
        }
      })
    },

    downloadQRCode(artifact) {
      // Open QR generator in new window for PNG download
      window.open(`/artifacts/${artifact.id}/download-qr`, '_blank', 'width=500,height=600')
    },

    showUploadModal(artifact) {
      this.selectedArtifact = artifact
      this.showModal = true
      this.uploadError = ''
      this.selectedFile = null
    },

    closeModal() {
      this.showModal = false
      this.selectedArtifact = null
      this.selectedFile = null
      this.uploadError = ''
      this.uploading = false
    },

    handleFileSelect(event) {
      const file = event.target.files[0]
      if (file) {
        // Validate file type
        if (file.type !== 'application/pdf') {
          this.uploadError = 'يرجى اختيار ملف PDF فقط.'
          this.selectedFile = null
          return
        }
        
        // Validate file size (100MB)
        if (file.size > 100 * 1024 * 1024) {
          this.uploadError = 'حجم الملف كبير جداً. الحد الأقصى 100 ميجابايت.'
          this.selectedFile = null
          return
        }
        
        this.selectedFile = file
        this.uploadError = ''
      }
    },

    hasUploadedCertificate(artifact) {
      // Check if artifact has a certificate with uploaded status
      return artifact.latest_certificate && artifact.latest_certificate.status === 'uploaded'
    },

    viewUploadedCertificate(artifact) {
      if (artifact.latest_certificate && artifact.latest_certificate.uploaded_certificate_path) {
        const url = `/storage/${artifact.latest_certificate.uploaded_certificate_path}`
        window.open(url, '_blank')
      } else {
        alert('لم يتم العثور على شهادة مرفوعة')
      }
    },

    confirmDeleteCertificate(artifact) {
      if (confirm(this.__('Are you sure you want to delete this certificate? This action cannot be undone.'))) {
        this.$inertia.delete(`/artifacts/${artifact.id}/delete-certificate`, {
          onSuccess: () => {
            alert('تم حذف الشهادة بنجاح!')
            this.$inertia.reload()
          },
          onError: (errors) => {
            console.error('Delete certificate errors:', errors)
            if (errors.error) {
              alert(errors.error)
            } else {
              alert('حدث خطأ أثناء حذف الشهادة. يرجى المحاولة مرة أخرى.')
            }
          }
        })
      }
    },

    uploadCertificate() {
      if (!this.selectedFile || !this.selectedArtifact) {
        return
      }

      // Show confirmation for replacement
      if (this.hasUploadedCertificate(this.selectedArtifact)) {
        if (!confirm(this.__('Are you sure you want to replace the existing certificate? The old certificate will be permanently deleted.'))) {
          return
        }
      }

      this.uploading = true
      this.uploadError = ''

      const formData = new FormData()
      formData.append('certificate_file', this.selectedFile)
      
      // الحصول على CSRF token بطريقة آمنة
      const csrfToken = this.$page.props.csrf_token || 
                       document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                       document.querySelector('input[name="_token"]')?.value
      
      if (csrfToken) {
        formData.append('_token', csrfToken)
      }

      // Add progress indication for large files
      if (this.selectedFile.size > 50 * 1024 * 1024) { // Files larger than 50MB
        this.uploadError = `جاري رفع الملف الكبير (${Math.round(this.selectedFile.size / 1024 / 1024)} MB)... يرجى الانتظار...`
      }

      this.$inertia.post(`/artifacts/${this.selectedArtifact.id}/upload-certificate`, formData, {
        forceFormData: true,
        onSuccess: () => {
          const successMessage = this.hasUploadedCertificate(this.selectedArtifact) ? 
            'تم استبدال الشهادة بنجاح!' : 'تم رفع الشهادة بنجاح!'
          alert(successMessage)
          this.closeModal()
          // Refresh the page to show updated data
          this.$inertia.reload()
        },
        onError: (errors) => {
          console.error('Upload errors:', errors)
          
          // معالجة خطأ 419 (CSRF token expired)
          if (errors.status === 419 || errors.message?.includes('419')) {
            this.uploadError = 'انتهت صلاحية الجلسة. يرجى تحديث الصفحة والمحاولة مرة أخرى.'
            // إعادة تحميل الصفحة تلقائياً بعد 3 ثوان
            setTimeout(() => {
              this.$inertia.reload()
            }, 3000)
            return
          }
          
          if (errors.error) {
            this.uploadError = errors.error
          } else if (errors.certificate_file) {
            this.uploadError = errors.certificate_file[0]
          } else if (errors.message) {
            this.uploadError = errors.message
          } else if (errors.exception && errors.exception.includes('PostTooLargeException')) {
            this.uploadError = 'الملف كبير جداً. يرجى التأكد من أن الملف أقل من 100 ميجابايت.'
          } else {
            this.uploadError = 'حدث خطأ أثناء رفع الشهادة. يرجى المحاولة مرة أخرى.'
          }
        },
        onFinish: () => {
          this.uploading = false
        }
      })
    }
  }
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style> 