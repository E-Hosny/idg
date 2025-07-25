<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-900 via-green-800 to-green-700 py-12 px-4 sm:px-6 lg:px-8" :class="{ 'font-arabic': $page.props.locale === 'ar' }">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo and Title -->
      <div class="text-center">
        <img class="mx-auto h-24 w-24 rounded-full shadow-lg" src="/images/idg_logo.jpg" alt="IDG">
        <h2 class="mt-6 text-3xl font-extrabold text-white">
          {{ __('Create Account') }}
        </h2>
        <p class="mt-2 text-sm text-green-200">
          {{ __('Join IDG Artifacts Dashboard') }}
        </p>
      </div>

      <!-- Register Form -->
      <form class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8" @submit.prevent="submit">
        <div class="space-y-4">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Full Name') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
              </div>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="appearance-none rounded-md relative block w-full pl-10 pr-3 py-2 border"
                :class="[
                  form.errors.name 
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' 
                    : 'border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500'
                ]"
                :placeholder="__('Enter your full name')"
              >
            </div>
            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
              {{ form.errors.name }}
            </div>
          </div>

          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Email Address') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
              </div>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="appearance-none rounded-md relative block w-full pl-10 pr-3 py-2 border"
                :class="[
                  form.errors.email 
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' 
                    : 'border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500'
                ]"
                :placeholder="__('Enter your email')"
              >
            </div>
            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
              {{ form.errors.email }}
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Password') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="appearance-none rounded-md relative block w-full pl-10 pr-10 py-2 border"
                :class="[
                  form.errors.password 
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' 
                    : 'border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500'
                ]"
                :placeholder="__('Enter your password')"
              >
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400 hover:text-gray-600"></i>
              </button>
            </div>
            <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
              {{ form.errors.password }}
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              {{ __('Confirm Password') }}
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                class="appearance-none rounded-md relative block w-full pl-10 pr-10 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500"
                :placeholder="__('Confirm your password')"
              >
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400 hover:text-gray-600"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div>
          <button
            type="submit"
            :disabled="form.processing"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <i v-if="form.processing" class="fas fa-spinner fa-spin text-green-500"></i>
              <i v-else class="fas fa-user-plus text-green-500"></i>
            </span>
            {{ form.processing ? __('Creating account...') : __('Create Account') }}
          </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            {{ __('Already have an account?') }}
            <Link :href="$route('login')" class="font-medium text-green-600 hover:text-green-500">
              {{ __('Sign in here') }}
            </Link>
          </p>
        </div>
      </form>

      <!-- Language Switcher -->
      <div class="text-center">
        <div class="inline-flex rounded-md shadow-sm">
          <Link
            :href="$route('lang.switch', 'en')"
            class="px-4 py-2 text-sm font-medium text-white bg-green-800 rounded-l-md hover:bg-green-700"
            :class="{ 'bg-green-700': $page.props.locale === 'en' }"
            @click.prevent="switchLang('en')"
          >
            English
          </Link>
          <Link
            :href="$route('lang.switch', 'ar')"
            class="px-4 py-2 text-sm font-medium text-white bg-green-800 rounded-r-md hover:bg-green-700 border-l border-green-700"
            :class="{ 'bg-green-700': $page.props.locale === 'ar' }"
            @click.prevent="switchLang('ar')"
          >
            العربية
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useForm, Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },
  setup() {
    const form = useForm({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    })

    const submit = () => {
      form.post(window.route('register'))
    }

    return {
      form,
      submit
    }
  },
  data() {
    return {
      showPassword: false,
      showConfirmPassword: false
    }
  },
  methods: {
    switchLang(locale) {
      this.$inertia.visit(this.$route('lang.switch', locale), {
        onSuccess: () => window.location.reload()
      });
    },
    __(key, replace = {}) {
      const translations = {
        en: {
          'Create Account': 'Create Account',
          'Join IDG Artifacts Dashboard': 'Join IDG Artifacts Dashboard',
          'Full Name': 'Full Name',
          'Enter your full name': 'Enter your full name',
          'Email Address': 'Email Address',
          'Enter your email': 'Enter your email',
          'Password': 'Password',
          'Enter your password': 'Enter your password',
          'Confirm Password': 'Confirm Password',
          'Confirm your password': 'Confirm your password',
          'Create Account': 'Create Account',
          'Creating account...': 'Creating account...',
          'Already have an account?': 'Already have an account?',
          'Sign in here': 'Sign in here'
        },
        ar: {
          'Create Account': 'إنشاء حساب',
          'Join IDG Artifacts Dashboard': 'انضم إلى لوحة تحكم القطع الأثرية',
          'Full Name': 'الاسم الكامل',
          'Enter your full name': 'أدخل اسمك الكامل',
          'Email Address': 'عنوان البريد الإلكتروني',
          'Enter your email': 'أدخل بريدك الإلكتروني',
          'Password': 'كلمة المرور',
          'Enter your password': 'أدخل كلمة المرور',
          'Confirm Password': 'تأكيد كلمة المرور',
          'Confirm your password': 'أكد كلمة المرور',
          'Creating account...': 'جاري إنشاء الحساب...',
          'Already have an account?': 'هل لديك حساب بالفعل؟',
          'Sign in here': 'سجل دخولك هنا'
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