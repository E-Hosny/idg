<template>
  <DashboardLayout :pageTitle="__('Reception Dashboard')">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">{{ __('Clients List') }}</h2>
        <Link :href="$route('reception.new-client')" class="btn btn-primary">
          <i class="fas fa-user-plus mr-2"></i> {{ __('New Client') }}
        </Link>
      </div>

      <!-- Advanced Search Form -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Advanced Search') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Customer Code Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Customer Code') }}</label>
            <input 
              v-model="searchForm.customer_code" 
              type="text" 
              :placeholder="__('Enter customer code')"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>

          <!-- Phone Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone') }}</label>
            <input 
              v-model="searchForm.phone" 
              type="text" 
              :placeholder="__('Enter phone number')"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>

          <!-- Email Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
            <input 
              v-model="searchForm.email" 
              type="text" 
              :placeholder="__('Enter email address')"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Search Actions -->
        <div class="flex justify-between items-center mt-4">
          <div class="text-sm text-gray-600">
            {{ __('Found') }}: <span class="font-semibold">{{ filteredClients.length }}</span> {{ __('clients') }}
          </div>
          <div class="flex gap-2">
            <button 
              @click="clearSearch" 
              class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
            >
              {{ __('Clear Search') }}
            </button>
            <button 
              @click="performSearch" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
            >
              <i class="fas fa-search mr-2"></i>{{ __('Search') }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredClients.length === 0" class="text-center text-gray-500 py-12">
        <div v-if="hasActiveSearch" class="mb-4">
          <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
          <p class="text-lg text-gray-600 mb-2">{{ __('No clients found matching your search criteria.') }}</p>
          <button @click="clearSearch" class="text-blue-600 hover:text-blue-800 underline">
            {{ __('Clear search and show all clients') }}
          </button>
        </div>
        <div v-else>
          {{ __('No clients yet.') }}
        </div>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow border">
        <thead>
          <tr class="bg-gray-100 border-b">
            <th class="px-4 py-2 text-left font-bold">{{ __('Customer Code') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Name') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Phone') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Email') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Items') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in filteredClients" :key="client.id" class="border-b hover:bg-gray-50 transition cursor-pointer group"
              @click="goToDetails(client.id)">
            <td class="px-4 py-2">{{ client.customer_code }}</td>
            <td class="px-4 py-2">{{ client.full_name }}</td>
            <td class="px-4 py-2">{{ client.phone }}</td>
            <td class="px-4 py-2">{{ client.email }}</td>
            <td class="px-4 py-2">{{ client.artifacts.length }}</td>
            <td class="px-4 py-2 flex gap-1" @click.stop>
              <button @click="addArtifact(client.id)" class="p-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors" :title="__('Add Item')">
                <i class="fas fa-plus text-sm"></i>
              </button>
              <Link :href="$route('reception.show-client', client.id)" class="p-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 transition-colors" :title="__('Show Details')">
                <i class="fas fa-eye text-sm"></i>
              </Link>
              <button @click="deleteClient(client.id, client.full_name)" class="p-2 bg-red-100 text-red-800 rounded hover:bg-red-200 transition-colors" :title="__('Delete')">
                <i class="fas fa-trash text-sm"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
        </div>
      </div>
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: { DashboardLayout, Link },
  props: {
    clients: Object
  },
  data() {
    return {
      searchForm: {
        customer_code: '',
        phone: '',
        email: ''
      },
      searchTimeout: null
    }
  },
  computed: {
    filteredClients() {
      if (!this.hasActiveSearch) {
        return this.clients.data || []
      }

      return (this.clients.data || []).filter(client => {
        const customerCode = this.searchForm.customer_code.toLowerCase()
        const phone = this.searchForm.phone.toLowerCase()
        const email = this.searchForm.email.toLowerCase()

        return (
          (customerCode === '' || (client.customer_code && client.customer_code.toLowerCase().includes(customerCode))) &&
          (phone === '' || (client.phone && client.phone.toLowerCase().includes(phone))) &&
          (email === '' || (client.email && client.email.toLowerCase().includes(email)))
        )
      })
    },
    hasActiveSearch() {
      return Object.values(this.searchForm).some(value => value.trim() !== '')
    }
  },
  methods: {
    __(key) {
      const t = {
        'Reception Dashboard': 'لوحة استقبال العملاء',
        'Clients List': 'قائمة العملاء',
        'New Client': 'استقبال عميل جديد',
        'No clients yet.': 'لا يوجد عملاء بعد',
        'Name': 'الاسم',
        'Phone': 'الهاتف',
        'Email': 'البريد الإلكتروني',
        'Items': 'العناصر',
        'Actions': 'إجراءات',
        'Add Item': 'إضافة عنصر',
        'Show Details': 'عرض التفاصيل',
        'Delete': 'حذف',
        'Are you sure you want to delete': 'هل أنت متأكد من أنك تريد حذف',
        'Error deleting client. Please try again.': 'خطأ في حذف العميل. يرجى المحاولة مرة أخرى.',
        'Advanced Search': 'البحث المتقدم',
        'Enter customer code': 'أدخل كود العميل',
        'Enter phone number': 'أدخل رقم الهاتف',
        'Enter email address': 'أدخل البريد الإلكتروني',
        'Found': 'تم العثور على',
        'clients': 'عميل',
        'Clear Search': 'مسح البحث',
        'Search': 'بحث',
        'No clients found matching your search criteria.': 'لم يتم العثور على عملاء يطابقون معايير البحث.',
        'Clear search and show all clients': 'مسح البحث وعرض جميع العملاء'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    goToDetails(id) {
      this.$inertia.visit(this.$route('reception.show-client', id))
    },
    addArtifact(clientId) {
      // Redirect to add artifact page
      this.$inertia.visit(this.$route('reception.artifact.create', clientId))
    },
    deleteClient(clientId, clientName) {
      if (confirm(this.__('Are you sure you want to delete') + ' "' + clientName + '"?')) {
        this.$inertia.delete(this.$route('reception.delete-client', clientId), {
          onSuccess: () => {
            // Client will be automatically removed from the list
          },
          onError: (errors) => {
            if (errors.error) {
              alert(errors.error)
            } else {
              alert(this.__('Error deleting client. Please try again.'))
            }
          }
        })
      }
    },
    debouncedSearch() {
      // إلغاء البحث السابق
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      
      // تأخير البحث لمدة 300 مللي ثانية لتحسين الأداء
      this.searchTimeout = setTimeout(() => {
        this.performSearch()
      }, 300)
    },
    performSearch() {
      // البحث يتم تلقائياً عبر computed property
      // هذه الدالة يمكن استخدامها للبحث الفوري
    },
    clearSearch() {
      this.searchForm = {
        customer_code: '',
        phone: '',
        email: ''
      }
    }
  }
}
</script> 