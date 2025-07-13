<template>
  <DashboardLayout :pageTitle="__('Artifacts List')">
    <div class="max-w-5xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Artifacts List') }}</h2>
      <div class="bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full bg-white rounded shadow border">
          <thead>
            <tr class="bg-gray-100 border-b">
              <th class="px-4 py-2 text-left font-bold">#</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Code') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Type') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Service') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Status') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Client') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Created At') }}</th>
              <th class="px-4 py-2 text-left font-bold">{{ __('Evaluate') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(artifact, idx) in artifacts.data" :key="artifact.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ idx + 1 }}</td>
              <td class="px-4 py-2">{{ artifact.artifact_code }}</td>
              <td class="px-4 py-2">{{ artifact.type }}</td>
              <td class="px-4 py-2">{{ artifact.service }}</td>
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
              <td class="px-4 py-2">{{ artifact.client ? artifact.client.full_name : '-' }}</td>
              <td class="px-4 py-2">{{ formatDate(artifact.created_at) }}</td>
              <td class="px-4 py-2">
                <button @click.stop="$inertia.visit(`/dashboard/artifacts/${artifact.id}/evaluate`)" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold">
                  {{ __('Evaluate') }}
                </button>
              </td>
            </tr>
            <tr v-if="!artifacts.data.length">
              <td colspan="7" class="text-center text-gray-400 py-4">{{ __('No artifacts found.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  components: { DashboardLayout },
  props: {
    artifacts: Object
  },
  methods: {
    __(key) {
      const t = {
        'Artifacts List': 'قائمة القطع',
        'Code': 'الكود',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Status': 'الحالة',
        'Client': 'العميل',
        'Created At': 'تاريخ الإنشاء',
        'No artifacts found.': 'لا توجد قطع',
        'pending': 'قيد الاستلام',
        'under_evaluation': 'قيد التقييم',
        'evaluated': 'تم التقييم',
        'certified': 'معتمد',
        'rejected': 'مرفوض',
        'Evaluate': 'تقييم',
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    }
  }
}
</script> 