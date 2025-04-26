<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Profit Sharing</h2>
    <table class="min-w-full border border-gray-300">
      <thead>
        <tr>
          <th class="border px-4 py-2">Investor Name</th>
          <th class="border px-4 py-2">Capital Amount</th>
          <th class="border px-4 py-2">Ownership Percentage</th>
          <th class="border px-4 py-2">Distributed Amount</th>
          <th class="border px-4 py-2">Distribution Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="distribution in distributions" :key="distribution.id">
          <td class="border px-4 py-2">{{ distribution.capital_investor.name }}</td>
          <td class="border px-4 py-2">${{ distribution.capital_investor.capital_amount.toFixed(2) }}</td>
          <td class="border px-4 py-2">{{ distribution.capital_investor.ownership_percentage }}%</td>
          <td class="border px-4 py-2">${{ distribution.amount.toFixed(2) }}</td>
          <td class="border px-4 py-2">{{ new Date(distribution.distribution_date).toLocaleDateString() }}</td>
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
    fetch('/api/profit-distributions', {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      credentials: 'include',
    })
      .then(response => response.json())
      .then(data => {
        this.distributions = data;
      })
      .catch(error => {
        console.error('Error fetching profit distributions:', error);
      });
  },
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
