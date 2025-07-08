<template>
  <DashboardLayout :pageTitle="__('Reception Dashboard')">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">{{ __('Clients List') }}</h2>
      <Link :href="$route('reception.new')" class="btn btn-primary">
        <i class="fas fa-user-plus mr-2"></i> {{ __('New Client') }}
      </Link>
    </div>
    <div v-if="clients.data.length === 0" class="text-center text-gray-500 py-12">
      {{ __('No clients yet.') }}
    </div>
    <div v-else>
      <table class="min-w-full bg-white rounded shadow border">
        <thead>
          <tr class="bg-gray-100 border-b">
            <th class="px-4 py-2 text-left font-bold">#</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Name') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Phone') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Email') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Artifacts') }}</th>
            <th class="px-4 py-2 text-left font-bold">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients.data" :key="client.id" class="border-b hover:bg-gray-50 transition cursor-pointer group"
              @click="goToDetails(client.id)">
            <td class="px-4 py-2">{{ client.id }}</td>
            <td class="px-4 py-2">{{ client.full_name }}</td>
            <td class="px-4 py-2">{{ client.phone }}</td>
            <td class="px-4 py-2">{{ client.email }}</td>
            <td class="px-4 py-2">{{ client.artifacts.length }}</td>
            <td class="px-4 py-2 flex gap-2" @click.stop>
              <Link :href="$route('reception.artifact.create', { client: client.id })" class="inline-flex items-center px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-xs font-semibold">
                <i class="fas fa-plus mr-1"></i> {{ __('Add Artifact') }}
              </Link>
              <Link :href="$route('reception.client.show', client.id)" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 text-xs font-semibold border border-blue-200">
                <i class="fas fa-eye mr-1"></i> {{ __('Show Details') }}
              </Link>
            </td>
          </tr>
        </tbody>
      </table>
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
        'Artifacts': 'القطع',
        'Actions': 'إجراءات',
        'Add Artifact': 'إضافة قطعة'
      }
      return this.$page.props.locale === 'ar' ? t[key] || key : key
    },
    goToDetails(id) {
      this.$inertia.visit(this.$route('reception.client.show', id))
    }
  }
}
</script> 