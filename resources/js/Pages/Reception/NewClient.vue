<template>
  <DashboardLayout :pageTitle="__('New Client Registration')">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('New Client Registration') }}</h2>
        <p class="text-gray-600">{{ __('Register a new client and their artifact') }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow-md p-6">
        <!-- Client Information Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-user mr-2 text-blue-500"></i>
            {{ __('Client Information') }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Full Name') }} <span class="text-red-500">*</span>
              </label>
              <input v-model="form.full_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.full_name }" required />
              <p v-if="errors.full_name" class="text-red-500 text-sm mt-1">{{ errors.full_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Company Name') }}
              </label>
              <input v-model="form.company_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.company_name }" />
              <p v-if="errors.company_name" class="text-red-500 text-sm mt-1">{{ errors.company_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Mobile') }} <span class="text-red-500">*</span>
              </label>
              <input v-model="form.mobile" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.mobile }" required />
              <p v-if="errors.mobile" class="text-red-500 text-sm mt-1">{{ errors.mobile }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email') }}
              </label>
              <input v-model="form.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.email }" />
              <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('City') }}
              </label>
              <input v-model="form.city" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.city }" />
              <p v-if="errors.city" class="text-red-500 text-sm mt-1">{{ errors.city }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Delivery Date') }}
              </label>
              <input v-model="form.delivery_date" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" :class="{ 'border-red-500': errors.delivery_date }" />
              <p v-if="errors.delivery_date" class="text-red-500 text-sm mt-1">{{ errors.delivery_date }}</p>
            </div>
          </div>
        </div>

        <!-- Artifacts Table Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-gem mr-2 text-green-500"></i>
            {{ __('Artifacts Information') }}
          </h3>
          <table class="min-w-full bg-white rounded shadow mb-4">
            <thead>
              <tr>
                <th class="px-2 py-1">#</th>
                <th class="px-2 py-1">{{ __('Type') }}</th>
                <th class="px-2 py-1">{{ __('Service') }}</th>
                <th class="px-2 py-1">{{ __('Weight') }}</th>
                <th class="px-2 py-1">{{ __('Delivery Type') }}</th>
                <th class="px-2 py-1">{{ __('Notes') }}</th>
                <th class="px-2 py-1"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(artifact, idx) in form.artifacts" :key="idx">
                <td class="px-2 py-1">{{ idx + 1 }}</td>
                <td class="px-2 py-1">
                  <select v-model="artifact.type" class="w-full px-2 py-1 border border-gray-300 rounded">
                    <option value="" disabled>{{ __("Select Type") }}</option>
                    <option v-for="option in typeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-2 py-1">
                  <select v-model="artifact.service" class="w-full px-2 py-1 border border-gray-300 rounded">
                    <option value="" disabled>{{ __("Select Service") }}</option>
                    <option v-for="option in serviceOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-2 py-1">
                  <input v-model="artifact.weight" type="text" class="w-full px-2 py-1 border border-gray-300 rounded" />
                </td>
                <td class="px-2 py-1">
                  <select v-model="artifact.delivery_type" class="w-full px-2 py-1 border border-gray-300 rounded">
                    <option value="" disabled>{{ __("Select Delivery Type") }}</option>
                    <option v-for="option in deliveryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                  </select>
                </td>
                <td class="px-2 py-1">
                  <input v-model="artifact.notes" type="text" class="w-full px-2 py-1 border border-gray-300 rounded" />
                </td>
                <td class="px-2 py-1">
                  <button type="button" @click="removeArtifact(idx)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
          <button type="button" @click="addArtifact" class="btn btn-secondary"><i class="fas fa-plus mr-1"></i> {{ __('Add Artifact') }}</button>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
          <Link 
            :href="$route('reception.index')" 
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            {{ __('Cancel') }}
          </Link>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center">
              <i class="fas fa-spinner fa-spin mr-2"></i>
              {{ __('Saving...') }}
            </span>
            <span v-else class="flex items-center">
              <i class="fas fa-save mr-2"></i>
              {{ __('Save Client & Artifact') }}
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
      { value: 'Other Color Gemstones', label: locale === 'ar' ? 'أحجار ملونة أخرى' : 'Other Color Gemstones' },
      { value: 'Colorless Diamonds', label: locale === 'ar' ? 'ألماس عديم اللون' : 'Colorless Diamonds' },
      { value: 'Jewellery Report', label: locale === 'ar' ? 'تقرير مجوهرات' : 'Jewellery Report' },
      { value: 'Loose Stones', label: locale === 'ar' ? 'أحجار مفكوكة' : 'Loose Stones' },
      { value: 'Loose Diamonds', label: locale === 'ar' ? 'ألماس مفكوك' : 'Loose Diamonds' },
      { value: 'Diamond Screening', label: locale === 'ar' ? 'فحص الألماس' : 'Diamond Screening' },
      { value: 'Consultation', label: locale === 'ar' ? 'استشارة' : 'Consultation' },
      { value: 'Ring', label: locale === 'ar' ? 'خاتم' : 'Ring' },
      { value: 'Necklace', label: locale === 'ar' ? 'عقد' : 'Necklace' },
      { value: 'Pair of Earring', label: locale === 'ar' ? 'زوج أقراط' : 'Pair of Earring' },
      { value: 'Earring', label: locale === 'ar' ? 'قرط' : 'Earring' },
      { value: 'Bracelet', label: locale === 'ar' ? 'سوار' : 'Bracelet' },
      { value: 'Charm Bracelet', label: locale === 'ar' ? 'سوار تعويذة' : 'Charm Bracelet' },
      { value: 'Nose Pin', label: locale === 'ar' ? 'دبوس أنف' : 'Nose Pin' },
      { value: 'Chokker', label: locale === 'ar' ? 'شوكر' : 'Chokker' },
      { value: 'Bangle', label: locale === 'ar' ? 'سوار دائري' : 'Bangle' },
      { value: 'Cuff links', label: locale === 'ar' ? 'أزرار أكمام' : 'Cuff links' },
      { value: 'Armlet', label: locale === 'ar' ? 'عضادة' : 'Armlet' },
      { value: 'Hair Clip', label: locale === 'ar' ? 'مشبك شعر' : 'Hair Clip' },
      { value: 'Brooch', label: locale === 'ar' ? 'بروش' : 'Brooch' },
      { value: 'Anklet', label: locale === 'ar' ? 'خلخال' : 'Anklet' },
      { value: 'Amulet', label: locale === 'ar' ? 'تعويذة' : 'Amulet' },
      { value: 'Locket', label: locale === 'ar' ? 'قلادة صغيرة' : 'Locket' },
      { value: 'Pendant', label: locale === 'ar' ? 'قلادة' : 'Pendant' },
    ];
    // Service options
    const serviceOptions = [
      { value: 'Regular (ID Report and ID + Origin Report)', label: locale === 'ar' ? 'عادي (تقرير هوية وتقرير هوية + أصل)' : 'Regular (ID Report and ID + Origin Report)' },
      { value: 'Mini Card Report (ID Report and ID + Origin Report)', label: locale === 'ar' ? 'تقرير بطاقة مصغرة (تقرير هوية وتقرير هوية + أصل)' : 'Mini Card Report (ID Report and ID + Origin Report)' },
      { value: 'Regular (ID Report)', label: locale === 'ar' ? 'عادي (تقرير هوية)' : 'Regular (ID Report)' },
      { value: 'Mini Card Report (ID Report)', label: locale === 'ar' ? 'تقرير بطاقة مصغرة (تقرير هوية)' : 'Mini Card Report (ID Report)' },
      { value: 'Regular (Diamond Grading Report)', label: locale === 'ar' ? 'عادي (تقرير تصنيف الألماس)' : 'Regular (Diamond Grading Report)' },
      { value: 'Mini Card Report (Mini Report)', label: locale === 'ar' ? 'تقرير بطاقة مصغرة (تقرير مصغر)' : 'Mini Card Report (Mini Report)' },
      { value: 'Regular (Jewellery Report)', label: locale === 'ar' ? 'عادي (تقرير مجوهرات)' : 'Regular (Jewellery Report)' },
      { value: 'Mini Card Report (Mini Jewellery Report)', label: locale === 'ar' ? 'تقرير بطاقة مصغرة (تقرير مجوهرات مصغر)' : 'Mini Card Report (Mini Jewellery Report)' },
      { value: 'Duplicate Identification Report', label: locale === 'ar' ? 'تقرير هوية مكرر' : 'Duplicate Identification Report' },
      { value: 'Duplicate PVC Card Report', label: locale === 'ar' ? 'تقرير بطاقة PVC مكرر' : 'Duplicate PVC Card Report' },
      { value: 'Premium Report', label: locale === 'ar' ? 'تقرير مميز' : 'Premium Report' },
      { value: 'Prestigious Report', label: locale === 'ar' ? 'تقرير مرموق' : 'Prestigious Report' },
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
    return { form, typeOptions, serviceOptions, deliveryOptions }
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
        'Register a new client and their artifact': 'تسجيل عميل جديد وقطعته',
        'Client Information': 'معلومات العميل',
        'Artifact Information': 'معلومات القطعة',
        'Full Name': 'الاسم الكامل',
        'Phone Number': 'رقم الهاتف',
        'National ID': 'رقم الهوية الوطنية',
        'Email': 'البريد الإلكتروني',
        'Nationality': 'الجنسية',
        'Address': 'العنوان',
        'Client Notes': 'ملاحظات العميل',
        'Artifact Name': 'اسم القطعة',
        'Artifact Type': 'نوع القطعة',
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
        'Dimensions': 'الأبعاد',
        'Condition': 'الحالة',
        'Select Condition': 'اختر الحالة',
        'Excellent': 'ممتازة',
        'Very Good': 'جيدة جداً',
        'Good': 'جيدة',
        'Fair': 'مقبولة',
        'Poor': 'ضعيفة',
        'Artifact Description': 'وصف القطعة',
        'Artifact Notes': 'ملاحظات القطعة',
        'Cancel': 'إلغاء',
        'Save Client & Artifact': 'حفظ العميل والقطعة',
        'Saving...': 'جاري الحفظ...',
        'Nationality Placeholder': 'الجنسية (مثال: سعودي، مصري...)',
        'Address Placeholder': 'العنوان (مثال: الرياض، شارع الملك فهد...)',
        'Client Notes Placeholder': 'أي ملاحظات إضافية عن العميل...',
        'Origin Country Placeholder': 'بلد المنشأ (مثال: مصر، العراق...)',
        'Year Made Placeholder': 'مثال: 1920 أو القرن 19',
        'Materials Placeholder': 'مثال: ذهب، فضة، خشب، برونز',
        'Weight Placeholder': 'مثال: 50 جرام',
        'Dimensions Placeholder': 'مثال: 10×15×5 سم',
        'Artifact Description Placeholder': 'وصف تفصيلي للقطعة...',
        'Artifact Notes Placeholder': 'أي ملاحظات إضافية عن القطعة...',
        'Nationality Placeholder': 'Nationality (e.g. Saudi, Egyptian...)',
        'Address Placeholder': 'Address (e.g. Riyadh, King Fahd St...)',
        'Client Notes Placeholder': 'Any additional notes about the client...',
        'Origin Country Placeholder': 'Origin Country (e.g. Egypt, Iraq...)',
        'Year Made Placeholder': 'e.g. 1920 or 19th century',
        'Materials Placeholder': 'e.g. Gold, Silver, Wood, Bronze',
        'Weight Placeholder': 'e.g. 50 grams',
        'Dimensions Placeholder': 'e.g. 10×15×5 cm',
        'Artifact Description Placeholder': 'Detailed description of the artifact...',
        'Artifact Notes Placeholder': 'Any additional notes about the artifact...'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    submitForm() {
      this.loading = true
      
      // تأكد من وجود قطعة واحدة على الأقل
      if (this.form.artifacts.length === 0) {
        this.addArtifact()
      }
      
      console.log('Sending data:', this.form.data())
      
      this.form.post(this.$route('reception.store'), {
        onSuccess: (page) => {
          console.log('Success!', page)
          this.loading = false
          // التوجيه التلقائي سيتم من الكنترولر
        },
        onError: (errors) => {
          console.log('Errors:', errors)
          this.loading = false
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
        notes: '',
        delivery_type: ''
      })
    },
    removeArtifact(index) {
      this.form.artifacts.splice(index, 1)
    }
  }
}
</script> 