<script setup>
import { computed, ref, watch } from 'vue'

const OTHER = '__other__'

const props = defineProps({
  modelValue: { type: [String, Number, null], default: '' },
  options: { type: Array, required: true },
  name: { type: String, default: '' },
  locale: { type: String, default: 'en' },
  otherLabel: { type: String, default: '' },
  otherPlaceholder: { type: String, default: '' },
  inputClass: { type: String, default: 'input' },
})

const emit = defineEmits(['update:modelValue'])

const normalized = computed(() =>
  (props.options || []).map((o) =>
    typeof o === 'object' && o !== null
      ? { value: String(o.value), label: String(o.label ?? o.value) }
      : { value: String(o), label: String(o) }
  )
)

const knownLower = computed(() => new Set(normalized.value.map((o) => o.value.toLowerCase())))

const otherMode = ref(false)

function syncFromValue(v) {
  const s = v == null ? '' : String(v).trim()
  if (!s) {
    return
  }
  otherMode.value = !knownLower.value.has(s.toLowerCase())
}

watch(() => props.modelValue, syncFromValue, { immediate: true })

const radioModel = computed({
  get() {
    if (otherMode.value) {
      return OTHER
    }
    const s = props.modelValue == null ? '' : String(props.modelValue).trim()
    if (!s) {
      return ''
    }
    const match = normalized.value.find((o) => o.value.toLowerCase() === s.toLowerCase())
    return match ? match.value : ''
  },
  set(val) {
    if (val === OTHER) {
      otherMode.value = true
      const cur = props.modelValue == null ? '' : String(props.modelValue).trim()
      if (cur && !knownLower.value.has(cur.toLowerCase())) {
        return
      }
      emit('update:modelValue', '')
      return
    }
    otherMode.value = false
    emit('update:modelValue', val || '')
  },
})

const textModel = computed({
  get: () => (props.modelValue == null ? '' : String(props.modelValue)),
  set: (v) => emit('update:modelValue', v),
})

const resolvedOtherLabel = computed(
  () => props.otherLabel || (props.locale === 'ar' ? 'أخرى' : 'Other')
)

const resolvedOtherPlaceholder = computed(
  () => props.otherPlaceholder || (props.locale === 'ar' ? 'أدخل قيمة مخصصة…' : 'Enter custom value…')
)
</script>

<template>
  <div class="space-y-2">
    <div class="flex flex-wrap items-center gap-4">
      <label
        v-for="opt in normalized"
        :key="opt.value"
        class="flex items-center gap-1"
      >
        <input
          type="radio"
          :name="name || undefined"
          :value="opt.value"
          v-model="radioModel"
        />
        {{ opt.label }}
      </label>
      <label class="flex items-center gap-1">
        <input
          type="radio"
          :name="name || undefined"
          :value="OTHER"
          v-model="radioModel"
        />
        {{ resolvedOtherLabel }}
      </label>
    </div>
    <input
      v-if="otherMode"
      type="text"
      v-model="textModel"
      :class="inputClass"
      :placeholder="resolvedOtherPlaceholder"
    />
  </div>
</template>
