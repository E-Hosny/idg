<template>
  <DashboardLayout :pageTitle="t.pageTitle">
    <div class="max-w-6xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-900">{{ t.pageTitle }}</h2>
          <p class="text-sm text-gray-600 mt-1">{{ t.pageSubtitle }}</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-700 text-white text-sm font-semibold hover:bg-green-800"
          @click="openCreate"
        >
          <i class="fas fa-user-plus mr-2"></i>
          {{ t.addUser }}
        </button>
      </div>

      <div
        v-if="$page.props.flash?.success"
        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
      >
        {{ $page.props.flash.success }}
      </div>

      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-center">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">{{ t.name }}</th>
                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">{{ t.loginEmail }}</th>
                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">{{ t.notifyEmail }}</th>
                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">{{ t.role }}</th>
                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">{{ t.actions }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900 align-middle">{{ user.name }}</td>
                <td class="px-4 py-3 text-sm text-gray-700 align-middle">{{ user.email }}</td>
                <td class="px-4 py-3 text-sm text-gray-700 align-middle">
                  <span v-if="user.uses_login_email_for_notifications" class="text-gray-500 italic">
                    {{ t.usesLoginEmail }} ({{ user.effective_notification_email }})
                  </span>
                  <span v-else>{{ user.notification_email }}</span>
                </td>
                <td class="px-4 py-3 text-sm align-middle">
                  <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold" :class="roleBadgeClass(user.role)">
                    {{ roleLabel(user.role) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm align-middle">
                  <div class="inline-flex flex-wrap items-center justify-center gap-2">
                  <button
                    type="button"
                    class="inline-flex items-center px-3 py-1.5 rounded bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700"
                    @click="openEdit(user)"
                  >
                    <i class="fas fa-edit mr-1"></i>
                    {{ t.edit }}
                  </button>
                  <button
                    v-if="user.id !== $page.props.auth.user?.id"
                    type="button"
                    class="inline-flex items-center px-3 py-1.5 rounded bg-red-600 text-white text-xs font-semibold hover:bg-red-700"
                    @click="confirmDelete(user)"
                  >
                    <i class="fas fa-trash mr-1"></i>
                    {{ t.delete }}
                  </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div
      v-if="modalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">
            {{ editingUser ? t.editUser : t.addUser }}
          </h3>
          <button type="button" class="text-gray-400 hover:text-gray-700" @click="closeModal">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form class="px-6 py-5 space-y-4" @submit.prevent="submit">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t.name }}</label>
            <input v-model="form.name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t.loginEmail }}</label>
            <input v-model="form.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t.notifyEmailOptional }}</label>
            <input
              v-model="form.notification_email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
              :placeholder="t.notifyEmailPlaceholder"
            >
            <p class="mt-1 text-xs text-gray-500">{{ t.notifyEmailHint }}</p>
            <p v-if="form.errors.notification_email" class="mt-1 text-xs text-red-600">{{ form.errors.notification_email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t.role }}</label>
            <select v-model="form.role" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
              <option v-for="r in roles" :key="r.value" :value="r.value">
                {{ roleLabel(r.value) }}
              </option>
            </select>
            <p v-if="form.errors.role" class="mt-1 text-xs text-red-600">{{ form.errors.role }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ editingUser ? t.passwordOptional : t.password }}
            </label>
            <input
              v-model="form.password"
              type="password"
              :required="!editingUser"
              autocomplete="new-password"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
            >
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
          </div>

          <div class="flex gap-3 pt-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 py-2.5 rounded-lg bg-green-700 text-white text-sm font-semibold hover:bg-green-800 disabled:opacity-50"
            >
              {{ form.processing ? t.saving : t.save }}
            </button>
            <button
              type="button"
              class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50"
              @click="closeModal"
            >
              {{ t.cancel }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'UsersIndex',
  components: { DashboardLayout },
  props: {
    users: { type: Array, required: true },
    roles: { type: Array, required: true },
  },
  setup() {
    const form = useForm({
      name: '',
      email: '',
      notification_email: '',
      role: 'receptionist',
      password: '',
    })

    return { form }
  },
  data() {
    return {
      modalOpen: false,
      editingUser: null,
    }
  },
  computed: {
    locale() {
      return this.$page.props.locale === 'ar' ? 'ar' : 'en'
    },
    t() {
      const en = {
        pageTitle: 'Users',
        pageSubtitle: 'Manage accounts, roles, and notification email addresses.',
        addUser: 'Add user',
        editUser: 'Edit user',
        name: 'Name',
        loginEmail: 'Login email',
        notifyEmail: 'Notification email',
        notifyEmailOptional: 'Notification email (optional)',
        notifyEmailPlaceholder: 'Leave empty to use login email',
        notifyEmailHint: 'Workflow bell and email alerts are sent to this address. Empty = login email.',
        usesLoginEmail: 'Login email',
        role: 'Role',
        actions: 'Actions',
        edit: 'Edit',
        delete: 'Delete',
        password: 'Password',
        passwordOptional: 'New password (optional)',
        save: 'Save',
        saving: 'Saving…',
        cancel: 'Cancel',
        confirmDelete: 'Delete this user?',
      }
      const ar = {
        pageTitle: 'المستخدمون',
        pageSubtitle: 'إدارة الحسابات والأدوار وعناوين بريد الإشعارات.',
        addUser: 'إضافة مستخدم',
        editUser: 'تعديل مستخدم',
        name: 'الاسم',
        loginEmail: 'بريد تسجيل الدخول',
        notifyEmail: 'بريد الإشعارات',
        notifyEmailOptional: 'بريد الإشعارات (اختياري)',
        notifyEmailPlaceholder: 'اتركه فارغاً لاستخدام بريد الدخول',
        notifyEmailHint: 'إشعارات الجرس والبريد تُرسل إلى هذا العنوان. فارغ = بريد الدخول.',
        usesLoginEmail: 'بريد الدخول',
        role: 'الدور',
        actions: 'إجراءات',
        edit: 'تعديل',
        delete: 'حذف',
        password: 'كلمة المرور',
        passwordOptional: 'كلمة مرور جديدة (اختياري)',
        save: 'حفظ',
        saving: 'جاري الحفظ…',
        cancel: 'إلغاء',
        confirmDelete: 'حذف هذا المستخدم؟',
      }
      return this.locale === 'ar' ? ar : en
    },
  },
  methods: {
    roleLabel(role) {
      const r = this.roles.find((x) => x.value === role)
      if (!r) return role
      return this.locale === 'ar' ? r.label_ar : r.label_en
    },
    roleBadgeClass(role) {
      const map = {
        admin: 'bg-purple-100 text-purple-800',
        receptionist: 'bg-blue-100 text-blue-800',
        lab: 'bg-teal-100 text-teal-800',
      }
      return map[role] || 'bg-gray-100 text-gray-800'
    },
    openCreate() {
      this.editingUser = null
      this.form.clearErrors()
      this.form.reset()
      this.form.role = 'receptionist'
      this.modalOpen = true
    },
    openEdit(user) {
      this.editingUser = user
      this.form.clearErrors()
      this.form.name = user.name
      this.form.email = user.email
      this.form.notification_email = user.notification_email || ''
      this.form.role = user.role
      this.form.password = ''
      this.modalOpen = true
    },
    closeModal() {
      this.modalOpen = false
      this.editingUser = null
    },
    submit() {
      const payload = {
        name: this.form.name,
        email: this.form.email,
        notification_email: this.form.notification_email || null,
        role: this.form.role,
        password: this.form.password || null,
      }

      if (this.editingUser) {
        this.form.transform(() => payload).put(this.$route('dashboard.users.update', this.editingUser.id), {
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
        })
      } else {
        this.form.transform(() => payload).post(this.$route('dashboard.users.store'), {
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
        })
      }
    },
    confirmDelete(user) {
      if (!confirm(this.t.confirmDelete)) return
      this.$inertia.delete(this.$route('dashboard.users.destroy', user.id), { preserveScroll: true })
    },
  },
}
</script>
