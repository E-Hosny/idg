<template>
  <DashboardLayout :page-title="t.pageTitle">
    <div class="max-w-7xl mx-auto space-y-6">
      <div>
        <h2 class="text-xl font-bold text-gray-900">{{ t.pageTitle }}</h2>
        <p class="text-sm text-gray-600 mt-1">{{ t.pageSubtitle }}</p>
        <p class="text-xs text-gray-500 mt-2 font-mono" dir="ltr">/dashboard/customers/visibility</p>
      </div>

      <div
        v-if="$page.props.flash?.success"
        class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
      >
        {{ $page.props.flash.success }}
      </div>

      <div
        v-if="error"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
      >
        {{ error }}
      </div>

      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div class="flex-1">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="t.searchPlaceholder"
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-green-500 focus:border-green-500"
              />
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <select
              v-model="visibilityFilter"
              class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-green-500 focus:border-green-500"
            >
              <option value="all">{{ t.allCustomers }}</option>
              <option value="visible">{{ t.visibleOnly }}</option>
              <option value="hidden">{{ t.hiddenOnly }}</option>
            </select>

            <button
              type="button"
              class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50"
              @click="toggleSelectAll"
            >
              <i class="fas fa-check-double mr-2"></i>
              {{ allFilteredSelected ? t.deselectAll : t.selectAll }}
            </button>

            <button
              type="button"
              :disabled="!canHide || processing"
              class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 disabled:opacity-50"
              @click="hideSelected"
            >
              <i class="fas fa-eye-slash mr-2"></i>
              {{ t.hideSelected }} ({{ selectedVisibleCount }})
            </button>

            <button
              type="button"
              :disabled="!canShow || processing"
              class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
              @click="showSelected"
            >
              <i class="fas fa-eye mr-2"></i>
              {{ t.showSelected }} ({{ selectedHiddenCount }})
            </button>
          </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 text-sm text-gray-600 flex flex-wrap gap-4">
          <span>{{ t.total }}: {{ customers.length }}</span>
          <span>{{ t.hidden }}: {{ hiddenCount }}</span>
          <span>{{ t.visible }}: {{ customers.length - hiddenCount }}</span>
          <span>{{ t.selected }}: {{ selectedIds.length }}</span>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left">
                  <input
                    type="checkbox"
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                    :checked="allFilteredSelected"
                    :indeterminate.prop="someFilteredSelected && !allFilteredSelected"
                    @change="toggleSelectAll"
                  />
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.reference }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.customer }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.organization }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.email }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.status }}</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t.visibility }}</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="filteredCustomers.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  {{ t.noCustomers }}
                </td>
              </tr>
              <tr
                v-for="customer in filteredCustomers"
                :key="customer.id"
                class="hover:bg-gray-50"
                :class="{ 'bg-amber-50': customer.is_hidden }"
              >
                <td class="px-4 py-3">
                  <input
                    type="checkbox"
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                    :value="customer.id"
                    v-model="selectedIds"
                  />
                </td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900" dir="ltr">
                  {{ formatReference(customer.id) }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">
                  {{ customer.name || customer.display_name || '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ customer.organization || '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700">
                  {{ customer.email || customer.email_address || '—' }}
                </td>
                <td class="px-4 py-3 text-sm">
                  <span
                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold"
                    :class="customer.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ customer.status || '—' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <span
                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold"
                    :class="customer.is_hidden ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800'"
                  >
                    {{ customer.is_hidden ? t.hiddenLabel : t.visibleLabel }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { router } from '@inertiajs/vue3'

export default {
  components: {
    DashboardLayout,
  },
  props: {
    customers: {
      type: Array,
      default: () => [],
    },
    hiddenCount: {
      type: Number,
      default: 0,
    },
    error: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      searchQuery: '',
      visibilityFilter: 'all',
      selectedIds: [],
      processing: false,
    }
  },
  computed: {
    locale() {
      return this.$page.props.locale || 'ar'
    },
    t() {
      const ar = {
        pageTitle: 'إدارة ظهور العملاء',
        pageSubtitle: 'حدد العملاء لإخفائهم مؤقتاً من قائمة العملاء أو لإعادة إظهارهم. هذه الصفحة متاحة للمدير فقط.',
        searchPlaceholder: 'بحث بالاسم أو البريد أو الرقم المرجعي...',
        allCustomers: 'جميع العملاء',
        visibleOnly: 'الظاهرون فقط',
        hiddenOnly: 'المخفيون فقط',
        selectAll: 'تحديد الكل',
        deselectAll: 'إلغاء التحديد',
        hideSelected: 'إخفاء المحدد',
        showSelected: 'إظهار المحدد',
        total: 'الإجمالي',
        hidden: 'مخفي',
        visible: 'ظاهر',
        selected: 'محدد',
        reference: 'الرقم المرجعي',
        customer: 'العميل',
        organization: 'المنشأة',
        email: 'البريد',
        status: 'الحالة',
        visibility: 'الظهور',
        hiddenLabel: 'مخفي',
        visibleLabel: 'ظاهر',
        noCustomers: 'لا يوجد عملاء',
        confirmHide: 'هل تريد إخفاء العملاء المحددين من قائمة العملاء؟',
        confirmShow: 'هل تريد إعادة إظهار العملاء المحددين في قائمة العملاء؟',
      }

      const en = {
        pageTitle: 'Customer Visibility',
        pageSubtitle: 'Select customers to temporarily hide from the customers list or restore them. This page is admin-only.',
        searchPlaceholder: 'Search by name, email, or reference...',
        allCustomers: 'All customers',
        visibleOnly: 'Visible only',
        hiddenOnly: 'Hidden only',
        selectAll: 'Select all',
        deselectAll: 'Deselect all',
        hideSelected: 'Hide selected',
        showSelected: 'Show selected',
        total: 'Total',
        hidden: 'Hidden',
        visible: 'Visible',
        selected: 'Selected',
        reference: 'Reference',
        customer: 'Customer',
        organization: 'Organization',
        email: 'Email',
        status: 'Status',
        visibility: 'Visibility',
        hiddenLabel: 'Hidden',
        visibleLabel: 'Visible',
        noCustomers: 'No customers found',
        confirmHide: 'Hide the selected customers from the customers list?',
        confirmShow: 'Restore the selected customers to the customers list?',
      }

      return this.locale === 'ar' ? ar : en
    },
    filteredCustomers() {
      let list = this.customers

      if (this.visibilityFilter === 'visible') {
        list = list.filter((customer) => !customer.is_hidden)
      } else if (this.visibilityFilter === 'hidden') {
        list = list.filter((customer) => customer.is_hidden)
      }

      if (!this.searchQuery.trim()) {
        return list
      }

      const query = this.searchQuery.toLowerCase().trim()

      return list.filter((customer) => {
        const ref = this.formatReference(customer.id).toLowerCase()
        return (
          ref.includes(query) ||
          String(customer.id).includes(query) ||
          (customer.name && customer.name.toLowerCase().includes(query)) ||
          (customer.display_name && customer.display_name.toLowerCase().includes(query)) ||
          (customer.organization && customer.organization.toLowerCase().includes(query)) ||
          (customer.email && customer.email.toLowerCase().includes(query)) ||
          (customer.email_address && customer.email_address.toLowerCase().includes(query))
        )
      })
    },
    selectedCustomers() {
      return this.customers.filter((customer) => this.selectedIds.includes(customer.id))
    },
    selectedVisibleCount() {
      return this.selectedCustomers.filter((customer) => !customer.is_hidden).length
    },
    selectedHiddenCount() {
      return this.selectedCustomers.filter((customer) => customer.is_hidden).length
    },
    canHide() {
      return this.selectedVisibleCount > 0
    },
    canShow() {
      return this.selectedHiddenCount > 0
    },
    allFilteredSelected() {
      return this.filteredCustomers.length > 0
        && this.filteredCustomers.every((customer) => this.selectedIds.includes(customer.id))
    },
    someFilteredSelected() {
      return this.filteredCustomers.some((customer) => this.selectedIds.includes(customer.id))
    },
  },
  methods: {
    formatReference(id) {
      return `CUS${String(id).padStart(3, '0')}`
    },
    toggleSelectAll() {
      if (this.allFilteredSelected) {
        const filteredIds = this.filteredCustomers.map((customer) => customer.id)
        this.selectedIds = this.selectedIds.filter((id) => !filteredIds.includes(id))
        return
      }

      const merged = new Set([...this.selectedIds, ...this.filteredCustomers.map((customer) => customer.id)])
      this.selectedIds = Array.from(merged)
    },
    hideSelected() {
      const ids = this.selectedCustomers
        .filter((customer) => !customer.is_hidden)
        .map((customer) => customer.id)

      if (ids.length === 0 || !window.confirm(this.t.confirmHide)) {
        return
      }

      this.submitAction('hide', ids)
    },
    showSelected() {
      const ids = this.selectedCustomers
        .filter((customer) => customer.is_hidden)
        .map((customer) => customer.id)

      if (ids.length === 0 || !window.confirm(this.t.confirmShow)) {
        return
      }

      this.submitAction('show', ids)
    },
    submitAction(action, customerIds) {
      this.processing = true

      router.post(route(`dashboard.customers.visibility.${action}`), {
        customer_ids: customerIds,
      }, {
        preserveScroll: true,
        onFinish: () => {
          this.processing = false
          this.selectedIds = []
        },
      })
    },
  },
}
</script>
