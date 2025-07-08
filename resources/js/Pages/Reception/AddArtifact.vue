<template>
  <DashboardLayout :pageTitle="__('Add Artifact')">
    <div class="max-w-xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Add Artifact') }}</h2>
        <p class="text-gray-600">{{ __('Add a new artifact for this client.') }}</p>
      </div>
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }} <span class="text-red-500">*</span></label>
          <input v-model="form.type" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Service') }}</label>
          <input v-model="form.service" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Weight') }}</label>
          <input v-model="form.weight" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Delivery Type') }}</label>
          <input v-model="form.delivery_type" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
          <input v-model="form.notes" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="flex justify-end space-x-4 pt-4">
          <Link :href="$route('reception.index')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</Link>
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('Save Artifact') }}</button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

export default {
  components: { DashboardLayout, Link },
  props: {
    client_id: Number
  },
  setup(props) {
    const form = useForm({
      type: '',
      service: '',
      weight: '',
      delivery_type: '',
      notes: '',
      client_id: props.client_id
    })
    const submitForm = () => {
      form.post(route('reception.artifact.store', { client: props.client_id }))
    }
    return { form, submitForm }
  },
  methods: {
    __(key) {
      const t = {
        'Add Artifact': 'إضافة قطعة',
        'Add a new artifact for this client.': 'إضافة قطعة جديدة لهذا العميل',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Delivery Type': 'نوع التسليم',
        'Notes': 'ملاحظات',
        'Cancel': 'إلغاء',
        'Save Artifact': 'حفظ القطعة'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    }
  }
}
</script> 