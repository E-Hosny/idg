<template>
  <div v-if="enabled" class="relative" ref="root">
    <button
      type="button"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 rounded-full"
      :aria-label="bellAriaLabel"
      @click.stop="togglePanel"
    >
      <i class="fas fa-bell text-lg"></i>
      <span
        v-if="unreadCount > 0"
        class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] px-1 flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] font-bold leading-none"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <div
      v-if="panelOpen"
      class="absolute mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-[min(24rem,70vh)] flex flex-col"
      :class="panelPositionClass"
      @click.stop
    >
      <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-2 flex-shrink-0">
        <h3 class="text-sm font-semibold text-gray-900">{{ titleText }}</h3>
        <button
          v-if="unreadCount > 0"
          type="button"
          class="text-xs text-green-700 hover:text-green-900 font-medium whitespace-nowrap"
          @click="markAllRead"
        >
          {{ markAllText }}
        </button>
      </div>

      <div v-if="loading" class="px-4 py-8 text-center text-sm text-gray-500">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        {{ loadingText }}
      </div>

      <div v-else-if="items.length === 0" class="px-4 py-8 text-center text-sm text-gray-500">
        {{ emptyText }}
      </div>

      <ul v-else class="overflow-y-auto flex-1 divide-y divide-gray-100">
        <li
          v-for="item in items"
          :key="item.id"
          class="px-4 py-3 transition-colors"
          :class="[
            !item.read_at ? 'bg-green-50/60' : '',
            !item.read_at ? 'cursor-pointer hover:bg-green-50' : '',
          ]"
          @click="markNotificationRead(item)"
        >
          <p class="text-sm text-gray-900 leading-snug">{{ item.message }}</p>
          <p v-if="item.actor_name" class="text-xs text-gray-500 mt-1">{{ item.actor_name }}</p>
          <p class="text-xs text-gray-400 mt-1">{{ item.created_at_human }}</p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotificationBell',
  props: {
    variant: {
      type: String,
      default: 'light',
    },
  },
  data() {
    return {
      panelOpen: false,
      loading: false,
      items: [],
      unreadCount: 0,
      pollTimer: null,
    }
  },
  computed: {
    enabled() {
      return this.$page.props.notifications != null
    },
    locale() {
      return this.$page.props.locale || 'en'
    },
    panelPositionClass() {
      return this.$page.props.locale === 'ar' ? 'left-0' : 'right-0'
    },
    titleText() {
      return this.locale === 'ar' ? 'الإشعارات' : 'Notifications'
    },
    markAllText() {
      return this.locale === 'ar' ? 'تعليم الكل كمقروء' : 'Mark all read'
    },
    emptyText() {
      return this.locale === 'ar' ? 'لا توجد إشعارات' : 'No notifications'
    },
    loadingText() {
      return this.locale === 'ar' ? 'جاري التحميل...' : 'Loading...'
    },
    bellAriaLabel() {
      return this.locale === 'ar' ? 'الإشعارات' : 'Notifications'
    },
  },
  mounted() {
    this.unreadCount = this.$page.props.notifications?.unread_count ?? 0
    document.addEventListener('click', this.handleClickOutside)
    this.pollTimer = window.setInterval(() => this.fetchUnreadCount(), 45000)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
    if (this.pollTimer) {
      clearInterval(this.pollTimer)
    }
  },
  methods: {
    handleClickOutside(event) {
      if (this.panelOpen && !this.$refs.root?.contains(event.target)) {
        this.panelOpen = false
      }
    },
    togglePanel() {
      this.panelOpen = !this.panelOpen
      if (this.panelOpen) {
        this.fetchNotifications()
      }
    },
    async fetchUnreadCount() {
      if (!this.enabled) return
      try {
        const { data } = await window.axios.get('/dashboard/notifications/unread-count')
        this.unreadCount = data.unread_count ?? 0
      } catch {
        /* ignore polling errors */
      }
    },
    async fetchNotifications() {
      this.loading = true
      try {
        const { data } = await window.axios.get('/dashboard/notifications', { params: { limit: 25 } })
        this.items = data.notifications ?? []
        this.unreadCount = data.unread_count ?? 0
      } catch {
        this.items = []
      } finally {
        this.loading = false
      }
    },
    async markAllRead() {
      try {
        await window.axios.post('/dashboard/notifications/read-all')
        this.unreadCount = 0
        this.items = this.items.map((n) => ({ ...n, read_at: new Date().toISOString() }))
      } catch {
        /* ignore */
      }
    },
    async markNotificationRead(item) {
      if (item.read_at) {
        return
      }
      try {
        const { data } = await window.axios.post(`/dashboard/notifications/${item.id}/read`)
        this.unreadCount = data.unread_count ?? 0
        item.read_at = new Date().toISOString()
      } catch {
        /* ignore */
      }
    },
  },
}
</script>
