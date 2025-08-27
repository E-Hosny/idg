<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-900 via-green-800 to-green-700 py-12 px-4 sm:px-6 lg:px-8" :class="{ 'font-arabic': $page.props.locale === 'ar' }">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo and Title -->
      <div class="text-center">
        <img class="mx-auto h-24 w-24 rounded-full shadow-lg" src="/images/idg_logo.jpg" alt="IDG">
        <h2 class="mt-6 text-3xl font-extrabold text-white">
          {{ __('Welcome Back') }}
        </h2>
        <p class="mt-2 text-sm text-green-200">
          {{ __('Sign in to IDG Items Dashboard') }}
        </p>
      </div>

      <!-- Login Form -->
      <form class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8" @submit.prevent="submit">
        <div class="space-y-4">
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
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-900">
              {{ __('Remember me') }}
            </label>
          </div>

          <div class="text-sm">
            <a href="#" class="font-medium text-green-600 hover:text-green-500">
              {{ __('Forgot your password?') }}
            </a>
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
              <i v-else class="fas fa-sign-in-alt text-green-500"></i>
            </span>
            {{ form.processing ? __('Signing in...') : __('Sign in') }}
          </button>
        </div>

        <!-- Register Link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            {{ __("Don't have an account?") }}
            <Link :href="$route('register')" class="font-medium text-green-600 hover:text-green-500">
              {{ __('Register here') }}
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
import { Head } from '@inertiajs/vue3'

export default {
  components: {
    Link,
    Head
  },
  setup() {
    const form = useForm({
      email: '',
      password: '',
      remember: false,
    })

    const submit = () => {
      form.post(window.route('login'))
    }

    return {
      form,
      submit
    }
  },
  data() {
    return {
      showPassword: false
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
          'Welcome Back': 'Welcome Back',
          'Sign in to IDG Items Dashboard': 'Sign in to IDG Items Dashboard',
          'Email Address': 'Email Address',
          'Enter your email': 'Enter your email',
          'Password': 'Password',
          'Enter your password': 'Enter your password',
          'Remember me': 'Remember me',
          'Forgot your password?': 'Forgot your password?',
          'Sign in': 'Sign in',
          'Signing in...': 'Signing in...',
          "Don't have an account?": "Don't have an account?",
          'Register here': 'Register here'
        },
        ar: {
          'Welcome Back': 'مرحباً بعودتك',
          'Sign in to IDG Items Dashboard': 'تسجيل الدخول إلى لوحة تحكم العناصر',
          'Email Address': 'عنوان البريد الإلكتروني',
          'Enter your email': 'أدخل بريدك الإلكتروني',
          'Password': 'كلمة المرور',
          'Enter your password': 'أدخل كلمة المرور',
          'Remember me': 'تذكرني',
          'Forgot your password?': 'نسيت كلمة المرور؟',
          'Sign in': 'تسجيل الدخول',
          'Signing in...': 'جاري تسجيل الدخول...',
          "Don't have an account?": "ليس لديك حساب؟",
          'Register here': 'سجل هنا'
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

[dir="rtl"] .text-right {
  text-align: right;
}

[dir="rtl"] .text-left {
  text-align: left;
}
</style> 