<template>
  <DashboardLayout :page-title="(isEditing ? 'Edit ' : '') + artifactType + ' Evaluation'">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto mt-8 border border-green-700">
      <h1 class="text-2xl font-bold text-green-800 mb-6 text-center">
        {{ isEditing ? 'Edit ' : '' }}{{ artifactType }} Evaluation
      </h1>
      <form @submit.prevent="submitEvaluation" class="space-y-8">
        <!-- رقم العنصر -->
        <div class="mb-6">
          <label class="block text-gray-700">Item/Product ID #</label>
          <input type="text" :value="artifact?.artifact_code || ''" class="input bg-gray-100" readonly />
        </div>
        <!-- 1. Job Information -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١. معلومات المهمة' : '1. Job Information' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">{{ locale === 'ar' ? 'تاريخ الفحص' : 'Test Date' }}</label>
              <input type="date" v-model="form.test_date" class="input" :max="today" required />
            </div>
            <div>
              <label class="block text-gray-700">Test Location</label>
              <input type="text" v-model="form.test_location" class="input" />
            </div>
          </div>
        </section>

        <!-- 2. Stone Information -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٢. معلومات الحجر' : '2. Stone Information' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Weight</label>
              <input type="number" v-model="form.weight" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Colour</label>
              <select v-model="form.colour" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر اللون' : 'Select Colour' }}</option>
                <option v-for="option in colourOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Transparency</label>
              <select v-model="form.transparency" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر الشفافية' : 'Select Transparency' }}</option>
                <option v-for="option in transparencyOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Lustre</label>
              <select v-model="form.lustre" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر اللمعان' : 'Select Lustre' }}</option>
                <option v-for="option in lustreOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Tone</label>
              <select v-model="form.tone" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر النغمة' : 'Select Tone' }}</option>
                <option v-for="option in toneOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Phenomena</label>
              <select v-model="form.phenomena" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر الظاهرة' : 'Select Phenomena' }}</option>
                <option v-for="option in phenomenaOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Saturation</label>
              <select v-model="form.saturation" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر التشبع' : 'Select Saturation' }}</option>
                <option v-for="option in saturationOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Measurements (mm)</label>
              <input type="text" v-model="form.measurements" class="input" placeholder="e.g. 7.2 x 5.1 x 3.0" />
            </div>
            <div>
              <label class="block text-gray-700">Shape</label>
              <select v-model="form.shape" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر الشكل' : 'Select Shape' }}</option>
                <option v-for="option in shapeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Cut</label>
              <select v-model="form.cut" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر القطع' : 'Select Cut' }}</option>
                <option v-for="option in cutOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
          </div>
        </section>

        <!-- 3. Pleochroism -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٣. البلوكرويزم' : '3. Pleochroism' }}</h2>
          <div class="flex items-center gap-4">
            <label v-for="option in pleochroismOptions" :key="option.value" class="flex items-center gap-1">
              <input type="radio" v-model="form.pleochroism" :value="option.value" /> {{ option.label }}
            </label>
          </div>
        </section>

        <!-- 4. Optic Character -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٤. خصائص البصرية' : '4. Optic Character' }}</h2>
          <select v-model="form.optic_character" class="input">
            <option value="">{{ locale === 'ar' ? 'اختر الخصائص البصرية' : 'Select Optic Character' }}</option>
            <option v-for="option in opticCharacterOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
          </select>
        </section>

        <!-- 5. Refractive Index -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٥. مؤشر الانكسار' : '5. Refractive Index' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
            <input v-for="i in 5" :key="i" type="text" v-model="form.refractive_index[i-1]" class="input" placeholder="{{ i }}" />
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">RI Result</label>
            <input type="text" v-model="form.ri_result" class="input" />
          </div>
        </section>

        <!-- 6. Inclusion (Magnification) -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٦. التضمين (التكبير)' : '6. Inclusion (Magnification)' }}</h2>
          <div class="grid grid-cols-1 gap-3">
            <div>
              <label class="block text-gray-700 mb-1">{{ locale === 'ar' ? 'اختر من القائمة' : 'Select from list' }}</label>
              <select v-model="inclusionSelectModel" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر التضمينات' : 'Select Inclusion' }}</option>
                <option v-for="option in inclusionOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                <option value="__manual__">{{ locale === 'ar' ? 'إدخال يدوي…' : 'Other / manual entry…' }}</option>
              </select>
            </div>
            <div v-if="inclusionManualMode">
              <label class="block text-gray-700 mb-1">{{ locale === 'ar' ? 'تفاصيل التضمين (يدوي)' : 'Inclusion details (manual)' }}</label>
              <textarea v-model="form.inclusion" class="input" rows="2" :placeholder="locale === 'ar' ? 'اكتب وصف التضمين…' : 'Describe inclusion…'"></textarea>
            </div>
          </div>
        </section>

        <!-- 7. Specific Gravity -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٧. كثافة نوعية' : '7. Specific Gravity' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Weight in Air</label>
              <input type="number" v-model="form.weight_air" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Weight in Water</label>
              <input type="number" v-model="form.weight_water" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">S.G. Result</label>
              <input type="text" v-model="form.sg_result" class="input" />
            </div>
          </div>
        </section>

        <!-- 8. Fluorescence -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٨. الفلورسنس' : '8. Fluorescence' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700">Long Wave</label>
              <select v-model="form.fluorescence_long" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر الفلورسنس' : 'Select Fluorescence' }}</option>
                <option v-for="option in fluorescenceOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Short Wave</label>
              <select v-model="form.fluorescence_short" class="input">
                <option value="">{{ locale === 'ar' ? 'اختر الفلورسنس' : 'Select Fluorescence' }}</option>
                <option v-for="option in fluorescenceOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
              </select>
            </div>
          </div>
        </section>

        <!-- 9. Result -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '٩. النتيجة' : '9. Result' }}</h2>
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-1">
              <input type="radio" v-model="form.result" value="Pass" /> Pass
            </label>
            <label class="flex items-center gap-1">
              <input type="radio" v-model="form.result" value="Fail" /> Fail
            </label>
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">Group</label>
            <select v-model="form.stone_group" class="input">
              <option value="">{{ locale === 'ar' ? 'اختر المجموعة' : 'Select Group' }}</option>
              <option v-for="option in groupOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">Species</label>
            <select v-model="form.species" class="input">
              <option value="">{{ locale === 'ar' ? 'اختر النوع الأساسي' : 'Select Species' }}</option>
              <option v-for="(option, idx) in speciesOptions" :key="'species-' + idx" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">Variety</label>
            <select v-model="form.variety" class="input">
              <option value="">{{ locale === 'ar' ? 'اختر النوع' : 'Select Variety' }}</option>
              <option v-for="(option, idx) in varietyOptions" :key="'variety-' + idx" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">Treatment</label>
            <select v-model="form.treatment" class="input">
              <option value="">{{ locale === 'ar' ? 'اختر المعالجة' : 'Select Treatment' }}</option>
              <option v-for="option in treatmentOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
          <div class="mt-2">
            <label class="block text-gray-700">Gemstone Type</label>
            <select v-model="form.gemstone_type" class="input">
              <option value="">{{ locale === 'ar' ? 'اختر نوع الحجر' : 'Select Gemstone Type' }}</option>
              <option v-for="option in gemstoneTypeOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
          </div>
        </section>

        <!-- 10. Comments (Treatments) -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١٠. تعليقات (المعالجات)' : '10. Comments (Treatments)' }}</h2>
          <textarea v-model="form.comments" class="input" rows="2"></textarea>
        </section>

        <!-- 11. Grader (auto-filled) -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١١. المرشح' : '11. Grader' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Name</label>
              <input type="text" v-model="form.grader_name" class="input bg-gray-100" readonly />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.grader_date" class="input bg-gray-100" readonly />
            </div>
          </div>
        </section>

        <!-- 12. Analytical Equipment's Technical Interpretation -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١٢. تفسير التقنية للمعدات التحليلية' : '12. Analytical Equipment\'s Technical Interpretation' }}</h2>
          <textarea v-model="form.analytical_interpretation" class="input" rows="2"></textarea>
        </section>

        <!-- 13. Stone Photography -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١٣. صورة الحجر' : '13. Stone Photography' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700">Image 1</label>
              <input type="file" @change="onFileChange($event, 'image1')" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Image 2</label>
              <input type="file" @change="onFileChange($event, 'image2')" class="input" />
            </div>
          </div>
        </section>

        <!-- 14. Retaining Information -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١٤. معلومات الاحتفاظ' : '14. Retaining Information' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Place</label>
              <input type="text" v-model="form.retaining_place" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Retained by</label>
              <input type="text" v-model="form.retained_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.retained_date" class="input" />
            </div>
          </div>
        </section>

        <!-- 15. Reporting Information -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? '١٥. معلومات التقرير' : '15. Reporting Information' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Report Done?</label>
              <input type="checkbox" v-model="form.report_done" />
            </div>
            <div>
              <label class="block text-gray-700">Label Done?</label>
              <input type="checkbox" v-model="form.label_done" />
            </div>
          </div>
        </section>

        <!-- Checked by (auto-filled) -->
        <section>
          <h2 class="text-lg font-semibold text-green-700 mb-2">{{ locale === 'ar' ? 'تم التحقق منه' : 'Checked by' }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Name</label>
              <input type="text" v-model="form.checked_by" class="input bg-gray-100" readonly />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.checked_date" class="input bg-gray-100" readonly />
            </div>
          </div>
        </section>

        <div class="flex justify-end mt-8">
          <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-6 rounded-lg shadow">Save Evaluation</button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { gemstoneEvaluationOptions } from '@/constants/gemstoneEvaluationOptions';

const props = defineProps({ 
  artifact: Object,
  existingEvaluation: Object,
  isEditing: {
    type: Boolean,
    default: false
  }
});
const { locale } = usePage().props;

// Safely access artifact data
const artifact = computed(() => props.artifact || {});
const artifactType = computed(() => artifact.value?.type || (locale === 'ar' ? 'عنصر' : 'Item'));

const today = new Date().toISOString().split('T')[0];
const user = usePage().props.auth?.user || { name: 'Current User' };

// Debug logging
console.log('Evaluate props:', {
  existingEvaluation: props.existingEvaluation,
  test_date: props.existingEvaluation?.test_date,
  test_date_type: typeof props.existingEvaluation?.test_date,
  grader_date: props.existingEvaluation?.grader_date,
  grader_date_type: typeof props.existingEvaluation?.grader_date,
})

// Helper function to format date
const formatDate = (date) => {
  if (!date) return today
  
  console.log('formatDate input:', date, typeof date)
  
  // Handle Carbon date objects from Laravel
  if (date && typeof date === 'object') {
    // If it's a Carbon object with date property
    if (date.date) {
      return date.date.split(' ')[0]
    }
    // If it's a Carbon object with toISOString method
    if (date.toISOString) {
      return date.toISOString().split('T')[0]
    }
    // If it's a Date object
    if (date instanceof Date) {
      return date.toISOString().split('T')[0]
    }
  }
  
  if (typeof date === 'string') {
    // If it's a datetime string, take only the date part
    const formatted = date.includes(' ') ? date.split(' ')[0] : date
    console.log('formatDate output:', formatted)
    return formatted
  }
  
  console.log('formatDate fallback:', date)
  return date
}

const splitShapeCut = (value) => {
  if (!value || typeof value !== 'string') return { shape: '', cut: '' }
  const parts = value.split('/').map((part) => part.trim()).filter(Boolean)
  return {
    shape: parts[0] || '',
    cut: parts[1] || ''
  }
}

const splitSpeciesGroup = (value) => {
  if (!value || typeof value !== 'string') return { group: '', species: '' }
  const parts = value.split('/').map((part) => part.trim()).filter(Boolean)
  return {
    group: parts[0] || '',
    species: parts[1] || ''
  }
}

const existingShapeCut = splitShapeCut(props.existingEvaluation?.shape_cut)
const existingSpeciesGroup = splitSpeciesGroup(props.existingEvaluation?.species_group)

const form = useForm({
  test_date: formatDate(props.existingEvaluation?.test_date),
  test_location: props.existingEvaluation?.test_location || '',
  item_id: props.existingEvaluation?.item_id || '',
  weight: props.existingEvaluation?.weight || '',
  colour: props.existingEvaluation?.colour || '',
  transparency: props.existingEvaluation?.transparency || '',
  lustre: props.existingEvaluation?.lustre || '',
  tone: props.existingEvaluation?.tone || '',
  phenomena: props.existingEvaluation?.phenomena || '',
  saturation: props.existingEvaluation?.saturation || '',
  measurements: props.existingEvaluation?.measurements || '',
  shape: props.existingEvaluation?.shape || existingShapeCut.shape,
  cut: props.existingEvaluation?.cut || existingShapeCut.cut,
  shape_cut: props.existingEvaluation?.shape_cut || '',
  pleochroism: props.existingEvaluation?.pleochroism || '',
  optic_character: props.existingEvaluation?.optic_character || '',
  refractive_index: props.existingEvaluation?.refractive_index || ['', '', '', '', ''],
  ri_result: props.existingEvaluation?.ri_result || '',
  inclusion: props.existingEvaluation?.inclusion || '',
  weight_air: props.existingEvaluation?.weight_air || '',
  weight_water: props.existingEvaluation?.weight_water || '',
  sg_result: props.existingEvaluation?.sg_result || '',
  fluorescence_long: props.existingEvaluation?.fluorescence_long || '',
  fluorescence_short: props.existingEvaluation?.fluorescence_short || '',
  result: props.existingEvaluation?.result || '',
  stone_group: props.existingEvaluation?.stone_group || existingSpeciesGroup.group,
  species: props.existingEvaluation?.species || existingSpeciesGroup.species,
  variety: props.existingEvaluation?.variety || '',
  species_group: props.existingEvaluation?.species_group || '',
  treatment: props.existingEvaluation?.treatment || '',
  gemstone_type: props.existingEvaluation?.gemstone_type || '',
  comments: props.existingEvaluation?.comments || '',
  grader_name: props.existingEvaluation?.grader_name || user.name,
  grader_date: formatDate(props.existingEvaluation?.grader_date),
  analytical_interpretation: props.existingEvaluation?.analytical_interpretation || '',
  image1: props.existingEvaluation?.image1 || null,
  image2: props.existingEvaluation?.image2 || null,
  retaining_place: props.existingEvaluation?.retaining_place || '',
  retained_by: props.existingEvaluation?.retained_by || '',
  retained_date: formatDate(props.existingEvaluation?.retained_date),
  report_done: props.existingEvaluation?.report_done || false,
  label_done: props.existingEvaluation?.label_done || false,
  checked_by: props.existingEvaluation?.checked_by || user.name,
  checked_date: formatDate(props.existingEvaluation?.checked_date),
});

// Watch for changes in existingEvaluation and update form
watch(() => props.existingEvaluation, (newEvaluation) => {
  if (newEvaluation) {
    console.log('Updating form with new evaluation data:', newEvaluation);
    form.test_date = formatDate(newEvaluation.test_date);
    form.grader_date = formatDate(newEvaluation.grader_date);
    form.retained_date = formatDate(newEvaluation.retained_date);
    form.checked_date = formatDate(newEvaluation.checked_date);
    // Update other fields as needed
  }
}, { immediate: true, deep: true });

// Debug form values after initialization
onMounted(() => {
  console.log('Form initialized with values:', {
    test_date: form.test_date,
    grader_date: form.grader_date,
    retained_date: form.retained_date,
    checked_date: form.checked_date,
  });
});

const toSelectOptions = (items) => items.map((item) => ({ value: item, label: item }))

const colourOptions = toSelectOptions(gemstoneEvaluationOptions.colours)
const transparencyOptions = toSelectOptions(gemstoneEvaluationOptions.transparency)
const lustreOptions = toSelectOptions(gemstoneEvaluationOptions.lustres)
const toneOptions = toSelectOptions(gemstoneEvaluationOptions.tones)
const saturationOptions = [
  { value: 'Weak', label: locale === 'ar' ? 'ضعيف' : 'Weak' },
  { value: 'Moderate', label: locale === 'ar' ? 'متوسط' : 'Moderate' },
  { value: 'Strong', label: locale === 'ar' ? 'قوي' : 'Strong' },
];
const phenomenaOptions = toSelectOptions(gemstoneEvaluationOptions.phenomena)
const shapeOptions = toSelectOptions(gemstoneEvaluationOptions.shapes)
const cutOptions = toSelectOptions(gemstoneEvaluationOptions.cuts)
const pleochroismOptions = [
  { value: 'Strong', label: locale === 'ar' ? 'قوي' : 'Strong' },
  { value: 'Moderate', label: locale === 'ar' ? 'متوسط' : 'Moderate' },
  { value: 'Weak', label: locale === 'ar' ? 'ضعيف' : 'Weak' },
  { value: 'None', label: locale === 'ar' ? 'لا يوجد' : 'None' },
];
const opticCharacterOptions = toSelectOptions(gemstoneEvaluationOptions.opticCharacters)
const fluorescenceOptions = toSelectOptions(gemstoneEvaluationOptions.fluorescence)
const inclusionOptions = toSelectOptions(gemstoneEvaluationOptions.inclusions)

const inclusionManualMode = ref(false)
const inclusionKnownSet = new Set(gemstoneEvaluationOptions.inclusions.map((s) => s.trim().toLowerCase()))

function syncInclusionUiFromFormValue(val) {
  const raw = typeof val === 'string' ? val.trim() : ''
  if (!raw) {
    inclusionManualMode.value = false
    return
  }
  const isListed = inclusionKnownSet.has(raw.toLowerCase())
  inclusionManualMode.value = !isListed
}

syncInclusionUiFromFormValue(form.inclusion)

watch(() => form.inclusion, (v) => syncInclusionUiFromFormValue(v))

watch(
  () => props.existingEvaluation?.inclusion,
  (v) => {
    if (v === undefined) return
    form.inclusion = v || ''
    syncInclusionUiFromFormValue(form.inclusion)
  },
  { immediate: false },
)

const inclusionSelectModel = computed({
  get() {
    if (inclusionManualMode.value) return '__manual__'
    const v = typeof form.inclusion === 'string' ? form.inclusion.trim() : ''
    return v && inclusionKnownSet.has(v.toLowerCase()) ? form.inclusion : ''
  },
  set(sel) {
    if (sel === '__manual__') {
      inclusionManualMode.value = true
      const cur = typeof form.inclusion === 'string' ? form.inclusion.trim() : ''
      if (cur && !inclusionKnownSet.has(cur.toLowerCase())) return
      form.inclusion = ''
      return
    }
    inclusionManualMode.value = false
    form.inclusion = sel || ''
  },
})

const groupOptions = toSelectOptions(gemstoneEvaluationOptions.groups)
const speciesOptions = toSelectOptions(gemstoneEvaluationOptions.species)
const varietyOptions = toSelectOptions(gemstoneEvaluationOptions.varieties)
const treatmentOptions = toSelectOptions(gemstoneEvaluationOptions.treatments)
const gemstoneTypeOptions = toSelectOptions(gemstoneEvaluationOptions.gemstoneTypes)

function onFileChange(event, field) {
  const file = event.target.files[0];
  form[field] = file;
}

function submitEvaluation() {
  if (!artifact.value?.id) {
    alert('Error: Item data not available');
    return;
  }
  
  console.log('Submitting evaluation data:', form.data());
  
  form.shape_cut = [form.shape, form.cut].filter(Boolean).join(' / ')
  form.species_group = [form.stone_group, form.species].filter(Boolean).join(' / ')

  if (props.isEditing && props.existingEvaluation) {
    // Update existing evaluation
    const hasFiles = form.image1 instanceof File || form.image2 instanceof File
    form.put(`/artifacts/${artifact.value.id}/update-evaluation`, {
      forceFormData: hasFiles,
      onSuccess: () => {
        alert('Evaluation updated successfully!');
      },
      onError: (errors) => {
        console.error('Evaluation update errors:', errors);
        
        let errorMessage = 'Error updating evaluation.';
        if (errors.error) {
          errorMessage += ' ' + errors.error;
        } else if (typeof errors === 'object') {
          const errorKeys = Object.keys(errors);
          if (errorKeys.length > 0) {
            errorMessage += ' Please check the following fields: ' + errorKeys.join(', ');
          }
        }
        
        alert(errorMessage);
      }
    });
  } else {
    // Create new evaluation (multipart required when uploading stone photos)
    const hasFiles = form.image1 instanceof File || form.image2 instanceof File
    form.post(`/dashboard/artifacts/${artifact.value.id}/evaluate`, {
      forceFormData: hasFiles,
      onSuccess: () => {
        alert('Evaluation saved successfully!');
      },
      onError: (errors) => {
        console.error('Evaluation save errors:', errors);
        
        let errorMessage = 'Error saving evaluation.';
        if (errors.error) {
          errorMessage += ' ' + errors.error;
        } else if (typeof errors === 'object') {
          const errorKeys = Object.keys(errors);
          if (errorKeys.length > 0) {
            errorMessage += ' Please check the following fields: ' + errorKeys.join(', ');
          }
        }
        
        alert(errorMessage);
      }
    });
  }
}
</script>

<style scoped>
.input {
  @apply w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-700 bg-white;
}
</style> 