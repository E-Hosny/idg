export const ARTIFACT_TYPE_PRECIOUS_METALS = 'Precious & Non-Precious Metals'

export function getTypeOptions(locale = 'en') {
  const ar = locale === 'ar'
  return [
    { value: 'Colored Gemstones', label: ar ? 'أحجار كريمة ملونة' : 'Colored Gemstones' },
    { value: 'Other Colored Gemstones', label: ar ? 'أحجار كريمة ملونة أخرى' : 'Other Colored Gemstones' },
    { value: 'Colorless Diamonds', label: ar ? 'ألماس عديم اللون' : 'Colorless Diamonds' },
    { value: 'Jewellery', label: ar ? 'مجوهرات' : 'Jewellery' },
    {
      value: ARTIFACT_TYPE_PRECIOUS_METALS,
      label: ar ? 'معادن ثمينة وغير ثمينة' : 'Precious & Non-Precious Metals',
    },
  ]
}

/** Preset subtypes only; manual text uses free-form input via __manual__ in the UI. */
export function getPreciousMetalsSubtypeOptions(locale = 'en') {
  const ar = locale === 'ar'
  return [
    { value: 'Jewelry', label: ar ? 'مجوهرات' : 'Jewelry' },
    { value: 'Coins', label: ar ? 'عملات' : 'Coins' },
    { value: 'Bars', label: ar ? 'سبائك' : 'Bars' },
  ]
}

export const preciousMetalsSubtypeValues = ['Jewelry', 'Coins', 'Bars']

export function usesPreciousMetalsSubtypeList(type) {
  return type === ARTIFACT_TYPE_PRECIOUS_METALS
}

export function getServiceOptions(type, locale = 'en') {
  const ar = locale === 'ar'
  const commonGem = [
    { value: 'Regular - ID Report', label: ar ? 'عادي - تقرير هوية' : 'Regular - ID Report' },
    { value: 'Regular - ID + Origin', label: ar ? 'عادي - هوية + أصل' : 'Regular - ID + Origin' },
    { value: 'Mini Card Report - ID Report', label: ar ? 'تقرير بطاقة مصغرة - تقرير هوية' : 'Mini Card Report - ID Report' },
    { value: 'Mini Card Report - ID + Origin', label: ar ? 'تقرير بطاقة مصغرة - هوية + أصل' : 'Mini Card Report - ID + Origin' },
  ]
  const map = {
    'Colored Gemstones': commonGem,
    'Other Colored Gemstones': commonGem.filter((s) => !s.value.includes('ID + Origin')),
    'Colorless Diamonds': [
      { value: 'Regular - Diamond Grading Report', label: ar ? 'عادي - تقرير تصنيف الألماس' : 'Regular - Diamond Grading Report' },
      { value: 'Mini Card Report - Mini Report', label: ar ? 'تقرير بطاقة مصغرة - تقرير مصغر' : 'Mini Card Report - Mini Report' },
    ],
    Jewellery: [
      { value: 'Regular - Jewellery Report', label: ar ? 'عادي - تقرير مجوهرات' : 'Regular - Jewellery Report' },
      { value: 'Mini Card Report - Mini Jewellery Report', label: ar ? 'تقرير بطاقة مصغرة - تقرير مجوهرات مصغر' : 'Mini Card Report - Mini Jewellery Report' },
    ],
    [ARTIFACT_TYPE_PRECIOUS_METALS]: [
      { value: 'Regular', label: ar ? 'عادي' : 'Regular' },
    ],
  }
  return map[type] || []
}
