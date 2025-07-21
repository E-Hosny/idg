<template>
  <DashboardLayout :pageTitle="__('Add Artifact')">
    <div class="max-w-xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Add Artifact') }}</h2>
        <p class="text-gray-600">{{ __('Add a new artifact for this client.') }}</p>
      </div>
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Type") }} <span class="text-red-500">*</span></label>
          <select v-model="form.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
            <option value="" disabled selected>{{ __("Select Type") }}</option>
            <option v-for="option in typeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Service") }}</label>
          <select v-model="form.service" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="" disabled selected>{{ __("Select Service") }}</option>
            <option v-for="option in serviceOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Weight") }}</label>
          <div class="flex space-x-2">
            <input v-model="form.weight" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <select v-model="form.weight_unit" class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              <option value="" disabled selected>{{ __("Select Weight Unit") }}</option>
              <option v-for="option in weightUnitOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
        </div>

        <!-- حقل السعر -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Price") }}</label>
          <div class="flex items-center space-x-2">
            <input v-model="calculatedPrice" type="text" readonly class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50" />
            <span class="text-sm text-gray-600">SAR</span>
            <button v-if="form.type && form.service && form.weight" @click="calculatePrice" type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
              {{ __("Calculate Price") }}
            </button>
          </div>
          <p v-if="priceInfo" class="text-sm text-gray-600 mt-1">{{ priceInfo }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Delivery Type") }}</label>
          <select v-model="form.delivery_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="" disabled selected>{{ __("Select Delivery Type") }}</option>
            <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Notes") }}</label>
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
import { ref, computed } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    client_id: Number
  },
  setup(props) {
    const { locale } = usePage().props;
    // Type options
    const typeOptions = [
      { value: 'Colored Gemstones', label: locale === 'ar' ? 'أحجار كريمة ملونة' : 'Colored Gemstones' },
      { value: 'Other Colored Gemstones', label: locale === 'ar' ? 'أحجار كريمة ملونة أخرى' : 'Other Colored Gemstones' },
      { value: 'Colorless Diamonds', label: locale === 'ar' ? 'ألماس عديم اللون' : 'Colorless Diamonds' },
      { value: 'Jewellery', label: locale === 'ar' ? 'مجوهرات' : 'Jewellery' },
    ];
    // Service options - ديناميكية بناءً على نوع القطعة
    const getServiceOptions = (artifactType) => {
      const allServices = [
        { value: 'Regular - ID Report', label: locale === 'ar' ? 'عادي - تقرير هوية' : 'Regular - ID Report' },
        { value: 'Regular - ID + Origin', label: locale === 'ar' ? 'عادي - هوية + أصل' : 'Regular - ID + Origin' },
        { value: 'Mini Card Report - ID Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير هوية' : 'Mini Card Report - ID Report' },
        { value: 'Mini Card Report - ID + Origin', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - هوية + أصل' : 'Mini Card Report - ID + Origin' },
        { value: 'Regular - Diamond Grading Report', label: locale === 'ar' ? 'عادي - تقرير تصنيف الألماس' : 'Regular - Diamond Grading Report' },
        { value: 'Mini Card Report - Mini Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مصغر' : 'Mini Card Report - Mini Report' },
        { value: 'Regular - Jewellery Report', label: locale === 'ar' ? 'عادي - تقرير المجوهرات' : 'Regular - Jewellery Report' },
        { value: 'Mini Card Report - Mini Jewellery Report', label: locale === 'ar' ? 'تقرير بطاقة مصغرة - تقرير مجوهرات مصغر' : 'Mini Card Report - Mini Jewellery Report' },
      ];

      // Other Colored Gemstones لا يحتوي على ID + Origin
      if (artifactType === 'Other Colored Gemstones') {
        return allServices.filter(service => !service.value.includes('ID + Origin'));
      }

      return allServices;
    };

    const serviceOptions = computed(() => getServiceOptions(form.type));
    // Weight unit options
    const weightUnitOptions = [
      { value: 'ct', label: locale === 'ar' ? 'قيراط' : 'ct' },
      { value: 'gm', label: locale === 'ar' ? 'جرام' : 'gm' },
    ];
    // Delivery options
    const deliveryOptions = [
      { value: 'Regular', label: locale === 'ar' ? 'عادي' : 'Regular' },
      { value: 'Express Service', label: locale === 'ar' ? 'خدمة سريعة' : 'Express Service' },
      { value: 'Same Day', label: locale === 'ar' ? 'نفس اليوم' : 'Same Day' },
      { value: '24 hours', label: locale === 'ar' ? '24 ساعة' : '24 hours' },
      { value: '18 hours', label: locale === 'ar' ? '18 ساعة' : '18 hours' },
      { value: '72 hours', label: locale === 'ar' ? '72 ساعة' : '72 hours' },
    ];
    const form = useForm({
      type: '',
      service: '',
      weight: '',
      weight_unit: '',
      delivery_type: '',
      notes: '',
      client_id: props.client_id
    })

    // متغيرات السعر
    const calculatedPrice = ref('')
    const priceInfo = ref('')

    const submitForm = () => {
      form.post(route('reception.artifact.store', { client: props.client_id }))
    }

    // دالة حساب السعر
    const calculatePrice = async () => {
      if (!form.type || !form.service || !form.weight) {
        return
      }

      try {
        // الحصول على CSRF token بطريقة آمنة
        const csrfToken = usePage().props.csrf_token || 
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
            type: form.type,
            service: form.service,
            weight: parseFloat(form.weight)
          })
        })

        const data = await response.json()
        
        if (data.price) {
          calculatedPrice.value = data.price
          priceInfo.value = `Weight range: ${data.weight_range}`
        } else {
          calculatedPrice.value = 'Price not available'
          priceInfo.value = 'No pricing found for this combination'
        }
      } catch (error) {
        console.error('Error calculating price:', error)
        calculatedPrice.value = 'Error'
        priceInfo.value = 'Failed to calculate price'
        // إظهار رسالة خطأ للمستخدم
        alert('خطأ في حساب السعر. يرجى المحاولة مرة أخرى.')
      }
    }

    return { 
      form, 
      submitForm, 
      typeOptions, 
      serviceOptions, 
      weightUnitOptions, 
      deliveryOptions,
      calculatedPrice,
      priceInfo,
      calculatePrice
    }
  },
  methods: {
    __(key) {
      const t = {
        'Add Artifact': 'إضافة قطعة',
        'Add a new artifact for this client.': 'إضافة قطعة جديدة لهذا العميل',
        'Type': 'النوع',
        'Select Type': 'اختر النوع',
        'Service': 'الخدمة',
        'Select Service': 'اختر الخدمة',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'Calculate Price': 'حساب السعر',
        'خطأ في حساب السعر. يرجى المحاولة مرة أخرى.': 'خطأ في حساب السعر. يرجى المحاولة مرة أخرى.',
        'Delivery Type': 'نوع التسليم',
        'Select Delivery Type': 'اختر طريقة التسليم',
        'Notes': 'ملاحظات',
        'Cancel': 'إلغاء',
        'Save Artifact': 'حفظ القطعة'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    }
  }
}
</script>

<style scoped>
/* تحسين مظهر القوائم المنسدلة */
select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 8px center;
  background-size: 16px;
  padding-right: 32px !important;
}

select option {
  padding: 8px;
}

select option:first-child {
  color: #6b7280;
}

/* تحسين مظهر الأزرار */
button {
  transition: all 0.2s ease-in-out;
}

button:hover {
  transform: translateY(-1px);
}
</style> 