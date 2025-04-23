<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Profit Sharing</h2>
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">Investor</th>
          <th class="border px-4 py-2">Ownership %</th>
          <th class="border px-4 py-2">Amount Distributed</th>
          <th class="border px-4 py-2">Distribution Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="distribution in distributions" :key="distribution.id">
          <td class="border px-4 py-2">{{ distribution.capital_investor.name }}</td>
          <td class="border px-4 py-2">{{ distribution.capital_investor.ownership_percentage }}</td>
          <td class="border px-4 py-2">{{ distribution.amount }}</td>
          <td class="border px-4 py-2">{{ distribution.distribution_date }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      distributions: [],
    };
  },
  mounted() {
    // Fetch profit distributions from API or backend
    // For now, use static data or fetch from an endpoint
    this.distributions = [
      {
        id: 1,
        capital_investor: { name: 'Investor A', ownership_percentage: 40 },
        amount: 10000,
        distribution_date: '2025-04-01',
      },
      {
        id: 2,
        capital_investor: { name: 'Investor B', ownership_percentage: 30 },
        amount: 7500,
        distribution_date: '2025-04-01',
      },
    ];
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
