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
          <select v-model="form.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="" disabled>{{ __("Select Type") }}</option>
            <option v-for="option in typeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Service") }}</label>
          <select v-model="form.service" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>{{ __("Select Service") }}</option>
            <option v-for="option in serviceOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Weight") }}</label>
          <input v-model="form.weight" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ __("Delivery Type") }}</label>
          <select v-model="form.delivery_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>{{ __("Select Delivery Type") }}</option>
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
    return { form, submitForm, typeOptions, serviceOptions, deliveryOptions }
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