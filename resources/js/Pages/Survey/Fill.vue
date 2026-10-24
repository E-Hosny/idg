<template>
  <div class="min-h-screen bg-stone-100 py-8 px-4" :dir="dir" :class="dir === 'rtl' ? 'font-arabic' : ''">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white border border-gray-200 rounded-t-lg px-6 py-6 text-center">
        <img src="/images/idg_logo.jpg" alt="IDG" class="w-20 h-20 mx-auto rounded-full object-cover" />
      </div>
      <div class="bg-green-800 text-white px-6 py-4 text-center">
        <h1 class="text-xl font-bold">Customers Feedback · استبيان آراء العملاء</h1>
        <p class="text-sm text-green-100 mt-2 flex flex-wrap justify-center gap-x-4 gap-y-1">
          <span>0580583000</span>
          <span>info@idg-lab.com.sa</span>
        </p>
      </div>

      <div v-if="invitation.completed" class="bg-white border border-t-0 border-gray-200 rounded-b-lg p-8 text-center space-y-3">
        <div class="text-green-700 text-3xl"><i class="fas fa-check-circle"></i></div>
        <h2 class="text-xl font-bold text-gray-900">شكراً لمشاركتكم</h2>
        <p class="text-gray-600">تم استلام تقييمكم. نقدّر وقتكم وثقتكم بمختبر IDG.</p>
        <p class="text-gray-500 text-sm">Thank you. Your feedback has been received.</p>
      </div>

      <form v-else class="bg-white border border-t-0 border-gray-200 rounded-b-lg p-6 space-y-6" @submit.prevent="submit">
        <p class="text-sm text-red-600">* {{ copy.requiredHint }}</p>

        <section v-if="step === 'language'" class="border border-red-300 rounded-lg p-4 space-y-3">
          <h2 class="font-semibold text-gray-900">Language / اللغة <span class="text-red-600">*</span></h2>
          <label class="flex items-center gap-2">
            <input v-model="form.language" type="radio" value="en" />
            English
          </label>
          <label class="flex items-center gap-2">
            <input v-model="form.language" type="radio" value="ar" />
            عربي
          </label>
          <p v-if="languageError" class="text-sm text-red-600">This is a required question · هذا السؤال مطلوب</p>
          <button type="button" class="mt-2 px-5 py-2 bg-white border border-gray-300 rounded-md font-semibold hover:bg-gray-50" @click="continueFromLanguage">
            Next | التالي
          </button>
        </section>

        <template v-else>
          <div class="bg-green-800 text-white rounded-md px-4 py-3 text-sm leading-7">
            <p>{{ copy.thanks }}</p>
            <p class="mt-1 text-green-100">{{ copy.scale }}</p>
            <p v-if="invitation.receiving_record_no" class="mt-1">{{ copy.record }}: {{ invitation.receiving_record_no }}</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ copy.customerName }} <span class="text-red-600">*</span></label>
            <input v-model="form.customer_name" type="text" class="field" />
            <p v-if="form.errors.customer_name" class="err">{{ form.errors.customer_name }}</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ copy.mobile }} <span class="text-red-600">*</span></label>
            <input v-model="form.mobile" type="text" class="field" />
            <p v-if="form.errors.mobile" class="err">{{ form.errors.mobile }}</p>
          </div>

          <RatingField
            v-model="form.service_quality_rating"
            :label="copy.serviceQuality"
            :error="form.errors.service_quality_rating"
            name="service_quality_rating"
          />
          <RatingField
            v-model="form.delivery_time_rating"
            :label="copy.deliveryTime"
            :error="form.errors.delivery_time_rating"
            name="delivery_time_rating"
          />
          <RatingField
            v-model="form.report_quality_rating"
            :label="copy.reportQuality"
            :error="form.errors.report_quality_rating"
            name="report_quality_rating"
          />

          <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ copy.signer }} <span class="text-red-600">*</span></label>
            <input v-model="form.signer_name" type="text" class="field" />
            <p v-if="form.errors.signer_name" class="err">{{ form.errors.signer_name }}</p>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-800 mb-2">{{ copy.complaint }} <span class="text-red-600">*</span></p>
            <div class="flex gap-6">
              <label class="flex items-center gap-2">
                <input v-model="form.has_complaint" type="radio" value="yes" />
                {{ copy.yes }}
              </label>
              <label class="flex items-center gap-2">
                <input v-model="form.has_complaint" type="radio" value="no" />
                {{ copy.no }}
              </label>
            </div>
            <p v-if="form.errors.has_complaint" class="err">{{ form.errors.has_complaint }}</p>
          </div>

          <div v-if="form.has_complaint === 'yes'">
            <label class="block text-sm font-semibold text-gray-800 mb-1">{{ copy.complaintDetails }} <span class="text-red-600">*</span></label>
            <textarea v-model="form.complaint_details" rows="4" class="field"></textarea>
            <p v-if="form.errors.complaint_details" class="err">{{ form.errors.complaint_details }}</p>
          </div>

          <div class="flex items-center justify-between gap-3 pt-2">
            <button type="button" class="px-5 py-2 border border-gray-300 rounded-md font-semibold hover:bg-gray-50" @click="step = 'language'">
              {{ copy.back }}
            </button>
            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-green-800 text-white rounded-md font-semibold hover:bg-green-900 disabled:opacity-50">
              {{ form.processing ? copy.sending : copy.submit }}
            </button>
          </div>
        </template>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import RatingField from './RatingField.vue'

const props = defineProps({
  invitation: { type: Object, required: true },
})

const step = ref('language')
const languageError = ref(false)

const form = useForm({
  language: '',
  customer_name: props.invitation.customer_name || '',
  mobile: props.invitation.customer_phone || '',
  service_quality_rating: null,
  delivery_time_rating: null,
  report_quality_rating: null,
  signer_name: '',
  has_complaint: '',
  complaint_details: '',
})

const dir = computed(() => (form.language === 'en' ? 'ltr' : 'rtl'))

const dictionary = {
  ar: {
    requiredHint: 'يشير إلى سؤال مطلوب',
    language: 'اللغة',
    languageRequired: 'هذا السؤال مطلوب',
    next: 'التالي',
    back: 'رجوع',
    submit: 'إرسال',
    sending: 'جاري الإرسال...',
    thanks: 'شكراً على وقتك! رأيك يساعدنا على تحسين خدماتنا. الاستبيان يستغرق أقل من دقيقتين.',
    scale: 'مقياس التقييم: 1 = غير راضٍ إطلاقاً، 2 = غير راضٍ، 3 = محايد، 4 = راضٍ، 5 = راضٍ جداً',
    record: 'رقم سجل الاستلام',
    customerName: 'اسم العميل / الشركة',
    mobile: 'رقم الجوال',
    serviceQuality: 'ما هو تقييمك لجودة خدماتنا؟',
    deliveryTime: 'ما هو تقييمك لالتزام بوقت تسليم التقارير؟',
    reportQuality: 'ما مدى رضاك عن وضوح وجودة تقرير الاختبار؟',
    signer: 'الاسم / التوقيع',
    complaint: 'هل لديك شكوى؟',
    yes: 'نعم',
    no: 'لا',
    complaintDetails: 'يرجى توضيح الشكوى',
  },
  en: {
    requiredHint: 'Indicates a required question',
    language: 'Language',
    languageRequired: 'This is a required question',
    next: 'Next',
    back: 'Back',
    submit: 'Submit',
    sending: 'Submitting...',
    thanks: 'Thank you for your time. Your feedback helps us improve. This survey takes less than two minutes.',
    scale: 'Scale: 1 = Very dissatisfied, 2 = Dissatisfied, 3 = Neutral, 4 = Satisfied, 5 = Very satisfied',
    record: 'Receiving record',
    customerName: 'Customer / company name',
    mobile: 'Mobile number',
    serviceQuality: 'How do you rate the quality of our services?',
    deliveryTime: 'How do you rate our commitment to report delivery time?',
    reportQuality: 'How satisfied are you with the clarity and quality of the test report?',
    signer: 'Name / signature',
    complaint: 'Do you have a complaint?',
    yes: 'Yes',
    no: 'No',
    complaintDetails: 'Please describe the complaint',
  },
}

const copy = computed(() => dictionary[form.language === 'en' ? 'en' : 'ar'])

function continueFromLanguage() {
  if (form.language !== 'ar' && form.language !== 'en') {
    languageError.value = true
    return
  }
  languageError.value = false
  step.value = 'questions'
}

function submit() {
  form.post(route('survey.submit', { token: props.invitation.token }), {
    preserveScroll: true,
  })
}
</script>

<style scoped>
.field {
  width: 100%;
  border: 0;
  border-bottom: 1px solid #9ca3af;
  background: transparent;
  padding: 8px 2px;
  outline: none;
}
.field:focus {
  border-bottom-color: #166534;
}
.err {
  margin-top: 4px;
  font-size: 13px;
  color: #dc2626;
}
.font-arabic {
  font-family: 'Tajawal', sans-serif;
}
</style>
