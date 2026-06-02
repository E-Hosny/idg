export const XRF_ELEMENTS = [
  { symbol: 'Au', name: 'Gold' },
  { symbol: 'Cu', name: 'Copper' },
  { symbol: 'Ag', name: 'Silver' },
  { symbol: 'Sn', name: 'Tin' },
  { symbol: 'In', name: 'Indium' },
  { symbol: 'Cd', name: 'Cadmium' },
  { symbol: 'Pd', name: 'Palladium' },
  { symbol: 'Rh', name: 'Rhodium' },
  { symbol: 'Ru', name: 'Ruthenium' },
  { symbol: 'Pb', name: 'Lead' },
  { symbol: 'Pt', name: 'Platinum' },
  { symbol: 'Ir', name: 'Iridium' },
  { symbol: 'W', name: 'Tungsten' },
  { symbol: 'Ge', name: 'Germanium' },
  { symbol: 'Zn', name: 'Zinc' },
  { symbol: 'Ni', name: 'Nickel' },
  { symbol: 'Co', name: 'Cobalt' },
  { symbol: 'Fe', name: 'Iron' },
  { symbol: 'Mn', name: 'Manganese' },
  { symbol: 'Cr', name: 'Chromium' },
  { symbol: 'Ti', name: 'Titanium' },
]

export const XRF_READING_NUMBERS = [1, 2, 3, 4, 5]

export const METAL_PURITY_OPTIONS = {
  gold: ['18K', '21K', '22K', '24K', '916', '925', '585', 'N/A'],
  platinum: ['750', '875', '850', '900', '925', '950', '999', 'N/A'],
  silver: ['500', '720', '800', '830', '900', '925', '999', 'N/A'],
}

export const RESULT_FLAGS = ['Reject', 'Fail', 'Hold', 'Pass']

export function emptyXrfData() {
  const data = {}
  XRF_READING_NUMBERS.forEach((n) => {
    data[String(n)] = {}
    XRF_ELEMENTS.forEach((el) => {
      data[String(n)][el.symbol] = { percent: null, error: null }
    })
  })
  return data
}

export function emptyMetalPurities() {
  return {
    gold: { selected: [], other: null },
    platinum: { selected: [], other: null },
    silver: { selected: [], other: null },
  }
}

export function buildFormDefaults(artifact = null, existing = null) {
  const base = {
    test_date: null,
    test_location: null,
    item_product_id: artifact?.artifact_code || null,
    receiving_record: artifact?.testRequest?.receiving_record_no || null,
    product: null,
    product_number: null,
    ref_number: null,
    gross_weight: null,
    net_weight: null,
    metal_purities: emptyMetalPurities(),
    xrf_data: emptyXrfData(),
    result_flags: [],
    comments: null,
    grader_name: null,
    grader_date: null,
    grader_signature: null,
    analytical_interpretation: null,
    analytical_name: null,
    analytical_date: null,
    analytical_signature: null,
    image1_ref: null,
    image2_ref: null,
    image_taken_by: null,
    image_date: null,
    image_signature: null,
    retaining_place: null,
    retaining_by: null,
    retaining_date: null,
    retaining_signature: null,
    report_done: null,
    report_done_notes: null,
    label_done: null,
    label_done_notes: null,
    report_done_by: null,
    report_date: null,
    report_signature: null,
    report_number: null,
    report_number_pmr: null,
    checked_by: null,
    checked_date: null,
    checked_signature: null,
  }
  if (!existing) return base
  return {
    ...base,
    ...existing,
    metal_purities: existing.metal_purities || emptyMetalPurities(),
    xrf_data: existing.xrf_data || emptyXrfData(),
    result_flags: existing.result_flags || [],
  }
}
