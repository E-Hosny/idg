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
              <input 
                v-model="form.full_name" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.full_name }"
                required
              />
              <p v-if="errors.full_name" class="text-red-500 text-sm mt-1">{{ errors.full_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Phone Number') }} <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.phone" 
                type="tel" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.phone }"
                required
              />
              <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('National ID') }}
              </label>
              <input 
                v-model="form.national_id" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.national_id }"
              />
              <p v-if="errors.national_id" class="text-red-500 text-sm mt-1">{{ errors.national_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email') }}
              </label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.email }"
              />
              <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Nationality') }}
              </label>
              <input 
                v-model="form.nationality" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.nationality }"
                :placeholder="__( 'Nationality Placeholder' )"
              />
              <p v-if="errors.nationality" class="text-red-500 text-sm mt-1">{{ errors.nationality }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Address') }}
              </label>
              <input 
                v-model="form.address" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.address }"
                :placeholder="__( 'Address Placeholder' )"
              />
              <p v-if="errors.address" class="text-red-500 text-sm mt-1">{{ errors.address }}</p>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ __('Client Notes') }}
            </label>
            <textarea 
              v-model="form.client_notes" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.client_notes }"
              :placeholder="__( 'Client Notes Placeholder' )"
            ></textarea>
            <p v-if="errors.client_notes" class="text-red-500 text-sm mt-1">{{ errors.client_notes }}</p>
          </div>
        </div>

        <!-- Artifact Information Section -->
        <div class="mb-8">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-gem mr-2 text-green-500"></i>
            {{ __('Artifact Information') }}
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Artifact Name') }} <span class="text-red-500">*</span>
              </label>
              <input 
                v-model="form.artifact_name" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.artifact_name }"
                required
              />
              <p v-if="errors.artifact_name" class="text-red-500 text-sm mt-1">{{ errors.artifact_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Artifact Type') }} <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.artifact_type" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.artifact_type }"
                required
              >
                <option value="">{{ __('Select Type') }}</option>
                <option value="antique">{{ __('Antique') }}</option>
                <option value="jewelry">{{ __('Jewelry') }}</option>
                <option value="artwork">{{ __('Artwork') }}</option>
                <option value="furniture">{{ __('Furniture') }}</option>
                <option value="coins">{{ __('Coins') }}</option>
                <option value="books">{{ __('Books') }}</option>
                <option value="other">{{ __('Other') }}</option>
              </select>
              <p v-if="errors.artifact_type" class="text-red-500 text-sm mt-1">{{ errors.artifact_type }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Origin Country') }}
              </label>
              <input 
                v-model="form.origin_country" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.origin_country }"
                :placeholder="__( 'Origin Country Placeholder' )"
              />
              <p v-if="errors.origin_country" class="text-red-500 text-sm mt-1">{{ errors.origin_country }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Year Made') }}
              </label>
              <input 
                v-model="form.year_made" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.year_made }"
                :placeholder="__( 'Year Made Placeholder' )"
              />
              <p v-if="errors.year_made" class="text-red-500 text-sm mt-1">{{ errors.year_made }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Materials') }}
              </label>
              <input 
                v-model="form.materials" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.materials }"
                :placeholder="__( 'Materials Placeholder' )"
              />
              <p v-if="errors.materials" class="text-red-500 text-sm mt-1">{{ errors.materials }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Weight') }}
              </label>
              <input 
                v-model="form.weight" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.weight }"
                :placeholder="__( 'Weight Placeholder' )"
              />
              <p v-if="errors.weight" class="text-red-500 text-sm mt-1">{{ errors.weight }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Dimensions') }}
              </label>
              <input 
                v-model="form.dimensions" 
                type="text" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.dimensions }"
                :placeholder="__( 'Dimensions Placeholder' )"
              />
              <p v-if="errors.dimensions" class="text-red-500 text-sm mt-1">{{ errors.dimensions }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Condition') }} <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.condition" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.condition }"
                required
              >
                <option value="">{{ __('Select Condition') }}</option>
                <option value="excellent">{{ __('Excellent') }}</option>
                <option value="very_good">{{ __('Very Good') }}</option>
                <option value="good">{{ __('Good') }}</option>
                <option value="fair">{{ __('Fair') }}</option>
                <option value="poor">{{ __('Poor') }}</option>
              </select>
              <p v-if="errors.condition" class="text-red-500 text-sm mt-1">{{ errors.condition }}</p>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ __('Artifact Description') }}
            </label>
            <textarea 
              v-model="form.artifact_description" 
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.artifact_description }"
              :placeholder="__( 'Artifact Description Placeholder' )"
            ></textarea>
            <p v-if="errors.artifact_description" class="text-red-500 text-sm mt-1">{{ errors.artifact_description }}</p>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ __('Artifact Notes') }}
            </label>
            <textarea 
              v-model="form.artifact_notes" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.artifact_notes }"
              :placeholder="__( 'Artifact Notes Placeholder' )"
            ></textarea>
            <p v-if="errors.artifact_notes" class="text-red-500 text-sm mt-1">{{ errors.artifact_notes }}</p>
          </div>
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
      national_id: '',
      phone: '',
      email: '',
      nationality: '',
      address: '',
      client_notes: '',
      // Artifact data
      artifact_name: '',
      artifact_description: '',
      artifact_type: '',
      origin_country: '',
      year_made: '',
      materials: '',
      weight: '',
      dimensions: '',
      condition: '',
      artifact_notes: ''
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
      this.form.post(this.$route('reception.store'), {
        onSuccess: () => {
          this.loading = false
        },
        onError: () => {
          this.loading = false
        }
      })
    }
  }
}
</script> 