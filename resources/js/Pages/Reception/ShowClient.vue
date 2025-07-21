<template>
  <DashboardLayout :pageTitle="__('Client Details')">
    <div class="max-w-4xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Client Details') }}</h2>
        <p class="text-gray-600">{{ __('View all information about the client and their artifacts.') }}</p>
      </div>

      <!-- Client Info -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
          <i class="fas fa-user mr-2 text-blue-500"></i>
          {{ __('Client Information') }}
        </h3>
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

      <!-- Artifacts Table -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
          <i class="fas fa-gem mr-2 text-green-500"></i>
          {{ __('Artifacts') }}
        </h3>
        <table class="min-w-full bg-white rounded shadow mb-4 border">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="px-4 py-2 text-left font-bold">#</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Code') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Service') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Weight') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Price') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Notes') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Delivery Type') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Status') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(artifact, idx) in client.artifacts" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ idx + 1 }}</td>
              <td class="px-4 py-2">{{ artifact.artifact_code || '-' }}</td>
              <td class="px-4 py-2">{{ artifact.type }}</td>
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
                <span v-if="artifact.price" class="font-semibold text-green-600">
                  {{ artifact.price }} SAR
                </span>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-4 py-2">{{ artifact.notes }}</td>
              <td class="px-4 py-2">{{ artifact.delivery_type }}</td>
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
            </tr>
            <tr v-if="!client.artifacts.length">
              <td colspan="8" class="text-center text-gray-400 py-4">{{ __('No artifacts found.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-8 flex justify-end">
        <Link :href="$route('reception.index')" class="btn btn-secondary">{{ __('Back to Clients') }}</Link>
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
    client: Object,
    received_by: String
  },


  methods: {
    __(key) {
      const t = {
        'Client Details': 'تفاصيل العميل',
        'View all information about the client and their artifacts.': 'عرض جميع بيانات العميل وقطعه المسجلة',
        'Client Information': 'معلومات العميل',
        'Full Name': 'الاسم الكامل',
        'Company Name': 'اسم الشركة',
        'Customer Code': 'كود العميل',
        'Mobile': 'الجوال',
        'Email': 'البريد الإلكتروني',
        'City/Address': 'المدينة/العنوان',
        'Received Date': 'تاريخ الاستلام',
        'Delivery Date': 'تاريخ التسليم المتوقع',
        'Artifacts': 'القطع',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'ct': 'قيراط',
        'gm': 'جرام',
        'Notes': 'ملاحظات',
        'Status': 'الحالة',
        'No artifacts found.': 'لا توجد قطع مسجلة',
        'Back to Clients': 'العودة لقائمة العملاء',
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض',
        'Not specified': 'غير محدد',
        'Receiving Record No': 'رقم سجل الاستلام',
        'Received By': 'تم الاستلام بواسطة',
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    }
  }
}
</script> 