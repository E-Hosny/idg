<template>
  <DashboardLayout :page-title="isEditing ? 'Edit Diamond Evaluation' : 'Diamond Evaluation'">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-6xl mx-auto mt-8 border border-green-700">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-green-800 mb-2">
          {{ isEditing ? 'Edit Diamond Evaluation' : 'Loose Diamond Worksheet' }}
        </h1>
      </div>

      <form @submit.prevent="submitEvaluation" class="space-y-8">
        <!-- Item ID -->
        <div class="mb-6">
          <label class="block text-gray-700 font-semibold">Item/Product ID #</label>
          <input type="text" :value="artifact?.artifact_code || ''" class="input bg-gray-100" readonly />
        </div>

        <!-- 1. Job Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">1. Job Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700">Test Date</label>
              <input type="date" v-model="form.test_date" class="input" :max="today" required />
            </div>
            <div>
              <label class="block text-gray-700">Test Location</label>
              <input type="text" v-model="form.test_location" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Item/Product ID</label>
              <input type="text" v-model="form.item_product_id" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Receiving Record</label>
              <input type="text" v-model="form.receiving_record" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Prepared by</label>
              <input type="text" v-model="form.prepared_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Approved by</label>
              <input type="text" v-model="form.approved_by" class="input" />
            </div>
          </div>
        </section>

        <!-- 2. Stone Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">2. Stone Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700">Weight (ct)</label>
              <input type="number" v-model="form.weight" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Shape</label>
              <select v-model="form.shape" class="input">
                <option value="">Select Shape</option>
                <option value="Round Brilliant">Round Brilliant</option>
                <option value="Princess">Princess</option>
                <option value="Emerald">Emerald</option>
                <option value="Oval">Oval</option>
                <option value="Marquise">Marquise</option>
                <option value="Pear">Pear</option>
                <option value="Cushion">Cushion</option>
                <option value="Radiant">Radiant</option>
                <option value="Asscher">Asscher</option>
                <option value="Heart">Heart</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-gray-700 mb-2">Laser Inscription</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.laser_inscription" value="No" />
                  No
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.laser_inscription" value="Yes" />
                  Yes
                </label>
              </div>
            </div>
          </div>
        </section>

        <!-- 3. Lab-Grown Diamond Screen -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">3. Lab-Grown Diamond Screen</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-700 mb-2">A. HPHT Screen</h3>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.hpht_screen" value="Natural" />
                  Natural
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.hpht_screen" value="Lab-Grown" />
                  Lab-Grown
                </label>
              </div>
            </div>
            <div>
              <h3 class="font-semibold text-gray-700 mb-2">B. CVD Check</h3>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.cvd_check" value="Natural" />
                  Natural
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.cvd_check" value="Lab-Grown" />
                  Lab-Grown
                </label>
              </div>
            </div>
          </div>
        </section>

        <!-- 4. Proportion Grade -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">4. Proportion Grade</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Diameter (mm)</label>
              <input type="number" v-model="form.diameter" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Total Depth (%)</label>
              <input type="number" v-model="form.total_depth" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Table (%)</label>
              <input type="number" v-model="form.table" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Star Facet (%)</label>
              <input type="number" v-model="form.star_facet" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Crown Angle (°)</label>
              <input type="number" v-model="form.crown_angle" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Crown Height (%)</label>
              <input type="number" v-model="form.crown_height" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Girdle Thickness Min (%)</label>
              <input type="number" v-model="form.girdle_thickness_min" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Girdle Thickness Max (%)</label>
              <input type="number" v-model="form.girdle_thickness_max" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Pavilion Depth (%)</label>
              <input type="number" v-model="form.pavilion_depth" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Pavilion Angle (°)</label>
              <input type="number" v-model="form.pavilion_angle" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Lower Girdle (%)</label>
              <input type="number" v-model="form.lower_girdle" class="input" step="0.1" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Culet Size</label>
              <select v-model="form.culet_size" class="input">
                <option value="">Select Culet Size</option>
                <option value="None">None</option>
                <option value="Very Small">Very Small</option>
                <option value="Small">Small</option>
                <option value="Medium">Medium</option>
                <option value="Large">Large</option>
                <option value="Very Large">Very Large</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Girdle Condition</label>
              <select v-model="form.girdle_condition" class="input">
                <option value="">Select Condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Very Good">Very Good</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Culet Condition</label>
              <select v-model="form.culet_condition" class="input">
                <option value="">Select Condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Very Good">Very Good</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
          </div>
        </section>

        <!-- 5. Grade Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">5. Grade Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Polish:</label>
              <div class="space-y-2">
                <label v-for="grade in gradeOptions" :key="grade" class="flex items-center gap-2">
                  <input type="radio" v-model="form.polish" :value="grade" />
                  {{ grade }}
                </label>
              </div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Symmetry:</label>
              <div class="space-y-2">
                <label v-for="grade in gradeOptions" :key="grade" class="flex items-center gap-2">
                  <input type="radio" v-model="form.symmetry" :value="grade" />
                  {{ grade }}
                </label>
              </div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Cut:</label>
              <div class="space-y-2">
                <label v-for="grade in gradeOptions" :key="grade" class="flex items-center gap-2">
                  <input type="radio" v-model="form.cut" :value="grade" />
                  {{ grade }}
                </label>
              </div>
            </div>
          </div>
        </section>

        <!-- 6. Visual Inspection -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">6. Visual Inspection</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700">Clarity</label>
              <select v-model="form.clarity" class="input">
                <option value="">Select Clarity</option>
                <option value="FL">FL (Flawless)</option>
                <option value="IF">IF (Internally Flawless)</option>
                <option value="VVS1">VVS1</option>
                <option value="VVS2">VVS2</option>
                <option value="VS1">VS1</option>
                <option value="VS2">VS2</option>
                <option value="SI1">SI1</option>
                <option value="SI2">SI2</option>
                <option value="I1">I1</option>
                <option value="I2">I2</option>
                <option value="I3">I3</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700">Colour</label>
              <select v-model="form.colour" class="input">
                <option value="">Select Colour</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="G">G</option>
                <option value="H">H</option>
                <option value="I">I</option>
                <option value="J">J</option>
                <option value="K">K</option>
                <option value="L">L</option>
                <option value="M">M</option>
                <option value="N">N</option>
                <option value="O">O</option>
                <option value="P">P</option>
                <option value="Q">Q</option>
                <option value="R">R</option>
                <option value="S">S</option>
                <option value="T">T</option>
                <option value="U">U</option>
                <option value="V">V</option>
                <option value="W">W</option>
                <option value="X">X</option>
                <option value="Y">Y</option>
                <option value="Z">Z</option>
              </select>
            </div>
          </div>
        </section>

        <!-- 7. Fluorescence -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">7. Fluorescence</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Strength:</label>
              <div class="space-y-2">
                <label v-for="strength in fluorescenceStrengths" :key="strength" class="flex items-center gap-2">
                  <input type="radio" v-model="form.fluorescence_strength" :value="strength" />
                  {{ strength }}
                </label>
              </div>
            </div>
            <div>
              <label class="block text-gray-700">Colour</label>
              <input type="text" v-model="form.fluorescence_colour" class="input" placeholder="e.g. Blue, Yellow, etc." />
            </div>
          </div>
        </section>



        <!-- 8. Result -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">8. Result</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Evaluation Result:</label>
              <div class="space-y-2">
                <label v-for="result in resultOptions" :key="result" class="flex items-center gap-2">
                  <input type="radio" v-model="form.result" :value="result" />
                  {{ result }}
                </label>
              </div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Stone Type:</label>
              <div class="space-y-2">
                <label v-for="type in stoneTypeOptions" :key="type" class="flex items-center gap-2">
                  <input type="radio" v-model="form.stone_type" :value="type" />
                  {{ type }}
                </label>
              </div>
            </div>
          </div>
        </section>

        <!-- 9. Comments -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">9. Comments</h2>
          <textarea v-model="form.comments" class="input h-32" placeholder="Enter any additional comments or observations..."></textarea>
        </section>

        <!-- 10. Grader -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">10. Grader</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Name</label>
              <input type="text" v-model="form.grader_name" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.grader_date" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Signature</label>
              <input type="text" v-model="form.grader_signature" class="input" />
            </div>
          </div>
        </section>

        <!-- 11. Analytical Equipment -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">11. Analytical Equipment's Used Technical Interpretation of the Test Result</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Name</label>
              <input type="text" v-model="form.analytical_name" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.analytical_date" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Signature</label>
              <input type="text" v-model="form.analytical_signature" class="input" />
            </div>
          </div>
        </section>

        <!-- 12. Retaining Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">12. Retaining Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-gray-700">Place</label>
              <input type="text" v-model="form.retaining_place" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Retained by</label>
              <input type="text" v-model="form.retaining_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.retaining_date" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Signature</label>
              <input type="text" v-model="form.retaining_signature" class="input" />
            </div>
          </div>
        </section>

        <!-- 13. Reporting Information -->
        <section class="pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">13. Reporting Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Report Done?</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.report_done" value="Yes" />
                  Yes
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.report_done" value="No" />
                  No
                </label>
              </div>
            </div>
            <div>
              <label class="block text-gray-700 font-semibold mb-2">Label Done?</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.label_done" value="Yes" />
                  Yes
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.label_done" value="No" />
                  No
                </label>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div>
              <label class="block text-gray-700">Report done by</label>
              <input type="text" v-model="form.report_done_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Date</label>
              <input type="date" v-model="form.report_date" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Checked by</label>
              <input type="text" v-model="form.checked_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Report # DR</label>
              <input type="text" v-model="form.report_number" class="input" />
            </div>
          </div>
        </section>

        <!-- Submit Buttons -->
        <div class="flex justify-between items-center pt-6 border-t">
          <Link 
            :href="$route('dashboard.artifacts')" 
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </Link>
          
          <div class="flex space-x-4">
            <button 
              type="button"
              @click="saveDraft"
              :disabled="loading"
              class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 disabled:opacity-50"
            >
              <span v-if="loading">Saving...</span>
              <span v-else>Save as Draft</span>
            </button>
            <button 
              type="submit" 
              :disabled="loading"
              class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
            >
              <span v-if="loading">{{ isEditing ? 'Updating...' : 'Saving...' }}</span>
              <span v-else>{{ isEditing ? 'Update Evaluation' : 'Complete Evaluation' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'

export default {
  components: { DashboardLayout, Link },
  props: {
    artifact: Object,
    existingEvaluation: Object,
    isEditing: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const loading = ref(false)
    const today = new Date().toISOString().split('T')[0]
    const { auth } = usePage().props

    // Safely access artifact data
    const artifact = computed(() => props.artifact || {})
    
    // Debug logging
    console.log('EvaluateDiamond props:', {
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

    const form = useForm({
      // Job Information
      test_date: formatDate(props.existingEvaluation?.test_date),
      test_location: props.existingEvaluation?.test_location || '',
      item_product_id: props.existingEvaluation?.item_product_id || artifact.value?.artifact_code || '',
      receiving_record: props.existingEvaluation?.receiving_record || '',
      prepared_by: props.existingEvaluation?.prepared_by || auth?.user?.name || '',
      approved_by: props.existingEvaluation?.approved_by || '',

      // Stone Information
      weight: props.existingEvaluation?.weight || artifact.value?.weight || '',
      shape: props.existingEvaluation?.shape || '',
      laser_inscription: props.existingEvaluation?.laser_inscription || 'No',

      // Lab-Grown Diamond Screen
      hpht_screen: props.existingEvaluation?.hpht_screen || '',
      cvd_check: props.existingEvaluation?.cvd_check || '',

      // Proportion Grade
      diameter: props.existingEvaluation?.diameter || '',
      total_depth: props.existingEvaluation?.total_depth || '',
      table: props.existingEvaluation?.table || '',
      star_facet: props.existingEvaluation?.star_facet || '',
      crown_angle: props.existingEvaluation?.crown_angle || '',
      crown_height: props.existingEvaluation?.crown_height || '',
      girdle_thickness_min: props.existingEvaluation?.girdle_thickness_min || '',
      girdle_thickness_max: props.existingEvaluation?.girdle_thickness_max || '',
      pavilion_depth: props.existingEvaluation?.pavilion_depth || '',
      pavilion_angle: props.existingEvaluation?.pavilion_angle || '',
      lower_girdle: props.existingEvaluation?.lower_girdle || '',
      culet_size: props.existingEvaluation?.culet_size || '',
      girdle_condition: props.existingEvaluation?.girdle_condition || '',
      culet_condition: props.existingEvaluation?.culet_condition || '',

      // Grade Information
      polish: props.existingEvaluation?.polish || '',
      symmetry: props.existingEvaluation?.symmetry || '',
      cut: props.existingEvaluation?.cut || '',

      // Visual Inspection
      clarity: props.existingEvaluation?.clarity || '',
      colour: props.existingEvaluation?.colour || '',

      // Fluorescence
      fluorescence_strength: props.existingEvaluation?.fluorescence_strength || '',
      fluorescence_colour: props.existingEvaluation?.fluorescence_colour || '',

      // Result
      result: props.existingEvaluation?.result || '',
      stone_type: props.existingEvaluation?.stone_type || '',

      // Comments
      comments: props.existingEvaluation?.comments || '',

      // Grader
      grader_name: props.existingEvaluation?.grader_name || '',
      grader_date: formatDate(props.existingEvaluation?.grader_date),
      grader_signature: props.existingEvaluation?.grader_signature || '',

      // Analytical Equipment
      analytical_name: props.existingEvaluation?.analytical_name || '',
      analytical_date: formatDate(props.existingEvaluation?.analytical_date),
      analytical_signature: props.existingEvaluation?.analytical_signature || '',

      // Retaining Information
      retaining_place: props.existingEvaluation?.retaining_place || '',
      retaining_by: props.existingEvaluation?.retaining_by || '',
      retaining_date: formatDate(props.existingEvaluation?.retaining_date),
      retaining_signature: props.existingEvaluation?.retaining_signature || '',

      // Reporting Information
      report_done: props.existingEvaluation?.report_done || '',
      label_done: props.existingEvaluation?.label_done || '',
      report_done_by: props.existingEvaluation?.report_done_by || '',
      report_date: formatDate(props.existingEvaluation?.report_date),
      checked_by: props.existingEvaluation?.checked_by || '',
      report_number: props.existingEvaluation?.report_number || '',
      
      // Status
      status: props.existingEvaluation?.status || 'draft',
    })

    // Watch for changes in existingEvaluation and update form
    watch(() => props.existingEvaluation, (newEvaluation) => {
      if (newEvaluation) {
        console.log('Updating form with new evaluation data:', newEvaluation);
        form.test_date = formatDate(newEvaluation.test_date);
        form.grader_date = formatDate(newEvaluation.grader_date);
        form.analytical_date = formatDate(newEvaluation.analytical_date);
        form.retaining_date = formatDate(newEvaluation.retaining_date);
        form.report_date = formatDate(newEvaluation.report_date);
        // Update other fields as needed
      }
    }, { immediate: true, deep: true });

    // Debug form values after initialization
    onMounted(() => {
      console.log('Form initialized with values:', {
        test_date: form.test_date,
        grader_date: form.grader_date,
        analytical_date: form.analytical_date,
        retaining_date: form.retaining_date,
        report_date: form.report_date,
      });
    });

    const gradeOptions = ['Excellent', 'Very Good', 'Good', 'Fair', 'Poor']
    const fluorescenceStrengths = ['V. Str.', 'Str.', 'Med.', 'Faint', 'None']
    const resultOptions = ['Pass', 'Fail', 'Reject']
    const stoneTypeOptions = ['Natural', 'Synthetic', 'Imitation', 'Treated']

    const submitEvaluation = () => {
      if (!artifact.value?.id) {
        alert('Error: Artifact data not available')
        return
      }
      
      loading.value = true
      // إضافة حقل status للتقييم المكتمل
      form.status = 'completed'
      
      if (props.isEditing && props.existingEvaluation) {
        // Update existing evaluation
        form.put(`/diamond-evaluations/${props.existingEvaluation.id}`, {
          onSuccess: (page) => {
            loading.value = false
            alert('تم تحديث التقييم بنجاح!')
          },
          onError: (errors) => {
            loading.value = false
            console.error('Evaluation update errors:', errors)
            if (errors.error) {
              alert(errors.error)
            } else {
              alert('حدث خطأ أثناء تحديث التقييم. يرجى المحاولة مرة أخرى.')
            }
          }
        })
      } else {
        // Create new evaluation
        form.post(`/dashboard/artifacts/${artifact.value.id}/evaluate`, {
          onSuccess: (page) => {
            loading.value = false
            alert('تم حفظ التقييم بنجاح!')
          },
          onError: (errors) => {
            loading.value = false
            console.error('Evaluation submission errors:', errors)
            if (errors.error) {
              alert(errors.error)
            } else {
              alert('حدث خطأ أثناء حفظ التقييم. يرجى المحاولة مرة أخرى.')
            }
          }
        })
      }
    }

    const saveDraft = () => {
      if (!artifact.value?.id) {
        alert('Error: Artifact data not available')
        return
      }
      
      loading.value = true
      // إضافة حقل status للمسودة
      form.status = 'draft'
      
      form.post(`/dashboard/artifacts/${artifact.value.id}/evaluate`, {
        onSuccess: (page) => {
          loading.value = false
          alert('تم حفظ المسودة بنجاح!')
        },
        onError: (errors) => {
          loading.value = false
          console.error('Draft save errors:', errors)
          if (errors.error) {
            alert(errors.error)
          } else {
            alert('حدث خطأ أثناء حفظ المسودة. يرجى المحاولة مرة أخرى.')
          }
        }
      })
    }

    return {
      artifact,
      form,
      loading,
      today,
      gradeOptions,
      fluorescenceStrengths,
      resultOptions,
      stoneTypeOptions,
      submitEvaluation,
      saveDraft
    }
  }
}
</script>

<style scoped>
.input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500;
}
</style> 