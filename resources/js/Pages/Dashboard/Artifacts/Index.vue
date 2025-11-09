<template>
  <DashboardLayout :pageTitle="__('Items List')">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ getPageTitle() }}</h2>
        <div class="text-sm text-gray-600">
          {{ __('Total') }}: {{ artifacts.total || 0 }} {{ __('items') }}
        </div>
      </div>

      <!-- Filter Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Filter') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Code') }}
            </label>
            <input
              v-model="filters.code"
              type="text"
              @input="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
              :placeholder="__('Enter code')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Receiving Request No') }}
            </label>
            <input
              v-model="filters.receiving_record_no"
              type="text"
              @input="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
              :placeholder="__('Enter receiving record number')"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
            >
              {{ __('Clear Filters') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Cards (only for All Artifacts view) -->
      <div v-if="viewType === 'all'" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Pending') }}</h3>
          <p class="text-3xl font-bold text-yellow-600">{{ pendingCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Under Evaluation') }}</h3>
          <p class="text-3xl font-bold text-blue-600">{{ underEvaluationCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Evaluated') }}</h3>
          <p class="text-3xl font-bold text-green-600">{{ evaluatedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-700">{{ __('Certified') }}</h3>
          <p class="text-3xl font-bold text-purple-600">{{ certifiedCount }}</p>
        </div>
      </div>

      <!-- Items Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full bg-white rounded shadow border">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="px-4 py-2 text-left font-bold">#</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Code') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Receiving Request No') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Service') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Weight') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Status') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Created At') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(artifact, idx) in artifacts.data" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ (artifacts.current_page - 1) * artifacts.per_page + idx + 1 }}</td>
              <td class="px-4 py-2">{{ artifact.artifact_code }}</td>
              <td class="px-4 py-2">{{ artifact.test_request?.receiving_record_no || '-' }}</td>
              <td class="px-4 py-2">{{ getFullType(artifact) }}</td>
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
                <span :class="{
                  'text-yellow-600 font-semibold': artifact.status === 'pending',
                  'text-blue-600 font-semibold': artifact.status === 'under_evaluation',
                  'text-green-600 font-semibold': artifact.status === 'certified' || artifact.status === 'evaluated',
                  'text-red-600 font-semibold': artifact.status === 'rejected',
                }">
                  {{ __(artifact.status) }}
                </span>
              </td>
              <td class="px-4 py-2">{{ formatDate(artifact.created_at) }}</td>
              <td class="px-4 py-2">
                <div class="flex space-x-2">
                  <!-- زر التقييم للقطع غير المقيمة -->
                  <button 
                    v-if="artifact.status !== 'evaluated' && artifact.status !== 'certified'"
                    @click.stop="$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluate`)" 
                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold"
                  >
                    {{ artifact.status === 'under_evaluation' ? __('Continue') : __('Evaluate') }}
                  </button>
                  
                  <!-- زر عرض التقييم للقطع المقيمة أو قيد التقييم -->
                  <button 
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified' || artifact.status === 'under_evaluation'"
                    @click.stop="viewEvaluation(artifact)" 
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
                  >
                    {{ (artifact.status === 'evaluated' || artifact.status === 'certified') ? __('View Report') : __('View Evaluation') }}
                  </button>
                  
                  <!-- زر الطباعة للقطع المقيمة -->
                  <button 
                    v-if="artifact.status === 'evaluated' || artifact.status === 'certified'"
                    @click.stop="printEvaluation(artifact)" 
                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-xs font-semibold"
                    :title="__('Print Report')"
                  >
                    🖨️
                  </button>
                  
                  <!-- زر إنشاء الشهادة للقطع المقيمة -->
                  <button 
                    v-if="artifact.status === 'evaluated'"
                    @click.stop="generateCertificate(artifact)" 
                    class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-xs font-semibold"
                    :title="__('Generate Certificate')"
                  >
                    📜
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!artifacts.data.length">
              <td colspan="9" class="text-center text-gray-400 py-4">{{ getNoDataMessage() }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="artifacts.last_page > 1" class="flex justify-between items-center mt-6">
        <div class="text-sm text-gray-600">
          {{ __('Showing') }} {{ ((artifacts.current_page - 1) * artifacts.per_page) + 1 }} 
          {{ __('to') }} {{ Math.min(artifacts.current_page * artifacts.per_page, artifacts.total) }} 
          {{ __('of') }} {{ artifacts.total }} {{ __('items') }}
        </div>
        <div class="flex space-x-2">
          <Link 
            v-if="artifacts.prev_page_url"
            :href="artifacts.prev_page_url"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            {{ __('Previous') }}
          </Link>
          <span 
            v-for="page in getPageNumbers()" 
            :key="page"
            class="px-4 py-2 border rounded-md transition"
            :class="page === artifacts.current_page 
              ? 'bg-green-600 text-white border-green-600' 
              : 'border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer'"
            @click="page !== artifacts.current_page && goToPage(page)"
          >
            {{ page }}
          </span>
          <Link 
            v-if="artifacts.next_page_url"
            :href="artifacts.next_page_url"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
          >
            {{ __('Next') }}
          </Link>
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
    artifacts: Object,
    viewType: {
      type: String,
      default: 'all'
    },
    stats: {
      type: Object,
      default: () => ({
        pending: 0,
        under_evaluation: 0,
        evaluated: 0,
        certified: 0
      })
    },
    filters: {
      type: Object,
      default: () => ({
        code: '',
        receiving_record_no: ''
      })
    }
  },

  data() {
    // تهيئة الفلاتر من URL parameters
    const urlParams = new URLSearchParams(window.location.search)
    return {
      filterTimeout: null,
      filters: {
        code: urlParams.get('code') || '',
        receiving_record_no: urlParams.get('receiving_record_no') || ''
      }
    }
  },

  computed: {
    pendingCount() {
      return this.stats?.pending || 0
    },
    underEvaluationCount() {
      return this.stats?.under_evaluation || 0
    },
    evaluatedCount() {
      return this.stats?.evaluated || 0
    },
    certifiedCount() {
      return this.stats?.certified || 0
    }
  },

  methods: {
    getFullType(artifact) {
      if (!artifact.type) return '-';
      return artifact.subtype ? `${artifact.type} - ${artifact.subtype}` : artifact.type;
    },
    
    __(key) {
      const t = {
        'Items List': 'قائمة العناصر',
        'Pending Items': 'العناصر المعلقة',
        'Code': 'الكود',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'ct': 'قيراط',
        'gm': 'جرام',
        'Status': 'الحالة',
        'Client': 'العميل',
        'Created At': 'تاريخ الإنشاء',
        'No items found.': 'لا توجد عناصر',
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض',
        'Evaluate': 'تقييم',
        'Continue': 'متابعة',
        'View Evaluation': 'عرض التقييم',
        'View Report': 'عرض التقرير',
        'Print Report': 'طباعة التقرير',
        'Generate Certificate': 'إنشاء شهادة',
        'All Items': 'جميع العناصر',
        'Total': 'المجموع',
        'items': 'عنصر',
        'Pending': 'معلق',
        'Under Evaluation': 'قيد التقييم',
        'No pending items found.': 'لا توجد عناصر معلقة.',
        'Actions': 'الإجراءات',
        'Receiving Request No': 'رقم طلب الاستلام',
        'Filter': 'فلترة',
        'Enter code': 'أدخل الكود',
        'Enter receiving record number': 'أدخل رقم طلب الاستلام',
        'Clear Filters': 'مسح الفلاتر',
        'Showing': 'عرض',
        'to': 'من',
        'of': 'من',
        'Previous': 'السابق',
        'Next': 'التالي',
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    
    getPageNumbers() {
      const current = this.artifacts.current_page
      const last = this.artifacts.last_page
      const pages = []
      
      // Show first page
      if (last > 0) pages.push(1)
      
      // Show pages around current
      const start = Math.max(2, current - 1)
      const end = Math.min(last - 1, current + 1)
      
      if (start > 2) pages.push('...')
      
      for (let i = start; i <= end; i++) {
        if (i !== 1 && i !== last) {
          pages.push(i)
        }
      }
      
      if (end < last - 1) pages.push('...')
      
      // Show last page
      if (last > 1) pages.push(last)
      
      return pages
    },
    
    goToPage(page) {
      if (page === '...' || page === this.artifacts.current_page) return
      
      const url = new URL(window.location.href)
      url.searchParams.set('page', page)
      this.$inertia.visit(url.toString())
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },
    
    viewEvaluation(artifact) {
      // توجيه لصفحة عرض التقييم حسب نوع القطعة
      this.$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluation`)
    },

    printEvaluation(artifact) {
      // فتح صفحة التقييم في نافذة جديدة للطباعة
      const url = `/dashboard/artifacts/${artifact.id}/evaluation`
      const printWindow = window.open(url, '_blank')
      
      // انتظار تحميل الصفحة ثم تشغيل الطباعة
      if (printWindow) {
        printWindow.addEventListener('load', () => {
          setTimeout(() => {
            printWindow.print()
          }, 500)
        })
      }
    },

    getPageTitle() {
      if (this.viewType === 'pending') {
        return this.__('Pending Items')
      }
      return this.__('Items List')
    },

    getNoDataMessage() {
      if (this.viewType === 'pending') {
        return this.__('No pending items found.')
      }
      return this.__('No items found.')
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

    applyFilters() {
      // استخدام debounce لتقليل عدد الطلبات
      clearTimeout(this.filterTimeout)
      this.filterTimeout = setTimeout(() => {
        const url = new URL(window.location.href)
        
        if (this.filters.code) {
          url.searchParams.set('code', this.filters.code)
        } else {
          url.searchParams.delete('code')
        }
        
        if (this.filters.receiving_record_no) {
          url.searchParams.set('receiving_record_no', this.filters.receiving_record_no)
        } else {
          url.searchParams.delete('receiving_record_no')
        }
        
        // إعادة تعيين الصفحة إلى 1 عند الفلترة
        url.searchParams.set('page', '1')
        
        this.$inertia.visit(url.toString(), {
          preserveState: true,
          preserveScroll: true
        })
      }, 500) // انتظار 500ms بعد توقف الكتابة
    },

    clearFilters() {
      this.filters = {
        code: '',
        receiving_record_no: ''
      }
      
      const url = new URL(window.location.href)
      url.searchParams.delete('code')
      url.searchParams.delete('receiving_record_no')
      url.searchParams.set('page', '1')
      
      this.$inertia.visit(url.toString(), {
        preserveState: true,
        preserveScroll: true
      })
    }
  }
}
</script> 