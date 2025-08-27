<template>
  <DashboardLayout :pageTitle="__('Reception Dashboard')">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">{{ __('Clients List') }}</h2>
        <Link :href="$route('reception.new-client')" class="btn btn-primary">
          <i class="fas fa-user-plus mr-2"></i> {{ __('New Client') }}
        </Link>
      </div>
      <div v-if="clients.data.length === 0" class="text-center text-gray-500 py-12">
        {{ __('No clients yet.') }}
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
          <tr v-for="client in clients.data" :key="client.id" class="border-b hover:bg-gray-50 transition cursor-pointer group"
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
        'Error deleting client. Please try again.': 'خطأ في حذف العميل. يرجى المحاولة مرة أخرى.'
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
    }
  }
}
</script> 