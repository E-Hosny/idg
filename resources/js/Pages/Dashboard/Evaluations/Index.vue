<template>
  <DashboardLayout :pageTitle="__('Evaluations')">
    <div class="max-w-5xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Evaluations') }}</h2>
      <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-600 mb-4">{{ __('This is a placeholder for the evaluations list. You can customize this page to show all evaluations in the system.') }}</p>
        <!-- يمكنك لاحقًا إضافة جدول التقييمات هنا -->
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  components: { DashboardLayout },
  methods: {
    __(key) {
      const t = {
        'Evaluations': 'التقييمات',
        'This is a placeholder for the evaluations list. You can customize this page to show all evaluations in the system.': 'هذه صفحة مبدئية لعرض جميع التقييمات. يمكنك تخصيصها لاحقًا.'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    }
  }
}
</script> 