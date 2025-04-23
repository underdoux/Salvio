<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Commission List</h2>
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">User</th>
          <th class="border px-4 py-2">Product</th>
          <th class="border px-4 py-2">Category</th>
          <th class="border px-4 py-2">Commission Rate (%)</th>
          <th class="border px-4 py-2">Min Cap</th>
          <th class="border px-4 py-2">Max Cap</th>
          <th class="border px-4 py-2">Calculated Commission</th>
          <th class="border px-4 py-2">Rule Type</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="commission in commissions" :key="commission.id">
          <td class="border px-4 py-2">{{ commission.user.name }}</td>
          <td class="border px-4 py-2">{{ commission.product.name }}</td>
          <td class="border px-4 py-2">{{ commission.category.name }}</td>
          <td class="border px-4 py-2">{{ commission.commission_rate }}</td>
          <td class="border px-4 py-2">{{ commission.min_cap }}</td>
          <td class="border px-4 py-2">{{ commission.max_cap }}</td>
          <td class="border px-4 py-2">{{ commission.calculated_commission }}</td>
          <td class="border px-4 py-2">{{ commission.rule_type }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      commissions: [],
    };
  },
  mounted() {
    // Fetch commissions from API or backend
    // For now, use static data or fetch from an endpoint
    this.commissions = [
      {
        id: 1,
        user: { name: 'John Doe' },
        product: { name: 'Product A' },
        category: { name: 'Category 1' },
        commission_rate: 5,
        min_cap: 10,
        max_cap: 100,
        calculated_commission: 20,
        rule_type: 'global',
      },
      {
        id: 2,
        user: { name: 'Jane Smith' },
        product: { name: 'Product B' },
        category: { name: 'Category 2' },
        commission_rate: 7,
        min_cap: 15,
        max_cap: 150,
        calculated_commission: 30,
        rule_type: 'product',
      },
    ];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
