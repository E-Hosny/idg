<template>
  <DashboardLayout :page-title="isEditing ? 'Edit Jewellery Evaluation' : 'Jewellery Evaluation'">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-6xl mx-auto mt-8 border border-green-700">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-green-800 mb-2">
          {{ isEditing ? 'Edit Jewellery Evaluation' : 'Jewellery Worksheet' }}
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
              <label class="block text-gray-700">Item/Product ID #</label>
              <input type="text" v-model="form.item_product_id" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Receiving Record #</label>
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

        <!-- 2. Product Information -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">2. Product Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700">Product #</label>
              <input type="text" v-model="form.product_number" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Style #</label>
              <input type="text" v-model="form.style_number" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Ref. #</label>
              <input type="text" v-model="form.ref_number" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Gross Wt. (gm)</label>
              <input type="number" v-model="form.gross_weight" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Net Wt. (gm)</label>
              <input type="number" v-model="form.net_weight" class="input" step="0.01" min="0" />
            </div>
          </div>

          <!-- Product Type -->
          <div class="mt-4">
            <label class="block text-gray-700 font-semibold mb-2">Product Type:</label>
            <div class="flex flex-wrap gap-4">
              <label v-for="product in productTypes" :key="product" class="flex items-center gap-2">
                <input type="checkbox" v-model="form.product_types" :value="product" />
                {{ product }}
              </label>
            </div>
          </div>

          <!-- Metal Type -->
          <div class="mt-4">
            <label class="block text-gray-700 font-semibold mb-2">Metal:</label>
            <div class="flex flex-wrap gap-4">
              <label v-for="metal in metalTypes" :key="metal.value" class="flex items-center gap-2">
                <input type="checkbox" v-model="form.metal_types" :value="metal.value" />
                {{ metal.label }}
              </label>
            </div>
          </div>

          <!-- Stamp -->
          <div class="mt-4">
            <label class="block text-gray-700 font-semibold mb-2">Stamp:</label>
            <div class="flex flex-wrap gap-4">
              <label v-for="stamp in stampOptions" :key="stamp" class="flex items-center gap-2">
                <input type="checkbox" v-model="form.stamps" :value="stamp" />
                {{ stamp }}
              </label>
            </div>
          </div>
        </section>

        <!-- 3. Lab-Grown Diamond Screen -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">3. Lab-Grown Diamond Screen</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-700 mb-2">A. HPHT Screen</h3>
              <div class="flex gap-4 mb-2">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.hpht_screen" value="Natural" />
                  Natural
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.hpht_screen" value="Lab-Grown" />
                  Lab-Grown
                </label>
              </div>
              <div>
                <label class="block text-gray-700">HPHT Diamond Pcs.</label>
                <input type="number" v-model="form.hpht_diamond_pcs" class="input" min="0" />
              </div>
            </div>
            <div>
              <h3 class="font-semibold text-gray-700 mb-2">B. CVD Check</h3>
              <div class="flex gap-4 mb-2">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.cvd_check" value="Natural" />
                  Natural
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.cvd_check" value="Lab-Grown" />
                  Lab-Grown
                </label>
              </div>
              <div>
                <label class="block text-gray-700">CVD Diamond Pcs.</label>
                <input type="number" v-model="form.cvd_diamond_pcs" class="input" min="0" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-gray-700 font-semibold mb-2">Need to be Unmount?</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2">
                <input type="radio" v-model="form.need_unmount" value="Yes" />
                Yes
              </label>
              <label class="flex items-center gap-2">
                <input type="radio" v-model="form.need_unmount" value="No" />
                No
              </label>
            </div>
            <div v-if="form.need_unmount === 'Yes'" class="mt-2">
              <label class="block text-gray-700">Reason:</label>
              <input type="text" v-model="form.unmount_reason" class="input" />
            </div>
          </div>
        </section>

        <!-- 4. X-Ray Fluorescence (XRF) Analyzer -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">4. X-Ray Fluorescence (XRF) Analyzer</h2>
          <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border border-gray-300 px-4 py-2">#</th>
                  <th class="border border-gray-300 px-4 py-2" colspan="2">Au</th>
                  <th class="border border-gray-300 px-4 py-2" colspan="2">Ag</th>
                  <th class="border border-gray-300 px-4 py-2" colspan="2">Cu</th>
                  <th class="border border-gray-300 px-4 py-2" colspan="2">Pt</th>
                </tr>
                <tr class="bg-gray-50">
                  <th class="border border-gray-300 px-4 py-2"></th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">%</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">ppm</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">%</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">ppm</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">%</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">ppm</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">%</th>
                  <th class="border border-gray-300 px-4 py-2 text-sm">ppm</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="i in 5" :key="i">
                  <td class="border border-gray-300 px-4 py-2 text-center">{{ i }}</td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].au_percent" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].au_ppm" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].ag_percent" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].ag_ppm" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].cu_percent" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].cu_ppm" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].pt_percent" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                  <td class="border border-gray-300 px-2 py-1">
                    <input type="number" v-model="form.xrf_data[i-1].pt_ppm" class="w-full px-1 py-1 text-sm border-none focus:outline-none" step="0.01" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- 5. Diamond/s -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">5. Diamond/s</h2>
          
          <!-- A. Total/Side Stones -->
          <div class="mb-6">
            <h3 class="font-semibold text-gray-700 mb-3">A. Total/Side Stones</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.side_stones_weight_type" value="Estimated" />
                  Wt. Estimated
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.side_stones_weight_type" value="Given" />
                  Wt. Given
                </label>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-gray-700">Diamond Wt. (ct)</label>
                  <input type="number" v-model="form.side_stones_weight" class="input" step="0.01" min="0" />
                </div>
                <div>
                  <label class="block text-gray-700">Diamond Pcs.</label>
                  <input type="number" v-model="form.side_stones_pieces" class="input" min="0" />
                </div>
              </div>
            </div>

            <!-- Shape -->
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Shape:</label>
              <div class="flex flex-wrap gap-4">
                <label v-for="shape in diamondShapes" :key="shape" class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.side_stones_shapes" :value="shape" />
                  {{ shape }}
                </label>
              </div>
            </div>

            <!-- Colour -->
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Colour:</label>
              <div class="flex flex-wrap gap-4">
                <label v-for="colour in diamondColours" :key="colour" class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.side_stones_colours" :value="colour" />
                  {{ colour }}
                </label>
              </div>
            </div>

            <!-- Clarity -->
            <div class="mb-4">
              <label class="block text-gray-700 font-semibold mb-2">Clarity:</label>
              <div class="flex flex-wrap gap-4">
                <label v-for="clarity in diamondClarities" :key="clarity" class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.side_stones_clarities" :value="clarity" />
                  {{ clarity }}
                </label>
              </div>
            </div>
          </div>

          <!-- B. Centre Stone -->
          <div>
            <h3 class="font-semibold text-gray-700 mb-3">B. Centre Stone</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-gray-700">Weight (ct)</label>
                <input type="number" v-model="form.centre_stone_weight" class="input" step="0.01" min="0" />
              </div>
              <div>
                <label class="block text-gray-700">Shape</label>
                <select v-model="form.centre_stone_shape" class="input">
                  <option value="">Select Shape</option>
                  <option v-for="shape in diamondShapes" :key="shape" :value="shape">{{ shape }}</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700">Colour</label>
                <select v-model="form.centre_stone_colour" class="input">
                  <option value="">Select Colour</option>
                  <option v-for="colour in diamondColours" :key="colour" :value="colour">{{ colour }}</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700">Clarity</label>
                <select v-model="form.centre_stone_clarity" class="input">
                  <option value="">Select Clarity</option>
                  <option v-for="clarity in diamondClarities" :key="clarity" :value="clarity">{{ clarity }}</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <!-- 6. Coloured Gemstones -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">6. Coloured Gemstones</h2>
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
              <label class="block text-gray-700">Weight (ct)</label>
              <input type="number" v-model="form.coloured_stones_weight" class="input" step="0.01" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Shape</label>
              <input type="text" v-model="form.coloured_stones_shape" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">No. of Stones</label>
              <input type="number" v-model="form.coloured_stones_count" class="input" min="0" />
            </div>
            <div>
              <label class="block text-gray-700">Group</label>
              <input type="text" v-model="form.coloured_stones_group" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Species/Variety</label>
              <input type="text" v-model="form.coloured_stones_species" class="input" />
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-gray-700 font-semibold mb-2">Conclusion:</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2">
                <input type="radio" v-model="form.coloured_stones_conclusion" value="Natural" />
                Natural
              </label>
              <label class="flex items-center gap-2">
                <input type="radio" v-model="form.coloured_stones_conclusion" value="Synthetic" />
                Synthetic
              </label>
            </div>
          </div>
          <div class="mt-4">
            <label class="block text-gray-700">Note:</label>
            <input type="text" v-model="form.coloured_stones_note" class="input" />
          </div>
        </section>

        <!-- 7. Result -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">7. Result</h2>
          <div class="flex gap-4">
            <label v-for="result in resultOptions" :key="result" class="flex items-center gap-2">
              <input type="radio" v-model="form.result" :value="result" />
              {{ result }}
            </label>
          </div>
        </section>

        <!-- 8. Comments -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">8. Comments</h2>
          <textarea v-model="form.comments" class="input h-32" placeholder="Enter any additional comments or observations..."></textarea>
        </section>

        <!-- 9. Grader -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">9. Grader</h2>
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

        <!-- 10. Analytical Equipment -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">10. Analytical Equipment's Used Technical Interpretation of the Test Result</h2>
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

        <!-- 11. Product Photography -->
        <section class="border-b pb-6">
          <h2 class="text-lg font-semibold text-green-700 mb-4">11. Product Photography</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-700 mb-3">Image 1#</h3>
              <div class="space-y-2">
                <div>
                  <label class="block text-gray-700">Taken by:</label>
                  <input type="text" v-model="form.image1_taken_by" class="input" />
                </div>
                <div>
                  <label class="block text-gray-700">Date:</label>
                  <input type="date" v-model="form.image1_date" class="input" />
                </div>
                <div>
                  <label class="block text-gray-700">Signature:</label>
                  <input type="text" v-model="form.image1_signature" class="input" />
                </div>
              </div>
            </div>
            <div>
              <h3 class="font-semibold text-gray-700 mb-3">Image 2#</h3>
              <div class="space-y-2">
                <div>
                  <label class="block text-gray-700">Taken by:</label>
                  <input type="text" v-model="form.image2_taken_by" class="input" />
                </div>
                <div>
                  <label class="block text-gray-700">Date:</label>
                  <input type="date" v-model="form.image2_date" class="input" />
                </div>
                <div>
                  <label class="block text-gray-700">Signature:</label>
                  <input type="text" v-model="form.image2_signature" class="input" />
                </div>
              </div>
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
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
              <label class="block text-gray-700">Report done by</label>
              <input type="text" v-model="form.report_done_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Checked by</label>
              <input type="text" v-model="form.checked_by" class="input" />
            </div>
            <div>
              <label class="block text-gray-700">Report #</label>
              <input type="text" v-model="form.report_number" class="input" />
            </div>
          </div>
        </section>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t">
          <Link 
            :href="$route('dashboard.artifacts')" 
            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </Link>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
          >
            <span v-if="loading">{{ isEditing ? 'Updating...' : 'Saving...' }}</span>
            <span v-else>{{ isEditing ? 'Update Evaluation' : 'Save Evaluation' }}</span>
          </button>
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
    console.log('EvaluateJewellery props:', {
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

      // Product Information
      product_number: props.existingEvaluation?.product_number || '',
      style_number: props.existingEvaluation?.style_number || '',
      ref_number: props.existingEvaluation?.ref_number || '',
      gross_weight: props.existingEvaluation?.gross_weight || '',
      net_weight: props.existingEvaluation?.net_weight || '',
      product_types: props.existingEvaluation?.product_types || [],
      metal_types: props.existingEvaluation?.metal_types || [],
      stamps: props.existingEvaluation?.stamps || [],

      // Lab-Grown Diamond Screen
      hpht_screen: props.existingEvaluation?.hpht_screen || '',
      hpht_diamond_pcs: props.existingEvaluation?.hpht_diamond_pcs || '',
      cvd_check: props.existingEvaluation?.cvd_check || '',
      cvd_diamond_pcs: props.existingEvaluation?.cvd_diamond_pcs || '',
      need_unmount: props.existingEvaluation?.need_unmount || '',
      unmount_reason: props.existingEvaluation?.unmount_reason || '',

      // XRF Data
      xrf_data: props.existingEvaluation?.xrf_data || Array.from({ length: 5 }, () => ({
        au_percent: '',
        au_ppm: '',
        ag_percent: '',
        ag_ppm: '',
        cu_percent: '',
        cu_ppm: '',
        pt_percent: '',
        pt_ppm: ''
      })),

      // Diamond/s
      side_stones_weight_type: props.existingEvaluation?.side_stones_weight_type || '',
      side_stones_weight: props.existingEvaluation?.side_stones_weight || '',
      side_stones_pieces: props.existingEvaluation?.side_stones_pieces || '',
      side_stones_shapes: props.existingEvaluation?.side_stones_shapes || [],
      side_stones_colours: props.existingEvaluation?.side_stones_colours || [],
      side_stones_clarities: props.existingEvaluation?.side_stones_clarities || [],
      centre_stone_weight: props.existingEvaluation?.centre_stone_weight || '',
      centre_stone_shape: props.existingEvaluation?.centre_stone_shape || '',
      centre_stone_colour: props.existingEvaluation?.centre_stone_colour || '',
      centre_stone_clarity: props.existingEvaluation?.centre_stone_clarity || '',

      // Coloured Gemstones
      coloured_stones_weight: props.existingEvaluation?.coloured_stones_weight || '',
      coloured_stones_shape: props.existingEvaluation?.coloured_stones_shape || '',
      coloured_stones_count: props.existingEvaluation?.coloured_stones_count || '',
      coloured_stones_group: props.existingEvaluation?.coloured_stones_group || '',
      coloured_stones_species: props.existingEvaluation?.coloured_stones_species || '',
      coloured_stones_conclusion: props.existingEvaluation?.coloured_stones_conclusion || '',
      coloured_stones_note: props.existingEvaluation?.coloured_stones_note || '',

      // Result
      result: props.existingEvaluation?.result || '',

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

      // Product Photography
      image1_taken_by: props.existingEvaluation?.image1_taken_by || '',
      image1_date: formatDate(props.existingEvaluation?.image1_date),
      image1_signature: props.existingEvaluation?.image1_signature || '',
      image2_taken_by: props.existingEvaluation?.image2_taken_by || '',
      image2_date: formatDate(props.existingEvaluation?.image2_date),
      image2_signature: props.existingEvaluation?.image2_signature || '',

      // Retaining Information
      retaining_place: props.existingEvaluation?.retaining_place || '',
      retaining_by: props.existingEvaluation?.retaining_by || '',
      retaining_date: formatDate(props.existingEvaluation?.retaining_date),
      retaining_signature: props.existingEvaluation?.retaining_signature || '',

      // Reporting Information
      report_done: props.existingEvaluation?.report_done || '',
      label_done: props.existingEvaluation?.label_done || '',
      report_done_by: props.existingEvaluation?.report_done_by || '',
      checked_by: props.existingEvaluation?.checked_by || '',
      report_number: props.existingEvaluation?.report_number || '',

      // Metal Analysis
      metal_analysis: props.existingEvaluation?.metal_analysis || {
        au_percent: '',
        au_ppm: '',
        ag_percent: '',
        ag_ppm: '',
        pt_percent: '',
        pt_ppm: ''
      },
    })

    // Watch for changes in existingEvaluation and update form
    watch(() => props.existingEvaluation, (newEvaluation) => {
      if (newEvaluation) {
        console.log('Updating form with new evaluation data:', newEvaluation);
        form.test_date = formatDate(newEvaluation.test_date);
        form.grader_date = formatDate(newEvaluation.grader_date);
        form.analytical_date = formatDate(newEvaluation.analytical_date);
        form.image1_date = formatDate(newEvaluation.image1_date);
        form.image2_date = formatDate(newEvaluation.image2_date);
        form.retaining_date = formatDate(newEvaluation.retaining_date);
        // Update other fields as needed
      }
    }, { immediate: true, deep: true });

    // Debug form values after initialization
    onMounted(() => {
      console.log('Form initialized with values:', {
        test_date: form.test_date,
        grader_date: form.grader_date,
        analytical_date: form.analytical_date,
        image1_date: form.image1_date,
        image2_date: form.image2_date,
        retaining_date: form.retaining_date,
      });
    });

    const productTypes = ['Ring', 'Necklace', 'Pendant', 'Bracelet', 'Pair of Earring']
    const metalTypes = [
      { value: 'W', label: 'White Gold (W)' },
      { value: 'Y', label: 'Yellow Gold (Y)' },
      { value: 'P', label: 'Pink Gold (P)' },
      { value: 'PT', label: 'Platinum (PT)' },
      { value: 'SL', label: 'Silver (SL)' }
    ]
    const stampOptions = ['18K', '22K', '24K', '375', '585', '750', '916', '925', 'N/A']
    const diamondShapes = ['RBC', 'Princess', 'Baguette', 'T. Baguette', 'Emerald', 'Marquise', 'Pear', 'Oval']
    const diamondColours = ['D-E-F', 'E-F', 'F-G', 'G-H', 'H-I', 'I-J', 'J-K']
    const diamondClarities = ['IF-VVS', 'VVS', 'VVS-VS', 'VS', 'VS-SI', 'SI', 'SI-I']
    const resultOptions = ['Reject', 'Hold', 'Fail', 'Pass']

    const submitEvaluation = () => {
      if (!artifact.value?.id) {
        alert('Error: Artifact data not available')
        return
      }
      
      loading.value = true
      
      if (props.isEditing && props.existingEvaluation) {
        // Update existing evaluation
        form.put(`/artifacts/${artifact.value.id}/update-evaluation`, {
          onSuccess: () => {
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
          onSuccess: () => {
            loading.value = false
            alert('تم حفظ التقييم بنجاح!')
          },
          onError: (errors) => {
            loading.value = false
            console.error('Evaluation save errors:', errors)
            if (errors.error) {
              alert(errors.error)
            } else {
              alert('حدث خطأ أثناء حفظ التقييم. يرجى المحاولة مرة أخرى.')
            }
          }
        })
      }
    }

    return {
      artifact,
      form,
      loading,
      today,
      productTypes,
      metalTypes,
      stampOptions,
      diamondShapes,
      diamondColours,
      diamondClarities,
      resultOptions,
      submitEvaluation
    }
  }
}
</script>

<style scoped>
.input {
  @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500;
}
</style> 