<template>
  <div>
    <p class="text-sm font-semibold text-gray-800 mb-2">{{ label }} <span class="text-red-600">*</span></p>
    <div class="flex flex-wrap gap-4">
      <label v-for="n in 5" :key="n" class="flex flex-col items-center gap-1 text-sm text-gray-700">
        <span>{{ n }}</span>
        <input type="radio" :name="name" :value="n" :checked="modelValue === n" @change="$emit('update:modelValue', n)" />
      </label>
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [Number, String, null], default: null },
  label: { type: String, required: true },
  name: { type: String, required: true },
  error: { type: String, default: '' },
})
defineEmits(['update:modelValue'])
</script>
