<template>
  <DashboardLayout :pageTitle="t.title">
    <div class="max-w-xl mx-auto">
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
          <h2 class="text-lg font-bold text-gray-900">{{ t.title }}</h2>
          <p class="text-sm text-gray-600 mt-1">{{ t.subtitle }}</p>
        </div>

        <div class="px-6 py-6 space-y-5">
          <div class="rounded-lg border border-gray-200 bg-gray-50/80 p-4 text-sm text-gray-700 space-y-1">
            <p><span class="font-medium">{{ t.mailer }}:</span> {{ mail.mailer }}</p>
            <p><span class="font-medium">{{ t.from }}:</span> {{ mail.from_name }} &lt;{{ mail.from_address }}&gt;</p>
            <p><span class="font-medium">{{ t.domain }}:</span> <span class="font-mono text-xs">{{ mail.domain }}</span></p>
            <p v-if="mail.is_sandbox" class="text-amber-700 text-xs mt-2">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              {{ t.sandboxHint }}
            </p>
          </div>

          <div
            v-if="$page.props.flash?.success"
            class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
          >
            <i class="fas fa-check-circle mr-2"></i>
            {{ $page.props.flash.success }}
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ t.emailLabel }}
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                class="w-full px-3 py-2 border rounded-md text-sm focus:ring-green-500 focus:border-green-500"
                :class="form.errors.email ? 'border-red-500' : 'border-gray-300'"
                :placeholder="t.emailPlaceholder"
              >
              <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-green-700 hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-paper-plane mr-2"></i>
              {{ form.processing ? t.sending : t.sendButton }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'MailTest',
  components: { DashboardLayout },
  props: {
    mail: {
      type: Object,
      required: true,
    },
  },
  setup() {
    const form = useForm({
      email: '',
    })

    return { form }
  },
  computed: {
    locale() {
      return this.$page.props.locale === 'ar' ? 'ar' : 'en'
    },
    t() {
      const en = {
        title: 'Test email (Mailgun)',
        subtitle: 'Enter a recipient address and send a test message.',
        mailer: 'Mailer',
        from: 'From',
        domain: 'Mailgun domain',
        sandboxHint: 'Sandbox: the recipient must be added as an Authorized Recipient in your Mailgun account.',
        emailLabel: 'Recipient email',
        emailPlaceholder: 'e.g. info@example.com',
        sendButton: 'Send test email',
        sending: 'Sending…',
      }
      const ar = {
        title: 'اختبار البريد (Mailgun)',
        subtitle: 'أدخل البريد المستلم وأرسل رسالة اختبار.',
        mailer: 'المُرسِل',
        from: 'من',
        domain: 'نطاق Mailgun',
        sandboxHint: 'Sandbox: يجب أن يكون المستلم مضافاً كمستلم مصرّح به (Authorized Recipient) في حساب Mailgun.',
        emailLabel: 'البريد المستلم',
        emailPlaceholder: 'مثال: info@example.com',
        sendButton: 'إرسال رسالة اختبار',
        sending: 'جاري الإرسال…',
      }
      return this.locale === 'ar' ? ar : en
    },
  },
  methods: {
    submit() {
      this.form.post(this.$route('dashboard.mail-test.send'), {
        preserveScroll: true,
      })
    },
  },
}
</script>
