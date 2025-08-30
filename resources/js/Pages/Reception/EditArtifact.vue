<template>
  <DashboardLayout :pageTitle="__('Edit Item')">
    <div class="max-w-7xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Edit Item') }}</h2>
        <p class="text-gray-600">{{ __('Update item information.') }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
        <!-- Item Information Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-gem mr-2 text-green-500"></i>
            {{ __('Item Information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Item Code') }}
              </label>
              <input :value="artifact.artifact_code" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md bg-gray-50 text-base" readonly />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Type') }} <span class="text-red-500">*</span>
              </label>
              <select v-model="form.type" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.type }" required>
                <option value="" disabled>{{ __("Select Type") }}</option>
                <option v-for="option in typeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
              <p v-if="errors.type" class="text-red-500 text-sm mt-1">{{ errors.type }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Service') }}
              </label>
              <select v-model="form.service" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.service }">
                <option value="" disabled>{{ __("Select Service") }}</option>
                <option v-for="option in getServiceOptions(form.type)" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
              <p v-if="errors.service" class="text-red-500 text-sm mt-1">{{ errors.service }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Weight') }}
              </label>
              <div class="flex space-x-2">
                <input v-model="form.weight" type="text" class="flex-1 px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.weight }" placeholder="0.00" />
                <select v-model="form.weight_unit" class="w-24 px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.weight_unit }">
                  <option value="" disabled>{{ __("Unit") }}</option>
                  <option v-for="option in weightUnitOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                </select>
              </div>
              <p v-if="errors.weight" class="text-red-500 text-sm mt-1">{{ errors.weight }}</p>
              <p v-if="errors.weight_unit" class="text-red-500 text-sm mt-1">{{ errors.weight_unit }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Price') }}
              </label>
              <div class="flex items-center space-x-2">
                <input v-model="form.price" type="text" readonly class="flex-1 px-4 py-3 border border-gray-300 rounded-md bg-gray-50 text-base" placeholder="0.00" />
                <button v-if="form.type && form.service && form.weight" @click="calculatePrice" type="button" class="px-4 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                  {{ __("Calc") }}
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Delivery Type') }}
              </label>
              <select v-model="form.delivery_type" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.delivery_type }">
                <option value="" disabled>{{ __("Select Delivery Type") }}</option>
                <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
              <p v-if="errors.delivery_type" class="text-red-500 text-sm mt-1">{{ errors.delivery_type }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Notes') }}
              </label>
              <textarea v-model="form.notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.notes }" placeholder="ملاحظات..."></textarea>
              <p v-if="errors.notes" class="text-red-500 text-sm mt-1">{{ errors.notes }}</p>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-6 pt-8 border-t border-gray-200">
          <Link 
            :href="$route('reception.show-client', artifact.client_id)" 
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
              {{ __('Update Item') }}
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
    artifact: Object
  },
  setup(props) {
    const form = useForm({
      type: props.artifact.type,
      service: props.artifact.service || '',
      weight: props.artifact.weight || '',
      weight_unit: props.artifact.weight_unit || '',
      price: props.artifact.price || '',
      delivery_type: props.artifact.delivery_type || '',
      notes: props.artifact.notes || ''
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
    },
    locale() {
      return this.$page.props.locale || 'en'
    },
    typeOptions() {
      return [
        { value: 'Colored Gemstones', label: this.locale === 'ar' ? 'أحجار كريمة ملونة' : 'Colored Gemstones' },
        { value: 'Other Colored Gemstones', label: this.locale === 'ar' ? 'أحجار كريمة ملونة أخرى' : 'Other Colored Gemstones' },
        { value: 'Colorless Diamonds', label: this.locale === 'ar' ? 'ألماس عديم اللون' : 'Colorless Diamonds' },
        { value: 'Jewellery', label: this.locale === 'ar' ? 'مجوهرات' : 'Jewellery' },
      ]
    },
    weightUnitOptions() {
      return [
        { value: 'ct', label: this.locale === 'ar' ? 'قيراط' : 'ct' },
        { value: 'gm', label: this.locale === 'ar' ? 'جرام' : 'gm' },
      ]
    },
    deliveryOptions() {
      return [
        { value: 'Regular', label: this.locale === 'ar' ? 'عادي' : 'Regular' },
        { value: 'Express Service', label: this.locale === 'ar' ? 'خدمة سريعة' : 'Express Service' },
        { value: 'Same Day', label: this.locale === 'ar' ? 'نفس اليوم' : 'Same Day' },
        { value: '24 hours', label: this.locale === 'ar' ? '24 ساعة' : '24 hours' },
        { value: '48 hours', label: this.locale === 'ar' ? '48 ساعة' : '48 hours' },
        { value: '72 hours', label: this.locale === 'ar' ? '72 ساعة' : '72 hours' },
      ]
    }
  },
  methods: {
    __(key) {
      const t = {
        'Edit Item': 'تعديل العنصر',
        'Update item information.': 'تحديث معلومات العنصر',
        'Item Information': 'معلومات العنصر',
        'Item Code': 'رمز العنصر',
        'Type': 'النوع',
        'Service': 'الخدمة',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'Calc': 'حساب',
        'Delivery Type': 'نوع التسليم',
        'Notes': 'ملاحظات',
        'Select Type': 'اختر النوع',
        'Select Service': 'اختر الخدمة',
        'Select Delivery Type': 'اختر نوع التسليم',
        'Unit': 'وحدة',
        'Cancel': 'إلغاء',
        'Update Item': 'تحديث العنصر',
        'Updating...': 'جاري التحديث...'
      }
      return this.locale === 'ar' ? t[key] || key : key
    },
    getServiceOptions(artifactType) {
      const allServices = [
        { value: 'Regular - ID Report', label: this.locale === 'ar' ? 'عادي - تقرير هوية' : 'Regular - ID Report' },
        { value: 'Regular - ID + Origin', label: this.locale === 'ar' ? 'عادي - هوية + أصل' : 'Regular - ID + Origin' },
        { value: 'Mini Card Report - ID Report', label: this.locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير هوية' : 'Mini Card Report - ID Report' },
        { value: 'Mini Card Report - ID + Origin', label: this.locale === 'ar' ? 'تقرير بطاقة مصغرة - هوية + أصل' : 'Mini Card Report - ID + Origin' },
        { value: 'Regular - Diamond Grading Report', label: this.locale === 'ar' ? 'عادي - تقرير تصنيف الألماس' : 'Regular - Diamond Grading Report' },
        { value: 'Mini Card Report - Mini Report', label: this.locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مصغر' : 'Mini Card Report - Mini Report' },
        { value: 'Regular - Jewellery Report', label: this.locale === 'ar' ? 'عادي - تقرير المجوهرات' : 'Regular - Jewellery Report' },
        { value: 'Mini Card Report - Mini Jewellery Report', label: this.locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مجوهرات مصغر' : 'Mini Card Report - Mini Jewellery Report' },
      ]

      switch (artifactType) {
        case 'Colored Gemstones':
          return allServices.filter(service => 
            service.value.includes('ID Report') || service.value.includes('ID + Origin')
          )
        case 'Other Colored Gemstones':
          return allServices.filter(service => 
            service.value.includes('ID Report') && !service.value.includes('ID + Origin')
          )
        case 'Colorless Diamonds':
          return allServices.filter(service => 
            service.value.includes('Diamond Grading Report') || service.value.includes('Mini Report')
          )
        case 'Jewellery':
          return allServices.filter(service => 
            service.value.includes('Jewellery Report')
          )
        default:
          return []
      }
    },
         async calculatePrice() {
       if (!this.form.type || !this.form.service || !this.form.weight) {
         return
       }

       try {
         const csrfToken = this.$page.props.csrf_token || 
                          document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="_token"]')?.value

         const response = await fetch('/reception/calculate-price', {
           method: 'POST',
           headers: {
             'Content-Type': 'application/json',
             'X-CSRF-TOKEN': csrfToken,
             'Accept': 'application/json'
           },
           body: JSON.stringify({
             type: this.form.type,
             service: this.form.service,
             weight: parseFloat(this.form.weight)
           })
         })

                   const data = await response.json()
          
          // تسجيل البيانات المستلمة للتشخيص
          console.log('Response data:', data)
          console.log('data.price type:', typeof data.price)
          console.log('data.price value:', data.price)
          console.log('data.price isNaN:', isNaN(data.price))
          
          // تحويل السعر إلى رقم إذا كان string
          const priceValue = parseFloat(data.price)
          console.log('Parsed price value:', priceValue)
          console.log('Parsed price type:', typeof priceValue)
          
          if (data.price && priceValue && !isNaN(priceValue)) {
            let finalPrice = priceValue
            let priceMultiplier = 1
            let multiplierText = ''

            // حساب السعر بناءً على نوع التوصيل
            if (this.form.delivery_type === 'Same Day') {
              finalPrice = priceValue * 2 // 200% من السعر
              priceMultiplier = 2
              multiplierText = ' (Same Day: 200%)'
            } else if (this.form.delivery_type === '48 hours') {
              finalPrice = priceValue * 0.7 // 70% من السعر
              priceMultiplier = 0.7
              multiplierText = ' (48 hours: 70%)'
            } else if (this.form.delivery_type === '72 hours') {
              finalPrice = priceValue * 0.5 // 50% من السعر
              priceMultiplier = 0.5
              multiplierText = ' (72 hours: 50%)'
            }

            this.form.price = finalPrice.toFixed(2)
            console.log(`Base Price: ${priceValue} | Final Price: ${finalPrice}${multiplierText}`)
          } else {
            this.form.price = 'N/A'
          }
       } catch (error) {
         console.error('Error calculating price:', error)
         this.form.price = 'Error'
         alert(this.__('Error calculating price. Please try again.'))
       }
     },
    submitForm() {
      this.loading = true
      
      this.form.put(this.$route('reception.update-artifact', this.artifact.id), {
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
input, select, textarea {
  transition: all 0.2s ease-in-out;
}

input:focus, select:focus, textarea:focus {
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

/* تحسين مظهر القوائم المنسدلة */
select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 16px;
  padding-right: 40px !important;
}
</style>
