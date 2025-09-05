<template>
  <div class="min-h-screen bg-gray-200">
    <!-- Fixed Header Section - Full Width -->
    <div class="bg-gray-200 p-4">
      <div class="w-full">
        <!-- Title Section -->
        <div class="text-center mb-4">
          <h1 class="text-xl font-bold text-black">TEST Request</h1>
        </div>
        
        <!-- Empty 5x5 Table -->
        <div class="border border-dotted border-black bg-gray-200">
          <table class="w-full">
            <tbody>
              <tr class="border-b border-dotted border-black">
                <!-- دمج 3 خانات على الشمال -->
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 text-center align-middle" rowspan="3" colspan="3">
                  <span class="text-lg font-semibold">Test Request</span>
                </td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 text-center align-middle"></td>
                <!-- دمج 3 خانات على اليمين -->
                <td class="px-3 py-4 text-black w-1/5 text-center align-middle" rowspan="3">
                  <img src="/images/idg_logo.jpg" alt="IDG Logo" class="w-16 h-16 rounded-full mx-auto" />
                </td>
              </tr>
              <tr class="border-b border-dotted border-black">
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle"></td>
              </tr>
              <tr class="border-b border-dotted border-black">
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle"></td>
              </tr>
              <tr class="border-b border-dotted border-black">
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 font-semibold text-center align-middle">Document Number</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 font-semibold text-center align-middle">Document Classification</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 font-semibold text-center align-middle">Document Level</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black w-1/5 font-semibold text-center align-middle">Issue No., Revision No</td>
                <td class="px-3 py-4 text-black w-1/5 font-semibold text-center align-middle">Issue Date</td>
              </tr>
              <tr>
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle">HOT-F03</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle">Control</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle">Document</td>
                <td class="px-3 py-4 text-black border-r border-dotted border-black text-center align-middle">002, 001</td>
                <td class="px-3 py-4 text-black text-center align-middle">15/3/2025</td>
              </tr>
            </tbody>
          </table>
        </div>

      </
      div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
      <div class="max-w-7xl mx-auto">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Client Details') }}</h2>
          <p class="text-gray-600">{{ __('View all information about the client and their items.') }}</p>
        </div>

        <!-- Client Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
          <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-lg font-semibold text-gray-800">
              <i class="fas fa-user mr-2 text-blue-500"></i>
              {{ __('Client Information') }}
            </h3>
            <button @click="editClient" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
              <i class="fas fa-edit mr-2"></i> {{ __('Edit Client') }}
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><span class="font-bold">{{ __('Receiving Record No') }}:</span> {{ client.receiving_record_no || '-' }}</div>
            <div><span class="font-bold">{{ __('Full Name') }}:</span> {{ client.full_name }}</div>
            <div><span class="font-bold">{{ __('Company Name') }}:</span> {{ client.company_name || '-' }}</div>
            <div><span class="font-bold">{{ __('Customer Code') }}:</span> {{ client.customer_code }}</div>
            <div><span class="font-bold">{{ __('Mobile') }}:</span> {{ client.phone }}</div>
            <div><span class="font-bold">{{ __('Email') }}:</span> {{ client.email || '-' }}</div>
            <div><span class="font-bold">{{ __('City/Address') }}:</span> {{ client.address || '-' }}</div>
            <div><span class="font-bold">{{ __('Received Date') }}:</span> {{ client.received_date }}</div>
            <div><span class="font-bold">{{ __('Delivery Date') }}:</span> {{ client.delivery_date || '-' }}</div>
            <div><span class="font-bold">{{ __('Received By') }}:</span> {{ received_by || __('Not specified') }}</div>
          </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex justify-between items-center mb-6 border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
              <i class="fas fa-gem mr-3 text-green-500 text-2xl"></i>
              {{ __('Items') }}
            </h3>
            <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
              {{ __('Total') }}: {{ client.artifacts.length }} {{ __('items') }}
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-sm border border-gray-200">
              <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Code') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Type') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Service') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Weight') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Price') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Notes') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Delivery Type') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Status') }}</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(artifact, idx) in client.artifacts" :key="artifact.id" class="hover:bg-gray-50 transition-colors duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ idx + 1 }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ artifact.artifact_code || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ artifact.type }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ artifact.service || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="artifact.weight" class="text-sm text-gray-900">
                      {{ artifact.weight }} 
                      <span v-if="artifact.weight_unit" class="text-gray-500 text-xs ml-1">
                        {{ __(artifact.weight_unit) }}
                      </span>
                    </div>
                    <div v-else class="text-sm text-gray-400">-</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="artifact.price" class="text-sm font-semibold text-green-600">
                      {{ artifact.price }} SAR
                    </div>
                    <div v-else class="text-sm text-gray-400">-</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 max-w-xs truncate" :title="artifact.notes">{{ artifact.notes || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ artifact.delivery_type || '-' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="{
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800': artifact.status === 'pending',
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800': artifact.status === 'under_evaluation',
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800': artifact.status === 'certified' || artifact.status === 'evaluated',
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800': artifact.status === 'rejected',
                    }">
                      {{ __(artifact.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
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
                  <td colspan="10" class="px-6 py-12 text-center">
                    <div class="text-gray-400">
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

        <div class="mt-8 flex justify-end">
          <Link :href="$route('reception.index')" class="btn btn-secondary">{{ __('Back to Clients') }}</Link>
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
        'Full Name': 'الاسم الكامل',
        'Company Name': 'اسم الشركة',
        'Customer Code': 'كود العميل',
        'Mobile': 'الجوال',
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
        'Edit Client': 'تعديل العميل',
        'Actions': 'الإجراءات',
        'Edit': 'تعديل',
        'Delete': 'حذف',
        'Are you sure you want to delete item': 'هل أنت متأكد من أنك تريد حذف العنصر',
        'Error deleting item. Please try again.': 'خطأ في حذف العنصر. يرجى المحاولة مرة أخرى.'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    editClient() {
      this.$inertia.visit(this.$route('reception.edit-client', this.client.id))
    },
    editArtifact(artifactId) {
      this.$inertia.visit(this.$route('reception.edit-artifact', artifactId))
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