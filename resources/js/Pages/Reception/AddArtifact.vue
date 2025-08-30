<template>
  <DashboardLayout :pageTitle="__('Add Item')">
    <div class="max-w-xl mx-auto">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Add Item') }}</h2>
<p class="text-gray-600">{{ __('Add a new item for this client.') }}</p>
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
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Delivery Type") }}</label>
          <select v-model="form.delivery_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="" disabled selected>{{ __("Select Delivery Type") }}</option>
            <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
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
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Notes") }}</label>
          <input v-model="form.notes" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="flex justify-end space-x-4 pt-4">
          <Link :href="$route('reception.index')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</Link>
          <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('Save Item') }}</button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    client_id: Number
  },
  setup(props) {
    const { locale } = usePage().props;
    
    // مراقب تغيير نوع القطعة لتحديث وحدة الوزن
    watch(() => form.type, (newType) => {
      if (newType) {
        updateWeightUnit(newType);
      }
    });
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

      // تصفية الخدمات حسب نوع القطعة
      switch (artifactType) {
        case 'Colored Gemstones':
          // يحتوي على جميع خدمات ID Report و ID + Origin
          return allServices.filter(service => 
            service.value.includes('ID Report') || service.value.includes('ID + Origin')
          );
        
        case 'Other Colored Gemstones':
          // يحتوي على ID Report فقط (لا يحتوي على ID + Origin)
          return allServices.filter(service => 
            service.value.includes('ID Report') && !service.value.includes('ID + Origin')
          );
        
        case 'Colorless Diamonds':
          // يحتوي على Diamond Grading Report و Mini Report فقط
          return allServices.filter(service => 
            service.value.includes('Diamond Grading Report') || service.value.includes('Mini Report')
          );
        
        case 'Jewellery':
          // يحتوي على Jewellery Report و Mini Jewellery Report فقط
          return allServices.filter(service => 
            service.value.includes('Jewellery Report')
          );
        
        default:
          return [];
      }
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
      { value: '48 hours', label: locale === 'ar' ? '48 ساعة' : '48 hours' },
      { value: '72 hours', label: locale === 'ar' ? '72 ساعة' : '72 hours' },
    ];
    const form = useForm({
      type: '',
      service: '',
      weight: '',
      weight_unit: 'ct', // افتراضي: قيراط
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
          if (form.delivery_type === 'Same Day') {
            finalPrice = priceValue * 2 // 200% من السعر
            priceMultiplier = 2
            multiplierText = ' (Same Day: 200%)'
          } else if (form.delivery_type === '48 hours') {
            finalPrice = priceValue * 0.7 // 70% من السعر
            priceMultiplier = 0.7
            multiplierText = ' (48 hours: 70%)'
          } else if (form.delivery_type === '72 hours') {
            finalPrice = priceValue * 0.5 // 50% من السعر
            priceMultiplier = 0.5
            multiplierText = ' (72 hours: 50%)'
          }

          calculatedPrice.value = finalPrice.toFixed(2)
          priceInfo.value = `Base Price: ${priceValue} | Weight range: ${data.weight_range}${multiplierText}`
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

    // تحديث وحدة الوزن تلقائياً عند تغيير نوع القطعة
    const updateWeightUnit = (newType) => {
      if (newType === 'Jewellery') {
        form.weight_unit = 'gm'; // مجوهرات: جرام
      } else {
        form.weight_unit = 'ct'; // باقي الأنواع: قيراط
      }
    };

    // إعادة تعيين الخدمة عند تغيير نوع القطعة
    const resetServiceWhenTypeChanges = () => {
      const availableServices = getServiceOptions(form.type);
      const currentServiceExists = availableServices.some(service => service.value === form.service);
      
      if (!currentServiceExists) {
        form.service = '';
      }
    };

    return { 
      form, 
      submitForm, 
      typeOptions, 
      serviceOptions, 
      weightUnitOptions, 
      deliveryOptions,
      calculatedPrice,
      priceInfo,
      calculatePrice,
      updateWeightUnit,
      resetServiceWhenTypeChanges
    }
  },
  methods: {
    __(key) {
      const t = {
        'Add Item': 'إضافة عنصر',
        'Add a new item for this client.': 'إضافة عنصر جديد لهذا العميل',
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
        'Save Item': 'حفظ العنصر'
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