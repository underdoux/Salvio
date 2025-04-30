<template>
  <div class="commission-list">
    <!-- Commission Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Date Range</label>
          <select v-model="filters.period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>

        <div v-if="filters.period === 'custom'" class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Custom Range</label>
          <div class="mt-1 flex space-x-4">
            <input type="date" v-model="filters.startDate" class="block w-full rounded-md border-gray-300 shadow-sm" />
            <input type="date" v-model="filters.endDate" class="block w-full rounded-md border-gray-300 shadow-sm" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="filters.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="paid">Paid</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Commission Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900">Total Commission</h3>
        <p class="mt-2 text-3xl font-semibold text-indigo-600">
          {{ formatCurrency(summary.total) }}
        </p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900">Pending Commission</h3>
        <p class="mt-2 text-3xl font-semibold text-yellow-600">
          {{ formatCurrency(summary.pending) }}
        </p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900">Paid Commission</h3>
        <p class="mt-2 text-3xl font-semibold text-green-600">
          {{ formatCurrency(summary.paid) }}
        </p>
      </div>
    </div>

    <!-- Commission List Table -->
    <div class="bg-white shadow rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Order ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Product
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Original Price
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Commission
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="commission in commissions" :key="commission.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(commission.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                #{{ commission.order_item.order_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ commission.order_item.product.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(commission.order_item.original_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(commission.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(commission.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ commission.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button
                  v-if="canApprove && commission.status === 'pending'"
                  @click="approveCommission(commission.id)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Approve
                </button>
                <button
                  v-if="canReject && commission.status === 'pending'"
                  @click="rejectCommission(commission.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Reject
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </div>
          <div class="flex-1 flex justify-end">
            <button
              v-for="link in pagination.links"
              :key="link.label"
              @click="goToPage(link.url)"
              :disabled="!link.url"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md',
                link.active
                  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
              ]"
              v-html="link.label"
            ></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CommissionList',

  props: {
    canApprove: {
      type: Boolean,
      default: false
    },
    canReject: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      commissions: [],
      summary: {
        total: 0,
        pending: 0,
        paid: 0
      },
      filters: {
        period: 'month',
        startDate: null,
        endDate: null,
        status: ''
      },
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 0,
        links: []
      }
    }
  },

  created() {
    this.fetchCommissions()
  },

  methods: {
    async fetchCommissions() {
      try {
        const response = await axios.get('/api/commissions', {
          params: {
            ...this.filters,
            page: this.pagination.current_page
          }
        })

        this.commissions = response.data.data
        this.summary = response.data.summary
        this.pagination = response.data.meta
      } catch (error) {
        console.error('Error fetching commissions:', error)
      }
    },

    async approveCommission(id) {
      try {
        await axios.post(`/api/commissions/${id}/approve`)
        this.fetchCommissions()
      } catch (error) {
        console.error('Error approving commission:', error)
      }
    },

    async rejectCommission(id) {
      try {
        await axios.post(`/api/commissions/${id}/reject`)
        this.fetchCommissions()
      } catch (error) {
        console.error('Error rejecting commission:', error)
      }
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(value)
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID')
    },

    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        paid: 'bg-blue-100 text-blue-800',
        rejected: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },

    goToPage(url) {
      if (!url) return
      const page = new URL(url).searchParams.get('page')
      this.pagination.current_page = parseInt(page)
      this.fetchCommissions()
    }
  },

  watch: {
    filters: {
      handler() {
        this.pagination.current_page = 1
        this.fetchCommissions()
      },
      deep: true
    }
  }
}
</script>
