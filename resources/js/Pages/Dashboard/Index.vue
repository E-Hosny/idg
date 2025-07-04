<template>
  <DashboardLayout :pageTitle="__('Dashboard')">
    <div class="space-y-6" :class="{ 'font-arabic': $page.props.locale === 'ar' }">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Artifacts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-gem text-2xl text-green-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Total Artifacts') }}
                  </dt>
                  <dd>
                    <div class="text-lg font-medium text-gray-900">
                      {{ stats.total_artifacts }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('dashboard.artifacts')" class="font-medium text-green-700 hover:text-green-900">
                {{ __('View all') }}
              </Link>
            </div>
          </div>
        </div>

        <!-- Pending Evaluations -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Pending Evaluations') }}
                  </dt>
                  <dd>
                    <div class="text-lg font-medium text-gray-900">
                      {{ stats.pending_evaluations }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('dashboard.evaluations')" class="font-medium text-yellow-700 hover:text-yellow-900">
                {{ __('Review pending') }}
              </Link>
            </div>
          </div>
        </div>

        <!-- Under Evaluation -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-clipboard-check text-2xl text-blue-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Under Evaluation') }}
                  </dt>
                  <dd>
                    <div class="text-lg font-medium text-gray-900">
                      {{ stats.under_evaluation }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('dashboard.evaluations')" class="font-medium text-blue-700 hover:text-blue-900">
                {{ __('Track progress') }}
              </Link>
            </div>
          </div>
        </div>

        <!-- Certified Artifacts -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <i class="fas fa-certificate text-2xl text-purple-600"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ __('Certified Artifacts') }}
                  </dt>
                  <dd>
                    <div class="text-lg font-medium text-gray-900">
                      {{ stats.certified_artifacts }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
              <Link :href="route('dashboard.artifacts')" class="font-medium text-purple-700 hover:text-purple-900">
                {{ __('View certified') }}
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Welcome Message -->
      <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <img class="h-16 w-16 rounded-full border-2 border-white" src="/images/idg_logo.jpg" alt="IDG">
          </div>
          <div class="ml-6">
            <h2 class="text-2xl font-bold">
              {{ __('Welcome to IDG Artifacts Dashboard') }}
            </h2>
            <p class="text-green-100 mt-1">
              {{ __('Manage and evaluate precious artifacts with our comprehensive system') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ __('Quick Actions') }}
          </h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
              <i class="fas fa-plus mr-2 text-green-600"></i>
              {{ __('Add New Artifact') }}
            </button>
            <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
              <i class="fas fa-search mr-2 text-blue-600"></i>
              {{ __('Search Artifacts') }}
            </button>
            <Link :href="$route('dashboard.categories')" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
              <i class="fas fa-tags mr-2 text-purple-600"></i>
              {{ __('Manage Categories') }}
            </Link>
            <Link :href="$route('dashboard.analytics')" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
              <i class="fas fa-chart-line mr-2 text-orange-600"></i>
              {{ __('View Analytics') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'

export default {
  components: {
    DashboardLayout,
    Link
  },
  props: {
    stats: Object,
    recentArtifacts: Array,
    myPendingEvaluations: Array,
    evaluationStats: Array,
    categoryStats: Array,
  },
  mounted() {
    this.initEvaluationChart()
  },
  methods: {
    initEvaluationChart() {
      if (!this.$refs.evaluationChart) return;
      const ctx = this.$refs.evaluationChart.getContext('2d');
      if (!ctx) return;
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: this.evaluationStats ? this.evaluationStats.map(stat => stat.month) : [],
          datasets: [{
            label: this.__('Completed Evaluations'),
            data: this.evaluationStats ? this.evaluationStats.map(stat => stat.completed) : [],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          }
        }
      });
    },
    statusClasses(status) {
      const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'under_evaluation': 'bg-blue-100 text-blue-800',
        'evaluated': 'bg-green-100 text-green-800',
        'certified': 'bg-purple-100 text-purple-800',
        'rejected': 'bg-red-100 text-red-800',
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString(this.$page.props.locale === 'ar' ? 'ar-EG' : 'en-US')
    },
    __(key, replace = {}) {
      const translations = {
        en: {
          'Dashboard': 'Dashboard',
          'Total Artifacts': 'Total Artifacts',
          'Pending Evaluations': 'Pending Evaluations',
          'Under Evaluation': 'Under Evaluation',
          'Certified Artifacts': 'Certified Artifacts',
          'Welcome to IDG Artifacts Dashboard': 'Welcome to IDG Artifacts Dashboard',
          'Manage and evaluate precious artifacts with our comprehensive system': 'Manage and evaluate precious artifacts with our comprehensive system',
          'Quick Actions': 'Quick Actions',
          'Add New Artifact': 'Add New Artifact',
          'Search Artifacts': 'Search Artifacts',
          'Manage Categories': 'Manage Categories',
          'View Analytics': 'View Analytics'
        },
        ar: {
          'Dashboard': 'لوحة التحكم',
          'Total Artifacts': 'إجمالي القطع الأثرية',
          'Pending Evaluations': 'التقييمات المعلقة',
          'Under Evaluation': 'قيد التقييم',
          'Certified Artifacts': 'القطع المعتمدة',
          'Welcome to IDG Artifacts Dashboard': 'مرحباً بكم في لوحة تحكم القطع الأثرية',
          'Manage and evaluate precious artifacts with our comprehensive system': 'إدارة وتقييم القطع الأثرية الثمينة من خلال نظامنا الشامل',
          'Quick Actions': 'إجراءات سريعة',
          'Add New Artifact': 'إضافة قطعة أثرية جديدة',
          'Search Artifacts': 'البحث في القطع الأثرية',
          'Manage Categories': 'إدارة الفئات',
          'View Analytics': 'عرض التحليلات'
        }
      }

      const locale = this.$page.props.locale || 'en'
      return translations[locale]?.[key] || key
    }
  }
}
</script>

<style scoped>
.font-arabic {
  font-family: 'Tajawal', sans-serif;
}
</style> 