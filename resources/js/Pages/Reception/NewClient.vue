<template>
  <DashboardLayout :pageTitle="__('New Client Registration')">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('New Client Registration') }}</h2>
        <p class="text-gray-600">{{ __('Register a new client and their item') }}</p>
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
                {{ __('Company Name') }}
              </label>
              <input v-model="form.company_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.company_name }" />
              <p v-if="errors.company_name" class="text-red-500 text-sm mt-1">{{ errors.company_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Customer Name') }} <span class="text-red-500">*</span>
              </label>
              <input v-model="form.full_name" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-base" :class="{ 'border-red-500': errors.full_name }" required />
              <p v-if="errors.full_name" class="text-red-500 text-sm mt-1">{{ errors.full_name }}</p>
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

        <!-- Items Table Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-gem mr-2 text-green-500"></i>
                          {{ __('Items Information') }}
          </h3>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow mb-4">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50">#</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[130px]">{{ __('Type') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[160px]">{{ __('Service') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[120px]">{{ __('Delivery Type') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[100px]">{{ __('Weight') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[80px]">{{ __('Price') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 min-w-[150px]">{{ __('Notes') }}</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700 bg-gray-50 w-[60px]"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(artifact, idx) in form.artifacts" :key="idx" class="border-b hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-center font-medium">{{ idx + 1 }}</td>
                <td class="px-4 py-3">
                  <select v-model="artifact.type" class="w-full px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>{{ __("Select Type") }}</option>
                    <option v-for="option in typeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-4 py-3">
                  <select v-model="artifact.service" class="w-full px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>{{ __("Select Service") }}</option>
                    <option v-for="option in getServiceOptions(artifact.type)" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-4 py-3">
                  <select v-model="artifact.delivery_type" class="w-full px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>{{ __("Select Delivery Type") }}</option>
                    <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-4 py-3">
                  <div class="flex space-x-1">
                    <input v-model="artifact.weight" type="text" class="w-16 px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00" />
                    <select v-model="artifact.weight_unit" class="w-16 px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="" disabled selected>{{ __("Unit") }}</option>
                      <option v-for="option in weightUnitOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center space-x-1">
                    <input v-model="artifact.price" type="text" readonly class="w-20 px-2 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm" placeholder="0.00" />
                    <button v-if="artifact.type && artifact.service && artifact.weight" @click="calculatePriceForArtifact(idx)" type="button" class="px-2 py-2 bg-blue-500 text-white rounded-md text-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                      {{ __("Calc") }}
                    </button>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <input v-model="artifact.notes" type="text" class="w-full px-2 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="ملاحظات..." />
                </td>
                <td class="px-4 py-3 text-center">
                  <button type="button" @click="removeArtifact(idx)" class="text-red-500 hover:text-red-700 p-2 rounded-md hover:bg-red-50 transition-colors">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          </div>
          <button type="button" @click="addArtifact" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors font-medium shadow-sm">
                          <i class="fas fa-plus mr-2"></i> {{ __('Add Item') }}
          </button>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-6 pt-8 border-t border-gray-200">
          <Link 
            :href="$route('reception.index')" 
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
              {{ __('Saving...') }}
            </span>
            <span v-else class="flex items-center">
              <i class="fas fa-save mr-2"></i>
              {{ __('Save Client & Item') }}
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
  setup() {
    const form = useForm({
      // Client data
      full_name: '',
      company_name: '',
      mobile: '',
      email: '',
      city: '',
      delivery_date: '',
      // Artifact data
      artifacts: []
    })
    const locale = window?.Inertia?.page?.props?.locale || 'en';
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
    // إعادة تعيين الخدمة عند تغيير نوع القطعة
    const resetServiceWhenTypeChanges = (artifactIndex) => {
      const artifact = form.artifacts[artifactIndex];
      const availableServices = getServiceOptions(artifact.type);
      const currentServiceExists = availableServices.some(service => service.value === artifact.service);
      
      if (!currentServiceExists) {
        artifact.service = '';
      }
    };

    return { form, typeOptions, getServiceOptions, weightUnitOptions, deliveryOptions, resetServiceWhenTypeChanges }
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
        'New Client Registration': 'تسجيل عميل جديد',
        'Register a new client and their item': 'تسجيل عميل جديد وعنصره',
        'Client Information': 'معلومات العميل',
        'Items Information': 'معلومات العنصر',
        'Customer Name': 'اسم العميل',
        'Phone Number': 'رقم الهاتف',
        'National ID': 'رقم الهوية الوطنية',
        'Email': 'البريد الإلكتروني',
        'Nationality': 'الجنسية',
        'Address': 'العنوان',
        'Client Notes': 'ملاحظات العميل',
        'Item Name': 'اسم العنصر',
        'Item Type': 'نوع العنصر',
        'Select Type': 'اختر النوع',
        'Antique': 'تحفة أثرية',
        'Jewelry': 'مجوهرات',
        'Artwork': 'عمل فني',
        'Furniture': 'أثاث',
        'Coins': 'عملات',
        'Books': 'كتب',
        'Other': 'أخرى',
        'Origin Country': 'بلد المنشأ',
        'Year Made': 'سنة الصنع',
        'Materials': 'المواد',
        'Weight': 'الوزن',
        'Price': 'السعر',
        'Calc': 'حساب',
        'خطأ في حساب السعر. يرجى المحاولة مرة أخرى.': 'خطأ في حساب السعر. يرجى المحاولة مرة أخرى.',
        'Dimensions': 'الأبعاد',
        'Condition': 'الحالة',
        'Select Condition': 'اختر الحالة',
        'Excellent': 'ممتازة',
        'Very Good': 'جيدة جداً',
        'Good': 'جيدة',
        'Fair': 'مقبولة',
        'Poor': 'ضعيفة',
        'Item Description': 'وصف العنصر',
        'Item Notes': 'ملاحظات العنصر',
        'Cancel': 'إلغاء',
        'Save Client & Item': 'حفظ العميل والعنصر',
        'Saving...': 'جاري الحفظ...',
        'Nationality Placeholder': 'الجنسية (مثال: سعودي، مصري...)',
        'Address Placeholder': 'العنوان (مثال: الرياض، شارع الملك فهد...)',
        'Client Notes Placeholder': 'أي ملاحظات إضافية عن العميل...',
        'Origin Country Placeholder': 'بلد المنشأ (مثال: مصر، العراق...)',
        'Year Made Placeholder': 'مثال: 1920 أو القرن 19',
        'Materials Placeholder': 'مثال: ذهب، فضة، خشب، برونز',
        'Weight Placeholder': 'مثال: 50',
        'Unit': 'وحدة',
        'Dimensions Placeholder': 'مثال: 10×15×5 سم',
        'Item Description Placeholder': 'وصف تفصيلي للعنصر...',
        'Item Notes Placeholder': 'أي ملاحظات إضافية عن العنصر...',
        'Nationality Placeholder': 'Nationality (e.g. Saudi, Egyptian...)',
        'Address Placeholder': 'Address (e.g. Riyadh, King Fahd St...)',
        'Client Notes Placeholder': 'Any additional notes about the client...',
        'Origin Country Placeholder': 'Origin Country (e.g. Egypt, Iraq...)',
        'Year Made Placeholder': 'e.g. 1920 or 19th century',
        'Materials Placeholder': 'e.g. Gold, Silver, Wood, Bronze',
        'Weight Placeholder': 'e.g. 50 grams',
        'Dimensions Placeholder': 'e.g. 10×15×5 cm',
        'Item Description Placeholder': 'Detailed description of the item...',
        'Item Notes Placeholder': 'Any additional notes about the item...'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    submitForm() {
      this.loading = true
      
      // تأكد من وجود قطعة واحدة على الأقل
      if (this.form.artifacts.length === 0) {
        this.addArtifact()
      }
      
      // التحقق من صحة البيانات
      let hasValidArtifact = false
      for (let artifact of this.form.artifacts) {
        if (artifact.type && artifact.service && artifact.weight) {
          hasValidArtifact = true
          break
        }
      }
      
      if (!hasValidArtifact) {
        this.loading = false
        alert('يرجى إدخال نوع القطعة والخدمة والوزن لقطعة واحدة على الأقل')
        return
      }
      
      console.log('Sending data:', this.form.data())
      
      this.form.post(this.$route('reception.store-client'), {
        onSuccess: (page) => {
          console.log('Success!', page)
          this.loading = false
          // التوجيه التلقائي سيتم من الكنترولر
        },
        onError: (errors) => {
          console.log('Errors:', errors)
          this.loading = false
          
          // إظهار رسالة خطأ للمستخدم
          if (errors.error) {
            alert(errors.error)
          } else if (errors.artifacts) {
            alert('يرجى التأكد من إدخال معلومات القطع بشكل صحيح')
          } else {
            alert('حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى.')
          }
        },
        onFinish: () => {
          this.loading = false
        }
      })
    },
    addArtifact() {
      this.form.artifacts.push({
        type: '',
        service: '',
        weight: '',
        weight_unit: '',
        price: '',
        notes: '',
        delivery_type: ''
      })
    },
    async calculatePriceForArtifact(index) {
      const artifact = this.form.artifacts[index]
      if (!artifact.type || !artifact.service || !artifact.weight) {
        return
      }

      try {
        // الحصول على CSRF token بطريقة آمنة
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
            type: artifact.type,
            service: artifact.service,
            weight: parseFloat(artifact.weight)
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
          if (artifact.delivery_type === 'Same Day') {
            finalPrice = priceValue * 2 // 200% من السعر
            priceMultiplier = 2
            multiplierText = ' (Same Day: 200%)'
          } else if (artifact.delivery_type === '48 hours') {
            finalPrice = priceValue * 0.7 // 70% من السعر
            priceMultiplier = 0.7
            multiplierText = ' (48 hours: 70%)'
          } else if (artifact.delivery_type === '72 hours') {
            finalPrice = priceValue * 0.5 // 50% من السعر
            priceMultiplier = 0.5
            multiplierText = ' (72 hours: 50%)'
          }

          this.form.artifacts[index].price = finalPrice.toFixed(2)
          console.log(`Base Price: ${priceValue} | Final Price: ${finalPrice}${multiplierText}`)
        } else {
          this.form.artifacts[index].price = 'N/A'
        }
      } catch (error) {
        console.error('Error calculating price:', error)
        this.form.artifacts[index].price = 'Error'
        // إظهار رسالة خطأ للمستخدم
        alert(this.__('خطأ في حساب السعر. يرجى المحاولة مرة أخرى.'))
      }
    },
    removeArtifact(index) {
      this.form.artifacts.splice(index, 1)
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
  background-position: right 12px center;
  background-size: 16px;
  padding-right: 40px !important;
}

select option {
  padding: 12px;
  font-size: 14px;
}

select option:first-child {
  color: #6b7280;
  font-style: italic;
}

/* تحسين مظهر الجدول */
table {
  border-collapse: collapse;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

th, td {
  border: 1px solid #e5e7eb;
}

/* تحسين مظهر الأزرار */
button {
  transition: all 0.2s ease-in-out;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* تحسين مظهر الحقول */
input, select {
  transition: all 0.2s ease-in-out;
}

input:focus, select:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
}

/* تحسين مظهر النموذج */
.form-container {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

/* تحسين مظهر العناوين */
.section-header {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style> 