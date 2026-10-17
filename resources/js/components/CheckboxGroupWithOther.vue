<script setup>
import { computed, ref, watch } from 'vue'

const OTHER = '__other__'

const props = defineProps({
  /** @type {import('vue').PropType<string[]>} */
  modelValue: { type: Array, default: () => [] },
  options: { type: Array, required: true },
  locale: { type: String, default: 'en' },
  otherLabel: { type: String, default: '' },
  otherPlaceholder: { type: String, default: '' },
  inputClass: { type: String, default: 'input' },
  /** Values that mean "other" sentinel in stored arrays (e.g. OTHER) */
  otherSentinels: { type: Array, default: () => ['OTHER', '__other__'] },
})

const emit = defineEmits(['update:modelValue'])

const normalized = computed(() =>
  (props.options || [])
    .map((o) =>
      typeof o === 'object' && o !== null
        ? { value: String(o.value), label: String(o.label ?? o.value) }
        : { value: String(o), label: String(o) }
    )
    .filter((o) => !props.otherSentinels.map(String).includes(o.value) && o.label !== '')
)

const knownLower = computed(() => new Set(normalized.value.map((o) => o.value.toLowerCase())))
const sentinelLower = computed(() => new Set(props.otherSentinels.map((s) => String(s).toLowerCase())))

const selectedKnown = computed(() =>
  (props.modelValue || []).filter(
    (v) => knownLower.value.has(String(v).toLowerCase())
  )
)

const customValues = computed(() =>
  (props.modelValue || []).filter((v) => {
    const s = String(v)
    if (!s || sentinelLower.value.has(s.toLowerCase())) {
      return false
    }
    return !knownLower.value.has(s.toLowerCase())
  })
)

const otherChecked = ref(false)
const otherText = ref('')

function syncFromModel(list) {
  const customs = (list || []).filter((v) => {
    const s = String(v)
    if (!s || sentinelLower.value.has(s.toLowerCase())) {
      return false
    }
    return !knownLower.value.has(s.toLowerCase())
  })
  const hasSentinel = (list || []).some((v) =>
    sentinelLower.value.has(String(v).toLowerCase())
  )
  if (customs.length > 0) {
    otherChecked.value = true
    otherText.value = customs.join(', ')
  } else if (hasSentinel) {
    otherChecked.value = true
    if (!otherText.value) {
      otherText.value = ''
    }
  } else if (!otherChecked.value) {
    otherText.value = ''
  }
}

watch(() => props.modelValue, syncFromModel, { immediate: true, deep: true })

function emitCombined(known, checked, text) {
  const next = [...known]
  const trimmed = (text || '').trim()
  if (checked) {
    if (trimmed) {
      next.push(trimmed)
    } else {
      next.push(OTHER)
    }
  }
  emit('update:modelValue', next)
}

function toggleKnown(value, checked) {
  const set = new Set(selectedKnown.value.map(String))
  if (checked) {
    set.add(String(value))
  } else {
    set.delete(String(value))
  }
  emitCombined([...set], otherChecked.value, otherText.value)
}

function onOtherToggle(checked) {
  otherChecked.value = checked
  if (!checked) {
    otherText.value = ''
  }
  emitCombined(selectedKnown.value, checked, otherText.value)
}

function onOtherText(v) {
  otherText.value = v
  otherChecked.value = true
  emitCombined(selectedKnown.value, true, v)
}

const resolvedOtherLabel = computed(
  () => props.otherLabel || (props.locale === 'ar' ? 'أخرى' : 'Other')
)

const resolvedOtherPlaceholder = computed(
  () => props.otherPlaceholder || (props.locale === 'ar' ? 'أدخل قيمة مخصصة…' : 'Enter custom value…')
)
</script>

<template>
  <div class="space-y-2">
    <div class="flex flex-wrap gap-4">
      <label
        v-for="opt in normalized"
        :key="opt.value"
        class="flex items-center gap-2"
      >
        <input
          type="checkbox"
          :value="opt.value"
          :checked="selectedKnown.map(String).includes(opt.value)"
          @change="toggleKnown(opt.value, $event.target.checked)"
        />
        {{ opt.label }}
      </label>
      <label class="flex items-center gap-2">
        <input
          type="checkbox"
          :checked="otherChecked"
          @change="onOtherToggle($event.target.checked)"
        />
        {{ resolvedOtherLabel }}
      </label>
    </div>
    <input
      v-if="otherChecked"
      type="text"
      :value="otherText"
      :class="inputClass"
      :placeholder="resolvedOtherPlaceholder"
      @input="onOtherText($event.target.value)"
    />
  </div>
</template>
