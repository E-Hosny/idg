<template>
  <div class="flex min-h-screen bg-gray-100" :class="{ 'font-arabic': $page.props.locale === 'ar' }">
    <!-- Sidebar -->
    <div 
      class="hidden lg:flex lg:flex-shrink-0"
      :class="{ 'lg:order-2': $page.props.locale === 'ar' }"
    >
      <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-gradient-to-b from-green-800 to-green-900">
          <!-- Logo -->
          <div class="flex items-center flex-shrink-0 px-6 py-4">
            <img class="w-12 h-12 rounded-full" :src="'/images/idg_logo.jpg'" alt="IDG">
            <div :class="$page.props.locale === 'ar' ? 'mr-4' : 'ml-4'">
              <h1 class="text-xl font-bold text-white">IDG</h1>
              <p class="text-sm text-green-200">{{ __('Items Dashboard') }}</p>
            </div>
          </div>

          <!-- Navigation -->
          <nav class="mt-5 flex-1">
            <div class="space-y-1">
              <Link 
                :href="$route('dashboard')" 
                class="sidebar-link"
                :class="{ 'active': $page.component === 'Dashboard/Index' }"
              >
                <i class="fas fa-tachometer-alt w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Dashboard') }}</span>
              </Link>

              <Link 
                :href="$route('dashboard.artifacts')" 
                class="sidebar-link"
                :class="{ 'active': $page.component.startsWith('Dashboard/Artifacts') }"
              >
                <i class="fas fa-gem w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Items') }}</span>
              </Link>

              <Link 
                :href="$route('dashboard.evaluations')" 
                class="sidebar-link"
                :class="{ 'active': $page.component.startsWith('Dashboard/Evaluations') }"
              >
                <i class="fas fa-clipboard-check w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Evaluations') }}</span>
              </Link>

              <Link 
                :href="$route('dashboard.categories')" 
                class="sidebar-link"
                :class="{ 'active': $page.component.startsWith('Dashboard/Categories') }"
              >
                <i class="fas fa-tags w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Categories') }}</span>
              </Link>

              <Link 
                :href="$route('dashboard.analytics')" 
                class="sidebar-link"
                :class="{ 'active': $page.component === 'Dashboard/Analytics' }"
              >
                <i class="fas fa-chart-line w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Analytics') }}</span>
              </Link>

              <Link 
                :href="$route('reception.index')" 
                class="sidebar-link"
                :class="{ 'active': $page.component.startsWith('Reception') }"
              >
                <i class="fas fa-user-plus w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Reception') }}</span>
              </Link>

              <Link 
                :href="$route('dashboard.customers')" 
                class="sidebar-link"
                :class="{ 'active': $page.component.startsWith('Dashboard/Customers') && !$page.component.includes('Invoice') }"
              >
                <i class="fas fa-users w-5 h-5 ml-3"></i>
                <span class="ml-3">{{ __('Customers') }}</span>
              </Link>

              <Link 
                href="#"
                @click.prevent="showHelpTooltip"
                class="sidebar-link flex items-center justify-between"
                :class="{ 'active': $page.component.includes('Invoice') }"
              >
                <div class="flex items-center">
                  <i class="fas fa-file-alt w-5 h-5 ml-3"></i>
                  <span class="ml-3">{{ __('Invoices') }}</span>
                </div>
                <i class="fas fa-info-circle w-4 h-4 mr-3 text-green-300" title="View invoices for specific customers"></i>
              </Link>
            </div>
          </nav>

          <!-- User Menu -->
          <div class="flex-shrink-0 flex border-t border-green-700 p-4">
            <div class="flex items-center w-full">
              <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white text-sm"></i>
              </div>
              <div class="ml-3 min-w-0 flex-1">
                <p class="text-sm font-medium text-white truncate">
                  {{ $page.props.auth.user?.name }}
                </p>
                <p class="text-xs text-green-200 truncate">
                  {{ $page.props.auth.user?.email }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div 
      v-if="sidebarOpen" 
      class="fixed inset-0 z-40 lg:hidden"
      @click="sidebarOpen = false"
    >
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
      <!-- Mobile sidebar content would go here -->
    </div>

    <!-- Main content -->
    <div class="flex-1 overflow-y-auto" :class="{ 'lg:order-1': $page.props.locale === 'ar' }">
      <!-- Top navigation -->
      <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
        <!-- Mobile menu button -->
        <button 
          @click="sidebarOpen = !sidebarOpen"
          class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 lg:hidden"
        >
          <i class="fas fa-bars"></i>
        </button>

        <!-- Top nav content -->
        <div class="flex-1 px-4 flex justify-between items-center">
          <div class="flex-1">
            <h1 class="text-2xl font-semibold text-gray-900">
              <slot name="header">{{ pageTitle }}</slot>
            </h1>
          </div>

          <div class="ml-4 flex items-center md:ml-6 space-x-4">
            <!-- Language Switcher -->
            <div class="relative" ref="languageDropdown">
              <button 
                @click="languageDropdownOpen = !languageDropdownOpen"
                class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none"
              >
                <i class="fas fa-globe mr-2"></i>
                {{ $page.props.locale === 'ar' ? 'العربية' : 'English' }}
                <i class="fas fa-chevron-down ml-1"></i>
              </button>
              <div 
                v-if="languageDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <Link 
                  :href="$route('lang.switch', 'en')" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click.prevent="switchLang('en')"
                >
                  <i class="fas fa-flag mr-2"></i> English
                </Link>
                <Link 
                  :href="$route('lang.switch', 'ar')" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click.prevent="switchLang('ar')"
                >
                  <i class="fas fa-flag mr-2"></i> العربية
                </Link>
              </div>
            </div>

            <!-- User menu -->
            <div class="relative" ref="userDropdown">
              <button 
                @click="userDropdownOpen = !userDropdownOpen"
                class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none"
              >
                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                  <i class="fas fa-user text-white text-sm"></i>
                </div>
                <span class="ml-2">{{ $page.props.auth.user?.name }}</span>
                <i class="fas fa-chevron-down ml-1"></i>
              </button>
              <div 
                v-if="userDropdownOpen"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user mr-2"></i> {{ __('Profile') }}
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-cog mr-2"></i> {{ __('Settings') }}
                </a>
                <div class="border-t border-gray-100"></div>
                <Link 
                  :href="$route('logout')" 
                  method="post"
                  as="button"
                  type="button"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Page content -->
      <main class="flex-1 relative focus:outline-none overflow-y-auto">
        <div class="py-6 min-h-full">
          <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    }
  },
  data() {
    return {
      sidebarOpen: false,
      languageDropdownOpen: false,
      userDropdownOpen: false,
    }
  },
  mounted() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    handleClickOutside(event) {
      // Check if click is outside language dropdown
      if (this.languageDropdownOpen && !this.$refs.languageDropdown?.contains(event.target)) {
        this.languageDropdownOpen = false
      }
      // Check if click is outside user dropdown
      if (this.userDropdownOpen && !this.$refs.userDropdown?.contains(event.target)) {
        this.userDropdownOpen = false
      }
    },
    switchLang(locale) {
      this.$inertia.visit(this.$route('lang.switch', locale), {
        onSuccess: () => window.location.reload()
      });
    },
    
    showHelpTooltip() {
      alert(this.__('To view invoices, first go to Customers page and select a customer.'));
    },
    __(key, replace = {}) {
      // Simple translation function
      const translations = {
        en: {
          'Items Dashboard': 'Items Dashboard',
          'Dashboard': 'Dashboard',
          'Items': 'Items',
          'Evaluations': 'Evaluations',
          'Categories': 'Categories',
          'Analytics': 'Analytics',
          'Reception': 'Reception',
          'Customers': 'Customers',
          'Invoices': 'Invoices',
          'Profile': 'Profile',
          'Settings': 'Settings',
          'Logout': 'Logout',
          'To view invoices, first go to Customers page and select a customer.': 'To view invoices, first go to Customers page and select a customer.'
        },
        ar: {
          'Items Dashboard': 'لوحة تحكم العناصر',
          'Dashboard': 'لوحة التحكم',
          'Items': 'العناصر',
          'Evaluations': 'التقييمات',
          'Categories': 'الفئات',
          'Analytics': 'التحليلات',
          'Reception': 'الاستقبال',
          'Customers': 'العملاء',
          'Invoices': 'الفواتير',
          'Profile': 'الملف الشخصي',
          'Settings': 'الإعدادات',
          'Logout': 'تسجيل الخروج',
          'To view invoices, first go to Customers page and select a customer.': 'لعرض الفواتير، يرجى أولاً الذهاب إلى صفحة العملاء واختيار عميل معين.'
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

.sidebar-link {
  @apply flex items-center px-6 py-3 text-gray-300 hover:bg-green-700 hover:text-white transition-colors duration-200;
}

.sidebar-link.active {
  @apply bg-green-700 text-white border-r-4 border-white;
}

[dir="rtl"] .sidebar-link.active {
  @apply border-r-0 border-l-4;
}
</style> 