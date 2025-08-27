<template>
  <DashboardLayout :pageTitle="__('Edit Client')">
    <div class="max-w-7xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Edit Client') }}</h2>
        <p class="text-gray-600">{{ __('Update client information.') }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
        <!-- Client Information Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-user mr-2 text-blue-500"></i>
            {{ __('Client Information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Customer Name') }} <span class="text-red-500">*</span>
              </label>
              <input v-model="form.full_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.full_name }" required />
              <p v-if="errors.full_name" class="text-red-500 text-sm mt-1">{{ errors.full_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Company Name') }}
              </label>
              <input v-model="form.company_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.company_name }" />
              <p v-if="errors.company_name" class="text-red-500 text-sm mt-1">{{ errors.company_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Mobile') }} <span class="text-red-500">*</span>
              </label>
              <input v-model="form.mobile" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.mobile }" required />
              <p v-if="errors.mobile" class="text-red-500 text-sm mt-1">{{ errors.mobile }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email') }}
              </label>
              <input v-model="form.email" type="email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.email }" />
              <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('City') }}
              </label>
              <input v-model="form.city" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.city }" />
              <p v-if="errors.city" class="text-red-500 text-sm mt-1">{{ errors.city }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Delivery Date') }}
              </label>
              <input v-model="form.delivery_date" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.delivery_date }" />
              <p v-if="errors.delivery_date" class="text-red-500 text-sm mt-1">{{ errors.delivery_date }}</p>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-6 pt-8 border-t border-gray-200">
          <Link 
            :href="$route('reception.show-client', client.id)" 
            class="px-8 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors font-medium"
          >
            <i class="fas fa-times mr-2"></i>
            {{ __('Cancel') }}
          </Link>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium shadow-sm"
          >
            <span v-if="loading" class="flex items-center">
              <i class="fas fa-spinner fa-spin mr-2"></i>
              {{ __('Updating...') }}
            </span>
            <span v-else class="flex items-center">
              <i class="fas fa-save mr-2"></i>
              {{ __('Update Client') }}
            </span>
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

export default {
  components: { DashboardLayout, Link },
  props: {
    client: Object
  },
  setup(props) {
    const form = useForm({
      full_name: props.client.full_name,
      company_name: props.client.company_name || '',
      mobile: props.client.phone,
      email: props.client.email || '',
      city: props.client.address || '',
      delivery_date: props.client.delivery_date || ''
    })

    return { form }
  },
  data() {
    return {
      loading: false
    }
  },
  computed: {
    errors() {
      return this.$page.props.errors || {}
    }
  },
  methods: {
    __(key) {
      const t = {
        'Edit Client': 'تعديل العميل',
        'Update client information.': 'تحديث معلومات العميل',
        'Client Information': 'معلومات العميل',
        'Customer Name': 'اسم العميل',
        'Company Name': 'اسم الشركة',
        'Mobile': 'الجوال',
        'Email': 'البريد الإلكتروني',
        'City': 'المدينة',
        'Delivery Date': 'تاريخ التسليم',
        'Cancel': 'إلغاء',
        'Update Client': 'تحديث العميل',
        'Updating...': 'جاري التحديث...'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    submitForm() {
      this.loading = true
      
      this.form.put(this.$route('reception.update-client', this.client.id), {
        onSuccess: () => {
          this.loading = false
        },
        onError: (errors) => {
          this.loading = false
          console.log('Errors:', errors)
        },
        onFinish: () => {
          this.loading = false
        }
      })
    }
  }
}
</script>

<style scoped>
/* تحسين مظهر الحقول */
input {
  transition: all 0.2s ease-in-out;
}

input:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
}

/* تحسين مظهر الأزرار */
button {
  transition: all 0.2s ease-in-out;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>
