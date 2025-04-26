<template>
  <nav class="bg-white shadow h-full">
    <ul class="flex flex-col p-4 space-y-2">
      <li v-if="hasRole('Admin') || hasRole('Cashier') || hasRole('Sales')">
        <a href="/dashboard" class="block px-4 py-2 rounded hover:bg-gray-100">Dashboard</a>
      </li>
      <li v-if="hasRole('Admin')">
        <a href="/products" class="block px-4 py-2 rounded hover:bg-gray-100">Product Management</a>
      </li>
      <li v-if="hasRole('Admin') || hasRole('Cashier') || hasRole('Sales')">
        <a href="/orders" class="block px-4 py-2 rounded hover:bg-gray-100">Order Management</a>
      </li>
      <li v-if="hasRole('Admin')">
        <a href="/commissions" class="block px-4 py-2 rounded hover:bg-gray-100">Commissions</a>
      </li>
      <li v-if="hasRole('Admin')">
        <a href="/profit-distributions" class="block px-4 py-2 rounded hover:bg-gray-100">Profit Sharing</a>
      </li>
      <li v-if="hasRole('Admin') || hasRole('Cashier') || hasRole('Sales')">
        <a href="/notifications" class="block px-4 py-2 rounded hover:bg-gray-100">Notifications</a>
      </li>
      <li v-if="hasRole('Admin')">
        <a href="/reports" class="block px-4 py-2 rounded hover:bg-gray-100">Reports</a>
      </li>
      <li v-if="hasRole('Admin')">
        <a href="/insights" class="block px-4 py-2 rounded hover:bg-gray-100">Insights</a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      userRoles: [], // Fetch from API or auth state
    };
  },
  methods: {
    hasRole(role) {
      return this.userRoles.includes(role);
    },
  },
  mounted() {
    // Fetch user roles from API or auth state
    // For now, mock roles
    this.userRoles = ['Admin'];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
