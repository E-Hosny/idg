<template>
  <DashboardLayout :pageTitle="__('Categories')">
    <div class="max-w-5xl mx-auto">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('Categories') }}</h2>
      <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-600 mb-4">{{ __('This is a placeholder for the categories list. You can customize this page to show and manage categories.') }}</p>
        <!-- يمكنك لاحقًا إضافة جدول الفئات هنا -->
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
        'Categories': 'الفئات',
        'This is a placeholder for the categories list. You can customize this page to show and manage categories.': 'هذه صفحة مبدئية لعرض الفئات. يمكنك تخصيصها لاحقًا.'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    }
  }
}
</script> 